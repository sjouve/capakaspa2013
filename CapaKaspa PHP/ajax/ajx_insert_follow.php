<?
/*
 * Insert a follow
 * 
 */
session_start();
// Parameters
if (!isset($_CONFIG))
	require '../include/config.php';

// Connect DB
require '../include/connectdb.php';
require '../dac/dac_players.php';

// Insert a comment for an entity
$playerID=$_GET["player"];

$res = insertFavPlayer($_SESSION['playerID'], $playerID);
$favoriteID = mysql_insert_id();
if ($res) {
?>
<input id="btnUnfollow" value="<? echo _("Unfollow")?>" type="button" class="button" onclick="javascript:deleteFav(<?echo($favoriteID);?>, <?echo($playerID);?>);">
<?
// TODO Send notification
}
mysql_close();
?>