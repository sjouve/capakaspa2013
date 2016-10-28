<?
/* Depend on:
if (!isset($_CONFIG))
		require 'include/config.php';
require_once('dac/dac_players.php');
require_once('bwc/bwc_common.php');
require_once('bwc/bwc_chessutils.php');

require 'dac/dac_players.php';
require 'bwc/bwc_common.php';
require 'bwc/bwc_chessutils.php';
require 'bwc/bwc_players.php';
*/

/* Création du joueur et de ses préférences */
function createPlayer()
{
	global $dbh;
	@mysqli_query($dbh,"BEGIN");
	
	if ($_POST['pwdPassword']=='') return FALSE;
	if ($_POST['pwdPassword']!=$_POST['pwdPassword2']) return FALSE;
	
	$socialID = "avatar_homme.jpg";
	
	// Crée l'utilisateur
	$playerID = insertPlayer($_POST['pwdPassword'], "", "", $_POST['txtNick'], $_POST['txtEmail'], "FR", "", "M", $socialID, "CK");	
	if (!$playerID)
	{
		@mysqli_query($dbh,"ROLLBACK");
		return FALSE;  
	}
	
	// set Language preference
	$lang = getenv("LC_ALL");
	$res = insertPreference($playerID, "language", $lang);
	if (!$res)
	{
		@mysqli_query($dbh,"ROLLBACK");
		return FALSE;
	}
	
	// set Theme preference
	$res = insertPreference($playerID, "theme", "merida");
	if (!$res)
	{
		@mysqli_query($dbh,"ROLLBACK");
		return FALSE;  
	}
	
	// set Email notification preference
	$res = insertPreference($playerID, "emailnotification", "oui");
	if (!$res)
	{
		@mysqli_query($dbh,"ROLLBACK");
		return FALSE;  
	}
	
	// set Share invitation
	$res = insertPreference($playerID, "shareinvitation", "oui");
	if (!$res)
	{
		@mysqli_query($dbh,"ROLLBACK");
		return FALSE;
	}
	
	// set Share result
	$res = insertPreference($playerID, "shareresult", "oui");
	if (!$res)
	{
		@mysqli_query($dbh,"ROLLBACK");
		return FALSE;
	}
	
	// Envoi du message de confirmation avec langue d'affichage
	$mailSubject = _("[CapaKaspa] Sign up confirmation");
	$mailMsg = _("To complete your sign up please click the following link (in case of problems copy the link into the address bar of your browser)")." :\n";
	$mailMsg .= "http://jouerauxechecs.capakaspa.info/index.php?ToDo=activer&playerID=".$playerID."&nick=".$_POST['txtNick'];
	$res = sendMail($_POST['txtEmail'], $mailSubject, $mailMsg);
	
	if (!$res)
	{
		@mysqli_query($dbh,"ROLLBACK");
		return FALSE;
	}
			
	@mysqli_query($dbh,"COMMIT");
	return TRUE;
}

