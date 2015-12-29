<?
function createTournamentAuto()
{
	global $dbh;
	
	$tmpTournaments = listTournaments(0, 10, WAITING);
	
	if (mysqli_num_rows($tmpTournaments) < 1)
	{
		$type = CLASSIC;
		$nbPlayers = 4;
		$timeMove = 2;
		$eloMin = 0;
		$eloMax = 0;
		$name = "CapaKaspa Masters";
		
		$res = insertTournament($name, $type, $nbPlayers, $timeMove, $eloMin, $eloMax);
		if (!$res)
		{
		  	return FALSE;
		}
		
		$type = CLASSIC;
		$nbPlayers = 4;
		$timeMove = 5;
		$eloMin = 0;
		$eloMax = 0;
		$name = "CapaKaspa Masters";
		
		$res = insertTournament($name, $type, $nbPlayers, $timeMove, $eloMin, $eloMax);
		if (!$res)
		{
		  	return FALSE;
		}
		
		$type = CLASSIC;
		$nbPlayers = 4;
		$timeMove = 7;
		$eloMin = 0;
		$eloMax = 0;
		$name = "CapaKaspa Masters";
		
		$res = insertTournament($name, $type, $nbPlayers, $timeMove, $eloMin, $eloMax);
		if (!$res)
		{
		  	return FALSE;
		}
	}
	return TRUE;
}

function createTournament($name, $type, $nbPlayers, $timeMove, $eloMin, $eloMax)
{
	global $dbh;
	
	$res = insertTournament($name, $type, $nbPlayers, $timeMove, $eloMin, $eloMax);
	if (!$res)
	{
	  	return FALSE;
	}	
	
	return TRUE;
}

function registerTournamentPlayer($tournamentID, $playerID, $isLastPlayer)
{
	$res = insertTournamentPlayer($tournamentID, $playerID);
	if (!$res)
	{
	  	return FALSE;
	}
	
	if ($isLastPlayer == 1)
	{
		// Changer l'état du tournoi
		$res = updateTournament($tournamentID, INPROGRESS);
		
		// Créer les parties et les lier au tournoi
		$tmpPlayers = listTournamentPlayers($tournamentID);
		$players = array();
		$count = 0;
		while($tmpPlayer = mysqli_fetch_array($tmpPlayers, MYSQLI_ASSOC))
		{
			$count++;
			$players[$count] = $tmpPlayer['playerID'];
		}
		
		for ($i = 1; $i <= $count; $i++) 
		{
			for ($j = $i+1; $j <= $count; $j++)
			{
				$gameID = createGame($players[$i], $players[$j], CLASSIC, "", "", "", "", 2, "");
				insertTournamentGame($tournamentID, $gameID);
				$gameID = createGame($players[$j], $players[$i], CLASSIC, "", "", "", "", 2, "");
				insertTournamentGame($tournamentID, $gameID);
			}
		}
		
	}
	
	return TRUE;
}

function checkTournamentEnding($tournamentID)
{
	// Y a t-il des parties en cours ?
	$nbGames = getNbActiveTournamentGames($tournamentID);
	$tournament = getTournament($tournamentID);
	// Si non et tournoi en cours alors update tournament à Terminé
	if ($nbGames < 1 && $tournament['status'] == INPROGRESS)
		$res = updateTournament($tournamentID, ENDED);
}
?>