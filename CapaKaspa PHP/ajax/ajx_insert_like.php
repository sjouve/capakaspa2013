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
$likeID = mysql_insert_id();
if ($res) {
?>
<a href="javascript:deleteLike('<?echo($entityType);?>', <?echo($entityID);?>, <?echo($likeID);?>);"><?echo _("! I no longer think it's good");?></a>
<?
}
mysql_close();
?>