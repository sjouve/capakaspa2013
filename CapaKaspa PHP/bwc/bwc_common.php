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
	
	$mailMsg = "<html xmlns=\"http://www.w3.org/1999/xhtml\">
					<head>
						<link rel=\"stylesheet\" href=\"http://jouerauxechecs.capakaspa.info/css/capakaspa.css\" type=\"text/css\">
						<script type=\"application/ld+json\">
						{
						  \"@context\": \"http://schema.org\",
						  \"@type\": \"EmailMessage\",
						  \"potentialAction\": {
						    \"@type\": \"ViewAction\",
						    \"name\": \"View\",
						    \"target\": \"http://www.capakaspa.info\"
						  },
						  \"description\": \"View on site\"
						}
						</script> 
					</head>
					<body>
						<p><b>".$mailMsg."</b></p>".
						"<p>"._("This email was sent automatically from site CapaKaspa (http://jouerauxechecs.capakaspa.info)")."<br>".
						_("Follow us on")." <a href=\"https://www.facebook.com/capakaspa\">Facebook</a>, 
						<a href=\"https://plus.google.com/+CapakaspaInfo\">Google+</a>,
						<a href=\"https://www.twitter.com/CapaKaspa\">Twitter</a>,
						<a href=\"https://www.pinterest.com/capakaspa\">Pinterest</a>,
						<a href=\"https://www.youtube.com/user/CapaKaspaEchecs\">YouTube</a>".
						"</p>".
					"</body>
				</html>";
			
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

