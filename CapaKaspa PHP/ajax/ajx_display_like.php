<?
/*
 * Display likes for an entity
 * 
 */
session_start();
// Parameters
if (!isset($_CONFIG))
	require '../include/config.php';

require '../include/constants.php';
require '../dac/dac_activity.php';

// Connect DB
require '../include/connectdb.php';

// Load comments for an entity
$entityType=$_GET["type"];
$entityID=$_GET["id"];

$tmpLikes = listLike($entityType, $entityID);
echo("<div class='likeList'>");
while($tmpLike = mysqli_fetch_array($tmpLikes, MYSQLI_ASSOC))
{
	echo("<div class='likePlayer'>".getPlayerName(0, $tmpLike['nick'], $tmpLike['firstName'], $tmpLike['lastName'])."</div>");
}
echo("</div>");
mysqli_close($dbh);
?>