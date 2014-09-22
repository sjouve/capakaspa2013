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
$start = $_GET["start"];
$type = $_GET["type"];
$playerID = $_GET["player"];
$limit = 5;

$fmt = new IntlDateFormatter(getenv("LC_ALL"), IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);
$tmpActivities = listActivity($start, $limit, $type, $playerID);
$numActivities = mysqli_num_rows($tmpActivities);

if ($start == 0 && $numActivities == 0)
{
	echo ("<div style='text-align: center; height:400px;'><br>"._("No News to display. Share your activity and follow players !")."</div>");
}
else
	while($tmpActivity = mysqli_fetch_array($tmpActivities, MYSQLI_ASSOC))
	{
		$postDate = new DateTime($tmpActivity['postDate']);
		$strPostDate = $fmt->format($postDate);
		
		if ($tmpActivity['playerID']==$tmpActivity['wPlayerID'])
		{
			$playerID = $tmpActivity['wPlayerID'];
			$playerFirstName = $tmpActivity['wFirstName'];
			$playerLastName = $tmpActivity['wLastName'];
			$playerNick = $tmpActivity['wNick'];
			$playerSocialNW = $tmpActivity['wSocialNetwork'];
			$playerSocialID = $tmpActivity['wSocialID'];
			$opponentID = $tmpActivity['bPlayerID'];
			$opponentFirstName = $tmpActivity['bFirstName'];
			$opponentLastName = $tmpActivity['bLastName'];
			$opponentNick = $tmpActivity['bNick'];
			$playerColor = 'white';
		}
		else
		{
			$playerID = $tmpActivity['bPlayerID'];
			$playerFirstName = $tmpActivity['bFirstName'];
			$playerLastName = $tmpActivity['bLastName'];
			$playerNick = $tmpActivity['bNick'];
			$playerSocialNW = $tmpActivity['bSocialNetwork'];
			$playerSocialID = $tmpActivity['bSocialID'];
			$opponentID = $tmpActivity['wPlayerID'];
			$opponentFirstName = $tmpActivity['wFirstName'];
			$opponentLastName = $tmpActivity['wLastName'];
			$opponentNick = $tmpActivity['wNick'];
			$playerColor = 'black';
		}
		
		$activityType = "";
		switch($tmpActivity['msgType'])
		{
			// Invitation	
			case 'invitation':
				$message = _("invites someone to play a new game");
				$activityType = _("INVITATION");
				break;
					
			case 'withdrawal':
				$message = _("canceled its invitation to play a new game with");		
				$activityType = _("INVITATION");
				break;
					
			case 'accepted':
				$message = _("has accepted invitation. A new game began against");
				$activityType = _("INVITATION");
				break;
				
			case 'declined':
				$message = _("refused invitation to play a new game against");
				$activityType = _("INVITATION");
				break;
	
			// Result
			// won by resignation against
			// lost by resignation against
			// drew (mutual consent) against
			// drew (rules) against
			// won by checkmate against
			// lost by checkmate against
			case 'resignation':
				if ($tmpActivity['message'] == "lost")
					$message = _("lost")." ";
				else
					$message = _("won")." ";
				$message .= _("by resignation against");
				$activityType = _("RESULT");
				break;
					
			case 'draw':
				$message = _("do a draw game by mutual consent");
				$activityType = _("RESULT");
				break;
			
			case 'drawrule':
				$message = _("do a draw game (stalemate, 3 times same position, 50 moves rule) against");
				$activityType = _("RESULT");
				break;
			
			case 'checkmate':
				if ($tmpActivity['message'] == "lost")
					$message = _("lost")." ";
				else
					$message = _("won")." ";
				$message .= _("by checkmate against");
				$activityType = _("RESULT");
				break;
					
			// Move
			case 'move':
				$message = _("played the move")." <span style=\"font-family: 'pgn4web ChessSansPiratf', 'pgn4web Liberation Sans', sans-serif;\">".$tmpActivity['message']."</span>"._(" in the game against");
				$activityType = _("MOVE");
				break;
							
		}
		
		echo("
			<div class='activity' id='activity".$tmpActivity['activityID']."'>
					<div class='leftbar'>
						<img src='".getPicturePath($playerSocialNW, $playerSocialID)."' width='40' height='40' border='0'/>
					</div>
					<div class='details'>
						<div class='title'>
							<a href='player_view.php?playerID=".$playerID."'><span class='name'>".$playerFirstName." ".$playerLastName." (".$playerNick.")</span></a> ".$message." 
							<a href='player_view.php?playerID=".$opponentID."'><span class='name'>".$opponentFirstName." ".$opponentLastName." (".$opponentNick.")</span></a>
						</div>
						<div class='content'>
							<div class='gameboard'>");
								drawboardGame($tmpActivity['gameID'], $tmpActivity['wPlayerID'], $tmpActivity['bPlayerID'], $tmpActivity['position']);
							echo("</div>
							<div class='gamedetails'>
								<span class='activity_type'>".$activityType."</span>");
								echo("<br>
									<span style='float: left'><img src='pgn4web/".$_SESSION['pref_theme']."/20/wp.png'> ".$tmpActivity['wNick']."<br>".$tmpActivity['wElo']."</span>
									<span style='float: right'><img src='pgn4web/".$_SESSION['pref_theme']."/20/bp.png'> ".$tmpActivity['bNick']."<br>".$tmpActivity['bElo']."</span>");
								echo("<br><br>".getStrGameType($tmpActivity['type'], $tmpActivity['flagBishop'], $tmpActivity['flagKnight'], $tmpActivity['flagRook'], $tmpActivity['flagQueen']));
								if ($tmpActivity['type'] == 0)
									echo("<br>[".$tmpActivity['eco']."] ".$tmpActivity['ecoName']);
								
								if ($tmpActivity['gameMessage'] != "playerInvited")
									echo("<br><br><span style='float: right'><input type='button' value='"._("View")."' class='link' onclick='javascript:loadGameActivity(".$tmpActivity['gameID'].")'></span>");
										
							echo("</div>
						</div>
						<div class='footer'>");?>
						<?if (isset($tmpActivity['likeID'])){?> 
						<span id="like<?echo(ACTIVITY.$tmpActivity['activityID']);?>" ><a title="<? echo _("Stop liking this item")?>" href="javascript:deleteLike('<?echo(ACTIVITY);?>', <?echo($tmpActivity['activityID']);?>, <?echo($tmpActivity['likeID']);?>);"><?echo _("! Unlike");?></a></span>
						<?} else {?>
						<span id="like<?echo(ACTIVITY.$tmpActivity['activityID']);?>"><a title="<? echo _("I like this item")?>" href="javascript:insertLike('<?echo(ACTIVITY);?>', <?echo($tmpActivity['activityID']);?>);"><?echo _("! Like");?></a></span>
						<?}?>
						- <a href="javascript:displayComment('<?echo(ACTIVITY);?>', <?echo($tmpActivity['activityID']);?>);"><?echo _("Comment");?></a> 
						<? 
						if ($tmpActivity['nbLike'] > 0 || $tmpActivity['nbComment'] > 0 )
							echo(" - <span onmouseover=\"this.style.cursor='pointer';\" onclick=\"javascript:displayComment('".ACTIVITY."', ".$tmpActivity['activityID'].");\">");
						if ($tmpActivity['nbLike'] > 0) 
							echo("<img src='images/like.gif'>".$tmpActivity['nbLike'])." ";
						if ($tmpActivity['nbComment'] > 0)
							echo("<img src='images/comment.jpg'>".$tmpActivity['nbComment']);
						if ($tmpActivity['nbLike'] > 0 || $tmpActivity['nbComment'] > 0 )
							echo("</span>");
						echo(" - <span class='date'>".$strPostDate."</span>");
						if ($playerID == $_SESSION['playerID']) echo(" - <a title=\""._("Delete this news")."\" href=\"javascript:deleteActivity(".$tmpActivity['activityID'].")\">"._("Delete")."</a>");
						echo("</div>
						<div class='comment' id='comment".$tmpActivity['activityID']."'>
							<img src='images/ajaxloader.gif'/>
						</div>
					</div>
				</div>
		");
	}

if ($numActivities == $limit)
{
?>
	<div id="activities<?echo($start + $limit);?>" style="display: none;">
		<img src='images/ajaxloader.gif'/>
		<input type="hidden" id="startPage" value="<?echo($start + $limit);?>"/>
	</div>
<?
}
mysqli_close($dbh);
?>