function displaySuggestionAmazon()
{?>
	<div id="sugamazon">
	<iframe src="http://rcm-eu.amazon-adsystem.com/e/cm?t=capa-21&o=8&p=286&l=st1&mode=books-fr&search=jouer echecs&fc1=000000&lt1=_blank&lc1=3366FF&bg1=FFFFFF&f=ifr" marginwidth="0" marginheight="0" width="200" height="200" border="0" frameborder="0" style="border:none;" scrolling="no"></iframe>

	<?
	/**if (getLang() == "fr")
		$product = mt_rand(0,6);
	else $product = 7;
	
	switch($product)
	{
		case 0:
			?>
			<div class="product" style="padding-left: 0px;">
			<?echo(_("Buy this book >>"));?>
			<a rel="nofollow" href="http://www.amazon.fr/gp/product/2916340416/ref=as_li_tl?ie=UTF8&camp=1642&creative=6746&creativeASIN=2916340416&linkCode=as2&tag=capa-21"><img border="0" src="http://ws-eu.amazon-adsystem.com/widgets/q?_encoding=UTF8&ASIN=2916340416&Format=_SL250_&ID=AsinImage&MarketPlace=FR&ServiceVersion=20070822&WS=1&tag=capa-21" ></a><img src="http://ir-fr.amazon-adsystem.com/e/ir?t=capa-21&l=as2&o=8&a=2916340416" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />			
			</div>
			<?
			break;
			
		case 1:
			?>
			<div class="product" style="padding-left: 0px;">
			<?echo(_("Buy this book >>"));?>
			<a rel="nofollow" href="http://www.amazon.fr/gp/product/2916340858/ref=as_li_tl?ie=UTF8&camp=1642&creative=6746&creativeASIN=2916340858&linkCode=as2&tag=capa-21"><img border="0" src="http://ws-eu.amazon-adsystem.com/widgets/q?_encoding=UTF8&ASIN=2916340858&Format=_SL250_&ID=AsinImage&MarketPlace=FR&ServiceVersion=20070822&WS=1&tag=capa-21" ></a><img src="http://ir-fr.amazon-adsystem.com/e/ir?t=capa-21&l=as2&o=8&a=2916340858" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />
			</div>
			<?
			break;
		
		case 2:
			?>
			<div class="product" style="padding-left: 9px;">
			<?echo(_("Buy this book >>"));?>
			<a rel="nofollow" href="http://www.amazon.fr/gp/product/2221110137/ref=as_li_tl?ie=UTF8&camp=1642&creative=6746&creativeASIN=2221110137&linkCode=as2&tag=capa-21"><img border="0" src="http://ws-eu.amazon-adsystem.com/widgets/q?_encoding=UTF8&ASIN=2221110137&Format=_SL250_&ID=AsinImage&MarketPlace=FR&ServiceVersion=20070822&WS=1&tag=capa-21" ></a><img src="http://ir-fr.amazon-adsystem.com/e/ir?t=capa-21&l=as2&o=8&a=2221110137" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />
			</div>
			<?
			break;
		case 3:
			?>
			<div class="product" style="padding-left: 11px;">
			<?echo(_("Buy this nice chessboard >>"));
			$chessboard = mt_rand(0,4);
			switch($chessboard)
			{
				case 0:
			?>
				<a rel="nofollow" href="http://www.amazon.fr/gp/product/B0009WSPRO/ref=as_li_tl?ie=UTF8&camp=1642&creative=6746&creativeASIN=B0009WSPRO&linkCode=as2&tag=capa-21"><img border="0" src="http://ws-eu.amazon-adsystem.com/widgets/q?_encoding=UTF8&ASIN=B0009WSPRO&Format=_SL160_&ID=AsinImage&MarketPlace=FR&ServiceVersion=20070822&WS=1&tag=capa-21" ></a><img src="http://ir-fr.amazon-adsystem.com/e/ir?t=capa-21&l=as2&o=8&a=B0009WSPRO" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />
				</div>
			<? 	break;
				case 1:
			?>
				<a rel="nofollow" href="http://www.amazon.fr/gp/product/B000EGFM7Q/ref=as_li_tl?ie=UTF8&camp=1642&creative=6746&creativeASIN=B000EGFM7Q&linkCode=as2&tag=capa-21"><img border="0" src="http://ws-eu.amazon-adsystem.com/widgets/q?_encoding=UTF8&ASIN=B000EGFM7Q&Format=_SL160_&ID=AsinImage&MarketPlace=FR&ServiceVersion=20070822&WS=1&tag=capa-21" ></a><img src="http://ir-fr.amazon-adsystem.com/e/ir?t=capa-21&l=as2&o=8&a=B000EGFM7Q" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />
				</div>
			<? 	break;
				case 2:
			?>
				<a rel="nofollow" href="http://www.amazon.fr/gp/product/B00BK9U0W8/ref=as_li_tl?ie=UTF8&camp=1642&creative=6746&creativeASIN=B00BK9U0W8&linkCode=as2&tag=capa-21"><img border="0" src="http://ws-eu.amazon-adsystem.com/widgets/q?_encoding=UTF8&ASIN=B00BK9U0W8&Format=_SL160_&ID=AsinImage&MarketPlace=FR&ServiceVersion=20070822&WS=1&tag=capa-21" ></a><img src="http://ir-fr.amazon-adsystem.com/e/ir?t=capa-21&l=as2&o=8&a=B00BK9U0W8" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />
				</div>
			<? 	break;
				case 3:
			?>
				<a rel="nofollow" href="http://www.amazon.fr/gp/product/B000A7RVAU/ref=as_li_tl?ie=UTF8&camp=1642&creative=6746&creativeASIN=B000A7RVAU&linkCode=as2&tag=capa-21"><img border="0" src="http://ws-eu.amazon-adsystem.com/widgets/q?_encoding=UTF8&ASIN=B000A7RVAU&Format=_SL160_&ID=AsinImage&MarketPlace=FR&ServiceVersion=20070822&WS=1&tag=capa-21" ></a><img src="http://ir-fr.amazon-adsystem.com/e/ir?t=capa-21&l=as2&o=8&a=B000A7RVAU" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />
				</div>
			<? 	break;
				case 4:
			?>
				<a rel="nofollow" href="http://www.amazon.fr/gp/product/B003R5PKUK/ref=as_li_tl?ie=UTF8&camp=1642&creative=6746&creativeASIN=B003R5PKUK&linkCode=as2&tag=capa-21"><img border="0" src="http://ws-eu.amazon-adsystem.com/widgets/q?_encoding=UTF8&ASIN=B003R5PKUK&Format=_SL160_&ID=AsinImage&MarketPlace=FR&ServiceVersion=20070822&WS=1&tag=capa-21" ></a><img src="http://ir-fr.amazon-adsystem.com/e/ir?t=capa-21&l=as2&o=8&a=B003R5PKUK" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />
				</div>
			<? 	break;
			}
			break;
		
		case 4:
			?>
			<div class="product" style="padding-left: 0px;">
			<?echo(_("Buy this book >>"));?>
			<a rel="nofollow" href="http://www.amazon.fr/gp/product/2916340165/ref=as_li_tl?ie=UTF8&camp=1642&creative=6746&creativeASIN=2916340165&linkCode=as2&tag=capa-21"><img border="0" src="http://ws-eu.amazon-adsystem.com/widgets/q?_encoding=UTF8&ASIN=2916340165&Format=_SL250_&ID=AsinImage&MarketPlace=FR&ServiceVersion=20070822&WS=1&tag=capa-21" ></a><img src="http://ir-fr.amazon-adsystem.com/e/ir?t=capa-21&l=as2&o=8&a=2916340165" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />
			</div>
			<?
			break;

		case 5:
			?>
			<div class="product" style="padding-left: 0px;">
			<?echo(_("Buy this book >>"));?>
			<a rel="nofollow" href="http://www.amazon.fr/gp/product/2916340653/ref=as_li_tl?ie=UTF8&camp=1642&creative=6746&creativeASIN=2916340653&linkCode=as2&tag=capa-21"><img border="0" src="http://ws-eu.amazon-adsystem.com/widgets/q?_encoding=UTF8&ASIN=2916340653&Format=_SL250_&ID=AsinImage&MarketPlace=FR&ServiceVersion=20070822&WS=1&tag=capa-21" ></a><img src="http://ir-fr.amazon-adsystem.com/e/ir?t=capa-21&l=as2&o=8&a=2916340653" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />
			</div>
			<?
			break;
		
		case 6:
			?>
			<div class="product" style="padding-left: 10px;">
			<?echo(_("Buy this book >>"));?>
			<a rel="nofollow" href="http://www.amazon.fr/gp/product/2916340211/ref=as_li_tl?ie=UTF8&camp=1642&creative=6746&creativeASIN=2916340211&linkCode=as2&tag=capa-21"><img border="0" src="http://ws-eu.amazon-adsystem.com/widgets/q?_encoding=UTF8&ASIN=2916340211&Format=_SL250_&ID=AsinImage&MarketPlace=FR&ServiceVersion=20070822&WS=1&tag=capa-21" ></a><img src="http://ir-fr.amazon-adsystem.com/e/ir?t=capa-21&l=as2&o=8&a=2916340211" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />
			</div>
			<?
			break;
			
			
		case 7:
			?>
			<div class="product" style="padding-left: 15px;">
			<?echo(_("Buy this book >>"));?>
			<a rel="nofollow" href="http://www.amazon.fr/gp/product/0716022540/ref=as_li_tl?ie=UTF8&camp=1642&creative=6746&creativeASIN=0716022540&linkCode=as2&tag=capa-21"><img border="0" src="http://ws-eu.amazon-adsystem.com/widgets/q?_encoding=UTF8&ASIN=0716022540&Format=_SL160_&ID=AsinImage&MarketPlace=FR&ServiceVersion=20070822&WS=1&tag=capa-21" ></a><img src="http://ir-fr.amazon-adsystem.com/e/ir?t=capa-21&l=as2&o=8&a=0716022540" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" /></div>
			<?
			break;		
	}**/
	?>
	</div>
	<?
}

function displaySuggestion()
{
	
	$fmt = new IntlDateFormatter(getenv("LC_ALL"), IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);
	
	echo("<div class='navlinks'>
		<div class='title'>
			"._("Suggestion")."
		</div>
	</div>");
	
	/**echo("		
			<div class='suggestion'>		
					<div id='picture' style='float: left; margin-right: 5px;'>
						<img src='images/picto_cup_20.png' width='32' height='32' border='0'/>
					</div>
					<a href='tournament_list.php'><span class='name'>"._("Tournaments")."</span></a>");
					echo("<br>"._("Register for a tournament"));
			echo("</div>
			
			");**/
	
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
	
	displaySuggestionAmazon();
}
?>