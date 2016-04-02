<?
/* Accès aux donnéees concernant la table Games, History, Pieces, Messages */

/*
 * Load a game by ID
 */
function getGame($gameID)
{
	global $dbh;
	// Informations sur la partie : voir le type de partie (position normale ou pas) et le probème du code ECO
	$tmpQuery = "SELECT G.gameID, G.whitePlayer whitePlayer, G.blackPlayer blackPlayer, G.dialogue dialogue, G.position position,  
	G.lastMove, G.dateCreated, DATE_ADD(G.lastMove, INTERVAL G.timeMove DAY) expirationDate, G.timeMove, 
	G.type type, G.flagBishop flagBishop, G.flagKnight flagKnight, G.flagRook flagRook, G.flagQueen flagQueen, G.chess960,
	G.eco eco, G.gameMessage, E.name ecoName, L.likeID, T.tournamentID,
	W.nick whiteNick, W.elo whiteElo, W.elo960 whiteElo960, W.socialNetwork whiteSocialNet, W.socialID whiteSocialID, W.firstName whiteFirstName, W.lastName whiteLastName, W.email whiteEmail,
	B.nick blackNick, B.elo blackElo, B.elo960 blackElo960, B.socialNetwork blackSocialNet, B.socialID blackSocialID, B.firstName blackFirstName, B.lastName blackLastName, B.email blackEmail
	FROM games G LEFT JOIN eco E ON E.eco = G.eco AND E.ecoLang = '".getLang()."'
				LEFT JOIN like_entity L ON L.type = '".GAME."' AND L.entityID = G.gameID AND L.playerID = ".$_SESSION['playerID']."
				LEFT JOIN tournament_games T ON G.gameID = T.gameID, players W, players B
	WHERE G.gameID = ".$gameID."
	AND G.whitePlayer = W.playerID
	AND G.blackPlayer = B.playerID";
	
	$tmpGames = mysqli_query($dbh,$tmpQuery);
	$tmpGame = mysqli_fetch_array($tmpGames, MYSQLI_ASSOC);
	
	return $tmpGame;
}

