<?
/*
 * Display activity for followings
 * 
 */
session_start();
// Parameters
if (!isset($_CONFIG))
	require '../include/config.php';

require '../include/constants.php';
require '../dac/dac_activity.php';
require '../dac/dac_players.php';
require '../dac/dac_games.php';
require '../bwc/bwc_common.php';
require '../bwc/bwc_chessutils.php';
require '../bwc/bwc_games.php';
require '../bwc/bwc_players.php';

// Connect DB
require '../include/connectdb.php';

require '../include/localization.php';

// Load activities from 
$start=$_GET["start"];

$fmt = new IntlDateFormatter(getenv("LC_ALL"), IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);
$limit = $start + 19;
$tmpActivities = listActivityFollowing($start, $limit, $_SESSION['playerID']);
$numActivities = mysql_num_rows($tmpActivities);

while($tmpActivity = mysql_fetch_array($tmpActivities, MYSQL_ASSOC))
{
	$postDate = new DateTime($tmpActivity['postDate']);
	$strPostDate = $fmt->format($postDate);
	
	if ($tmpActivity['playerID']==$tmpActivity['wPlayerID'])
	{
		$playerID = $tmpActivity['wPlayerID'];
		$playerFirstName = $tmpActivity['wFirstName'];
		$playerLastName = $tmpActivity['wLastName'];
		$opponentID = $tmpActivity['bPlayerID'];
		$opponentFirstName = $tmpActivity['bFirstName'];
		$opponentLastName = $tmpActivity['bLastName'];
		$playerColor = 'white';
	}
	else
	{
		$playerID = $tmpActivity['bPlayerID'];
		$playerFirstName = $tmpActivity['bFirstName'];
		$playerLastName = $tmpActivity['bLastName'];
		$opponentID = $tmpActivity['wPlayerID'];
		$opponentFirstName = $tmpActivity['wFirstName'];
		$opponentLastName = $tmpActivity['wLastName'];
		$playerColor = 'black';
	}
	
	switch($tmpActivity['msgType'])
	{
			
		case 'invitation':
			$message = _("invites someone to play a new game :");
			break;
				
		case 'withdrawal':
			$message = _("canceled its invitation to play a new game with");
			break;
				
		case 'resignation':
			$message = _("resigned in the game against");
			break;
				
		case 'move':
			$message = _("play the move")." ".$tmpActivity['message']._(" in the game against");
			break;
				
		case 'accepted':
			$message = _("has accepted invitation. A new game began against");
			break;
				
		case 'declined':
			$message = _("refused invitation to play a new game against");
			break;
				
		case 'draw':
			$message = _("accepted draw proposal against");
			break;
	}
	
	echo("
		<div class='activity'>
				<div class='leftbar'>
					<img src='".getPicturePath("FB", "sebastien.jouve.fr")."' width='40' height='40' border='0'/>
				</div>
				<div class='details'>
					<div class='title'>
						<a href='player_view.php?playerID=".$playerID."'><span class='name'>".$playerFirstName." ".$playerLastName."</span></a> ".$message." <a href='player_view.php?playerID=".$opponentID."'><span class='name'>".$opponentFirstName." ".$opponentLastName."</span></a>
					</div>
					<div class='content'>
						");
					drawboardGame($tmpActivity['gameID'], $tmpActivity['wPlayerID'], $tmpActivity['bPlayerID'], $tmpActivity['position']);
				echo("</div>
					<div class='footer'>"._("! Good")."
						 - ");?> <a href="javascript:displayComment('<?echo(ACTIVITY);?>', <?echo($tmpActivity['activityID']);?>);"><?echo _("Comment");?></a> <? echo("- <span class='date'>".$strPostDate."</span>
					</div>
					<div class='comment' id='comment".$tmpActivity['activityID']."'>
						<img src='images/ajaxloader.gif'/>
					</div>
				</div>
			</div>
	");
}

if ($numActivities > 19)
{
?>
	<div id="activities<?echo($limit+1);?>" style="display: none;">
		<img src='images/ajaxloader.gif'/>
		<input type="hidden" id="startPage" value="<?echo($limit+1);?>"/>
	</div>
<?
}
mysql_close();
?>