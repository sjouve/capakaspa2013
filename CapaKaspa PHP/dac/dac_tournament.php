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
	$tmpQuery = "SELECT T.tournamentID, type, name, status, nbPlayers, timeMove, creationDate, eloMin, eloMax, beginDate, endDate, TP.playerID
				FROM tournament T";
	if ($status == WAITING)				
		$tmpQuery .= " LEFT JOIN tournament_players TP ON T.tournamentID = TP.tournamentID 
													AND TP.playerID = ".$_SESSION['playerID'];
				
	if ($status == INPROGRESS || $status == ENDED)
		$tmpQuery .= ", tournament_players TP";											
	
		$tmpQuery .= " WHERE status = '".$status."'";
	
	if ($status == INPROGRESS || $status == ENDED)
		$tmpQuery .= " AND T.tournamentID = TP.tournamentID 
						AND TP.playerID = ".$_SESSION['playerID'];
	
	if ($status == WAITING) $tmpQuery .= " ORDER BY creationDate DESC";
	if ($status == INPROGRESS) $tmpQuery .= " ORDER BY playerID DESC, beginDate DESC";
	if ($status == ENDED) $tmpQuery .= " ORDER BY playerID DESC, endDate DESC";
	
				//LIMIT ".$start.", ".$limit;
	
	$tmpTournaments = mysqli_query($dbh, $tmpQuery);
	return $tmpTournaments;
}

// Players
function insertTournamentPlayer($tournamentID, $playerID)
{
	global $dbh;
	$res_player = mysqli_query($dbh, "INSERT INTO tournament_players (tournamentID, playerID, rank, points)
			VALUES (".$tournamentID.", ".$playerID.", 0, 0)");
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
	$tmpQuery = "SELECT P.playerID, P.nick, P.elo, P.email, L.value language, L1.value prefEmail, L2.value prefResult, L3.value prefInvit
				FROM 	tournament_players T,
						players P 
							LEFT JOIN preferences L on L.playerID = P.playerID AND L.preference='language'
							LEFT JOIN preferences L1 on L1.playerID = P.playerID AND L1.preference='emailnotification'
							LEFT JOIN preferences L2 on L2.playerID = P.playerID AND L2.preference='shareresult'
							LEFT JOIN preferences L3 on L3.playerID = P.playerID AND L3.preference='shareinvitation'
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

function countIPTournament()
{
	global $dbh;
	$IPTournaments = mysqli_query($dbh,"SELECT count(tournamentID) nbTournaments 
										FROM tournament 
										WHERE status ='".INPROGRESS."'");
	return mysqli_fetch_array($IPTournaments, MYSQLI_ASSOC);
}

function updateTounamentPlayer($tournamentID, $playerID, $rank, $points)
{
	global $dbh;
	$res_tournament = mysqli_query($dbh,"UPDATE tournament_players
								SET rank = ".$rank.", points = ".$points."  
								WHERE tournamentID = ".$tournamentID."
								AND playerID = ".$playerID);
	 
	if ($res_tournament)
		return TRUE;
	else
		return FALSE;
}
?>