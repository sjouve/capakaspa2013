<?
/* Acc�s aux donn�es concernant les tables Players et Preferences */

/* Constantes du module */
define ("MAX_NB_JOUR_ABSENCE", 30);

/*
 * PLAYER READ
 */

/*
 * SELECT C.countryName Country, count(P.playerID) Players, avg(P.elo) "Avg Rating"
FROM players P, country C 
WHERE P.countryCode = C.countryCode
AND C.countryLang = 'fr'
group by P.countryCode
 */

/* Charger un utilisateur par son ID */
function getPlayer($playerID)
{
	global $dbh;
	$res_player = mysqli_query($dbh,"SELECT P.*, C.countryName 
								FROM players P, country C 
								WHERE playerID = ".$playerID." 
								AND P.countryCode = C.countryCode
								AND C.countryLang = '".getLang()."'");
	
    $player = mysqli_fetch_array($res_player, MYSQLI_ASSOC);
    return $player;
}

/* Charger un utilisateur pour email */
function getPlayerByEmail($email)
{
	global $dbh;
	$res_player = mysqli_query($dbh,"SELECT * FROM players WHERE email = '".$email."'");
    $player = mysqli_fetch_array($res_player, MYSQLI_ASSOC);
    return $player;
}

/* Charger un utilisateur pour un surnom et mot de passe */
function getPlayerByNickPassword($nick, $password)
{
	global $dbh;
	$res_player = mysqli_query($dbh,"SELECT * FROM players WHERE nick = '".$nick."' AND password = '".$password."'");
    $player = mysqli_fetch_array($res_player, MYSQLI_ASSOC);
    return $player;
}

/* Charger un utilisateur pour un surnom ou email */
function getPlayerByNickEmail($nick, $email)
{
	global $dbh;
	$res_player = mysqli_query($dbh,"SELECT playerID, nick, email FROM players WHERE nick = '".$nick."' OR email = '".$email."'");
    $player = mysqli_fetch_array($res_player, MYSQLI_ASSOC);
    return $player;
}

/* Liste tous les joueurs */
function listPlayers()
{
	global $dbh;
	$tmpQuery = "SELECT * FROM players ORDER BY email";

	return mysqli_query($dbh,$tmpQuery);
}

/* Liste les joueurs pour calcul Elo */
function listPlayersForElo($dateFin)
{
	global $dbh;
	$tmpQuery = "SELECT P.playerID playerID, E.elo elo, F.elo elo960, P.nick nick 
				FROM players P, elo_history E, elo960_history F
				WHERE P.playerID = E.playerID 
				AND P.playerID = F.playerID 
				AND P.activate=1 
				AND E.eloDate > '".$dateFin."' 
				AND F.eloDate > '".$dateFin."' 
				ORDER BY playerID";
	
	return mysqli_query($dbh,$tmpQuery);
}

/* Liste les joueurs par nom */
function listPlayersByNickName($str, $type)
{
	global $dbh;
	$tmpQuery = "SELECT playerID, nick, firstName, lastName
	FROM players ";
	if ($type != 0)
		$tmpQuery .= "WHERE nick = '".$str."'";
	else
		$tmpQuery .= "WHERE (nick like '%".$str."%' OR firstName like '%".$str."%' OR lastName like '%".$str."%')";

	$tmpQuery .= "	AND activate = 1
	AND playerID != '".$_SESSION['playerID']."'
	ORDER BY nick";

	return mysqli_query($dbh,$tmpQuery);
}

/* Historique Elo d'un joueur */
function listEloProgress($playerID)
{
	global $dbh;
	$tmpQuery = "SELECT elo, DATE_FORMAT(eloDate, '%c') eloDateF
	FROM elo_history
	WHERE playerID = ".$playerID."
	ORDER BY eloDate ASC";

	return mysqli_query($dbh,$tmpQuery);
}

function countActivePlayers()
{
	global $dbh;
	$res_player = mysqli_query($dbh,"SELECT count(playerID) nbPlayers 
								FROM players 
								WHERE DATE_ADD(lastConnection, INTERVAL 14 DAY) >= NOW() 
								AND activate = 1 
								ORDER BY lastConnection DESC");
	return mysqli_fetch_array($res_player, MYSQLI_ASSOC);
}

function countPassivePlayers()
{
	global $dbh;
	$res_player = mysqli_query($dbh,"SELECT count(playerID) nbPlayers 
								FROM players 
								WHERE DATE_ADD(lastConnection, INTERVAL 14 DAY) < NOW() 
								AND activate = 1 
								ORDER BY lastConnection DESC");
	return mysqli_fetch_array($res_player, MYSQLI_ASSOC);
}

/*
 * Recherche des utilisateurs
* $mode : count = renvoi le nb de résultat de la recherche sinon le résultat
* $debut :
* $limit : nb résultat par page
*/
function searchPlayers($mode, $debut, $limit, $playerID, $critFavorite, $critStatus, $critEloStart, $critEloEnd, $critCountry, $critName)
{

	global $dbh;
	if ($mode=="count")
		$tmpQuery = "SELECT count(*) nbPlayers
		FROM players P left join online_players O on O.playerID = P.playerID";
	else
		$tmpQuery = "SELECT P.playerID, P.nick, P.firstName, P.lastName, P.socialNetwork, P.socialID, P.profil,
		P.situationGeo, P.elo, P.elo960, P.lastConnection, P.creationDate,
		O.lastActionTime,
		C.countryName
		FROM players P left join online_players O on O.playerID = P.playerID, country C ";

	if ($critFavorite == "wing" || $critFavorite == "wers")
		$tmpQuery .= ", fav_players F";

	if ($mode=="count")
		$tmpQuery .= " WHERE P.activate=1
		AND P.playerID <> ".$playerID;
	else
		$tmpQuery .= " WHERE P.activate=1
		AND P.playerID <> ".$playerID."
		AND P.countryCode = C.countryCode
		AND C.countryLang = '".getLang()."'";

	if ($critStatus == "nouveau")
		$tmpQuery .= " AND DATE_ADD(P.creationDate, INTERVAL 14 DAY) >= NOW()";
	
	if ($critStatus == "actif")
		$tmpQuery .= " AND DATE_ADD(P.lastConnection, INTERVAL 14 DAY) >= NOW()";

	if ($critStatus == "passif")
		$tmpQuery .= " AND DATE_ADD(P.lastConnection, INTERVAL 14 DAY) < NOW()";

	if ($critEloStart != '' and $critEloEnd != '')
		$tmpQuery .= " AND P.elo >= ".$critEloStart." AND P.elo <= ".$critEloEnd;
	if ($critEloStart != '' and $critEloEnd == '')
		$tmpQuery .= " AND P.elo >= ".$critEloStart;
	if ($critEloStart == '' and $critEloEnd != '')
		$tmpQuery .= " AND P.elo <= ".$critEloEnd;

	if ($critCountry != '')
		$tmpQuery .= " AND P.countryCode = '".$critCountry."'";

	if ($critName != '')
		$tmpQuery .= " AND (P.nick like '%".$critName."%' OR P.firstName like '%".$critName."%' OR P.lastName like '%".$critName."%') ";

	if ($critFavorite == "wing")
		$tmpQuery .= " AND P.playerID = F.favPlayerID
		AND F.playerID = ".$playerID;

	if ($critFavorite == "wers")
		$tmpQuery .= " AND P.playerID = F.playerID
		AND F.favPlayerID = ".$playerID;

	$tmpQuery .= " ORDER BY O.lastActionTime DESC, P.nick ASC";

	if ($mode != "count")
		$tmpQuery .= " limit ".$debut.",".$limit;

	return mysqli_query($dbh,$tmpQuery);
}
/*
 * Recherche des utilisateurs pour les classements
* $debut :
* $limit : nb r�sultat par page
*/
function searchPlayersRanking($mode, $debut, $limit, $playerID, $critCountry, $critGameType, $critOrder)
{
	global $dbh;
	
	if ($mode=="count")
		$tmpQuery = "SELECT count(*) nbPlayers
		FROM players P ";
	else
		$tmpQuery = "SELECT P.playerID, P.nick, P.firstName, P.lastName, P.socialNetwork, P.socialID, P.profil,
		P.situationGeo, P.elo, P.elo960, P.lastConnection, P.creationDate, P.rank, P.rank960, C.countryName
		FROM players P, country C ";

	if ($mode=="count")
		$tmpQuery .= " WHERE P.activate=1";
	else
		$tmpQuery .= " WHERE P.activate=1
		AND P.countryCode = C.countryCode
		AND C.countryLang = '".getLang()."'";
	
	if ($critCountry != '')
		$tmpQuery .= " AND P.countryCode = '".$critCountry."'";

	if ($critGameType == 0)
		if ($critOrder == "ASC")
			$tmpQuery .= " ORDER BY P.elo ASC";
		else
			$tmpQuery .= " ORDER BY P.elo DESC";
	else
		if ($critOrder == "ASC")
			$tmpQuery .= " ORDER BY P.elo960 ASC";
		else
			$tmpQuery .= " ORDER BY P.elo960 DESC";

	if ($mode == "rank")
		$tmpQuery .= " limit ".$debut.",".$limit;

	return mysqli_query($dbh,$tmpQuery);
}


/*
 * PLAYER WRITE
*/

/* Insérer un joueur */	
function insertPlayer($password, $firstName, $lastName, $nick, $email, $countryCode, $anneeNaissance, $playerSex, $socialID, $socialNetwork)
{
	global $dbh;
	$strQuery = "INSERT INTO players (password, firstName, lastName, nick, email, countryCode, anneeNaissance, creationDate, playerSex, socialID, socialNetwork) 
	VALUES ('".$password."', '".addslashes(strip_tags($firstName))."', '".addslashes(strip_tags($lastName))."', '".$nick."', '".$email."', '".$countryCode."', '".$anneeNaissance."', now(), '".$playerSex."','".$socialID."','".$socialNetwork."')";
	$res_player = mysqli_query($dbh,$strQuery);

	if ($res_player)	
		return mysqli_insert_id($dbh);
	else
		return FALSE;
}

/* Mettre à jour un joueur */
function updatePlayer($playerID, $password, $firstName, $lastName, $nick, $email, $profil, $situationGeo, $anneeNaissance, $activate)
{ 		
	  global $dbh;
	  $res_player = mysqli_query($dbh,"UPDATE players SET password='".$password."', firstName='".addslashes(strip_tags($firstName))."', lastName='".addslashes(strip_tags($lastName))."', nick='".$nick."', email='".$email."', profil='".addslashes(strip_tags($profil))."', situationGeo='".addslashes(strip_tags($situationGeo))."', anneeNaissance='".$anneeNaissance."', activate=".$activate." WHERE playerID = ".$playerID);
	  
	if ($res_player)	
		return TRUE;
	else
		return FALSE;
}

/* Mettre à jour un joueur avec données réseau social */
function updatePlayerWithSocial($playerID, $password, $firstName, $lastName, $nick, $email, $profil, $situationGeo, $anneeNaissance, $activate, $socialNetwork, $socialID, $countryCode, $playerSex)
{ 		
	  global $dbh;
	  $res_player = mysqli_query($dbh,"UPDATE players 
	  							SET password='".$password."', 
		  							firstName='".addslashes(strip_tags($firstName))."', 
		  							lastName='".addslashes(strip_tags($lastName))."', 
		  							nick='".$nick."', email='".$email."', 
		  							profil='".addslashes(strip_tags($profil))."', 
		  							situationGeo='".addslashes(strip_tags($situationGeo))."', 
		  							anneeNaissance='".$anneeNaissance."', 
		  							activate=".$activate.", 
		  							socialID='".$socialID."', 
		  							socialNetwork='".$socialNetwork."',
	  								countryCode='".$countryCode."',
	  								playerSex='".$playerSex."'   
	  							WHERE playerID = ".$playerID);
	  
	if ($res_player)	
		return TRUE;
	else
		return FALSE;
}

/* 
 * PREFERENCES
 */

/* Insérer une pr�f�rence d'un joueur */
function insertPreference($playerID, $preference, $value)
{
	
	global $dbh;
	$res_preference = mysqli_query($dbh,"INSERT INTO preferences (playerID, preference, value) 
									VALUES (".$playerID.", '".$preference."', '".$value."')");
	return $res_preference;
}

/* Mise à jour d'une préférence */
function updatePreference($playerID, $preference, $value)
{
	
	global $dbh;
	$res_pref = mysqli_query($dbh,"UPDATE preferences SET value = '".$value."' 
								WHERE playerID = ".$playerID." AND preference = '".$preference."'");
	
	if ($res_pref)	
		return TRUE;
	else
		return FALSE;
}

function getPrefNotification($gameID, $playerColor)
{
	global $dbh;
	// Check player notification preferences
	if ($playerColor == 'white')
	{
		$tmpReceiver = mysqli_query($dbh,"SELECT P.email email, PR.value value, PR2.value language
				FROM games G, players P left join preferences PR2 on PR2.playerID = P.playerID AND PR2.preference='language', preferences PR
				WHERE G.gameID =".$gameID."
				AND G.whitePlayer = P.playerID
				AND PR.playerID = P.playerID
				AND PR.preference='emailnotification'");
	}
	else
	{
		$tmpReceiver = mysqli_query($dbh,"SELECT P.email email, PR.value value, PR2.value language
				FROM games G, players P left join preferences PR2 on PR2.playerID = P.playerID AND PR2.preference='language', preferences PR
				WHERE G.gameID =".$gameID."
				AND G.blackPlayer = P.playerID
				AND PR.playerID = P.playerID
				AND PR.preference='emailnotification'");
	}

	$receiver = mysqli_fetch_array($tmpReceiver, MYSQLI_ASSOC);

	return $receiver;
}

function getPrefValue($playerID, $prefName)
{
	global $dbh;
	$res_pref = mysqli_query($dbh,"SELECT value FROM preferences WHERE preference = '".$prefName."' AND playerID =".$playerID);
	$player = mysqli_fetch_array($res_pref, MYSQLI_ASSOC);
	return $player['value'];
}

/* 
 * VACATION
 */

/* Format date YYYY-MM-DD */
function insertVacation($playerID, $duration)
{
	global $dbh;
	$beginDate = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")+1,  date("Y")));
	$endDate = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")+$duration,  date("Y")));
	
	$res_absence = mysqli_query($dbh,"INSERT INTO vacation (playerID, beginDate, endDate, duration) 
								VALUES (".$playerID.", '".$beginDate."', '".$endDate."', ".$duration.")");
	return $res_absence;
}

/* Compte le nombre de jours d'absence pour un joueur sur une année */
/* Format de l'année YYYY */
function countVacation($playerID, $year)
{
	global $dbh;
	// Nombre de jours pour congés complètement sur l'année
	$res = mysqli_query($dbh,"SELECT SUM(duration) nbVacation 
						FROM vacation WHERE playerID=".$playerID." 
						AND YEAR(endDate)=".$year)  or die(mysqli_error($dbh)."\n".$requete);
	$res_vacation = mysqli_fetch_array($res, MYSQLI_ASSOC);   
	
	return $res_vacation['nbVacation'];
}

/* Récupère les vacances en cours d'un joueur */
function getCurrentVacation($playerID)
{
	global $dbh;
	$res_vacation = mysqli_query($dbh,"SELECT beginDate, endDate, duration 
								FROM vacation 
								WHERE playerID=".$playerID." 
								AND endDate >= NOW()");
	return $res_vacation;
}

/*
 * FOLLOW
 */

/* Créer un favori joueur */
function insertFavPlayer($playerID, $favPlayerID)
{
	
	global $dbh;
	$res_fav_player = mysqli_query($dbh,"INSERT INTO fav_players (playerID, favPlayerID) 
								VALUES (".$playerID.", '".$favPlayerID."')");
	return $res_fav_player;
}

/* Supprimer un favori joueur */
function deleteFavPlayer($favoriteID)
{
	global $dbh;
	$res_fav_player = mysqli_query($dbh,"DELETE FROM fav_players WHERE favoriteID = ".$favoriteID);  
							
	return $res_fav_player;
}

/* Liste les favoris d'un joueur */
function listPlayersFavoris($playerID)
{
	global $dbh;
	$tmpQuery = "SELECT P.playerID, P.nick, P.anneeNaissance, P.profil, P.situationGeo, P.elo 
				FROM players P, fav_players F
				WHERE P.playerID = F.favPlayerID 
				AND F.playerID = ".$playerID." 
				AND P.playerID <> ".$playerID." 
				AND P.activate=1 
				ORDER BY P.lastConnection DESC";
	
	return mysqli_query($dbh,$tmpQuery); 
}

/* Récupère un favori */
function getPlayerFavorite($playerID, $favPlayerID)
{
	global $dbh;
	$res_favorite = mysqli_query($dbh,"SELECT favoriteID FROM fav_players WHERE playerID = ".$playerID." AND favPlayerID = ".$favPlayerID);
    $favorite = mysqli_fetch_array($res_favorite, MYSQLI_ASSOC);
    return $favorite;
}

/*
 * ONLINE PLAYERS
 */

/* Charger un utilisateur en ligne par son ID */
function getOnlinePlayer($playerID)
{
	global $dbh;
	$res_olplayer = mysqli_query($dbh,"SELECT * FROM online_players WHERE playerID = ".$playerID);
    $olplayer = mysqli_fetch_array($res_olplayer, MYSQLI_ASSOC);
    return $olplayer;
}

/* Insérer joueur en ligne */	
function insertOnlinePlayer($playerID)
{
	global $dbh;
	$res_olplayer = mysqli_query($dbh,"INSERT INTO online_players (playerID, lastActionTime) 
								VALUES (".$playerID.", now())");

	if ($res_olplayer)	
		return mysqli_insert_id($dbh);
	else
		return FALSE;
}

/* Mettre à jour joueur en ligne*/
function updateOnlinePlayer($playerID)
{ 		
	  global $dbh;
	  $res_olplayer = mysqli_query($dbh,"UPDATE online_players 
	  							SET lastActionTime = now() 
	  							WHERE playerID = ".$playerID);
	  
	if ($res_olplayer)	
		return TRUE;
	else
		return FALSE;
}

/* Supprime tous les joueurs hors ligne */
function deleteOnlinePlayers()
{
	global $dbh;
	$res_olplayer = mysqli_query($dbh,"DELETE FROM online_players 
	  							WHERE now() > DATE_ADD(lastActionTime, INTERVAL 10 MINUTE)");
	
	return $res_olplayer;
}

function countOnlinePlayers()
{
	global $dbh;
	$res_olplayer = mysqli_query($dbh,"SELECT count(playerID) nbPlayers FROM online_players");
	return mysqli_fetch_array($res_olplayer, MYSQLI_ASSOC);
}

/* Historise le Elo des joueurs */
function createEloHistory()
{
	global $dbh;
	$res_elohistory = mysqli_query($dbh,"INSERT into elo_history SELECT now(), elo, playerID FROM players where activate=1");
	
	return $res_elohistory;
}

/* Historise le Elo Chess960 des joueurs */
function createElo960History()
{
	global $dbh;
	$res_elohistory = mysqli_query($dbh,"INSERT into elo960_history SELECT now(), elo960, playerID FROM players where activate=1");
	
	return $res_elohistory;
}

function updatePlayerRanks($playerID, $rank, $rank960)
{
	global $dbh;
	$res_player = mysqli_query($dbh,"UPDATE players SET rank=".$rank.", rank960=".$rank960." WHERE playerID = ".$playerID);
	  
	if ($res_player)	
		return TRUE;
	else
		return FALSE;
	
	
}
?>