function countActiveTournamentGames($tournamentID)
{
	global $dbh;
	$activeGames = mysqli_query($dbh,"SELECT count(G.gameID) nbGames 
									FROM games G, tournament_games T 
									WHERE G.gameID = T.gameID 
									AND T.tournamentID = ".$tournamentID." 
									AND (G.gameMessage is NULL OR G.gameMessage = '')");
	return mysqli_fetch_array($activeGames, MYSQLI_ASSOC);
}

function countActiveGame($playerID)
{
	global $dbh;
	$activeGames = mysqli_query($dbh,"SELECT count(gameID) nbGames FROM games WHERE (whitePlayer = ".$playerID." OR blackPlayer = ".$playerID.") AND gameMessage=''");
	return mysqli_fetch_array($activeGames, MYSQLI_ASSOC);
}

function countActiveGameForAll()
{
	global $dbh;
	$activeGames = mysqli_query($dbh,"SELECT count(gameID) nbGames FROM games WHERE gameMessage is null") or die(mysqli_error($dbh));
	return mysqli_fetch_array($activeGames, MYSQLI_ASSOC);
}

/**
* Liste des parties terminées d'un joueur pour calcul moyenne elo adversaire
**/
function listEndedGames($playerID, $dateDeb, $dateFin, $type)
{
	global $dbh;
	$tmpGames = mysqli_query($dbh,"SELECT G.whitePlayer whitePlayer, EW.elo whiteElo, G.blackPlayer blackPlayer, EB.elo blackElo
	                                FROM games G, players W, players B, elo_history EW, elo_history EB 
									WHERE (G.gameMessage <> '' AND G.gameMessage <> 'playerInvited' AND G.gameMessage <> 'inviteDeclined')
									AND (G.whitePlayer = ".$playerID." OR G.blackPlayer = ".$playerID.")
									AND W.playerID = G.whitePlayer AND B.playerID = G.blackPlayer
									AND W.playerID = EW.playerID AND B.playerID = EB.playerID
									AND EW.eloDate > '".$dateFin."' AND EB.eloDate > '".$dateFin."'
									AND G.type=".$type." AND G.lastMove >= '".$dateDeb."' AND DATE(G.lastMove) <= '".$dateFin."'");
	
	return $tmpGames;
}

function countLost($playerID, $dateDeb, $dateFin, $type)
{							
									
	global $dbh;
	$tmpGames = mysqli_query($dbh,"SELECT count(G.gameID) nbGames
	                                FROM games G
									WHERE (G.gameMessage <> '' AND G.gameMessage <> 'playerInvited' AND G.gameMessage <> 'inviteDeclined')
	                                AND (G.whitePlayer = ".$playerID." OR G.blackPlayer = ".$playerID.")
	                                AND ((G.gameMessage = 'playerResigned' AND G.messageFrom = 'white' AND G.whitePlayer = ".$playerID.")
	                                    OR (G.gameMessage = 'playerResigned' AND G.messageFrom = 'black' AND G.blackPlayer = ".$playerID.")
	                                    OR (G.gameMessage = 'checkMate' AND G.messageFrom = 'black' AND G.whitePlayer = ".$playerID.")
	                                    OR (G.gameMessage = 'checkMate' AND G.messageFrom = 'white' AND G.blackPlayer = ".$playerID.")) 
									AND G.type=".$type." AND G.lastMove >= '".$dateDeb."' AND DATE(G.lastMove) <= '".$dateFin."'");
	
	return mysqli_fetch_array($tmpGames, MYSQLI_ASSOC);
}

function countDraw($playerID, $dateDeb, $dateFin, $type)
{							
									
	global $dbh;
	$tmpGames = mysqli_query($dbh,"SELECT count(G.gameID) nbGames
	                            FROM games G
                                WHERE (G.gameMessage <> '' AND G.gameMessage <> 'playerInvited' AND G.gameMessage <> 'inviteDeclined')
                                AND (G.whitePlayer = ".$playerID." OR G.blackPlayer = ".$playerID.")
                                AND G.gameMessage = 'draw'
								AND G.type=".$type." AND G.lastMove >= '".$dateDeb."' AND DATE(G.lastMove) <= '".$dateFin."'");
	
	return mysqli_fetch_array($tmpGames, MYSQLI_ASSOC);
}

function countWin($playerID, $dateDeb, $dateFin, $type)
{							
									
	global $dbh;
	$tmpGames = mysqli_query($dbh,"SELECT count(G.gameID) nbGames
	                            FROM games G
                                WHERE (G.gameMessage <> '' AND G.gameMessage <> 'playerInvited' AND G.gameMessage <> 'inviteDeclined')
                                AND (G.whitePlayer = ".$playerID." OR G.blackPlayer = ".$playerID.")
                                AND ((G.gameMessage = 'playerResigned' AND G.messageFrom = 'white' AND G.blackPlayer = ".$playerID.")
                                    OR (G.gameMessage = 'playerResigned' AND G.messageFrom = 'black' AND G.whitePlayer = ".$playerID.")
                                    OR (G.gameMessage = 'checkMate' AND G.messageFrom = 'black' AND G.blackPlayer = ".$playerID.")
                                    OR (G.gameMessage = 'checkMate' AND G.messageFrom = 'white' AND G.whitePlayer = ".$playerID."))
								AND G.type=".$type." AND G.lastMove >= '".$dateDeb."' AND DATE(G.lastMove) <= '".$dateFin."'");
	
	return mysqli_fetch_array($tmpGames, MYSQLI_ASSOC);
}

// Pour calcul Elo : parties compatibles uniquement
function countLostElo($playerID, $dateDeb, $dateFin, $type)
{							
									
	global $dbh;
	$tmpGames = mysqli_query($dbh,"SELECT count(G.gameID) nbGames
	                                FROM games G, players W, players B, elo_history EW, elo_history EB 
									WHERE (G.gameMessage <> '' AND G.gameMessage <> 'playerInvited' AND G.gameMessage <> 'inviteDeclined')
	                                AND ((G.gameMessage = 'playerResigned' AND G.messageFrom = 'white' AND G.whitePlayer = ".$playerID.")
	                                    OR (G.gameMessage = 'playerResigned' AND G.messageFrom = 'black' AND G.blackPlayer = ".$playerID.")
	                                    OR (G.gameMessage = 'checkMate' AND G.messageFrom = 'black' AND G.whitePlayer = ".$playerID.")
	                                    OR (G.gameMessage = 'checkMate' AND G.messageFrom = 'white' AND G.blackPlayer = ".$playerID.")) 
									AND (G.whitePlayer = ".$playerID." OR G.blackPlayer = ".$playerID.")
	                                AND W.playerID = G.whitePlayer AND B.playerID = G.blackPlayer
									AND W.playerID = EW.playerID AND B.playerID = EB.playerID
									AND EW.eloDate > '".$dateFin."' AND EB.eloDate > '".$dateFin."'
									AND ABS(EW.elo - EB.elo) <= 350
	                                AND G.type=".$type." AND G.lastMove >= '".$dateDeb."' AND DATE(G.lastMove) <= '".$dateFin."'");
	
	return mysqli_fetch_array($tmpGames, MYSQLI_ASSOC);
}

// Pour calcul Elo : parties compatibles uniquement
function countDrawElo($playerID, $dateDeb, $dateFin, $type)
{							
									
	global $dbh;
	$tmpGames = mysqli_query($dbh,"SELECT count(G.gameID) nbGames
	                            FROM games G, players W, players B, elo_history EW, elo_history EB 
                                WHERE (G.gameMessage <> '' AND G.gameMessage <> 'playerInvited' AND G.gameMessage <> 'inviteDeclined')
                                AND (G.whitePlayer = ".$playerID." OR G.blackPlayer = ".$playerID.")
                                AND G.gameMessage = 'draw'
								AND W.playerID = G.whitePlayer AND B.playerID = G.blackPlayer
								AND W.playerID = EW.playerID AND B.playerID = EB.playerID
								AND EW.eloDate > '".$dateFin."' AND EB.eloDate > '".$dateFin."'
								AND ABS(EW.elo - EB.elo) <= 350
                                AND G.type=".$type." AND G.lastMove >= '".$dateDeb."' AND DATE(G.lastMove) <= '".$dateFin."'");
	
	return mysqli_fetch_array($tmpGames, MYSQLI_ASSOC);
}

// Pour calcul Elo : parties compatibles uniquement
function countWinElo($playerID, $dateDeb, $dateFin, $type)
{							
									
	global $dbh;
	$tmpGames = mysqli_query($dbh,"SELECT count(G.gameID) nbGames
	                            FROM games G, players W, players B, elo_history EW, elo_history EB 
                                WHERE (G.gameMessage <> '' AND G.gameMessage <> 'playerInvited' AND G.gameMessage <> 'inviteDeclined')
                                AND (G.whitePlayer = ".$playerID." OR G.blackPlayer = ".$playerID.")
                                AND ((G.gameMessage = 'playerResigned' AND G.messageFrom = 'white' AND G.blackPlayer = ".$playerID.")
                                    OR (G.gameMessage = 'playerResigned' AND G.messageFrom = 'black' AND G.whitePlayer = ".$playerID.")
                                    OR (G.gameMessage = 'checkMate' AND G.messageFrom = 'black' AND G.blackPlayer = ".$playerID.")
                                    OR (G.gameMessage = 'checkMate' AND G.messageFrom = 'white' AND G.whitePlayer = ".$playerID."))
								AND W.playerID = G.whitePlayer AND B.playerID = G.blackPlayer
								AND W.playerID = EW.playerID AND B.playerID = EB.playerID
								AND EW.eloDate > '".$dateFin."' AND EB.eloDate > '".$dateFin."'
								AND ABS(EW.elo - EB.elo) <= 350
                                AND G.type=".$type." AND G.lastMove >= '".$dateDeb."' AND DATE(G.lastMove) <= '".$dateFin."'");
	
	return mysqli_fetch_array($tmpGames, MYSQLI_ASSOC);
}

// TODO A supprimer. Inutilisé.
function calculMoyenneElo($playerID, $dateDeb, $dateFin)
{							
									
	global $dbh;
	$tmpGames = mysqli_query($dbh,"SELECT  nbGames
	                            FROM games G, players W, players B
                                WHERE (G.gameMessage <> '' AND G.gameMessage <> 'playerInvited' AND G.gameMessage <> 'inviteDeclined')
                                AND (G.whitePlayer = ".$playerID." OR G.blackPlayer = ".$playerID.")
                                AND W.playerID = G.whitePlayer AND B.playerID = G.blackPlayer
								AND G.type=0 AND G.lastMove >= '".$dateDeb."' AND G.lastMove <= '".$dateFin."'");
	
	return mysqli_fetch_array($tmpGames, MYSQLI_ASSOC);
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
 * - Type de classement : Classique, Chess960
 * - Code ECO
 * + Plage date de fin (sur date du dernier coup)
 * + Plage date de début
 */
function searchGames($mode, $debut, $limit, $gameState, $playerID, $playerColor, $gameResult, $gameType, $flagRank, $ecoCode)
{
	global $dbh;
	
	if ($mode == "count")
	{
		$req = "SELECT count(*) nbGames";
	}
	else
	{
		$req = "SELECT G.gameID, T.tournamentID, G.eco eco, 
				W.playerID whitePlayerID, W.nick whiteNick, W.elo whiteElo, W.elo960 whiteElo960,
				B.playerID blackPlayerID, B.nick blackNick, B.elo blackElo, B.elo960 blackElo960,
				G.gameMessage, G.messageFrom, G.dateCreated, G.lastMove, G.type, G.flagBishop, G.flagKnight, G.flagRook, G.flagQueen,
				E.name ecoName";
	}
	
	$req .=	" FROM games G LEFT JOIN tournament_games T ON T.gameID = G.gameID
							LEFT JOIN eco E ON G.eco = E.eco AND E.ecoLang = '".getLang()."', 
							players W, players B ";
	
	
	if ($gameState == "E")
		$req .= "WHERE (G.gameMessage <> '' AND G.gameMessage <> 'playerInvited' AND G.gameMessage <> 'inviteDeclined')";
	else
		$req .= "WHERE (gameMessage is NULL OR gameMessage = '')";
	
	if ($playerColor == "W")
		$req .= " AND (G.whitePlayer = ".$playerID.")";
	else if ($playerColor == "B")
		$req .= " AND (G.blackPlayer = ".$playerID.")";
	else
		$req .= " AND (G.whitePlayer = ".$playerID." OR G.blackPlayer = ".$playerID.")";

	if ($gameResult == "W")
		$req .= " AND ((G.gameMessage = 'playerResigned' AND G.messageFrom = 'white' AND G.blackPlayer = ".$playerID.")
			OR (G.gameMessage = 'playerResigned' AND G.messageFrom = 'black' AND G.whitePlayer = ".$playerID.")
			OR (G.gameMessage = 'checkMate' AND G.messageFrom = 'black' AND G.blackPlayer = ".$playerID.")
			OR (G.gameMessage = 'checkMate' AND G.messageFrom = 'white' AND G.whitePlayer = ".$playerID."))";
	if ($gameResult == "L")	
		$req .= " AND ((G.gameMessage = 'playerResigned' AND G.messageFrom = 'white' AND G.whitePlayer = ".$playerID.")
			OR (G.gameMessage = 'playerResigned' AND G.messageFrom = 'black' AND G.blackPlayer = ".$playerID.")
			OR (G.gameMessage = 'checkMate' AND G.messageFrom = 'black' AND G.whitePlayer = ".$playerID.")
			OR (G.gameMessage = 'checkMate' AND G.messageFrom = 'white' AND G.blackPlayer = ".$playerID."))";
	if ($gameResult == "D")
		$req .= " AND G.gameMessage = 'draw'";
		
	$req .=	" AND W.playerID = G.whitePlayer 
			AND B.playerID = G.blackPlayer";
	
	$req .= " AND G.type = ".$gameType;
	
	if ($flagRank == 1)
		$req .= " AND G.lastMove >= DATE_FORMAT((SELECT MAX(DISTINCT(eloDate)) from elo_history), '%Y-%m-01') 
					AND G.type in (0,2) 
					AND ((G.type = 0 AND ABS(W.elo - B.elo) <= 350) 
							OR (G.type=2 AND ABS(W.elo960 - B.elo960) <= 350)) ";
	
	// For classic game
	if ($gameType == 0)	
	{
		if ($ecoCode != '')
			$req .=	" AND G.eco = '".$ecoCode."'";
	}		
	
	$req .=	" ORDER BY";
	// For classic game
	//if ($gameType == 0)		
		//$req .= " E.eco ASC,"; 
	
	$req .= " G.lastMove DESC";
	
	if ($mode != "count")
		$req .= " limit ".$debut.",".$limit;
		
	return mysqli_query($dbh,$req);
	
}

function listInProgressGames($playerID)
{
	global $dbh;
	$tmpGames = mysqli_query($dbh,"SELECT G.gameID gameID, G.eco eco, G.dateCreated, G.lastMove, DATE_ADD(G.lastMove, INTERVAL G.timeMove DAY) expirationDate, G.whitePlayer whitePlayer, G.timeMove, 
									G.blackPlayer blackPlayer, G.position position, G.flagBishop, G.flagRook, G.flagKnight, G.flagQueen, G.type,  
									E.name ecoName, T.tournamentID tournamentID,
									W.playerID whitePlayerID, W.nick whiteNick, W.elo whiteElo, W.elo960 whiteElo960, W.socialID whiteSocialID, W.socialNetwork whiteSocialNetwork,
						B.playerID blackPlayerID, B.nick blackNick, B.elo blackElo, B.elo960 blackElo960, B.socialID blackSocialID, B.socialNetwork blackSocialNetwork,
						(SELECT COUNT(commentID) FROM comment WHERE type='".GAME."' and entityID = G.gameID) nbComment,
						(SELECT COUNT(likeID) FROM like_entity WHERE type='".GAME."' and entityID = G.gameID) nbLike,
						(SELECT COUNT(gameID) nbMove FROM history H WHERE H.gameID = G.gameID) nbMoves, L.likeID
						FROM games G LEFT JOIN eco E on E.eco = G.eco AND E.ecoLang = '".getLang()."' 
									LEFT JOIN tournament_games T ON G.gameID = T.gameID
									LEFT JOIN like_entity L on L.type = '".GAME."' AND L.entityID = G.gameID AND L.playerID = ".$_SESSION['playerID'].", players W, players B 
						WHERE (gameMessage is NULL OR gameMessage = '')
						AND (whitePlayer = ".$playerID." OR blackPlayer = ".$playerID.")
						AND W.playerID = G.whitePlayer 
						AND B.playerID = G.blackPlayer
						ORDER BY lastMove desc");
	
	return $tmpGames;
}

function listInvitationFor($playerID)
{
	global $dbh;
	$tmpQuery = "SELECT G.gameID, G.whitePlayer, G.blackPlayer, G.dateCreated, G.type, G.gameMessage, 
						G.flagBishop, G.flagRook, G.flagKnight, G.flagQueen, G.position, G.timeMove,
						W.playerID whitePlayerID, W.nick whiteNick, W.elo whiteElo, W.elo960 whiteElo960, W.socialID whiteSocialID, W.socialNetwork whiteSocialNetwork,
						B.playerID blackPlayerID, B.nick blackNick, B.elo blackElo, B.elo960 blackElo960, B.socialID blackSocialID, B.socialNetwork blackSocialNetwork,
						(SELECT COUNT(gameID) nbMove FROM history H WHERE H.gameID = G.gameID) nbMoves
				FROM games G, players W, players B 
				WHERE (gameMessage = 'playerInvited' 
				AND (
						(whitePlayer = ".$playerID." AND messageFrom = 'black') 
						OR (blackPlayer = ".$playerID." AND messageFrom = 'white')
					)
				)
				AND W.playerID = G.whitePlayer 
				AND B.playerID = G.blackPlayer 
				ORDER BY dateCreated";
	$tmpGames = mysqli_query($dbh,$tmpQuery);
	
	return $tmpGames;
}

function listInvitationForAll($playerID)
{
	global $dbh;
	$tmpQuery = "SELECT G.gameID, G.whitePlayer, G.blackPlayer, G.dateCreated, G.type, G.gameMessage, 
						G.flagBishop, G.flagRook, G.flagKnight, G.flagQueen, G.position, G.timeMove,
						W.playerID whitePlayerID, W.nick whiteNick, W.elo whiteElo, W.elo960 whiteElo960, W.socialID whiteSocialID, W.socialNetwork whiteSocialNetwork,
						B.playerID blackPlayerID, B.nick blackNick, B.elo blackElo, B.elo960 blackElo960, B.socialID blackSocialID, B.socialNetwork blackSocialNetwork,
						(SELECT COUNT(gameID) nbMove FROM history H WHERE H.gameID = G.gameID) nbMoves
				FROM games G, players W, players B 
				WHERE (gameMessage = 'playerInvited' 
				AND (
						(whitePlayer = 0 AND blackPlayer != ".$playerID.") 
						OR (blackPlayer = 0 AND whitePlayer != ".$playerID.")
					)
				)
				AND W.playerID = G.whitePlayer 
				AND B.playerID = G.blackPlayer 
				ORDER BY dateCreated";
	$tmpGames = mysqli_query($dbh,$tmpQuery);
	
	return $tmpGames;
}

function listInvitationFrom($playerID)
{
	global $dbh;
	/* if game is marked playerInvited and the invite is from the current player */
	$tmpQuery = "SELECT G.gameID, G.whitePlayer, G.blackPlayer,  G.dateCreated, G.type, G.gameMessage, 
						G.flagBishop, G.flagRook, G.flagKnight, G.flagQueen, G.position, G.timeMove,
						W.playerID whitePlayerID, W.nick whiteNick, W.elo whiteElo, W.elo960 whiteElo960, W.socialID whiteSocialID, W.socialNetwork whiteSocialNetwork,
						B.playerID blackPlayerID, B.nick blackNick, B.elo blackElo, B.elo960 blackElo960, B.socialID blackSocialID, B.socialNetwork blackSocialNetwork,
						(SELECT COUNT(gameID) nbMove FROM history H WHERE H.gameID = G.gameID) nbMoves
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
	
	$tmpGames = mysqli_query($dbh,$tmpQuery);
	
	return $tmpGames;
}

function listCapturedPieces($gameID)
{
	global $dbh;
	$tmpListPieces = mysqli_query($dbh,"SELECT curPiece, curColor, replaced
								FROM history 
								WHERE replaced > '' 
								AND gameID =  '".$gameID."'
								AND replaced != 'chess960' 
								ORDER BY curColor DESC , replaced DESC");
	return $tmpListPieces;
}

function listGamesProgressWithMoves($playerID)
{
	global $dbh;
	$tmpGames = mysqli_query($dbh,"SELECT count(H.gameID) nbMoves, G.gameID, G.whitePlayer, G.blackPlayer
							FROM games G left join history H on H.gameID = G.gameID
							WHERE (gameMessage is NULL OR gameMessage = '')
							AND (whitePlayer = ".$playerID." OR blackPlayer = ".$playerID.")
							GROUP BY G.gameID, G.whitePlayer, G.blackPlayer");
	
	return $tmpGames;
}

function countGamesByEco($playerID)
{
	global $dbh;
	$tmpGames = mysqli_query($dbh,"SELECT G.eco eco, count(G.gameID) nb
							FROM games G, eco E
							WHERE (G.gameMessage <> '' AND G.gameMessage <> 'playerInvited' AND G.gameMessage <> 'inviteDeclined')
							AND (G.whitePlayer = ".$playerID." OR G.blackPlayer = ".$playerID.")
							AND E.eco = G.eco AND E.ecoLang = '".getLang()."'
							GROUP BY G.eco
							ORDER BY nb desc");
	
	return $tmpGames;
}

function listEndedGamesForElo($playerID, $type)
{
	global $dbh;
	$tmpGames = mysqli_query($dbh,"SELECT G.gameID, W.playerID whitePlayerID, W.nick whiteNick, W.elo whiteElo, W.elo960 whiteElo960, B.playerID blackPlayerID, B.nick blackNick, B.elo blackElo, B.elo960 blackElo960, G.gameMessage, G.messageFrom, G.dateCreated, G.lastMove
						FROM games G, players W, players B
						WHERE (G.gameMessage <> '' AND G.gameMessage <> 'playerInvited' AND G.gameMessage <> 'inviteDeclined')
						AND (G.whitePlayer = ".$playerID." OR G.blackPlayer = ".$playerID.")
						AND G.lastMove >= DATE_FORMAT((SELECT MAX(DISTINCT(eloDate)) from elo_history), '%Y-%m-01')
						AND W.playerID = G.whitePlayer AND B.playerID = G.blackPlayer
						AND G.type=".$type." 
						ORDER BY G.lastMove DESC");
	
	return $tmpGames;
}

// Achievements
function countForAchievements($playerID)
{
	global $dbh;
	$tmpGames = mysqli_query($dbh,"SELECT 
								(SELECT count(G.gameID) 
								FROM games G
								WHERE (G.gameMessage <> '' AND G.gameMessage <> 'playerInvited' AND G.gameMessage <> 'inviteDeclined')
                                AND (G.whitePlayer = ".$playerID." OR G.blackPlayer = ".$playerID.")
								) PLAYER,
								(SELECT count(G.gameID) 
								FROM games G
								WHERE (G.gameMessage <> '' AND G.gameMessage <> 'playerInvited' AND G.gameMessage <> 'inviteDeclined')
                                AND (G.whitePlayer = ".$playerID." OR G.blackPlayer = ".$playerID.")
                                AND G.type=0
								) CLASSIC,
								(SELECT count(G.gameID) 
								FROM games G
								WHERE (G.gameMessage <> '' AND G.gameMessage <> 'playerInvited' AND G.gameMessage <> 'inviteDeclined')
                                AND (G.whitePlayer = ".$playerID." OR G.blackPlayer = ".$playerID.")
                                AND G.type!=0
								) OUTSIDE,
								(SELECT count(G.gameID)
	                            FROM games G
                                WHERE (G.gameMessage <> '' AND G.gameMessage <> 'playerInvited' AND G.gameMessage <> 'inviteDeclined')
                                AND (G.whitePlayer = ".$playerID." OR G.blackPlayer = ".$playerID.")
                                AND ((G.gameMessage = 'playerResigned' AND G.messageFrom = 'white' AND G.blackPlayer = ".$playerID.")
                                    OR (G.gameMessage = 'playerResigned' AND G.messageFrom = 'black' AND G.whitePlayer = ".$playerID.")
                                    OR (G.gameMessage = 'checkMate' AND G.messageFrom = 'black' AND G.blackPlayer = ".$playerID.")
                                    OR (G.gameMessage = 'checkMate' AND G.messageFrom = 'white' AND G.whitePlayer = ".$playerID."))
								) WINNER,
								(SELECT count(G.gameID)
	                            FROM games G
                                WHERE (G.gameMessage <> '' AND G.gameMessage <> 'playerInvited' AND G.gameMessage <> 'inviteDeclined')
                                AND (G.blackPlayer = ".$playerID.")
                                AND ((G.gameMessage = 'playerResigned' AND G.messageFrom = 'white' AND G.blackPlayer = ".$playerID.")
                                    OR (G.gameMessage = 'playerResigned' AND G.messageFrom = 'black' AND G.whitePlayer = ".$playerID.")
                                    OR (G.gameMessage = 'checkMate' AND G.messageFrom = 'black' AND G.blackPlayer = ".$playerID.")
                                    OR (G.gameMessage = 'checkMate' AND G.messageFrom = 'white' AND G.whitePlayer = ".$playerID."))
								) BLACKWIN,
								(SELECT count(distinct(G.whitePlayer * G.blackPlayer))
	                            FROM games G
                                WHERE (G.gameMessage <> '' AND G.gameMessage <> 'playerInvited' AND G.gameMessage <> 'inviteDeclined')
                                AND (G.whitePlayer = ".$playerID." OR G.blackPlayer = ".$playerID.")
								) SOCIAL"
								);
	
	return mysqli_fetch_array($tmpGames, MYSQLI_ASSOC);
}

function listTournamentGames($tournamentID)
{
	global $dbh;
	$tmpGames = mysqli_query($dbh,"SELECT G.gameID, G.eco eco, W.playerID whitePlayerID, W.nick whiteNick, B.playerID blackPlayerID, B.nick blackNick, 
				G.gameMessage, G.messageFrom, G.dateCreated, G.lastMove, G.type, G.flagBishop, G.flagKnight, G.flagRook, G.flagQueen, E.name ecoName
						FROM games G LEFT JOIN eco E on E.eco = G.eco AND E.ecoLang = '".getLang()."' 
									, tournament_games T, players W, players B
                        WHERE W.playerID = G.whitePlayer 
						AND B.playerID = G.blackPlayer
                        AND G.gameID = T.gameID AND T.tournamentID = ".$tournamentID." 
						ORDER BY whitePlayerID, blackPlayerID");

	return $tmpGames;
}
?>