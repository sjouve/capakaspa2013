<?
/*
 * Display activity for followings
 * 
 */
session_start();
// Parameters
if (!isset($_CONFIG))
	require '../../include/config.php';

require '../../include/constants.php';
require '../../dac/dac_activity.php';
require '../../dac/dac_players.php';
require '../../dac/dac_games.php';
require '../../bwc/bwc_common.php';
require '../../bwc/bwc_chessutils.php';
require '../../bwc/bwc_games.php';
require '../../bwc/bwc_players.php';

// Connect DB
require '../../include/connectdb.php';

require '../../include/localization.php';

// Load activities from 
$start = $_GET["start"];
$type = $_GET["type"];
$playerID = $_GET["player"];
$activityID = isset($_GET['actvt']) ? $_GET['actvt'] : "";
$limit = 5;

$fmt = new IntlDateFormatter(getenv("LC_ALL"), IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);
$tmpActivities = listActivity($start, $limit, $type, $playerID, $activityID);
$numActivities = mysqli_num_rows($tmpActivities);

if ($start == 0 && $numActivities == 0)
{
	echo ("<p>"._("No News to display. Share your activity and follow players !")."</p>");
}
else
	while($tmpActivity = mysqli_fetch_array($tmpActivities, MYSQLI_ASSOC))
	{
		$postDate = new DateTime($tmpActivity['postDate']);
		$strPostDate = $fmt->format($postDate);
		
		if ($tmpActivity['entityType'] == GAME)
		{
			// Elo
			if ($tmpActivity['gameType'] == 2)
			{
				$wElo = $tmpActivity['wElo960'];
				$bElo = $tmpActivity['bElo960'];
			}
			else
			{
				$wElo = $tmpActivity['wElo'];
				$bElo = $tmpActivity['bElo'];
			}
			
		if ($tmpActivity['playerID']==$tmpActivity['wPlayerID'])
			{
				$playerID = $tmpActivity['playerID'];
				$playerFirstName = $tmpActivity['firstName'];
				$playerLastName = $tmpActivity['lastName'];
				$playerNick = $tmpActivity['nick'];
				$playerSocialNW = $tmpActivity['socialNetwork'];
				$playerSocialID = $tmpActivity['socialID'];
				$opponentID = $tmpActivity['bPlayerID'];
				$opponentFirstName = $tmpActivity['bFirstName'];
				$opponentLastName = $tmpActivity['bLastName'];
				$opponentNick = $tmpActivity['bNick'];
				$playerColor = 'white';
			}
			else
			{
				$playerID = $tmpActivity['playerID'];
				$playerFirstName = $tmpActivity['firstName'];
				$playerLastName = $tmpActivity['lastName'];
				$playerNick = $tmpActivity['nick'];
				$playerSocialNW = $tmpActivity['socialNetwork'];
				$playerSocialID = $tmpActivity['socialID'];
				$opponentID = $tmpActivity['wPlayerID'];
				$opponentFirstName = $tmpActivity['wFirstName'];
				$opponentLastName = $tmpActivity['wLastName'];
				$opponentNick = $tmpActivity['wNick'];
				$playerColor = 'black';
			}
			
			$activityType = "";
			$pictoPath = "";
		switch($tmpActivity['msgType'])
			{
				// Invitation	
				case 'invitation':
					$message = _("invites someone to play a new game");
					$activityType = _("INVITATION");
					$pictoPath .= "images/activity_invitation_waiting.jpg";
					break;
						
				case 'withdrawal':
					$message = _("canceled its invitation to play a new game with");		
					$activityType = _("INVITATION");
					$pictoPath .= "images/activity_invitation_refused.jpg";
					break;
						
				case 'accepted':
					$message = _("has accepted invitation. A new game began against");
					$activityType = _("INVITATION");
					$pictoPath .= "images/activity_invitation_accepted.jpg";
					break;
					
				case 'declined':
					$message = _("refused invitation to play a new game against");
					$activityType = _("INVITATION");
					$pictoPath .= "images/activity_invitation_refused.jpg";
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
					$pictoPath .= "images/activity_result_lost_resignation.jpg";
					break;
				
				case 'time':
					if ($tmpActivity['message'] == "lost")
						$message = _("lost")." ";
					else
						$message = _("won")." ";
					$message .= _("by time expiration against");
					$activityType = _("RESULT");
					$pictoPath .= "images/activity_result_lost_on_time.jpg";
					break;
						
				case 'draw':
					$message = _("do a draw game by mutual consent");
					$activityType = _("RESULT");
					$pictoPath .= "images/activity_result_draw_mutual.jpg";
					break;
				
				case 'drawrule':
					$message = _("do a draw game (stalemate, 3 times same position, 50 moves rule) against");
					$activityType = _("RESULT");
					$pictoPath .= "images/activity_result_draw_mutual.jpg";
					break;
				
				case 'checkmate':
					if ($tmpActivity['message'] == "lost")
						$message = _("lost")." ";
					else
						$message = _("won")." ";
					$message .= _("by checkmate against");
					$activityType = _("RESULT");
					$pictoPath .= "images/activity_result_lost_checkmate.jpg";
					break;
						
				// Move
				case 'move':
					$message = _("played the move")." <span style=\"font-family: 'pgn4web ChessSansPiratf', 'pgn4web Liberation Sans', sans-serif;font-weight: bold;\">".$tmpActivity['message']."</span>"._(" in the game against");
					$activityType = _("MOVE");
					$pictoPath .= "images/activity_move.jpg";
					break;			
			}
		}
		else if($tmpActivity['entityType'] == TOURNAMENT)
		{
			$playerID = $tmpActivity['playerID'];
			$playerFirstName = $tmpActivity['firstName'];
			$playerLastName = $tmpActivity['lastName'];
			$playerNick = $tmpActivity['nick'];
			$playerSocialNW = $tmpActivity['socialNetwork'];
			$playerSocialID = $tmpActivity['socialID'];
				
			switch($tmpActivity['msgType'])
			{
				case 'won':
					$message = _("won the tournament")." #".$tmpActivity['entityID'];
					$content = "<img src='images/activity_tournament_won.jpg' width='100%'/>";
					$link = "onmouseover=\"this.style.cursor='pointer';\" onclick=\"location.href='tournament_view.php?ID=".$tmpActivity['entityID'].";\"";
					break;
				case 'start':
					$message = _("is registered in the tournament")." #".$tmpActivity['entityID'];
					$content = "<img src='images/activity_tournament_registered.jpg' width='100%'/>";
					$link = "onmouseover=\"this.style.cursor='pointer';\" onclick=\"location.href='tournament_view.php?ID=".$tmpActivity['entityID'].";\"";
					break;
			}
		}
		else
		{
			$playerID = $tmpActivity['playerID'];
			$playerFirstName = "";
			$playerLastName = $tmpActivity['lastName'];
			$playerNick = "";
			$playerSocialNW = $tmpActivity['socialNetwork'];
			$playerSocialID = $tmpActivity['socialID'];
				
			switch($tmpActivity['msgType'])
			{
				case 'info':
					$message = _("inform you !");
					$content = $tmpActivity['message'];
					$link = "";
			}
		}
		
		if ($tmpActivity['entityType'] == GAME)
		{
			echo("
				<div class='activity' id='activity".$tmpActivity['activityID']."'>
						
						<div class='details'>
							<div class='title'>
								
								<img src='".getPicturePathM($playerSocialNW, $playerSocialID)."' width='40' height='40' border='0' style='float: left;margin-right: 5px;'/>
								
								<a href='player_view.php?playerID=".$playerID."'><span class='name'>".$playerFirstName." ".$playerLastName." (".$playerNick.")</span></a> ".$message." 
								<a href='player_view.php?playerID=".$opponentID."'><span class='name'>".$opponentFirstName." ".$opponentLastName." (".$opponentNick.")</span></a><br>
								<span class='date'>".$strPostDate."</span>
								
							</div>
							<div class='content'"); 
								if (isset($tmpActivity['gameID']))
									echo("onmouseover=\"this.style.cursor='pointer';\" onclick=\"javascript:loadGameActivity(".$tmpActivity['entityID'].");\"");
									echo("><img src='".$pictoPath."' width='100%'/>
							</div>
							<div class='footer'>");?>
							<?if (isset($tmpActivity['likeID'])){?> 
							<span style="margin-right: 10px;" id="like<?echo(ACTIVITY.$tmpActivity['activityID']);?>" ><a title="<? echo _("Stop liking this item")?>" href="javascript:deleteLike('<?echo(ACTIVITY);?>', <?echo($tmpActivity['activityID']);?>, <?echo($tmpActivity['likeID']);?>);"><?echo _("Unlike");?></a></span>
							<?} else {?>
							<span style="margin-right: 10px;" id="like<?echo(ACTIVITY.$tmpActivity['activityID']);?>"><a title="<? echo _("I like this item")?>" href="javascript:insertLike('<?echo(ACTIVITY);?>', <?echo($tmpActivity['activityID']);?>);"><?echo _("Like");?></a></span>
							<?}?>
							<a style="margin-right: 10px;" href="javascript:displayComment('<?echo(ACTIVITY);?>', <?echo($tmpActivity['activityID']);?>);"><?echo _("Comment");?></a> 
							<? 
							if ($tmpActivity['nbLike'] > 0 || $tmpActivity['nbComment'] > 0 )
								echo("<span style='margin-right: 10px;' onmouseover=\"this.style.cursor='pointer';\" onclick=\"javascript:displayComment('".ACTIVITY."', ".$tmpActivity['activityID'].");\">");
							if ($tmpActivity['nbLike'] > 0) 
								echo("<img src='images/like.gif'>".$tmpActivity['nbLike'])." ";
							if ($tmpActivity['nbComment'] > 0)
								echo("<img src='images/comment.jpg'>".$tmpActivity['nbComment']);
							if ($tmpActivity['nbLike'] > 0 || $tmpActivity['nbComment'] > 0 )
								echo("</span>");
							if ($playerID == $_SESSION['playerID']) echo("<a title=\""._("Delete this news")."\" href=\"javascript:deleteActivity(".$tmpActivity['activityID'].")\">"._("Delete")."</a>");
							echo("</div>
							<div class='comment' id='comment".$tmpActivity['activityID']."'>
								<img src='images/ajaxloader.gif'/>
							</div>
						</div>
					</div>
			");
		}
		else
		{
			echo("
				<div class='activity' id='activity".$tmpActivity['activityID']."'>
					
					<div class='details'>
						<div class='title'>
							<img src='".getPicturePathM($playerSocialNW, $playerSocialID)."' width='40' height='40' border='0' style='float: left;margin-right: 5px;'/>
							");
							if ($playerID > 0)
								echo("<a href='player_view.php?playerID=".$playerID."'><span class='name'>".$playerFirstName." ".$playerLastName." (".$playerNick.")</span></a> ");
							else
								echo("<span class='name'>".$playerLastName." </span> ");
							echo($message."<br>
							<span class='date'>".$strPostDate."</span>
						</div>
					
						<div class='content' style='margin-left: 5px; margin-top: 5px;' ".$link.">
						".$content."
						</div>
						<div class='footer'>");?>
						<?if (isset($tmpActivity['likeID'])){?> 
						<span style="margin-right: 15px;" id="like<?echo(ACTIVITY.$tmpActivity['activityID']);?>" ><a title="<? echo _("Stop liking this item")?>" href="javascript:deleteLike('<?echo(ACTIVITY);?>', <?echo($tmpActivity['activityID']);?>, <?echo($tmpActivity['likeID']);?>);"><?echo _("Unlike");?></a></span>
						<?} else {?>
						<span style="margin-right: 15px;" id="like<?echo(ACTIVITY.$tmpActivity['activityID']);?>"><a title="<? echo _("I like this item")?>" href="javascript:insertLike('<?echo(ACTIVITY);?>', <?echo($tmpActivity['activityID']);?>);"><?echo _("Like");?></a></span>
						<?}?>
						  <a style="margin-right: 15px;" title="<? echo _("Comment this item")?>" href="javascript:displayComment('<?echo(ACTIVITY);?>', <?echo($tmpActivity['activityID']);?>);"><?echo _("Comment");?></a> 
						<? 
						if ($tmpActivity['nbLike'] > 0 || $tmpActivity['nbComment'] > 0 )
							echo("<span style='margin-right: 15px;' onmouseover=\"this.style.cursor='pointer';\" onclick=\"javascript:displayComment('".ACTIVITY."', ".$tmpActivity['activityID'].");\">");
						if ($tmpActivity['nbLike'] > 0) 
							echo("<img src='images/like.gif'><span class='socialcounter'>".$tmpActivity['nbLike'])."</span> ";
						if ($tmpActivity['nbComment'] > 0)
							echo("<img src='images/comment.jpg'><span class='socialcounter'>".$tmpActivity['nbComment']."</span>");
						if ($tmpActivity['nbLike'] > 0 || $tmpActivity['nbComment'] > 0 )
							echo("</span>");
						
						if ($playerID == $_SESSION['playerID']) echo("   <a title=\""._("Delete this news")."\" href=\"javascript:deleteActivity(".$tmpActivity['activityID'].")\">"._("Delete")."</a>");
						echo("</div>
						<div class='comment' id='comment".$tmpActivity['activityID']."'>
							<img src='images/ajaxloader.gif'/>
						</div>
					</div>
				</div>");
		}
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