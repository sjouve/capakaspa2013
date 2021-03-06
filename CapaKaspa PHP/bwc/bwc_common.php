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
						<link rel=\"stylesheet\" href=\"http://jouerauxechecs.capakaspa.info/css/capakaspa001.css\" type=\"text/css\">
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
			<? echo _("To")?> : <? echo(getPlayerName(0, $toNick, $toFirstName, $toLastName));?><br>
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
	<!-- <iframe src="http://rcm-eu.amazon-adsystem.com/e/cm?t=capa-21&o=8&p=286&l=st1&mode=books-fr&search=jouer echecs&fc1=000000&lt1=_blank&lc1=3366FF&bg1=FFFFFF&f=ifr" marginwidth="0" marginheight="0" width="200" height="200" border="0" frameborder="0" style="border:none;" scrolling="no"></iframe>
	 -->
	<?
	if (getLang() == "fr")
		$product = mt_rand(0,7);
	else $product = 8;
	
	switch($product)
	{
		case 0: // Livre : Les �checs, un jeu d'enfants !
			?>
			<div class="product" style="padding-left: 0px;">
			<?echo(_("Buy this book >>"));?>
			<a target="_blank" rel="nofollow" href="http://www.amazon.fr/gp/product/2916340416/ref=as_li_tl?ie=UTF8&camp=1642&creative=6746&creativeASIN=2916340416&linkCode=as2&tag=capa-21"><img border="0" src="http://ws-eu.amazon-adsystem.com/widgets/q?_encoding=UTF8&ASIN=2916340416&Format=_SL250_&ID=AsinImage&MarketPlace=FR&ServiceVersion=20070822&WS=1&tag=capa-21" ></a><img src="http://ir-fr.amazon-adsystem.com/e/ir?t=capa-21&l=as2&o=8&a=2916340416" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />			
			</div>
			<?
			break;
			
		case 1: // Livre : 1000 exercices pour bien progresser aux �checs
			?>
			<div class="product" style="padding-left: 0px;">
			<?echo(_("Buy this book >>"));?>
			<a target="_blank" rel="nofollow" href="http://www.amazon.fr/gp/product/2916340858/ref=as_li_tl?ie=UTF8&camp=1642&creative=6746&creativeASIN=2916340858&linkCode=as2&tag=capa-21"><img border="0" src="http://ws-eu.amazon-adsystem.com/widgets/q?_encoding=UTF8&ASIN=2916340858&Format=_SL250_&ID=AsinImage&MarketPlace=FR&ServiceVersion=20070822&WS=1&tag=capa-21" ></a><img src="http://ir-fr.amazon-adsystem.com/e/ir?t=capa-21&l=as2&o=8&a=2916340858" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />
			</div>
			<?
			break;
		
			case 2: // Livre : Les �checs - Nouvelle �dition Pour les nuls
			?>
			<div class="product" style="padding-left: 0px;">
			<?echo(_("Buy this book >>"));?>
			<a href="https://www.amazon.fr/%C3%89checs-Nouvelle-%C3%A9dition-Pour-nuls/dp/2754018700/ref=as_li_ss_il?ie=UTF8&linkCode=li3&tag=capa-21&linkId=77633245a51a11af03cbba600d7350b1" target="_blank"><img border="0" src="//ws-eu.amazon-adsystem.com/widgets/q?_encoding=UTF8&ASIN=2754018700&Format=_SL250_&ID=AsinImage&MarketPlace=FR&ServiceVersion=20070822&WS=1&tag=capa-21" ></a><img src="https://ir-fr.amazon-adsystem.com/e/ir?t=capa-21&l=li3&o=8&a=2754018700" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />
			</div>
			<?
			break;
		case 3: // Echiquiers
			?>
			<div class="product" style="padding-left: 11px;">
			<?echo(_("Buy this nice chessboard >>"));
			$chessboard = mt_rand(0,5);
			switch($chessboard)
			{
				case 0: // Grand jeu d'�checs EL GRANDE 51 x 52.5 cm
			?>
				<a target="_blank" rel="nofollow" href="http://www.amazon.fr/gp/product/B0009WSPRO/ref=as_li_tl?ie=UTF8&camp=1642&creative=6746&creativeASIN=B0009WSPRO&linkCode=as2&tag=capa-21"><img border="0" src="http://ws-eu.amazon-adsystem.com/widgets/q?_encoding=UTF8&ASIN=B0009WSPRO&Format=_SL160_&ID=AsinImage&MarketPlace=FR&ServiceVersion=20070822&WS=1&tag=capa-21" ></a><img src="http://ir-fr.amazon-adsystem.com/e/ir?t=capa-21&l=as2&o=8&a=B0009WSPRO" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />
				</div>
			<? 	break;
				case 1: // Legler - 2019767 - Jeu D'�chec
			?>
				<a target="_blank" rel="nofollow" href="http://www.amazon.fr/gp/product/B000EGFM7Q/ref=as_li_tl?ie=UTF8&camp=1642&creative=6746&creativeASIN=B000EGFM7Q&linkCode=as2&tag=capa-21"><img border="0" src="http://ws-eu.amazon-adsystem.com/widgets/q?_encoding=UTF8&ASIN=B000EGFM7Q&Format=_SL160_&ID=AsinImage&MarketPlace=FR&ServiceVersion=20070822&WS=1&tag=capa-21" ></a><img src="http://ir-fr.amazon-adsystem.com/e/ir?t=capa-21&l=as2&o=8&a=B000EGFM7Q" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />
				</div>
			<? 	break;
				case 2: // Chessebook Jeu d'Echecs Magn�tique s/w 36 x 36 cm
			?>
				<a href="https://www.amazon.fr/gp/product/B076VSZRT2/ref=as_li_ss_il?pf_rd_m=A1X6FK5RDHNB96&pf_rd_s=merchandised-search-3&pf_rd_r=1HCXWF71B46XKECM2FKV&pf_rd_t=101&pf_rd_p=1f52c5e6-6f5e-51a1-947a-847466cb171c&pf_rd_i=363591031&linkCode=li2&tag=capa-21&linkId=f9241b7fac9210418c350f1a0c22d3b1" target="_blank"><img border="0" src="//ws-eu.amazon-adsystem.com/widgets/q?_encoding=UTF8&ASIN=B076VSZRT2&Format=_SL160_&ID=AsinImage&MarketPlace=FR&ServiceVersion=20070822&WS=1&tag=capa-21" ></a><img src="https://ir-fr.amazon-adsystem.com/e/ir?t=capa-21&l=li2&o=8&a=B076VSZRT2" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />
				</div>
			<? 	break;
				case 3: // Jeu d'�checs en bois SAN MARCO - 30 x 30 cm
			?>
				<a target="_blank" rel="nofollow" href="http://www.amazon.fr/gp/product/B000A7RVAU/ref=as_li_tl?ie=UTF8&camp=1642&creative=6746&creativeASIN=B000A7RVAU&linkCode=as2&tag=capa-21"><img border="0" src="http://ws-eu.amazon-adsystem.com/widgets/q?_encoding=UTF8&ASIN=B000A7RVAU&Format=_SL160_&ID=AsinImage&MarketPlace=FR&ServiceVersion=20070822&WS=1&tag=capa-21" ></a><img src="http://ir-fr.amazon-adsystem.com/e/ir?t=capa-21&l=as2&o=8&a=B000A7RVAU" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />
				</div>
			<? 	break;
				case 4: // The Legend of Zelda Chess Jeu De Soci�t�
			?>
				<a href="https://www.amazon.fr/gp/product/B01FO7K4YA/ref=as_li_ss_il?pf_rd_m=A1X6FK5RDHNB96&pf_rd_s=merchandised-search-3&pf_rd_r=1HCXWF71B46XKECM2FKV&pf_rd_t=101&pf_rd_p=1f52c5e6-6f5e-51a1-947a-847466cb171c&pf_rd_i=363591031&linkCode=li2&tag=capa-21&linkId=8169cb9c98f04f3fc369f06f59cc1429" target="_blank"><img border="0" src="//ws-eu.amazon-adsystem.com/widgets/q?_encoding=UTF8&ASIN=B01FO7K4YA&Format=_SL160_&ID=AsinImage&MarketPlace=FR&ServiceVersion=20070822&WS=1&tag=capa-21" ></a><img src="https://ir-fr.amazon-adsystem.com/e/ir?t=capa-21&l=li2&o=8&a=B01FO7K4YA" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />
				</div>
			<? 	break;
				case 5: // BYTAN Jeu d'�checs Jeu Echec Magnetique �chec enfant, 25x25cm �chiquier Pliable comme Coffret avec Pions Aimant�s pour Enfant Cadeau
			?>
				<a href="https://www.amazon.fr/gp/product/B01N6S5ZV3/ref=as_li_ss_il?pf_rd_m=A1X6FK5RDHNB96&pf_rd_s=merchandised-search-3&pf_rd_r=1HCXWF71B46XKECM2FKV&pf_rd_t=101&pf_rd_p=1f52c5e6-6f5e-51a1-947a-847466cb171c&pf_rd_i=363591031&linkCode=li2&tag=capa-21&linkId=eb11f89d9f50294551d66167694ed427" target="_blank"><img border="0" src="//ws-eu.amazon-adsystem.com/widgets/q?_encoding=UTF8&ASIN=B01N6S5ZV3&Format=_SL160_&ID=AsinImage&MarketPlace=FR&ServiceVersion=20070822&WS=1&tag=capa-21" ></a><img src="https://ir-fr.amazon-adsystem.com/e/ir?t=capa-21&l=li2&o=8&a=B01N6S5ZV3" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />
				</div>
			<? 	break;
			
			}
			break;
		
		case 4: // Livre : Ma�triser les ouvertures - Volume 1: Recommande par la F�d�ration Fran�aise des Echecs
			?>
			<div class="product" style="padding-left: 0px;">
			<?echo(_("Buy this book >>"));?>
			<a target="_blank" rel="nofollow" href="http://www.amazon.fr/gp/product/2916340165/ref=as_li_tl?ie=UTF8&camp=1642&creative=6746&creativeASIN=2916340165&linkCode=as2&tag=capa-21"><img border="0" src="http://ws-eu.amazon-adsystem.com/widgets/q?_encoding=UTF8&ASIN=2916340165&Format=_SL250_&ID=AsinImage&MarketPlace=FR&ServiceVersion=20070822&WS=1&tag=capa-21" ></a><img src="http://ir-fr.amazon-adsystem.com/e/ir?t=capa-21&l=as2&o=8&a=2916340165" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />
			</div>
			<?
			break;

		case 5: // Livre : Les 100 finales qu'il faut conna�tre
			?>
			<div class="product" style="padding-left: 0px;">
			<?echo(_("Buy this book >>"));?>
			<a target="_blank" rel="nofollow" href="http://www.amazon.fr/gp/product/2916340653/ref=as_li_tl?ie=UTF8&camp=1642&creative=6746&creativeASIN=2916340653&linkCode=as2&tag=capa-21"><img border="0" src="http://ws-eu.amazon-adsystem.com/widgets/q?_encoding=UTF8&ASIN=2916340653&Format=_SL250_&ID=AsinImage&MarketPlace=FR&ServiceVersion=20070822&WS=1&tag=capa-21" ></a><img src="http://ir-fr.amazon-adsystem.com/e/ir?t=capa-21&l=as2&o=8&a=2916340653" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />
			</div>
			<?
			break;
		
		case 6: // Livre : Vive les �checs !
			?>
			<div class="product" style="padding-left: 10px;">
			<?echo(_("Buy this book >>"));?>
			<a target="_blank" rel="nofollow" href="http://www.amazon.fr/gp/product/2916340211/ref=as_li_tl?ie=UTF8&camp=1642&creative=6746&creativeASIN=2916340211&linkCode=as2&tag=capa-21"><img border="0" src="http://ws-eu.amazon-adsystem.com/widgets/q?_encoding=UTF8&ASIN=2916340211&Format=_SL250_&ID=AsinImage&MarketPlace=FR&ServiceVersion=20070822&WS=1&tag=capa-21" ></a><img src="http://ir-fr.amazon-adsystem.com/e/ir?t=capa-21&l=as2&o=8&a=2916340211" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />
			</div>
			<?
			break;
		
		case 7: // Horloge : Horloge d'�checs LESHP 
			?>
			<div class="product" style="padding-left: 15px;">
			<?echo(_("Buy this clock >>"));?>
			<a href="https://www.amazon.fr/LESHP-Num%C3%A9rique-Chronom%C3%A8tre-Applicable-Comp%C3%A9titions/dp/B06XVM48DS/ref=as_li_ss_il?ie=UTF8&qid=1517579762&sr=8-1&keywords=horloge+%C3%A9checs&linkCode=li2&tag=capa-21&linkId=015de5349803d787eb24ac00aacec775" target="_blank"><img border="0" src="//ws-eu.amazon-adsystem.com/widgets/q?_encoding=UTF8&ASIN=B06XVM48DS&Format=_SL160_&ID=AsinImage&MarketPlace=FR&ServiceVersion=20070822&WS=1&tag=capa-21" ></a><img src="https://ir-fr.amazon-adsystem.com/e/ir?t=capa-21&l=li2&o=8&a=B06XVM48DS" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />
			</div>
			<?
			break;	
			
		case 8: // Livre : Chess for Kids: How to Play and Win
			?>
			<div class="product" style="padding-left: 15px;">
			<?echo(_("Buy this book >>"));?>
			<a target="_blank" rel="nofollow" href="http://www.amazon.fr/gp/product/0716022540/ref=as_li_tl?ie=UTF8&camp=1642&creative=6746&creativeASIN=0716022540&linkCode=as2&tag=capa-21"><img border="0" src="http://ws-eu.amazon-adsystem.com/widgets/q?_encoding=UTF8&ASIN=0716022540&Format=_SL160_&ID=AsinImage&MarketPlace=FR&ServiceVersion=20070822&WS=1&tag=capa-21" ></a><img src="http://ir-fr.amazon-adsystem.com/e/ir?t=capa-21&l=as2&o=8&a=0716022540" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" /></div>
			<?
			break;		
	}
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
				<a href='player_view.php?playerID=".$tmpPlayer['playerID']."'><span class='name'>".getPlayerName(0, $tmpPlayer['nick'], $tmpPlayer['firstName'], $tmpPlayer['lastName'])."</span></a>");
		if ($tmpPlayer['lastActionTime'])
			echo("<img src='images/user_online.gif' style='vertical-align:bottom;' title='"._("Player online")."' alt='"._("Player online")."'/>");
		if (isNewPlayer($tmpPlayer['creationDate']))
			echo("<br><span class='newplayer'>"._("New player")."</span>");
		echo("</div>
				");
	}
	
	displaySuggestionAmazon();
	//echo("<a href=\"http://www.capakaspa.info/boutique-produits-personnalises/\" target=\"_blank\"><img src=\"http://www.capakaspa.info/wp-content/uploads/2017/09/CapaKaspa-Produits-personnalis%C3%A9s-bouton.jpg\" width=\"200\" alt=\"CapaKaspa boutique produits personnalis�s\" title=\"Personnalisez vos vetements et accessoires avec les design CapaKaspa !\"/></a>");
	
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
				<a href='player_view.php?playerID=".$tmpPlayer['playerID']."'><span class='name'>".getPlayerName(0, $tmpPlayer['nick'], $tmpPlayer['firstName'], $tmpPlayer['lastName'])."</span></a>");
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
					<a href='player_view.php?playerID=".$tmpPlayer['playerID']."'><span class='name'>".getPlayerName(0, $tmpPlayer['nick'], $tmpPlayer['firstName'], $tmpPlayer['lastName'])."</span></a>");
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