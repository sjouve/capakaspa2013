<?
/*
 * Delete a like for an entity
 * 
 */

// Parameters
if (!isset($_CONFIG))
	require '../include/config.php';

// Connect DB
require '../include/connectdb.php';
require '../dac/dac_activity.php';

require '../include/localization.php';

// Get like Id
$likeID = $_GET["id"];
$type = $_GET["type"];
$entityID = $_GET["entityid"];

$res = deleteLike($likeID);
if ($res) {
?>
<a href="javascript:insertLike('<?echo($type);?>', <?echo($entityID);?>);"><?echo _("! Like");?></a>
<?	
}
mysql_close();
?>