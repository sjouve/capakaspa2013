<?
/*
 * Insert a follow
 * 
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
$playerID=$_GET["player"];

$res = insertFavPlayer($_SESSION['playerID'], $playerID);
$favoriteID = mysql_insert_id();
if ($res) {
	// TODO Send notification
	// Email with receiver language
	// getPlayerPreference($playerID, $preference);
	$locale = isset($receiver['language'])?$receiver['language']:"en_EN";
	putenv("LC_ALL=$locale");
	setlocale(LC_ALL, $locale);
	bindtextdomain("messages", "./locale");
	textdomain("messages");
	
	$msgTo = "";
	$mailSubject = "[CapaKaspa] "._("You have a new follower");
	sendMail($msgTo, $mailSubject, $mailMsg);
?>
<input id="btnUnfollow" value="<? echo _("Unfollow")?>" type="button" class="button" onclick="javascript:deleteFav(<?echo($favoriteID);?>, <?echo($playerID);?>);">
<?

}
mysql_close();
?>