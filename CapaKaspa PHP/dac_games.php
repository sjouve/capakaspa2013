<?
/* Accès aux données concernant la table Games, History, Pieces, Messages */

function countActiveGame($playerID)
{
	$activeGames = mysql_query("SELECT count(gameID) nbGames FROM games WHERE (whitePlayer = ".$playerID." OR blackPlayer = ".$playerID.") AND gameMessage=''");
	return mysql_fetch_array($activeGames, MYSQL_ASSOC);
}

function countActiveGameForAll()
{
	$activeGames = mysql_query("SELECT count(gameID) nbGames FROM games WHERE gameMessage=''");
	return mysql_fetch_array($activeGames, MYSQL_ASSOC);
}

/**
* Liste des parties terminées d'un joueur pour calcul moyenne elo adversaire
*
**/
function listEndedGames($playerID, $dateDeb, $dateFin)
{
	$tmpGames = mysql_query("SELECT G.whitePlayer whitePlayer, EW.elo whiteElo, G.blackPlayer blackPlayer, EB.elo blackElo
	                                FROM games G, players W, players B, elo_history EW, elo_history EB 
									WHERE (G.gameMessage <> '' AND G.gameMessage <> 'playerInvited' AND G.gameMessage <> 'inviteDeclined')
									AND (G.whitePlayer = ".$playerID." OR G.blackPlayer = ".$playerID.")
									AND W.playerID = G.whitePlayer AND B.playerID = G.blackPlayer
									AND W.playerID = EW.playerID AND B.playerID = EB.playerID
									AND EW.eloDate > '2011-09-30' AND EB.eloDate > '2011-09-30'
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
?>