/* Mettre à jour le profil utilisateur */
function updateProfil($playerID, $pwdPassword, $pwdOldPassword, $firstName, $lastName, $email, $profil, $situationGeo, $anneeNaissance, $prefTheme, $prefEmailNotification, $prefLanguage, $prefInvitation, $prefResult, $socialNetwork, $socialID, $countryCode, $playerSex)
{
	global $dbh;
	$player = getPlayer($playerID);
	if (!$player)
	{
		return 0;
	}
	
	// Mauvais mot de passe
	if ($player['PASSWORD'] != $pwdOldPassword && $pwdOldPassword != "")
		return -1;
	
	// Désactivation
	if ($email == "")
	{
		$res = updatePlayer($playerID, $player['PASSWORD'], $player['firstName'], $player['lastName'], $player['nick'], $player['email'], $player['profil'], $player['situationGeo'], $player['anneeNaissance'], 0);
		if (!$res)
		{
			return 0;
		}
		return 1;
	}
	
	if ($socialID == "")
		if ($playerSex == "M")
			$socialID = "avatar_homme.jpg";
		else
			$socialID = "avatar_femme.jpg";
		
	@mysqli_query($dbh,"BEGIN");
		
	// Changement de mot de passe
	if (isset($pwdPassword) && $pwdPassword != "")
	{
		$res = updatePlayerWithSocial($playerID, $pwdPassword, $firstName, $lastName, $player['nick'], $email, $profil, $situationGeo, $anneeNaissance, $player['activate'], $socialNetwork, $socialID, $countryCode, $playerSex);
		if (!$res)
		{
			@mysqli_query($dbh,"ROLLBACK");
			return 0;  
		}
	}
	else
	{
		$res = updatePlayerWithSocial($playerID, $player['PASSWORD'], $firstName, $lastName, $player['nick'], $email, $profil, $situationGeo, $anneeNaissance, $player['activate'], $socialNetwork, $socialID, $countryCode, $playerSex);
		if (!$res)
		{
			@mysqli_query($dbh,"ROLLBACK");
			return 0;  
		}
	}
	
	// Préférences	
	// Thème
	$res = updatePreference($playerID, 'theme', $prefTheme);
	if (!$res)
	{
		@mysqli_query($dbh,"ROLLBACK");
		return 0;  
	}
		
	// Email Notification
	$res = updatePreference($playerID, 'emailnotification', $prefEmailNotification);
	if (!$res)
	{
		@mysqli_query($dbh,"ROLLBACK");
		return 0;  
	}
	
	// Language
	$res = updatePreference($playerID, 'language', $prefLanguage);
	if (!$res)
	{
		@mysqli_query($dbh,"ROLLBACK");
		return 0;
	}
	
	// Share invitation
	$res = updatePreference($playerID, 'shareinvitation', $prefInvitation);
	if (!$res)
	{
		@mysqli_query($dbh,"ROLLBACK");
		return 0;
	}
	
	// Share result
	$res = updatePreference($playerID, 'shareresult', $prefResult);
	if (!$res)
	{
		@mysqli_query($dbh,"ROLLBACK");
		return 0;
	}
	
	// Update current session
	$_SESSION['playerName'] = stripslashes(strip_tags($_POST['txtFirstName']))." ".stripslashes(strip_tags($_POST['txtLastName']));
	$_SESSION['firstName'] = stripslashes(strip_tags($_POST['txtFirstName']));
	$_SESSION['lastName'] = stripslashes(strip_tags($_POST['txtLastName']));
	$_SESSION['email'] = $_POST['txtEmail'];
	$_SESSION['situationGeo'] = stripslashes(strip_tags($_POST['txtSituationGeo']));
	$_SESSION['profil'] = stripslashes(strip_tags($_POST['txtProfil']));
	$_SESSION['anneeNaissance'] = $_POST['txtAnneeNaissance'];
	$_SESSION['pref_theme'] =  $_POST['rdoTheme'];
	$_SESSION['pref_emailnotification'] = $_POST['txtEmailNotification'];
	$_SESSION['socialID'] = $socialID;
	$_SESSION['socialNetwork'] = $_POST['rdoSocialNetwork'];
	$_SESSION['countryCode'] = $_POST['txtCountryCode'];
	$_SESSION['playerSex'] = $_POST['txtSex'];
	$_SESSION['pref_emailNotification'] = $_POST['txtEmailNotification'];
	$_SESSION['pref_theme'] = $_POST['rdoTheme'];
	$_SESSION['pref_language'] = $_POST['txtLanguage'];
	$_SESSION['pref_shareinvitation'] = $_POST['txtShareInvitation'];
	$_SESSION['pref_shareresult'] = $_POST['txtShareResult'];
	
	@mysqli_query($dbh,"COMMIT");
	return 1;
}

