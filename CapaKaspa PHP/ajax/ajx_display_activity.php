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
$numPerPage = 4;

$fmt = new IntlDateFormatter(getenv("LC_ALL"), IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);
$limit = $start + $numPerPage;
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
		$playerSocialNW = $tmpActivity['wSocialNetwork'];
		$playerSocialID = $tmpActivity['wSocialID'];
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
		$playerSocialNW = $tmpActivity['bSocialNetwork'];
		$playerSocialID = $tmpActivity['bSocialID'];
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
					<img src='".getPicturePath($playerSocialNW, $playerSocialID)."' width='40' height='40' border='0'/>
				</div>
				<div class='details'>
					<div class='title'>
						<a href='player_view.php?playerID=".$playerID."'><span class='name'>".$playerFirstName." ".$playerLastName."</span></a> ".$message." <a href='player_view.php?playerID=".$opponentID."'><span class='name'>".$opponentFirstName." ".$opponentLastName."</span></a>
					</div>
					<div class='content'>
						<div class='gameboard'>");
					drawboardGame($tmpActivity['gameID'], $tmpActivity['wPlayerID'], $tmpActivity['bPlayerID'], $tmpActivity['position']);
				echo("</div>
						<div class='gamedetails'>
							<span style='float: left'><img src='pgn4web/".$_SESSION['pref_theme']."/20/wp.png'> ".$tmpActivity['wFirstName']." ".$tmpActivity['wLastName']."<br>".$tmpActivity['wElo']."</span>
							<span style='float: right'><img src='pgn4web/".$_SESSION['pref_theme']."/20/bp.png'> ".$tmpActivity['bFirstName']." ".$tmpActivity['bLastName']."<br>".$tmpActivity['bElo']."</span>
							<br><br><br>
							[".$tmpActivity['eco']."] ".$tmpActivity['ecoName']."
						</div>
					</div>
					<div class='footer'>");?>
					<?if (isset($tmpActivity['likeID'])){?> 
					<span id="like<?echo(ACTIVITY.$tmpActivity['activityID']);?>"><a href="javascript:deleteLike('<?echo(ACTIVITY);?>', <?echo($tmpActivity['activityID']);?>, <?echo($tmpActivity['likeID']);?>);"><?echo _("! I no longer think it's good");?></a></span>
					<?} else {?>
					<span id="like<?echo(ACTIVITY.$tmpActivity['activityID']);?>"><a href="javascript:insertLike('<?echo(ACTIVITY);?>', <?echo($tmpActivity['activityID']);?>);"><?echo _("! I think it's good");?></a></span>
					<?}?>
					- <a href="javascript:displayComment('<?echo(ACTIVITY);?>', <?echo($tmpActivity['activityID']);?>);"><?echo _("Comment");?></a> 
					<? echo("- <span class='date'>".$strPostDate."</span>
					</div>
					<div class='comment' id='comment".$tmpActivity['activityID']."'>
						<img src='images/ajaxloader.gif'/>
					</div>
				</div>
			</div>
	");
}

if ($numActivities == $numPerPage)
{
?>
	<div id="activities<?echo($limit);?>" style="display: none;">
		<img src='images/ajaxloader.gif'/>
		<input type="hidden" id="startPage" value="<?echo($limit);?>"/>
	</div>
<?
}
mysql_close();
?>