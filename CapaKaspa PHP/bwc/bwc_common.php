<?
// Get language code 2 characters
function getLang()
{
	$lang = getenv("LC_ALL");
	if ($lang == "")
		$lang = $_SESSION['pref_language'];
	
	return substr($lang, 0, 2);
}

// Number of days between 2 dates
function nbDays($debut, $fin) {
	$tDeb = explode("-", $debut);
	$tFin = explode("-", $fin);

	$diff = mktime(0, 0, 0, $tFin[1], $tFin[2], $tFin[0]) -
	mktime(0, 0, 0, $tDeb[1], $tDeb[2], $tDeb[0]);

	return(($diff / 86400)+1);

}

/* Affichage navigation pagination liste
* $pge = numéro de la page courante
* $limit = nombre de résultats par page
* $nb_tot = nombre de résultats
* $nbpages = nombre de pages
*/
function displayPageNav($pge, $limit, $nb_tot, $nbpages)
{
	echo("<div id='navliste'>");
	// Affichage de la première page si nécessaire (si nb total de pages supérieur à 5)
	if($nbpages > 1 and $pge > 0)
		echo("<div class='bouton'><a href='javascript:loadPage(0)'><img src='images/bt_paginateur_premier.png'/></a></div> ");

	// AFFICHAGE DU LIEN PRECEDENT SI BESOIN EST (LA PREMIERE PAGES EST 0)
	if ($pge > 0)
	{
		$precedent = $pge - 1;
		echo("<div class='bouton'><a href='javascript:loadPage(".$precedent.")'><img src='images/bt_paginateur_precedent.png'/></a></div> ");
	}

	echo("<div class='pages'>");
	// AFFICHAGE DES NUMEROS DE PAGE
	$i=0;
	$j=1;
	if($nb_tot > $limit)
	{
		while($i < $nbpages)
		{ //  Pour limiter l'affichage du nombre de pages restantes
			if ($i > $pge-5 and $i < $pge+5)
			{
				if($i != $pge)
					echo("<a href='javascript:loadPage(".$i.")'>".$j."</a> ");
				else
					echo($j." "); // Page courante
			}
			$i++;
			$j++;
		}
	}
	echo("</div>");
		
	// AFFICHAGE DU LIEN SUIVANT SI BESOIN EST
	if($pge < $nbpages-1)
	{
		$suivant = $pge+1;
		echo("<div class='bouton'><a href='javascript:loadPage(".$suivant.")'><img src='images/bt_paginateur_suivant.png'/></a></div> ");
	}
	// Affichage de la dernière page si nécessaire
	if($nbpages > 1 and $pge < $nbpages-1)
	{
		$fin = $nbpages-1;
		echo("<div class='bouton'><a href='javascript:loadPage(".$fin.")'><img src='images/bt_paginateur_dernier.png'/></a></div> ");
	}
	echo("<div class='pages'> (".$nbpages." pages - ".$nb_tot." résultats)</div>");
	echo("</div>");
}

/* Fonction d'envoi de mail */
function sendMail($msgTo, $mailSubject, $mailMsg)
{
	global $CFG_MAILADDRESS, $CFG_USEEMAILNOTIFICATION;

	$headers = "From: CapaKaspa <".$CFG_MAILADDRESS.">\r\n";
	$headers .= "To: ".$msgTo."\r\n";
	$headers .= "Reply-To: CapaKaspa <".$CFG_MAILADDRESS.">\r\n";
	
	$mailMsg .= _("\n\nThis email was sent automatically from site CapaKaspa (http://www.capakaspa.info).\n\n");
	$mailMsg .= _("Follow us on Facebook (http://www.facebook.com/capakaspa)\n");
	$mailMsg .= _("Follow us on Google+ (http://plus.google.com/114694270583726807082)\n");
	$mailMsg .= _("Follow us on Twitter (http://http://www.twitter.com/CapaKaspa)\n");
	$mailMsg .= _("Follow us on YouTube (http://http://www.youtube.com/user/CapaKaspaEchecs)\n");
	
	$res = false;
	
	if ($CFG_USEEMAILNOTIFICATION)
	{
		// TODO Warning: mail(): SMTP server response: 452 4.3.1 Insufficient system storage in D:\eclipse\wokspace\capakaspa\CapaKaspa PHP\bwc\bwc_common.php on line 100
		// Catcher le warning
		$res = mail($msgTo, $mailSubject, $mailMsg, $headers);
	}
	
	return $res;
}

