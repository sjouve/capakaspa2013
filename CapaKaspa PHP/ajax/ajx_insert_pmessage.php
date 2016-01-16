<?
/*
 * send a private message
 * 
 */
session_start();
// Parameters
if (!isset($_CONFIG))
	require '../include/config.php';

// Connect DB
require '../include/connectdb.php';
require '../dac/dac_activity.php';
require '../dac/dac_players.php';
require '../bwc/bwc_common.php';

// Insert a private message
$fromPlayerID=$_GET["fromID"];
$toPlayerID=$_GET["toID"];
$message=urldecode($_GET["mes"]);
$toEmail=$_GET["toEmail"];

$res = insertPrivateMessage($fromPlayerID, $toPlayerID, $message);

if ($res) {
	// Email with receiver language
	$emailNotif = getPrefValue($toPlayerID, "emailnotification");
	if ($emailNotif == "oui")
	{
		$locale = getPrefValue($toPlayerID, "language");
		putenv("LC_ALL=$locale");
		setlocale(LC_ALL, $locale);
		bindtextdomain("messages", "./locale");
		bind_textdomain_codeset("messages", "UTF-8");
		textdomain("messages");
		
		$msgTo = $toEmail;
		$mailSubject = "[CapaKaspa] "._("You have a new private message");
		$mailMsg = $_SESSION['firstName']." ".$_SESSION['lastName']." (".$_SESSION['nick'].") "._("send you a new private message.");
		$mailMsg .= "<br>[".$message."]";
		sendMail($msgTo, $mailSubject, $mailMsg);
		
		$locale = $_SESSION['pref_language'];
		putenv("LC_ALL=$locale");
		setlocale(LC_ALL, $locale);
		bindtextdomain("messages", "./locale");
		bind_textdomain_codeset("messages", "UTF-8");
		textdomain("messages");		
	}
}
mysqli_close($dbh);
?>