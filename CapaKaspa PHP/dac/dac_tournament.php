<?php
/* Accès aux données concernant les tables tournois */

// Tournaments
function getTournament ($tournamentID)
{
	global $dbh;
	$res_tournament = mysqli_query($dbh,"SELECT T.*, K.likeID, 
											(SELECT COUNT(commentID) FROM comment WHERE type='".TOURNAMENT."' and entityID = T.tournamentID) nbComment,
											(SELECT COUNT(likeID) FROM like_entity WHERE type='".TOURNAMENT."' and entityID = T.tournamentID) nbLike
								FROM tournament T LEFT JOIN like_entity K on K.type = '".TOURNAMENT."' AND K.entityID = T.tournamentID AND K.playerID = ".$_SESSION['playerID']." 
								WHERE T.tournamentID = ".$tournamentID);
	
    $tournament = mysqli_fetch_array($res_tournament, MYSQLI_ASSOC);
    return $tournament;
}

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
				WHERE status = '".$status."'";
				
	if ($status == WAITING) $tmpQuery .= "ORDER BY creationDate DESC";
	if ($status == INPROGRESS) $tmpQuery .= "ORDER BY beginDate DESC";
	if ($status == ENDED) $tmpQuery .= "ORDER BY endDate DESC";
	
				//LIMIT ".$start.", ".$limit;
	
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
	$tmpQuery = "SELECT P.playerID, P.nick, P.elo, P.email, L.value language, L1.value prefEmail
				FROM 	tournament_players T,
						players P 
							LEFT JOIN preferences L on L.playerID = P.playerID AND L.preference='language'
							LEFT JOIN preferences L1 on L1.playerID = P.playerID AND L1.preference='emailnotification'
				WHERE T.tournamentID = ".$tournamentID." 
				AND T.playerID = P.playerID";
	
	//LEFT JOIN preferences L2 on L2.playerID = P.playerID AND L2.preference='shareresult'
	//LEFT JOIN preferences L3 on L3.playerID = P.playerID AND L2.preference='shareinvitation'
							
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