/* Demande d'activation d'un compte */
function activationRequest($nick, $password, $email)
{
	
	// Contrôle format email
	if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $email))
	{ 
		return -3;
	}
	
	// Contrôle unicité email
	$player = getPlayerByEmail($email);
	
	if ($player && $player['nick'] != $nick )
	{
		return -2;
	}
	
	// Existence du compte
	$player = getPlayerByNickPassword($nick, $password);
	
	if ($player)
	{
		$res = updatePlayer ($player['playerID'], $player['PASSWORD'], $player['firstName'], $player['lastName'], $player['nick'], $email, $player['profil'], $player['situationGeo'], $player['anneeNaissance'], $player['activate']);
		
		if (!$res)
		{
		  	return -1;
		}		
	}
	else
	{
		return 0;
	}
	
	// Envoi du message de confirmation avec langue affichage site
	$mailSubject = _("[CapaKaspa] Confirm activation");
	$mailMsg = _("To activate your account please click the following link (in case of problems copy the link into the address bar of your browser)")." :\n";
	$mailMsg .= "http://jouerauxechecs.capakaspa.info/index.php?ToDo=activer&playerID=".$player['playerID']."&nick=".$player['nick'];
	$mailMsg .= "\n\n"._("This message was sent automatically from the site CapaKaspa")." (http://jouerauxechecs.capakaspa.info).\n";
	$res = sendMail($_POST['txtEmail'], $mailSubject, $mailMsg);
	
	if (!$res)
	{
		return -1;
	}
	
	return 1;
}

/* Activer le compte d'un joueur */
function activatePlayer($playerID, $nick)
{
	$player = getPlayer($playerID);
	
	if ($player)
	{
		
		$res = updatePlayer ($playerID, $player['PASSWORD'], $player['firstName'], $player['lastName'], $player['nick'], $player['email'], $player['profil'], $player['situationGeo'], $player['anneeNaissance'], 1);
		
		if (!$res)
		{
		  	return FALSE;
		}		
	}
	else
	{
		return FALSE;
	}
	
	return TRUE;
}

/* Connexion d'un joueur */
function loginPlayer($nick, $password, $flagAuto)
{
	global $dbh;
	// check for a player with supplied nick and password
	$player = getPlayerByNickPassword($nick, $password);

	// Le joueur existe ?
	if (!$player)
	{
		return 0;
	}
	
	// Le joueur est-il activé ?
	if ($player['activate'] == 0)
	{
		return -1; 
	}
	
	// Save data in session
	$_SESSION['playerID'] = $player['playerID'];
	$_SESSION['lastInputTime'] = time();
	$_SESSION['playerName'] = stripslashes($player['firstName'])." ".stripslashes($player['lastName']);
	$_SESSION['firstName'] = stripslashes($player['firstName']);
	$_SESSION['lastName'] = stripslashes($player['lastName']);
	$_SESSION['nick'] = $player['nick'];
	$_SESSION['email'] = $player['email'];
	$_SESSION['situationGeo'] = stripslashes($player['situationGeo']);
	$_SESSION['profil'] = stripslashes($player['profil']);
	$_SESSION['anneeNaissance'] = $player['anneeNaissance'];
	$_SESSION['elo'] = $player['elo'];
	$_SESSION['elo960'] = $player['elo960'];
	$_SESSION['socialNetwork'] = $player['socialNetwork'];
	$_SESSION['socialID'] = $player['socialID'];
	$_SESSION['countryCode'] = $player['countryCode'];
	$_SESSION['playerSex'] = $player['playerSex'];

	// Load user preferences
	$tmpQuery = "SELECT * FROM preferences WHERE playerID = ".$_SESSION['playerID'];
	$tmpPreferences = mysqli_query($dbh,$tmpQuery);

	while($tmpPreference = mysqli_fetch_array($tmpPreferences, MYSQLI_ASSOC))
	{
		// setup SESSION var of name pref_PREF, like pref_theme
		$_SESSION['pref_'.$tmpPreference['preference']] = $tmpPreference['value'];
		
	}
	
	// Update last connection date
	$tmpQuery = "UPDATE players SET lastConnection = now() WHERE playerID = ".$_SESSION['playerID'];
	$tmpPlayers = mysqli_query($dbh,$tmpQuery);
	
	// Si se souvenir de moi création du cookie
	if ($flagAuto == "on")
	{
		setcookie('capakaspacn[nick]', $nick, (time()+3600*24*30));
		setcookie('capakaspacn[password]', $password, (time()+3600*24*30));
	}
	
return 1;
}

