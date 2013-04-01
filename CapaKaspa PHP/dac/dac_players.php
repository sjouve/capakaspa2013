<?
/* Accès aux données concernant les tables Players et Preferences */

/* Constantes du module */
define ("MAX_NB_JOUR_ABSENCE", 30);

/*
 * PLAYER READ
 */

/* Charger un utilisateur par son ID */
function getPlayer($playerID)
{
	$res_player = mysql_query("SELECT P.*, C.countryName 
								FROM players P, country C 
								WHERE playerID = ".$playerID." 
								AND P.countryCode = C.countryCode
								AND C.countryLang = '".getLang()."'");
	
    $player = mysql_fetch_array($res_player, MYSQL_ASSOC);
    return $player;
}

/* Charger un utilisateur pour email */
function getPlayerByEmail($email)
{
	$res_player = mysql_query("SELECT * FROM players WHERE email = '".$email."'");
    $player = mysql_fetch_array($res_player, MYSQL_ASSOC);
    return $player;
}

/* Charger un utilisateur pour un surnom et mot de passe */
function getPlayerByNickPassword($nick, $password)
{
	$res_player = mysql_query("SELECT * FROM players WHERE nick = '".$nick."' AND password = '".$password."'");
    $player = mysql_fetch_array($res_player, MYSQL_ASSOC);
    return $player;
}

/* Charger un utilisateur pour un surnom ou email */
function getPlayerByNickEmail($nick, $email)
{
	$res_player = mysql_query("SELECT playerID, nick, email FROM players WHERE nick = '".$nick."' OR email = '".$email."'");
    $player = mysql_fetch_array($res_player, MYSQL_ASSOC);
    return $player;
}

/* Liste tous les joueurs */
function listPlayers()
{
	$tmpQuery = "SELECT * FROM players ORDER BY email";

	return mysql_query($tmpQuery);
}

/* Liste les joueurs pour calcul Elo */
function listPlayersForElo()
{
	$tmpQuery = "SELECT P.playerID playerID, E.elo elo, P.nick nick 
				FROM players P, elo_history E 
				WHERE P.playerID = E.playerID 
				AND P.activate=1 
				AND E.eloDate > '2013-03-31' 
				ORDER BY playerID";

	return mysql_query($tmpQuery);
}

/* Liste les joueurs par nom */
function listPlayersByNickName($str, $type)
{
	$tmpQuery = "SELECT playerID, nick, firstName, lastName
	FROM players ";
	if ($type != 0)
		$tmpQuery .= "WHERE nick = '".$str."'";
	else
		$tmpQuery .= "WHERE (nick like '%".$str."%' OR firstName like '%".$str."%' OR lastName like '%".$str."%')";

	$tmpQuery .= "	AND activate = 1
	AND playerID != '".$_SESSION['playerID']."'
	ORDER BY nick";

	return mysql_query($tmpQuery);
}

/* Historique Elo d'un joueur */
function listEloProgress($playerID)
{
	$tmpQuery = "SELECT elo, DATE_FORMAT(eloDate, '%c/%y') eloDateF
	FROM elo_history
	WHERE playerID = ".$playerID."
	ORDER BY eloDate ASC";

	return mysql_query($tmpQuery);
}

function countActivePlayers()
{
	$res_player = mysql_query("SELECT count(playerID) nbPlayers 
								FROM players 
								WHERE DATE_ADD(lastConnection, INTERVAL 14 DAY) >= NOW() 
								AND activate = 1 
								ORDER BY lastConnection DESC");
	return mysql_fetch_array($res_player, MYSQL_ASSOC);
}

function countPassivePlayers()
{
	$res_player = mysql_query("SELECT count(playerID) nbPlayers 
								FROM players 
								WHERE DATE_ADD(lastConnection, INTERVAL 14 DAY) < NOW() 
								AND activate = 1 
								ORDER BY lastConnection DESC");
	return mysql_fetch_array($res_player, MYSQL_ASSOC);
}

/*
 * Recherche des utilisateurs
* $mode : count = renvoi le nb de résultat de la recherche sinon le résultat
* $debut :
* $limit : nb résultat par page
*/
function searchPlayers($mode, $debut, $limit, $playerID, $critFavorite, $critStatus, $critEloStart, $critEloEnd, $critCountry, $critName)
{

	if ($mode=="count")
		$tmpQuery = "SELECT count(*) nbPlayers
		FROM players P left join online_players O on O.playerID = P.playerID";
	else
		$tmpQuery = "SELECT P.playerID, P.nick, P.firstName, P.lastName, P.socialNetwork, P.socialID, P.profil,
		P.situationGeo, P.elo, P.lastConnection, P.creationDate,
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

	return mysql_query($tmpQuery);
}

/*
 * PLAYER WRITE
*/

/* Insérer un joueur */	
function insertPlayer($password, $firstName, $lastName, $nick, $email, $countryCode, $anneeNaissance, $playerSex, $socialID, $socialNetwork)
{
	$strQuery = "INSERT INTO players (password, firstName, lastName, nick, email, countryCode, anneeNaissance, creationDate, playerSex, socialID, socialNetwork) 
	VALUES ('".$password."', '".addslashes(strip_tags($firstName))."', '".addslashes(strip_tags($lastName))."', '".$nick."', '".$email."', '".$countryCode."', '".$anneeNaissance."', now(), '".$playerSex."','".$socialID."','".$socialNetwork."')";
	$res_player = mysql_query($strQuery);

	if ($res_player)	
		return mysql_insert_id();
	else
		return FALSE;
}

/* Mettre à jour un joueur */
function updatePlayer($playerID, $password, $firstName, $lastName, $nick, $email, $profil, $situationGeo, $anneeNaissance, $activate)
{ 		
	  $res_player = mysql_query("UPDATE players SET password='".$password."', firstName='".addslashes(strip_tags($firstName))."', lastName='".addslashes(strip_tags($lastName))."', nick='".$nick."', email='".$email."', profil='".addslashes(strip_tags($profil))."', situationGeo='".addslashes(strip_tags($situationGeo))."', anneeNaissance='".$anneeNaissance."', activate=".$activate." WHERE playerID = ".$playerID);
	  
	if ($res_player)	
		return TRUE;
	else
		return FALSE;
}

/* Mettre à jour un joueur avec données réseau social */
function updatePlayerWithSocial($playerID, $password, $firstName, $lastName, $nick, $email, $profil, $situationGeo, $anneeNaissance, $activate, $socialNetwork, $socialID, $countryCode, $playerSex)
{ 		
	  $res_player = mysql_query("UPDATE players 
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

/* Insérer une préférence d'un joueur */
function insertPreference($playerID, $preference, $value)
{
	
	$res_preference = mysql_query("INSERT INTO preferences (playerID, preference, value) 
									VALUES (".$playerID.", '".$preference."', '".$value."')");
	return $res_preference;
}

/* Mise à jour d'une préférence */
function updatePreference($playerID, $preference, $value)
{
	
	$res_pref = mysql_query("UPDATE preferences SET value = '".$value."' 
								WHERE playerID = ".$playerID." AND preference = '".$preference."'");
	
	if ($res_pref)	
		return TRUE;
	else
		return FALSE;
}

function getPrefNotification($gameID, $playerColor)
{
	// Check player notification preferences
	if ($playerColor == 'white')
	{
		$tmpReceiver = mysql_query("SELECT P.email email, PR.value value, PR2.value language
				FROM games G, players P left join preferences PR2 on PR2.playerID = P.playerID AND PR2.preference='language', preferences PR
				WHERE G.gameID =".$gameID."
				AND G.whitePlayer = P.playerID
				AND PR.playerID = P.playerID
				AND PR.preference='emailnotification'");
	}
	else
	{
		$tmpReceiver = mysql_query("SELECT P.email email, PR.value value, PR2.value language
				FROM games G, players P left join preferences PR2 on PR2.playerID = P.playerID AND PR2.preference='language', preferences PR
				WHERE G.gameID =".$gameID."
				AND G.blackPlayer = P.playerID
				AND PR.playerID = P.playerID
				AND PR.preference='emailnotification'");
	}

	$receiver = mysql_fetch_array($tmpReceiver, MYSQL_ASSOC);

	return $receiver;
}

function getPrefValue($playerID, $prefName)
{
	$res_pref = mysql_query("SELECT value FROM preferences WHERE preference = '".$prefName."' AND playerID =".$playerID);
	$player = mysql_fetch_array($res_pref, MYSQL_ASSOC);
	return $player['value'];
}

/* 
 * VACATION
 */

/* Format date YYYY-MM-DD */
function insertVacation($playerID, $duration)
{
	$beginDate = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")+1,  date("Y")));
	$endDate = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")+$duration,  date("Y")));
	
	$res_absence = mysql_query("INSERT INTO vacation (playerID, beginDate, endDate, duration) 
								VALUES (".$playerID.", '".$beginDate."', '".$endDate."', ".$duration.")");
	return $res_absence;
}

/* Compte le nombre de jours d'absence pour un joueur sur une année */
/* Format de l'année YYYY */
function countVacation($playerID, $year)
{
	// Nombre de jours pour congés complètement sur l'année
	$res = mysql_query("SELECT SUM(duration) nbVacation 
						FROM vacation WHERE playerID=".$playerID." 
						AND YEAR(endDate)=".$year)  or die(mysql_error()."\n".$requete);
	$res_vacation = mysql_fetch_array($res, MYSQL_ASSOC);   
	
	return $res_vacation['nbVacation'];
}

/* Récupère les vacances en cours d'un joueur */
function getCurrentVacation($playerID)
{
	$res_vacation = mysql_query("SELECT beginDate, endDate, duration 
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
	
	$res_fav_player = mysql_query("INSERT INTO fav_players (playerID, favPlayerID) 
								VALUES (".$playerID.", '".$favPlayerID."')");
	return $res_fav_player;
}

/* Supprimer un favori joueur */
function deleteFavPlayer($favoriteID)
{
	$res_fav_player = mysql_query("DELETE FROM fav_players WHERE favoriteID = ".$favoriteID);  
							
	return $res_fav_player;
}

/* Liste les favoris d'un joueur */
function listPlayersFavoris($playerID)
{
	$tmpQuery = "SELECT P.playerID, P.nick, P.anneeNaissance, P.profil, P.situationGeo, P.elo 
				FROM players P, fav_players F
				WHERE P.playerID = F.favPlayerID 
				AND F.playerID = ".$playerID." 
				AND P.playerID <> ".$playerID." 
				AND P.activate=1 
				ORDER BY P.lastConnection DESC";
	
	return mysql_query($tmpQuery); 
}

/* Récupère un favori */
function getPlayerFavorite($playerID, $favPlayerID)
{
	$res_favorite = mysql_query("SELECT favoriteID FROM fav_players WHERE playerID = ".$playerID." AND favPlayerID = ".$favPlayerID);
    $favorite = mysql_fetch_array($res_favorite, MYSQL_ASSOC);
    return $favorite;
}

/*
 * ONLINE PLAYERS
 */

/* Charger un utilisateur en ligne par son ID */
function getOnlinePlayer($playerID)
{
	$res_olplayer = mysql_query("SELECT * FROM online_players WHERE playerID = ".$playerID);
    $olplayer = mysql_fetch_array($res_olplayer, MYSQL_ASSOC);
    return $olplayer;
}

/* Insérer joueur en ligne */	
function insertOnlinePlayer($playerID)
{
	$res_olplayer = mysql_query("INSERT INTO online_players (playerID, lastActionTime) 
								VALUES (".$playerID.", now())");

	if ($res_olplayer)	
		return mysql_insert_id();
	else
		return FALSE;
}

/* Mettre à jour joueur en ligne*/
function updateOnlinePlayer($playerID)
{ 		
	  $res_olplayer = mysql_query("UPDATE online_players 
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
	$res_olplayer = mysql_query("DELETE FROM online_players 
	  							WHERE now() > DATE_ADD(lastActionTime, INTERVAL 10 MINUTE)");
	
	return $res_olplayer;
}

function countOnlinePlayers()
{
	$res_olplayer = mysql_query("SELECT count(playerID) nbPlayers FROM online_players");
	return mysql_fetch_array($res_olplayer, MYSQL_ASSOC);
}


?>