function displayPrivateMessage($toPlayerID, $toFirstName, $toLastName, $toNick, $toEmail)
{?>
	<div id="blanket" style="display:none"></div>
	<div id="popUpDiv" style="display:none">
		<div class="contentbody">
			<? 
			$toPlayerID = isset($toPlayerID)?$toPlayerID:"";
			$toFirstName = isset($toFirstName)?$toFirstName:"";
			$toLastName = isset($toLastName)?$toLastName:"";
			$toNick = isset($toNick)?$toNick:"";
			$toEmail = isset($toEmail)?$toEmail:"";		
			?>
			<h3><? echo _("New message")?></h3>
			<? echo _("To")?> : <? echo($toFirstName." ".$toLastName." (".$toNick.")");?><br>
			<textarea style="width: 370px" id="privateMessage" rows="4" placeholder="<? echo _("Your message...")?>"></textarea><br>
			<input type="button" class="button" value="<? echo _("Send")?>" onclick="insertPrivateMessage(<? echo($_SESSION['playerID'])?>,<? echo($toPlayerID)?>,'<? echo($toEmail)?>')">
			<input type="button" class="link" value="<? echo _("Cancel")?>" onclick="popup('popUpDiv')">
		</div>
	</div>
<?}

function displaySuggestion()
{
	
	/*$type = mt_rand(0,1);
	switch($type)
	{
		case 0:
			$title = _("New player");
			$result = searchPlayers("", 0, $limit, $_SESSION['playerID'], "", "nouveau", "", "", "", "");			
			break;
			
		case 1:
			$title = _("Your level");
			$result = searchPlayers("", 0, $limit, $_SESSION['playerID'], "", "actif", $_SESSION['elo']-50, $_SESSION['elo']+50, $_SESSION['countryCode'], "");
			break;
	}*/
	
	$fmt = new IntlDateFormatter(getenv("LC_ALL"), IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);
	
	echo("<div class='navlinks'>
		<div class='title'>
			"._("Suggestion")."
		</div>
	</div>");
	
	$limit = 10;
	$result = searchPlayers("", 0, $limit, $_SESSION['playerID'], "", "nouveau", "", "", "", "");
	while($tmpPlayer = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		//$lastConnection = new DateTime($tmpPlayer['lastConnection']);
		//$strLastConnection = $fmt->format($lastConnection);
			
		echo("
				<div class='suggestion'>
				<div id='picture' style='float: left; margin-right: 5px;'>
				<img src='".getPicturePath($tmpPlayer['socialNetwork'], $tmpPlayer['socialID'])."' width='32' height='32' border='0'/>
				</div>
				<a href='player_view.php?playerID=".$tmpPlayer['playerID']."'><span class='name'>".$tmpPlayer['firstName']." ".$tmpPlayer['lastName']." (".$tmpPlayer['nick'].")</span></a>");
		if ($tmpPlayer['lastActionTime'])
			echo("<img src='images/user_online.gif' style='vertical-align:bottom;' alt='"._("Player online")."'/>");
		if (isNewPlayer($tmpPlayer['creationDate']))
			echo("<br><span class='newplayer'>"._("New player")."</span>");
		echo("</div>
				");
	}
	
	$limit = 5;
	$result = searchPlayers("", 0, $limit, $_SESSION['playerID'], "", "actif", $_SESSION['elo']-50, $_SESSION['elo']+50, $_SESSION['countryCode'], "");	
	while($tmpPlayer = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		//$lastConnection = new DateTime($tmpPlayer['lastConnection']);
		//$strLastConnection = $fmt->format($lastConnection);
			
		echo("		
		<div class='suggestion'>		
				<div id='picture' style='float: left; margin-right: 5px;'>
					<img src='".getPicturePath($tmpPlayer['socialNetwork'], $tmpPlayer['socialID'])."' width='32' height='32' border='0'/>
				</div>
				<a href='player_view.php?playerID=".$tmpPlayer['playerID']."'><span class='name'>".$tmpPlayer['firstName']." ".$tmpPlayer['lastName']." (".$tmpPlayer['nick'].")</span></a>");
				if ($tmpPlayer['lastActionTime'])
					echo(" <img src='images/user_online.gif' style='vertical-align:bottom;' alt='"._("Player online")."'/>");
				if (isNewPlayer($tmpPlayer['creationDate']))
					echo("<br><span class='newplayer'>"._("New player")."</span>");
				echo("<br>"._("is the same level that you !"));
		echo("</div>
		
		");
	}
	
}
?>