/* Envoi mot de passe oublié */
function sendPassword($email)
{
	$player = getPlayerByEmail($email);
	
	// Le joueur existe ?
	if (!$player)
	{
		return 0;
	}
	
	// Envoi du message avec mot de passe
	$mailSubject = _("[CapaKaspa] Your password");
	$mailMsg = _("Data about your account")." :\n";
	$mailMsg .= _("User name")." : ".$player['nick']."\n";
	$mailMsg .= _("Password")." : ".$player['PASSWORD']."\n";
	$res = sendMail($email, $mailSubject, $mailMsg);
	
	if (!$res)
	{
		return -1;
	}
	 
	return 1; 
}

/* Compte le nombre de joueurs actifs sur le site */
function getNbActivePlayers()
{
	$res = countActivePlayers();
	return $res['nbPlayers'];
}

/* Compte le nombre de joueurs passifs sur le site */
function getNbPassivePlayers()
{
	$res = countPassivePlayers();
	return $res['nbPlayers'];
}

/* Creation des jours d'absence */
function createVacation($playerID, $nbDays)
{
	
	global $dbh;
	// Contrôler le nombre de jours disponibles
	if ($nbDays > countAvailableVacation($playerID) || $nbDays < 1)
	{
		return -100;
	}
	
	@mysqli_query($dbh,"BEGIN");
	
	// Insérer l'absence
	$res = insertVacation($playerID, $nbDays);
	if (!$res) 
	{
		@mysqli_query($dbh,"ROLLBACK");
		return 0;
	} 
	
	$nbDays = $nbDays + 1;
	
	/*Lors de la saisie du congé il faut modifier la date du dernier coup des parties du joueur :
      Pour chaque partie (non expirée)
      	Si pas de congé en cours pour l'adversaire on ajoute la durée du congé saisi +1 à la date du dernier coup
      	Sinon on ajoute la durée du congé saisi - (date de fin du congé en cours de l'adversaire - date de début du congé saisi)
      		si l'ajout reste positif      
	*/
	$tmpGames = mysqli_query($dbh,"SELECT * 
                             FROM games
                             WHERE (gameMessage = '' OR gameMessage is NULL)
                             AND (whitePlayer = ".$playerID." OR blackPlayer = ".$playerID.")
                             AND lastMove >= DATE_SUB(CURDATE(), INTERVAL timeMove DAY)  
                             ORDER BY dateCreated");

	// Ne pas modifier les parties expirées pas encore terminées
	while($tmpGame = mysqli_fetch_array($tmpGames, MYSQLI_ASSOC))
    {
    	if ($tmpGame['whitePlayer']==$playerID)
    		$res_adv_vacations = getCurrentVacation($tmpGame['blackPlayer']);
    	else
    		$res_adv_vacations = getCurrentVacation($tmpGame['whitePlayer']);
    	
    	if (mysqli_num_rows($res_adv_vacations) == 0)
    	{	
    		$res = mysqli_query($dbh,"UPDATE games
    						SET  lastMove = DATE_ADD(lastMove, INTERVAL ".$nbDays." DAY)
    						WHERE gameID = ".$tmpGame['gameID']);
    		if (!$res)
    		{
    			@mysqli_query($dbh,"ROLLBACK");
    			return 0;
    		}
    	}
    	else
    	{
    		
    		$beginDate = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"),  date("Y")));
    		$res_adv_vacation = mysqli_fetch_array($res_adv_vacations, MYSQLI_ASSOC);
    		$nbDaysPlus = nbDays($beginDate, $res_adv_vacation['endDate']);
    		
    		if ($nbDaysPlus < $nbDays)
    		{
	    		
    			$nbDaysToAdd = $nbDays - $nbDaysPlus;
    			
    			mysqli_query($dbh,"UPDATE games
	    						SET  lastMove = DATE_ADD(lastMove, INTERVAL ".$nbDaysToAdd." DAY)
	    						WHERE gameID = ".$tmpGame['gameID']);
	    		if (!$res)
	    		{
	    			@mysqli_query($dbh,"ROLLBACK");
	    			return 0;
	    		}
    		}
    	}
    }
    
    @mysqli_query($dbh,"COMMIT");
	return 1;
}

/* Compte le nombre de jours d'absence disponible sur l'année en cours */
function countAvailableVacation($playerID)
{
	
	$nbVacation = countVacation($playerID, date('Y'));
	return MAX_NB_JOUR_ABSENCE - $nbVacation;
}

/* Récupère le chemin de la photo du profil */
function getPicturePath($socialNetwork, $socialID)
{
	$picturePath = "images/uploads/avatar_homme.jpg";
	switch($socialNetwork)
	{	
		case "CK":
			$picturePath = "images/uploads/".$socialID;
			break;
			
		case "GP": // TODO � modifier car �volution API
			$picturePath = "https://plus.google.com/s2/photos/profile/".$socialID."?sz=32";
			/*$profil_googleplus_json = file_get_contents("https://www.googleapis.com/plus/v1/people/".$socialID."?key=AIzaSyDbsmnLMbP6QxydxzhqZlCwxOVG1ewIX0o");
			$profil_googleplus = json_decode($profil_googleplus_json);
			$picturePath = $profil_googleplus->image->url;*/
			break;
		
		case "FB": // TODO � modifier car �volution API
			$picturePath = "https://graph.facebook.com/".$socialID."/picture";
			break;
			
		case "TW": // TODO � modifier car �volution API
			$picturePath = "http://api.twitter.com/1/users/profile_image/".$socialID.".xml";
			break;
	}
	
	return $picturePath;
}

/* R�cup�re le chemin de la photo du profil pour mobile */
function getPicturePathM($socialNetwork, $socialID)
{
	$picturePath = "http://jouerauxechecs.capakaspa.info/images/uploads/avatar_homme.jpg";
	switch($socialNetwork)
	{	
		case "CK":
			$picturePath = "http://jouerauxechecs.capakaspa.info/images/uploads/".$socialID;
			break;
			
		case "GP":
			$picturePath = "https://plus.google.com/s2/photos/profile/".$socialID."?sz=32";
			/*$profil_googleplus_json = file_get_contents("https://www.googleapis.com/plus/v1/people/".$socialID."?key=AIzaSyDbsmnLMbP6QxydxzhqZlCwxOVG1ewIX0o");
			$profil_googleplus = json_decode($profil_googleplus_json);
			$picturePath = $profil_googleplus->image->url;*/
			break;
		
		case "FB":
			$picturePath = "https://graph.facebook.com/".$socialID."/picture";
			break;
			
		case "TW":
			$picturePath = "http://api.twitter.com/1/users/profile_image/".$socialID.".xml";
			break;
	}
	
	return $picturePath;
}

/* Compte le nombre de joueurs en ligne sur le site */
function getNbOnlinePlayers()
{
	$res = countOnlinePlayers();
	return $res['nbPlayers'];
}

/* Est un nouveau joueur en fonction date de création */
function isNewPlayer($creationDate)
{
	$maxDate = date('Y/m/d', strtotime('-14 day'));
	list($year, $month, $day) = explode("-", $creationDate);
	$creationDate = date("Y/m/d", mktime(0,0,0, $month, substr($day, 0, 2), $year));
	
	if ($creationDate >= $maxDate)
		return true;
	else
		return false; 
}

/* Calcul du classement des joueurs par Elo Classique */
function computeRank()
{
	$result = searchPlayersRanking("", 0, 0, "", "", 0, "DESC");
	$numPlayers = mysqli_num_rows($result);
	$rank = 0;
	$number = 0;
	$currentElo = "";
	$previousElo = "";
	
	while($tmpPlayer = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$currentElo = $tmpPlayer['elo'];
	
		// On incr�mente syst�matiquement le compteur
		$number += 1;
		
		// Si le rupture alors le classement est �gal au compteur de joueur
		if ($currentElo != $previousElo)
			$rank = $number;
			
		$previousElo = $tmpPlayer['elo'];
		
		// Update player rank
		$res = updatePlayerRanks($tmpPlayer['playerID'], $rank, $tmpPlayer['rank960']);
		if (!$res) return false;
	}
	
	return true;
}

/* Calcul du classement des joueurs par Elo Classique */
function computeRank960()
{
	$result = searchPlayersRanking("", 0, 0, "", "", 2, "DESC");
	$numPlayers = mysqli_num_rows($result);
	$rank = 0;
	$number = 0;
	$currentElo = "";
	$previousElo = "";
	
	while($tmpPlayer = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$currentElo = $tmpPlayer['elo960'];
	
		// On incr�mente syst�matiquement le compteur
		$number += 1;
		
		// Si le rupture alors le classement est �gal au compteur de joueur
		if ($currentElo != $previousElo)
			$rank = $number;
			
		$previousElo = $tmpPlayer['elo960'];
		
		// Update player rank
		$res = updatePlayerRanks($tmpPlayer['playerID'], $tmpPlayer['rank'], $rank);
	}
	
	return true;
}

// Achievements
function getAchievements($playerID)
{
	$achievementsValues = array (1=>0, 2=>0, 3=>0, 4=>0, 5=>0, 6=>0);
	$achievementsCounts = countForAchievements($playerID);
	$achievementsValues[1] = $achievementsCounts["PLAYER"]; // 1
	$achievementsValues[2] = $achievementsCounts["CLASSIC"]; // 2
	$achievementsValues[3] = $achievementsCounts["OUTSIDE"]; // 3
	$achievementsValues[4] = $achievementsCounts["WINNER"]; // 4
	$achievementsValues[5] = $achievementsCounts["BLACKWIN"]; // 5
	$achievementsValues[6] = $achievementsCounts["SOCIAL"]; // 6
	
	// Table des niveaux par objectifs
	$achievementsLevels = array (1 => array(1=>15, 2=>100, 3=>500, 4=>1000, 5=>3000),
								2 => array(1=>10, 2=>80, 3=>400, 4=>800, 5=>2000),
								3 => array(1=>5, 2=>50, 3=>200, 4=>500, 5=>1000),
								4 => array(1=>10, 2=>50, 3=>150, 4=>300, 5=>500),
								5 => array(1=>10, 2=>50, 3=>200, 4=>500, 5=>1000),
								6 => array(1=>10, 2=>30, 3=>60, 4=>120, 5=>240));
	
	$levels = array (1=>0, 2=>0, 3=>0, 4=>0, 5=>0, 6=>0);
	$steps = array (1=>0, 2=>0, 3=>0, 4=>0, 5=>0, 6=>0);
	
	for ($i=1; $i<7; $i++)
	{
		$achievementLevels = $achievementsLevels[$i];
		$level = 0;
		while ((($level < 5) && $achievementsValues[$i] >= $achievementLevels[$level+1])) {
			$level++;
		}
		$levels[$i] = $level;
		if ($level < 5)
			$steps[$i] = $achievementLevels[$level+1];
		else
			$steps[$i] = $achievementLevels[$level];
		
	}
	
	
	// Retourne tableau objectifs : Num objectif, Nombre, Niveau, Palier prochain niveau
	$playerAchievements = array (1 => array("VAL" => $achievementsValues[1], 
											"LVL" => $levels[1], 
											"NXT" => $steps[1], 
											"PCT" => "ach_player.png",
											"NAM" => _("Player"),
											"DSC" => _("Total number of games played")),
								2 => array("VAL" => $achievementsValues[2], "LVL" => $levels[2], "NXT" => $steps[2], "PCT" => "ach_classic.jpg", "NAM" => _("Classic"), "DSC" => _("Number of classic games played")),
								3 => array("VAL" => $achievementsValues[3], "LVL" => $levels[3], "NXT" => $steps[3], "PCT" => "ach_outside.jpg", "NAM" => _("Outside the box"), "DSC" => _("Number of other games played")),
								4 => array("VAL" => $achievementsValues[4], "LVL" => $levels[4], "NXT" => $steps[4], "PCT" => "ach_winner.jpg", "NAM" => _("Winner"), "DSC" => _("Number of games won")),
								5 => array("VAL" => $achievementsValues[5], "LVL" => $levels[5], "NXT" => $steps[5], "PCT" => "ach_black.jpg", "NAM" => _("Black wins"), "DSC" => _("Number of games won with blacks")),
								6 => array("VAL" => $achievementsValues[6], "LVL" => $levels[6], "NXT" => $steps[6], "PCT" => "ach_social.gif", "NAM" => _("Social"), "DSC" => _("Number of different opponents")));
								
	return $playerAchievements;
}

function getPlayerName($mode, $nick, $firstName, $lastName)
{
	return $nick;
}
?>