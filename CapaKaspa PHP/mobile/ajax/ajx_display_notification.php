<?
/*
 * Display comments for an entity
 * 
 */
session_start();
// Parameters
if (!isset($_CONFIG))
	require '../../include/config.php';

require '../../include/constants.php';
require '../../dac/dac_activity.php';
require '../../dac/dac_players.php';
require '../../bwc/bwc_common.php';
require '../../bwc/bwc_players.php';

// Connect DB
require '../../include/connectdb.php';

require '../../include/localization.php';

$start = $_GET["start"];
$playerID = $_GET["player"];
$limit = 5;

$fmt = new IntlDateFormatter(getenv("LC_ALL"), IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);
$tmpNotifications = listNotifications($start, $limit, $playerID);
$numNotifications = mysqli_num_rows($tmpNotifications);
$player = getPlayer($_SESSION['playerID']);
$lastDisplayNotif = new DateTime($player['lastDisplayNotif']);

if ($start == 0 && $numNotifications == 0)
{
	echo ("<div style='text-align: center; height:400px;'><br>"._("No notifications to display !")."</div>");
}
else
	while($tmpNotification = mysqli_fetch_array($tmpNotifications, MYSQLI_ASSOC))
	{
		$postDate = new DateTime($tmpNotification['postDate']);
		$strPostDate = $fmt->format($postDate);
		
		$message = "";
		$link = "";
		$picto = "";
		$unread = "";
		$comment = "";
		
		if ($postDate > $lastDisplayNotif)
			$unread = "<span class='newplayer'>"._("Unread")."</span>";
			
		switch($tmpNotification['notifType'])
		{
			case 'like': 
				$message = _("likes");
				$picto = "images/like.gif";
				break;
			case 'comment': 
				$message = _("comments");
				$picto = "images/comment.jpg";
				$comment = " \"".$tmpNotification['message']."...\"";
				break;
		}
		
		$message .= " ";
		
		switch($tmpNotification['type'])
		{
			case 'game': 
				$message .= _("your game");
				$message .= " #".$tmpNotification['entityID'];
				$message .= " ".$comment;
				$link = "onclick='javascript:loadGameActivity(".$tmpNotification['entityID'].")'";
				break;
			case 'activity': 
				$message .= _("your post");
				if ($tmpNotification['subEntityType'] != "")
				{
					$message .= " "._("about")." ";
					switch($tmpNotification['subEntityType'])
					{
						case 'game': 
							$message .= _("the game");
							$message .= " #".$tmpNotification['entityID'];
							break;
						case 'tournament': 
							$message .= _("the tournament");
							$message .= " #".$tmpNotification['entityID'];
							break;
					}
				}
				$message .= " ".$comment;
				$link = "onclick=\"location.href='activity.php?ID=".$tmpNotification['entityID']."'\"";
				break;
			case 'tournament': 
				$message .= _("the tournament");
				$message .= " ".$comment;
				$message .= " #".$tmpNotification['entityID'];
				$link = "onclick=\"location.href='tournament_view.php?ID=".$tmpNotification['entityID']."'\"";
				break;
			case 'comment': 
				$message .= _("your comment");
				if ($tmpNotification['subEntityType'] != "")
				{
					$message .= " "._("on")." ";
					switch($tmpNotification['subEntityType'])
					{
						case 'game': 
							$message .= _("the game");
							$message .= " #".$tmpNotification['entityID'];
							$link = "onclick='javascript:loadGameActivity(".$tmpNotification['subEntityID'].")'";
							break;
						case 'activity': 
							$message .= _("the post");
							$link = "onclick=\"location.href='activity.php?ID=".$tmpNotification['subEntityID']."'\"";
							break;
						case 'tournament': 
							$message .= _("the tournament");
							$message .= " #".$tmpNotification['entityID'];
							$link = "onclick=\"location.href='tournament_view.php?ID=".$tmpNotification['subEntityID']."'\"";
							break;
					}
				}
				
				break;
		}
		
		echo("
				<div class='activity' style='margin-bottom: 0px; margin-top: 0px; padding-top: 5px; font-size: 12px;' id='notif".$tmpNotification['notifType'].$tmpNotification['notifID']."' onmouseover=\"this.style.cursor='pointer';\" ".$link.">
					<div class='leftbar'>
						<img src='".getPicturePathM($tmpNotification['socialNetwork'], $tmpNotification['socialID'])."' width='40' height='40' border='0'/>
					</div>
					<div class='title'>
						<span class='name'>".$tmpNotification['firstName']." ".$tmpNotification['lastName']." (".$tmpNotification['nick'].")</span> ".$message." 
					</div>
					<div class='timedata'>
						<img src='".$picto."'/> <span class='date'>".$strPostDate."</span> ".$unread."
					</div>
				</div>");
	}

if ($numNotifications == $limit)
{
?>
	<div id="notifications<?echo($start + $limit);?>" style="display: none;">
		<img src='images/ajaxloader.gif'/>
		<input type="hidden" id="startPage" value="<?echo($start + $limit);?>"/>
	</div>
<?
}
updatePlayerDisplayNotif($_SESSION['playerID']);
mysqli_close($dbh);
?>