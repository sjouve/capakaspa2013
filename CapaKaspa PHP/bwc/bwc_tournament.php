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

function unregisterTournamentPlayer($tournamentID, $playerID)
{
	
	$tournament = getTournament($tournamentID);
	if ($tournament['status'] != WAITING)
		return false;
	
	$res = deleteTournamentPlayer($tournamentID, $playerID);
	if (!$res)
	{
	  	return FALSE;
	}
	return TRUE;
}	

function registerTournamentPlayer($tournamentID, $playerID)
{
	
	$tournament = getTournament($tournamentID);
	if ($tournament['status'] != WAITING)
		return FALSE;
	
	$tmpPlayers = listTournamentPlayers($tournamentID);
	$nbRegisteredPlayers = mysqli_num_rows($tmpPlayers);
	
	if ($nbRegisteredPlayers < $tournament['nbPlayers'])
	{
		$res = insertTournamentPlayer($tournamentID, $playerID);
		if (!$res)
		{
		  	return FALSE;
		}
	
		if ($nbRegisteredPlayers == $tournament['nbPlayers']-1)
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
				$locale = isset($tmpPlayer['language'])?$tmpPlayer['language']:"en_EN";
				putenv("LC_ALL=$locale");
				setlocale(LC_ALL, $locale);
				bindtextdomain("messages", "./locale");
				bind_textdomain_codeset("messages", "UTF-8");
				textdomain("messages");
				sendMail($tmpPlayer['email'], "[CapaKaspa] "._("Tournament started"), _("The tournament which you registered just started..."));
			}
			
			for ($i = 1; $i <= $count; $i++) 
			{
				for ($j = $i+1; $j <= $count; $j++)
				{
					$gameID = createGame($players[$i], $players[$j], CLASSIC, "", "", "", "", $tournament['timeMove'], "");
					insertTournamentGame($tournamentID, $gameID);
					$gameID = createGame($players[$j], $players[$i], CLASSIC, "", "", "", "", $tournament['timeMove'], "");
					insertTournamentGame($tournamentID, $gameID);
				}
			}
			
			$locale = $_SESSION["pref_language"];
			// Repositionne la langue de l'utilisateur
			putenv("LC_ALL=$locale");
			setlocale(LC_ALL, $locale);
			bindtextdomain("messages", "./locale");
			bind_textdomain_codeset("messages", "UTF-8");
			textdomain("messages");
			
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