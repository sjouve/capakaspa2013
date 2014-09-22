<?
/*
 * Delete a follow
 * 
 */
session_start();
// Parameters
if (!isset($_CONFIG))
	require '../../include/config.php';

// Connect DB
require '../../include/connectdb.php';
require '../../dac/dac_players.php';

// Insert a comment for an entity
$favoriteID=$_GET["id"];
$playerID=$_GET["player"];

$res = deleteFavPlayer($favoriteID);

if ($res) {
?>
<input id="btnFollow" value="<? echo _("Follow")?>" type="button" class="button" onclick="javascript:insertFav(<?echo($playerID);?>);">
<?
}
mysqli_close($dbh);
?>