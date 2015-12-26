<?php
/* Acc�s aux donn�es concernant les tables tournois */

// Tournaments
function insertTournament($name, $type, $nbPlayers, $timeMove, $eloMin, $eloMax)
{
	global $dbh;
	$res_tournament = mysqli_query($dbh, "INSERT INTO tournament (name, type, nbPlayers, timeMove, creationDate, eloMin, eloMax)
			VALUES ('".addslashes(strip_tags($name))."', ".$type.", ".$nbPlayers.", ".$timeMove.", now(), ".$eloMin.", ".$eloMax.")");
	return $res_tournament;
}

function updateTournament($tournamentID, $status)
{
	global $dbh;
	$tmpDate = "";
	
	if ($status == INPROGRESS)
		$tmpDate = "beginDate";
	if ($status == ENDED)
		$tmpDate = "endDate";
	
	$res_tournament = mysqli_query($dbh,"UPDATE tournament 
								SET status = '".$status."', ".$tmpDate." = now() 
								WHERE tournamentID = ".$tournamentID);
	 
	if ($res_tournament)
		return TRUE;
	else
		return FALSE;
}

function listTournaments($start, $limit, $status)
{
	global $dbh;
	$tmpQuery = "SELECT tournamentID, type, name, status, nbPlayers, timeMove, creationDate, eloMin, eloMax, beginDate, endDate
				FROM tournament
				WHERE status = '".$status."' 
				LIMIT ".$start.", ".$limit;
	
	$tmpTournaments = mysqli_query($dbh, $tmpQuery);
	return $tmpTournaments;
}

// Players
function insertTournamentPlayer($tournamentID, $playerID)
{
	global $dbh;
	$res_player = mysqli_query($dbh, "INSERT INTO tournament_players (tournamentID, playerID)
			VALUES (".$tournamentID.", ".$playerID.")");
	return $res_player;
}

function deleteTournamentPlayer($tournamentID, $playerID)
{
	global $dbh;
	$res_player = mysqli_query($dbh, "DELETE FROM tournament_players 
										WHERE tournamentID = ".$tournamentID." AND playerID = ".$playerID);
	
	return $res_player;
}

function listTournamentPlayers($tournamentID)
{
	global $dbh;
	$tmpQuery = "SELECT P.playerID, P.nick, P.elo 
				FROM tournament_players T, players P
				WHERE T.tournamentID = ".$tournamentID." 
				AND T.playerID = P.playerID";
	
	$tmpPlayers = mysqli_query($dbh, $tmpQuery);
	return $tmpPlayers;
}
// Games
function insertTournamentGame($tournamentID, $gameID)
{
	global $dbh;
	$res_game = mysqli_query($dbh, "INSERT INTO tournament_games (tournamentID, gameID)
			VALUES (".$tournamentID.", ".$gameID.")");
	return $res_game;
}

function deleteTournamentGame($tournamentID, $gameID)
{
	global $dbh;
	$res_game = mysqli_query($dbh, "DELETE FROM tournament_games WHERE tournamentID = ".$tournamentID." AND gameID = ".$gameID);
	
	return $res_game;
}
?>