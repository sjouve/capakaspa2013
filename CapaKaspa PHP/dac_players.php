<?
/* Accès aux données concernant les tables Players et Preferences */

/* Constantes du module */
define ("MAX_NB_JOUR_ABSENCE", 35);

/* Charger un utilisateur par son ID */
function getPlayer($playerID)
{
	$res_player = mysql_query("SELECT * FROM players WHERE playerID = ".$playerID);
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

/* Insérer un joueur */	
function insertPlayer($password, $firstName, $lastName, $nick, $email, $profil, $situationGeo, $anneeNaissance)
{
	$res_player = mysql_query("INSERT INTO players (password, firstName, lastName, nick, email, profil, situationGeo, anneeNaissance) VALUES ('".$password."', '".addslashes(strip_tags($firstName))."', '".addslashes(strip_tags($lastName))."', '".$nick."', '".$email."', '".addslashes(strip_tags($profil))."', '".addslashes(strip_tags($situationGeo))."', '".$anneeNaissance."')");

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

/* Liste tous les joueurs */
function listPlayers()
{
	$tmpQuery = "SELECT * FROM players ORDER BY email";
	
	return mysql_query($tmpQuery);  
}

/* Liste tous les joueurs */
function listPlayersForElo()
{
	$tmpQuery = "SELECT P.playerID playerID, E.elo elo, P.nick nick FROM players P, elo_history E WHERE P.playerID = E.playerID AND P.activate=1 AND E.eloDate > '2011-09-30' ORDER BY playerID";
	
	return mysql_query($tmpQuery);  
}

/* Liste les joueurs actifs */
function listPlayersActifs()
{
	$tmpQuery = "SELECT playerID, nick, anneeNaissance, profil, situationGeo, elo FROM players WHERE DATE_ADD(lastConnection, INTERVAL 14 DAY) >= NOW() AND playerID <> ".$_SESSION['playerID']." AND activate=1 ORDER BY lastConnection DESC";
	
	return mysql_query($tmpQuery);  
}

/* Liste les joueurs passifs */
function listPlayersPassifs()
{
	$tmpQuery = "SELECT playerID, nick, anneeNaissance, profil, situationGeo, elo FROM players WHERE DATE_ADD(lastConnection, INTERVAL 14 DAY) < NOW() AND playerID <> ".$_SESSION['playerID']." AND activate=1 ORDER BY lastConnection DESC";
	
  	return mysql_query($tmpQuery);  
}

function deletePlayer()
{
	// TODO suppression joueur ?
}

function countActivePlayers()
{
	$res_player = mysql_query("SELECT count(playerID) nbPlayers FROM players WHERE DATE_ADD(lastConnection, INTERVAL 14 DAY) >= NOW() AND activate = 1 ORDER BY lastConnection DESC");
	return mysql_fetch_array($res_player, MYSQL_ASSOC);
}

function countPassivePlayers()
{
	$res_player = mysql_query("SELECT count(playerID) nbPlayers FROM players WHERE DATE_ADD(lastConnection, INTERVAL 14 DAY) < NOW() AND activate = 1 ORDER BY lastConnection DESC");
	return mysql_fetch_array($res_player, MYSQL_ASSOC);
}
	
/* Préférences */
/* Insérer une préférence d'un joueur */
function insertPreference($playerID, $preference, $value)
{
	
	$res_preference = mysql_query("INSERT INTO preferences (playerID, preference, value) VALUES (".$playerID.", '".$preference."', '".$value."')");
	return $res_preference;
}

/* Mise à jour d'une préférence */
function updatePreference($playerID, $preference, $value)
{
	
	$res_pref = mysql_query("UPDATE preferences SET value = '".$value."' WHERE playerID = ".$playerID." AND preference = '".$preference."'");
	
	if ($res_pref)	
		return TRUE;
	else
		return FALSE;
}

/* Insérer un congé */
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
	$res = mysql_query("SELECT SUM(duration) nbVacation FROM vacation WHERE playerID=".$playerID." AND YEAR(endDate)=".$year)  or die(mysql_error()."\n".$requete);
	$res_vacation = mysql_fetch_array($res, MYSQL_ASSOC);   
	
	return $res_vacation['nbVacation'] + $d;;
}

/* Récupère les vacances en cours d'un joueur */
function getCurrentVacation($playerID)
{
	$res_vacation = mysql_query("SELECT beginDate, DATE_FORMAT(beginDate, '%d/%m/%Y') beginDateF, endDate, DATE_FORMAT(endDate, '%d/%m/%Y') endDateF, duration 
								FROM vacation 
								WHERE playerID=".$playerID." 
								AND endDate >= NOW()");
	return $res_vacation;
}

/* Créer un favori joueur */
function insertFavPlayer($playerID, $favPlayerID)
{
	
	$res_favplayer = mysql_query("INSERT INTO fav_players (playerID, favPlayerID) 
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
?>