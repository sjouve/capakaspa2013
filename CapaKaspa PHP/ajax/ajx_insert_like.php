<?
/*
 * Insert a like for an entity
 * 
 */
session_start();
// Parameters
if (!isset($_CONFIG))
	require '../include/config.php';

// Connect DB
require '../include/connectdb.php';
require '../dac/dac_activity.php';

require '../include/localization.php';

// Insert a comment for an entity
$entityType=$_GET["type"];
$entityID=$_GET["id"];

$res = insertLike($_SESSION['playerID'], $entityType, $entityID);
$likeID = mysqli_insert_id($dbh);
if ($res) {
?>
<a style="color: #888888;" title="<? echo _("Stop liking this item")?>" href="javascript:deleteLike('<?echo($entityType);?>', <?echo($entityID);?>, <?echo($likeID);?>);"><?echo _("Unlike");?></a>
<?
}
mysqli_close($dbh);
?>