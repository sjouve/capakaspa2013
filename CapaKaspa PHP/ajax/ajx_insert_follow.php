<?
/*
 * Insert a follow
 */
session_start();
// Parameters
if (!isset($_CONFIG))
	require '../include/config.php';

require '../dac/dac_players.php';
require '../bwc/bwc_common.php';

// Connect DB
require '../include/connectdb.php';

// Insert a comment for an entity
$playerID = $_GET["player"];
$playerEmail = $_GET["email"];

$res = insertFavPlayer($_SESSION['playerID'], $playerID);
$favoriteID = mysql_insert_id();
if ($res) {
	// Email with receiver language
	$emailNotif = getPrefValue($playerID, "emailnotification");
	if ($emailNotif == "oui")
	{
		$locale = getPrefValue($playerID, "language");
		putenv("LC_ALL=$locale");
		setlocale(LC_ALL, $locale);
		bindtextdomain("messages", "./locale");
		bind_textdomain_codeset("messages", "UTF-8");
		textdomain("messages");
		
		$msgTo = $playerEmail;
		$mailSubject = "[CapaKaspa] "._("You have a new follower");
		$mailMsg = $_SESSION['firstName']." ".$_SESSION['lastName']." (".$_SESSION['nick'].") "._("follow you on CapaKaspa.");
		sendMail($msgTo, $mailSubject, $mailMsg);
		
		$locale = $_SESSION['pref_language'];
		putenv("LC_ALL=$locale");
		setlocale(LC_ALL, $locale);
		bindtextdomain("messages", "./locale");
		bind_textdomain_codeset("messages", "UTF-8");
		textdomain("messages");
		
	}
?>
<input id="btnUnfollow" value="<? echo _("Unfollow")?>" type="button" class="button" onclick="javascript:deleteFav(<?echo($favoriteID);?>, <?echo($playerID);?>);">
<?
}
mysql_close();
?>