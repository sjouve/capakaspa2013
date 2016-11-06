<?
/* 
 * php5.4 -f /kunden/homepages/3/d148385019/htdocs/capakaspa/batch/bat_game_expiration.php 
 *
 * Commande pour test en local sur le PC 
 * C:\"Program Files (x86)"\EasyPHP-DevServer-14.1VC9\binaries\php\php_runningversion\php.exe "C:\Users\Sebastien\git\capakaspa2013\CapaKaspa PHP\batch\bat_game_expiration.php"
 */

// TODO A finaliser qd cron dispo sur hébergement

/* database settings */
/* local server */
$CFG_SERVER = "127.0.0.1";
$CFG_USER = "root";
$CFG_PASSWORD = "";
$CFG_DATABASE = "capakaspa";

/* email notification requires PHP to be properly configured for */
/* SMTP operations.  This flag allows you to easily activate
					   or deactivate this feature.  It is highly recommended you test
					   it before putting it into production */
$CFG_USEEMAILNOTIFICATION = true;

// email address people see when receiving CapaKaspa generated mail
$CFG_MAILADDRESS = "capakaspa@capakaspa.info";	

/* connect to database */
global $dbh;
$dbh=mysqli_connect($CFG_SERVER, $CFG_USER, $CFG_PASSWORD, $CFG_DATABASE)
	or die ('CapaKaspa cannot connect to the database.  Please check the database settings in your config : '.mysqli_connect_error());

mysqli_query($dbh, "SET NAMES UTF8");

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
		$res = mail($msgTo, $mailSubject, $mailMsg, $headers);
	}
	
	return $res;
}

/**
 * Batch alerte expiration partie
 */
function batchGameExpiration()
{
	global $dbh;
	sendMail("capakaspa@capakaspa.info", "[CapaKaspa] "._("Batch : Game expiration starts !"), "Le batch démarre.");
	$tmpGames = mysqli_query($dbh, "SELECT G.gameID gameID, DATE_ADD(G.lastMove, INTERVAL G.timeMove DAY) expirationDate, 
											W.playerID whitePlayerID, W.nick whiteNick, W.email whiteEmail, PRWL.value whiteLang, PRWE.value whiteNotif,
											B.playerID blackPlayerID, B.nick blackNick, B.email blackEmail, PRBL.value blackLang, PRBE.value blackNotif,
											(SELECT COUNT(gameID) nbMove FROM history H WHERE H.gameID = G.gameID) nbMoves
									FROM games G, players W LEFT JOIN preferences PRWL on PRWL.playerID = W.playerID AND PRWL.preference='language'
															LEFT JOIN preferences PRWE on PRWE.playerID = W.playerID AND PRWE.preference='emailnotification'
												, players B LEFT JOIN preferences PRBL on PRBL.playerID = B.playerID AND PRBL.preference='language'
															LEFT JOIN preferences PRBE on PRBE.playerID = W.playerID AND PRBE.preference='emailnotification'
									WHERE (gameMessage is NULL OR gameMessage = '')
									AND W.playerID = G.whitePlayer 
									AND B.playerID = G.blackPlayer
									AND DATE_ADD(G.lastMove, INTERVAL G.timeMove DAY) < DATE_ADD(now(), INTERVAL 1 DAY)");
	
	while ($thisGame = mysqli_fetch_array($tmpGames, MYSQLI_ASSOC))
	{
		if ($thisGame['nbMoves'] == -1 || ($thisGame['nbMoves'] % 2) == 1) {
			$email = $thisGame['whiteEmail'];
			$local = isset($thisGame['whiteLang'])?$thisGame['whiteLang']:"en_EN";
			$notif = $thisGame['whiteNotif'];
			$opponent = $thisGame['blackNick'];
		} else {
 			$email = $thisGame['blackEmail'];
			$local = isset($thisGame['blackLang'])?$thisGame['blackLang']:"en_EN";
			$notif = $thisGame['blackNotif'];
 			$opponent = $thisGame['whiteNick'];
		}
		
		if ($notif=="oui")
		{
			putenv("LC_ALL=$locale");
			setlocale(LC_ALL, $locale);
			bindtextdomain("messages", "./locale");
			bind_textdomain_codeset("messages", "UTF-8");
			textdomain("messages");
			
			sendMail($email, "[CapaKaspa] "._("Game expiration alert !"), _("You still have less than 24 hours to play your move in your game against")." ".$opponent);
		}
	}
	
	return TRUE;
}

/* Traitement des actions */
$err = 0;
$err = batchGameExpiration();

mysqli_close($dbh);
?>