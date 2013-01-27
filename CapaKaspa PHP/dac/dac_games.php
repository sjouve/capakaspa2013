<?
/* Accès aux données concernant la table Games, History, Pieces, Messages */

/*
 * Load a game by ID
 */
function getGame($gameID)
{
	// Informations sur la partie : voir le type de partie (position normale ou pas) et le problème du code ECO
	$tmpQuery = "SELECT G.whitePlayer whitePlayer, G.blackPlayer blackPlayer, G.dialogue dialogue, G.position position, G.eco eco, 
	G.lastMove, G.dateCreated, G.type type, G.flagBishop flagBishop, G.flagKnight flagKnight, G.flagRook flagRook, G.flagQueen flagQueen,
	E.name ecoName, 
	W.nick whiteNick, W.elo whiteElo, W.socialNetwork whiteSocialNet, W.socialID whiteSocialID, W.firstName whiteFirstName, W.lastName whiteLastName, 
	B.nick blackNick, B.elo blackElo, B.socialNetwork blackSocialNet, B.socialID blackSocialID, B.firstName blackFirstName, B.lastName blackLastName
	FROM games G left join eco E on E.eco = G.eco AND E.ecoLang = '".getLang()."', players W, players B
	WHERE gameID = ".$gameID."
	AND G.whitePlayer = W.playerID
	AND G.blackPlayer = B.playerID";
	
	$tmpGames = mysql_query($tmpQuery);
	$tmpGame = mysql_fetch_array($tmpGames, MYSQL_ASSOC);
	
	return $tmpGame;
}

function countActiveGame($playerID)
{
	$activeGames = mysql_query("SELECT count(gameID) nbGames FROM games WHERE (whitePlayer = ".$playerID." OR blackPlayer = ".$playerID.") AND gameMessage=''");
	return mysql_fetch_array($activeGames, MYSQL_ASSOC);
}

function countActiveGameForAll()
{
	$activeGames = mysql_query("SELECT count(gameID) nbGames FROM games WHERE gameMessage=''") or die(mysql_error());
	return mysql_fetch_array($activeGames, MYSQL_ASSOC);
}

