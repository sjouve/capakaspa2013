<?
// Get language code 2 characters
function getLang()
{
	$lang = getenv("LC_ALL");
	if ($lang == "")
		$lang = isset($_SESSION['pref_language']) ? $_SESSION['pref_language'] : "fr";
	
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

/* Fonction d'envoi de mail */
function sendMail($msgTo, $mailSubject, $mailMsg)
{
	global $CFG_MAILADDRESS, $CFG_USEEMAILNOTIFICATION;

	$headers = "From: CapaKaspa <".$CFG_MAILADDRESS.">\r\n";
	$headers .= "Reply-To: CapaKaspa <".$CFG_MAILADDRESS.">\r\n";
	$headers .= "Content-Type: text/html; charset=\"UTF-8\"";
	
	$mailMsg = "<html><body><b>".$mailMsg."</b>";
	$mailMsg .= "<p>"._("This email was sent automatically from site CapaKaspa (http://jouerauxechecs.capakaspa.info)")."<br>";
	$mailMsg .= _("Follow us on")." <a href=\"https://www.facebook.com/capakaspa\">Facebook</a>, 
				<a href=\"https://plus.google.com/+CapakaspaInfo\">Google+</a>,
				<a href=\"https://www.twitter.com/CapaKaspa\">Twitter</a>,
				<a href=\"https://www.pinterest.com/capakaspa\">Pinterest</a>,
				<a href=\"https://www.youtube.com/user/CapaKaspaEchecs\">YouTube</a>"."</p>";
	$mailMsg .= "</body></html>";
	
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
		<div id="popupMessageForm" class="contentbody">
			<? 
			$toPlayerID = isset($toPlayerID)?$toPlayerID:"";
			$toFirstName = isset($toFirstName)?$toFirstName:"";
			$toLastName = isset($toLastName)?$toLastName:"";
			$toNick = isset($toNick)?$toNick:"";
			$toEmail = isset($toEmail)?$toEmail:"";		
			?>
			<h3><? echo _("New message")?></h3>
			<? echo _("To")?> : <? echo($toFirstName." ".$toLastName." (".$toNick.")");?><br>
			<textarea style="width: 370px; height: 100px; font-size: 12px;" id="privateMessage" rows="5" placeholder="<? echo _("Your message...")?>"></textarea><br>
			<div style="margin-top: 10px;">
				<input type="button" class="button" value="<? echo _("Send")?>" onclick="insertPrivateMessagePopup(<? echo($_SESSION['playerID'])?>,<? echo($toPlayerID)?>,'<? echo($toEmail)?>')">
				<input type="button" class="link" value="<? echo _("Cancel")?>" onclick="popup('popUpDiv')">
				<div style="float: right"><input type="button" class="link" onclick="location.href='message.php?pID=<? echo($toPlayerID)?>&pE=<?echo(base64_encode($toEmail))?>'" value="<? echo _("See full conversation")?>"></div>
			</div>
		</div>
		<div id="popupMessageProgress" class="contentbody" style="display: none;">
			<img src='images/ajaxloader.gif'/>
		</div>
		<div id="popupMessageSuccess" class="contentbody" style="display: none;">
			<div class="success"><? echo _("Message sent successfully")?></div>
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
	
	echo("		
			<div class='suggestion'>		
					<div id='picture' style='float: left; margin-right: 5px;'>
						<img src='images/picto_cup_20.png' width='32' height='32' border='0'/>
					</div>
					<a href='tournament_list.php'><span class='name'>"._("Tournaments")."</span></a>");
					echo("<br>"._("Register for a tournament"));
			echo("</div>
			
			");
			
	$limit = 10;
	$result = searchPlayers("", 0, $limit, $_SESSION['playerID'], "", "nouveau", "", "", "", "");
	while($tmpPlayer = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
			
		echo("
				<div class='suggestion'>
				<div id='picture' style='float: left; margin-right: 3px;'>
				<img src='".getPicturePath($tmpPlayer['socialNetwork'], $tmpPlayer['socialID'])."' width='32' height='32' border='0'/>
				</div>
				<a href='player_view.php?playerID=".$tmpPlayer['playerID']."'><span class='name'>".$tmpPlayer['firstName']." ".$tmpPlayer['lastName']." (".$tmpPlayer['nick'].")</span></a>");
		if ($tmpPlayer['lastActionTime'])
			echo("<img src='images/user_online.gif' style='vertical-align:bottom;' title='"._("Player online")."' alt='"._("Player online")."'/>");
		if (isNewPlayer($tmpPlayer['creationDate']))
			echo("<br><span class='newplayer'>"._("New player")."</span>");
		echo("</div>
				");
	}
	
	$limit = 5;
	$result = searchPlayers("", 0, $limit, $_SESSION['playerID'], "", "actif", $_SESSION['elo']-50, $_SESSION['elo']+50, "", "");
	$nbPlayers = mysqli_num_rows($result);
	while($tmpPlayer = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
			
		echo("		
		<div class='suggestion'>		
				<div id='picture' style='float: left; margin-right: 5px;'>
					<img src='".getPicturePath($tmpPlayer['socialNetwork'], $tmpPlayer['socialID'])."' width='32' height='32' border='0'/>
				</div>
				<a href='player_view.php?playerID=".$tmpPlayer['playerID']."'><span class='name'>".$tmpPlayer['firstName']." ".$tmpPlayer['lastName']." (".$tmpPlayer['nick'].")</span></a>");
				if ($tmpPlayer['lastActionTime'])
					echo(" <img src='images/user_online.gif' style='vertical-align:bottom;' title='"._("Player online")."' alt='"._("Player online")."'/>");
				if (isNewPlayer($tmpPlayer['creationDate']))
					echo("<br><span class='newplayer'>"._("New player")."</span>");
				echo("<br>"._("is the same level that you !"));
		echo("</div>
		
		");
	}
	
	if ($nbPlayers < 5)
	{
		$limit = 5 - $nbPlayers;
		$result = searchPlayers("", 0, $limit, $_SESSION['playerID'], "", "passif", $_SESSION['elo']-50, $_SESSION['elo']+50, "", "");	
		while($tmpPlayer = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
				
			echo("		
			<div class='suggestion'>		
					<div id='picture' style='float: left; margin-right: 5px;'>
						<img src='".getPicturePath($tmpPlayer['socialNetwork'], $tmpPlayer['socialID'])."' width='32' height='32' border='0'/>
					</div>
					<a href='player_view.php?playerID=".$tmpPlayer['playerID']."'><span class='name'>".$tmpPlayer['firstName']." ".$tmpPlayer['lastName']." (".$tmpPlayer['nick'].")</span></a>");
					if ($tmpPlayer['lastActionTime'])
						echo(" <img src='images/user_online.gif' style='vertical-align:bottom;' title='"._("Player online")."' alt='"._("Player online")."'/>");
					if (isNewPlayer($tmpPlayer['creationDate']))
						echo("<br><span class='newplayer'>"._("New player")."</span>");
					echo("<br>"._("is the same level that you !"));
			echo("</div>
			
			");
		}
	}
	
	
}
?>