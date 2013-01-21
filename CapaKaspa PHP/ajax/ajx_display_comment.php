<?
/*
 * Display comments for an entity
 * 
 */
session_start();
// Parameters
if (!isset($_CONFIG))
	require '../include/config.php';
require '../include/localization.php';

// Connect DB
require '../include/connectdb.php';
require '../dac/dac_activity.php';

// Load comments for an entity
$entityType=$_GET["type"];
$entityID=$_GET["id"];

$fmt = new IntlDateFormatter(getenv("LC_ALL"), IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);

$tmpComments = listEntityComments($entityType, $entityID);

while($tmpComment = mysql_fetch_array($tmpComments, MYSQL_ASSOC))
{
	$postDate = new DateTime($tmpComment['postDate']);
	$strPostDate = $fmt->format($postDate);
	echo("
	<div class='item'>
		<a href='player_view.php?playerID=".$tmpComment['playerID']."'><span class='name'>".$tmpComment['firstName']." ".$tmpComment['lastName']."</span></a> ".$tmpComment['message']." 
		</br>
		<span class='date'>".$strPostDate."</span>");

		if ($_SESSION['playerID'] == $tmpComment['playerID']) {?> 
		- <a href="javascript:deleteComment('<?echo($entityType);?>',<?echo($entityID);?>,<?echo($tmpComment['commentID']);?>)"><?echo _("Delete")?></a>
	<?}
	
	echo("</div>");
}

echo("
	<div class='item'>
		<textarea id='commenttext".$entityID."' rows='2' cols='95' placeholder='"._("Write a comment...")."'></textarea>");
?>
		<a href="javascript:insertComment('<?echo($entityType);?>',<?echo($entityID);?>)"><?php echo _("Add")?></a>
	</div>