/**
* Liste des parties terminées d'un joueur pour calcul moyenne elo adversaire
**/
function listEndedGames($playerID, $dateDeb, $dateFin)
{
	$tmpGames = mysql_query("SELECT G.whitePlayer whitePlayer, EW.elo whiteElo, G.blackPlayer blackPlayer, EB.elo blackElo
	                                FROM games G, players W, players B, elo_history EW, elo_history EB 
									WHERE (G.gameMessage <> '' AND G.gameMessage <> 'playerInvited' AND G.gameMessage <> 'inviteDeclined')
									AND (G.whitePlayer = ".$playerID." OR G.blackPlayer = ".$playerID.")
									AND W.playerID = G.whitePlayer AND B.playerID = G.blackPlayer
									AND W.playerID = EW.playerID AND B.playerID = EB.playerID
									AND EW.eloDate > '2012-09-30' AND EB.eloDate > '2012-09-30'
									AND G.type=0 AND G.lastMove >= '".$dateDeb."' AND G.lastMove <= '".$dateFin."'");
	
	return $tmpGames;
}


function countLost($playerID, $dateDeb, $dateFin)
{							
									
	$tmpGames = mysql_query("SELECT count(G.gameID) nbGames
	                                FROM games G, players W, players B
									WHERE (G.gameMessage <> '' AND G.gameMessage <> 'playerInvited' AND G.gameMessage <> 'inviteDeclined')
	                                AND (G.whitePlayer = ".$playerID." OR G.blackPlayer = ".$playerID.")
	                                AND ((G.gameMessage = 'playerResigned' AND G.messageFrom = 'white' AND G.whitePlayer = ".$playerID.")
	                                    OR (G.gameMessage = 'playerResigned' AND G.messageFrom = 'black' AND G.blackPlayer = ".$playerID.")
	                                    OR (G.gameMessage = 'checkMate' AND G.messageFrom = 'black' AND G.whitePlayer = ".$playerID.")
	                                    OR (G.gameMessage = 'checkMate' AND G.messageFrom = 'white' AND G.blackPlayer = ".$playerID.")) 
									AND W.playerID = G.whitePlayer AND B.playerID = G.blackPlayer
									AND G.type=0 AND G.lastMove >= '".$dateDeb."' AND G.lastMove <= '".$dateFin."'");
	
	return mysql_fetch_array($tmpGames, MYSQL_ASSOC);
}

function countDraw($playerID, $dateDeb, $dateFin)
{							
									
	$tmpGames = mysql_query("SELECT count(G.gameID) nbGames
	                            FROM games G, players W, players B
                                WHERE (G.gameMessage <> '' AND G.gameMessage <> 'playerInvited' AND G.gameMessage <> 'inviteDeclined')
                                AND (G.whitePlayer = ".$playerID." OR G.blackPlayer = ".$playerID.")
                                AND G.gameMessage = 'draw'
                                AND W.playerID = G.whitePlayer AND B.playerID = G.blackPlayer
								AND G.type=0 AND G.lastMove >= '".$dateDeb."' AND G.lastMove <= '".$dateFin."'");
	
	return mysql_fetch_array($tmpGames, MYSQL_ASSOC);
}

function countWin($playerID, $dateDeb, $dateFin)
{							
									
	$tmpGames = mysql_query("SELECT count(G.gameID) nbGames
	                            FROM games G, players W, players B
                                WHERE (G.gameMessage <> '' AND G.gameMessage <> 'playerInvited' AND G.gameMessage <> 'inviteDeclined')
                                AND (G.whitePlayer = ".$playerID." OR G.blackPlayer = ".$playerID.")
                                AND ((G.gameMessage = 'playerResigned' AND G.messageFrom = 'white' AND G.blackPlayer = ".$playerID.")
                                    OR (G.gameMessage = 'playerResigned' AND G.messageFrom = 'black' AND G.whitePlayer = ".$playerID.")
                                    OR (G.gameMessage = 'checkMate' AND G.messageFrom = 'black' AND G.blackPlayer = ".$playerID.")
                                    OR (G.gameMessage = 'checkMate' AND G.messageFrom = 'white' AND G.whitePlayer = ".$playerID."))
                                AND W.playerID = G.whitePlayer AND B.playerID = G.blackPlayer
								AND G.type=0 AND G.lastMove >= '".$dateDeb."' AND G.lastMove <= '".$dateFin."'");
	
	return mysql_fetch_array($tmpGames, MYSQL_ASSOC);
}

function calculMoyenneElo($playerID, $dateDeb, $dateFin)
{							
									
	$tmpGames = mysql_query("SELECT  nbGames
	                            FROM games G, players W, players B
                                WHERE (G.gameMessage <> '' AND G.gameMessage <> 'playerInvited' AND G.gameMessage <> 'inviteDeclined')
                                AND (G.whitePlayer = ".$playerID." OR G.blackPlayer = ".$playerID.")
                                AND W.playerID = G.whitePlayer AND B.playerID = G.blackPlayer
								AND G.type=0 AND G.lastMove >= '".$dateDeb."' AND G.lastMove <= '".$dateFin."'");
	
	return mysql_fetch_array($tmpGames, MYSQL_ASSOC);
}

/*
 * Recherche de parties
 * Critères :
 * - Etat : En cours, terminées
 * - Joueurs : Tous, Id joueur
 * - Pas le joueur connecté
 * - Couleur du joueur (si sélectionné) : Blancs, Noirs
 * - Résultat du joueur : Victoire, Défaite, Nulle
 * - Type partie : Normal ou avec position
 * - Code ECO
 * - Plage date de fin (sur date du dernier coup)
 * - Plage date de début
 */
function searchGames($debut, $limit)
{
	// TODO Recherche de parties à implémenter
	$requete = "SELECT G.gameID, G.eco eco, W.playerID whitePlayerID, W.nick whiteNick, B.playerID blackPlayerID, B.nick blackNick, G.gameMessage, G.messageFrom, DATE_FORMAT(G.dateCreated, '%d/%m/%Y %T') dateCreatedF, DATE_FORMAT(G.lastMove, '%d/%m/%Y %T') lastMove
                FROM games G, players W, players B
                WHERE W.playerID = G.whitePlayer
                AND AND B.playerID = G.blackPlayer  
                AND G.gameMessage = ''
                AND (G.whitePlayer != ".$_SESSION['playerID']." AND G.blackPlayer != ".$_SESSION['playerID'].")
                
                ORDER BY G.dateCreated DESC";
}

function listInProgressGames($playerID)
{
	$tmpGames = mysql_query("SELECT G.gameID gameID, G.eco eco, G.dateCreated, G.lastMove, G.whitePlayer whitePlayer, 
									G.blackPlayer blackPlayer, G.position position, G.flagBishop, G.flagRook, G.flagKnight, G.flagQueen, G.type,
									E.name ecoName,
									W.playerID whitePlayerID, W.nick whiteNick, W.elo whiteElo, 
									B.playerID blackPlayerID, B.nick blackNick, B.elo blackElo
						FROM games G left join eco E on E.eco = G.eco AND E.ecoLang = '".getLang()."', players W, players B
						WHERE gameMessage is NULL
						AND (whitePlayer = ".$playerID." OR blackPlayer = ".$playerID.")
						AND W.playerID = G.whitePlayer 
						AND B.playerID = G.blackPlayer
						ORDER BY lastMove desc");
	
	return $tmpGames;
}

function listInvitationFor($playerID)
{
	$tmpQuery = "SELECT G.gameID, G.whitePlayer, G.blackPlayer, G.dateCreated, G.type, G.gameMessage, G.flagBishop, G.flagRook, G.flagKnight, G.flagQueen, G.position,
						W.playerID whitePlayerID, W.nick whiteNick, W.elo whiteElo,
						B.playerID blackPlayerID, B.nick blackNick, B.elo blackElo
				FROM games G, players W, players B 
				WHERE (gameMessage = 'playerInvited' 
				AND ((whitePlayer = ".$playerID." AND messageFrom = 'black') 
					OR (blackPlayer = ".$playerID." AND messageFrom = 'white')))
				AND W.playerID = G.whitePlayer 
				AND B.playerID = G.blackPlayer 
				ORDER BY dateCreated";
	$tmpGames = mysql_query($tmpQuery);
	
	return $tmpGames;
}

function listInvitationFrom($playerID)
{
	/* if game is marked playerInvited and the invite is from the current player */
	$tmpQuery = "SELECT G.gameID, G.whitePlayer, G.blackPlayer,  G.dateCreated, G.type, G.gameMessage, G.flagBishop, G.flagRook, G.flagKnight, G.flagQueen, G.position,
						W.playerID whitePlayerID, W.nick whiteNick, W.elo whiteElo,
						B.playerID blackPlayerID, B.nick blackNick, B.elo blackElo
					FROM games G, players W, players B 
					WHERE ((gameMessage = 'playerInvited' 
						AND ((whitePlayer = ".$playerID." AND messageFrom = 'white') 
							OR (blackPlayer = ".$playerID." AND messageFrom = 'black'))";
	
	/* OR game is marked inviteDeclined and the response is from the opponent */
	$tmpQuery .= ") OR (gameMessage = 'inviteDeclined' 
						AND ((whitePlayer = ".$playerID." AND messageFrom = 'black') 
							OR (blackPlayer = ".$playerID." AND messageFrom = 'white'))))
					AND W.playerID = G.whitePlayer 
					AND B.playerID = G.blackPlayer  
					ORDER BY dateCreated";
	
	$tmpGames = mysql_query($tmpQuery);
	
	return $tmpGames;
}

function listCapturedPieces($gameID)
{
	$tmpListPieces = mysql_query("SELECT curPiece, curColor, replaced
								FROM history 
								WHERE replaced > '' 
								AND gameID =  '".$gameID."' 
								ORDER BY curColor DESC , replaced DESC");
	return $tmpListPieces;
}	
?>