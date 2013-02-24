<?
/*
 * Display comments for an entity
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

require '../include/localization.php';

// Load comments for an entity
$entityType=$_GET["type"];
$entityID=$_GET["id"];

$fmt = new IntlDateFormatter(getenv("LC_ALL"), IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);

//$nbLike = countLike($entityType, $entityID);
$tmpLikes = listLike($entityType, $entityID);
$nbLikes = mysql_num_rows($tmpLikes);

if ($nbLikes > 0)
{
	echo("<div class='item'>".$nbLikes." "._("person(s) like this").": ");
	while($tmpLike = mysql_fetch_array($tmpLikes, MYSQL_ASSOC))
	{
		echo("<a href='player_view.php?playerID=".$tmpLike['playerID']."'>".$tmpLike['nick']."</a> ");
	}
	echo("</div>");
}

$tmpComments = listEntityComments($entityType, $entityID);

while($tmpComment = mysql_fetch_array($tmpComments, MYSQL_ASSOC))
{
	$postDate = new DateTime($tmpComment['postDate']);
	$strPostDate = $fmt->format($postDate);
	echo("
	<div class='item'>
		<a href='player_view.php?playerID=".$tmpComment['playerID']."'><span class='name'>".$tmpComment['firstName']." ".$tmpComment['lastName']." (".$tmpComment['nick'].")</span></a> ".nl2br(stripslashes($tmpComment['message']))." 
		</br>
		<span class='date'>".$strPostDate."</span> - ");
		if (isset($tmpComment['likeID'])){?>
		<span id="like<?echo(COMMENT.$tmpComment['commentID']);?>"><a title="<? echo _("Stop liking this item")?>" href="javascript:deleteLike('<?echo(COMMENT);?>', <?echo($tmpComment['commentID']);?>, <?echo($tmpComment['likeID']);?>);"><?echo _("! Unlike");?></a></span>
		<?} else {?>
		<span id="like<?echo(COMMENT.$tmpComment['commentID']);?>"><a title="<? echo _("I like this item")?>" href="javascript:insertLike('<?echo(COMMENT);?>', <?echo($tmpComment['commentID']);?>);"><?echo _("! Like");?></a></span>
		<?}
						
		if ($_SESSION['playerID'] == $tmpComment['playerID']) {?> 
		- <a title="<? echo _("Delete this comment")?>" href="javascript:deleteComment('<?echo($entityType);?>',<?echo($entityID);?>,<?echo($tmpComment['commentID']);?>)"><?echo _("Delete")?></a>
		<?}
	
	echo("</div>");
}

echo("
	<div class='item'>
		<textarea id='commenttext".$entityID."' rows='2' placeholder='"._("Write a comment...")."'></textarea>");
?>
		<br><input type="button" name="addComment" id="addComment" class="link" value="<?echo _("Publish");?>" onclick="javascript:insertComment('<?echo($entityType);?>',<?echo($entityID);?>)">
		
	</div>
<?
mysql_close();
?>