<?
session_start();

/* load settings */
if (!isset($_CONFIG))
	require '../include/config.php';

require '../include/constants.php';
require '../dac/dac_players.php';
require '../dac/dac_games.php';
require '../dac/dac_activity.php';
require '../dac/dac_tournament.php';
require '../bwc/bwc_common.php';
require '../bwc/bwc_chessutils.php';
require '../bwc/bwc_board.php';
require '../bwc/bwc_players.php';
require '../bwc/bwc_games.php';
require '../bwc/bwc_tournament.php';

/* connect to the database */
require '../include/connectdb.php';

$tournamentID = isset($_GET['ID']) ? $_GET['ID']:Null;
$tournament = getTournament($tournamentID);

/* check session status */
require '../include/sessioncheck.php';

require '../include/localization.php';
$fmt = new IntlDateFormatter(getenv("LC_ALL"), IntlDateFormatter::SHORT, IntlDateFormatter::SHORT);

$titre_page = _("Tournament view");
$desc_page = _("View details of a tournament");
require 'include/page_header.php';
?>
<script src="http://jouerauxechecs.capakaspa.info/javascript/comment.js" type="text/javascript"></script>
<script src="http://jouerauxechecs.capakaspa.info/javascript/like.js" type="text/javascript"></script>
<script type="text/javascript">
function loadGame(gameID)
{
	document.games.gameID.value = gameID;
	document.games.submit();
}
</script>
<?
require 'include/page_body.php';

?>
	<div id="onglet">
			<table width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td><div class="ongletdisable" onclick="location.href='game_in_progress.php'"><a href="game_in_progress.php"><? echo _("Games");?></a></div></td>
				<td><div class="ongletdisable" onclick="location.href='tournament_list.php'"><a href="tournament_list.php"><? echo _("Tournaments");?></a></div></td>
				<td><div class="ongletdisable" onclick="location.href='activity.php'"><a href="activity.php"><? echo _("News");?></a></div></td>
				<td><div class="ongletdisable" onclick="location.href='player_search.php'"><a href="player_search.php"><? echo _("Players");?></a></div></td>	
			</tr>
			</table>
		</div>
		<? 	
			$tournamentCreation = new DateTime($tournament['creationDate']);
			$strTournamentCreation = $fmt->format($tournamentCreation);
			$tournamentDate = new DateTime($tournament['beginDate']);
			$strTournamentDate = $fmt->format($tournamentDate);
			$tournamentEnded = new DateTime($tournament['endDate']);
			$strTournamentEnded = $fmt->format($tournamentEnded);
			
			$strStatus = "";
			if ($tournament['status'] == WAITING)
				$strStatus = _("Registration");
			if ($tournament['status'] == INPROGRESS)
				$strStatus = _("In progress");
			if ($tournament['status'] == ENDED)
				$strStatus = _("Completed ");

			$strType = "";
				if ($tournament['type'] == CLASSIC)
					$strType = _("Classic game");
					
			$tmpPlayers = listTournamentPlayers($tournament['tournamentID']);
			$nbRegisteredPlayers = mysqli_num_rows($tmpPlayers);
		?>
  		<div class="blockform">	
			<h3><? echo _("Tournament")." #".$tournament['tournamentID']." - ".$tournament['name']." - ".$strStatus;?></h3>
			<? if ($tournament['status'] == WAITING) { ?>
			<? echo "Created ".$strTournamentCreation;
			} ?>
			<? if ($tournament['status'] == INPROGRESS) { ?>
			<? echo _("Started")." ".$strTournamentDate;
			} ?>
			<? if ($tournament['status'] == ENDED) { ?>
			<? echo _("Started")." ".$strTournamentDate." - "._("Completed")." ".$strTournamentEnded;
			} ?>
			<p><? echo $strType." - ".$tournament['nbPlayers']." "._("players")." - ".$tournament['timeMove']." "._("days per move");
				if ($tournament['eloMin'] > 0) echo " - "."Elo "._("from")." ".$tournament['eloMin']." "._("to")." ".$tournament['eloMax'];
			?></p>
			<div style="font-size: 11px; border-top-style: solid; border-width: 1px; border-color: #CCCCCC;">
				<?if (isset($tournament['likeID'])){?> 
				<span id="like<?echo(TOURNAMENT.$tournament['tournamentID']);?>" ><a title="<? echo _("Stop liking this item")?>" href="javascript:deleteLike('<?echo(TOURNAMENT);?>', <?echo($tournament['tournamentID']);?>, <?echo($tournament['likeID']);?>);"><?echo _("! Unlike");?></a></span>
				<?} else {?>
				<span id="like<?echo(TOURNAMENT.$tournament['tournamentID']);?>"><a title="<? echo _("I like this item")?>" href="javascript:insertLike('<?echo(TOURNAMENT);?>', <?echo($tournament['tournamentID']);?>);"><?echo _("! Like");?></a></span>
				<?}?>
				- <a href="javascript:displayComment('<?echo(TOURNAMENT);?>', <?echo($tournament['tournamentID']);?>);"><?echo _("Comment");?></a>
				<?php if ($tournament['nbLike'] > 0 || $tournament['nbComment'] > 0 )
							echo(" - <span onmouseover=\"this.style.cursor='pointer';\" onclick=\"javascript:displayComment('".TOURNAMENT."', ".$tournament['tournamentID'].");\">");
						if ($tournament['nbLike'] > 0) 
							echo("<img src='images/like.gif'>".$tournament['nbLike'])." ";
						if ($tournament['nbComment'] > 0)
							echo("<img src='images/comment.jpg'>".$tournament['nbComment']);
						if ($tournament['nbLike'] > 0 || $tournament['nbComment'] > 0 )
							echo("</span>");
				?>
				<div class="comment" id="comment<? echo $tournament['tournamentID'];?>">
					<img src='images/ajaxloader.gif'/>
				</div>
			</div>
		</div>
		<div class="comment" id="comment<? echo $tournament['tournamentID'];?>">
			<img src='images/ajaxloader.gif'/>
		</div>
		<? if ($nbRegisteredPlayers > 0) {
		?>
		<div class="blockform">
			<div class="tabliste">
				<table border="0" width="100%">
	            <tr>
	              <th width="15%">&nbsp</th>
	              <th width="55%"><? echo _("Player")?></th>
	              <th width="15%"><? echo _("Elo")?></th>
	              <th width="15%"><? echo _("Score")?></th>
	            </tr>
	            <? 	
					$ranking = array();
					$nickPlayer = array();
					$eloPlayer = array();
					
					while($tmpPlayer = mysqli_fetch_array($tmpPlayers, MYSQLI_ASSOC))
					{
						$nickPlayer[$tmpPlayer['playerID']] = $tmpPlayer['nick'];
						$eloPlayer[$tmpPlayer['playerID']] = $tmpPlayer['elo'];
						$ranking[$tmpPlayer['playerID']] = 0;
					}
					
					$result = listTournamentGames($tournament['tournamentID']);
					while($tmpGame = mysqli_fetch_array($result, MYSQLI_ASSOC))
					{
						if (($tmpGame['gameMessage'] == "playerResigned") && ($tmpGame['messageFrom'] == "white"))
						{
							$ranking[$tmpGame['blackPlayerID']] ++;
						}
						else if (($tmpGame['gameMessage'] == "playerResigned") && ($tmpGame['messageFrom'] == "black"))
						{
							$ranking[$tmpGame['whitePlayerID']] ++;
						}
						else if (($tmpGame['gameMessage'] == "checkMate") && ($tmpGame['messageFrom'] == "white"))
						{
							$ranking[$tmpGame['whitePlayerID']] ++;
						}
						else if ($tmpGame['gameMessage'] == "checkMate")
						{
							$ranking[$tmpGame['blackPlayerID']] ++;
						}
						else if ($tmpGame['gameMessage'] == "draw")
						{
							$ranking[$tmpGame['blackPlayerID']] += 0.5;
							$ranking[$tmpGame['whitePlayerID']] += 0.5;
						}
					}
					
					arsort($ranking);
					$rank = 0;
					$nbPointPrev = -1;
					foreach ($ranking as $playerID => $nbPoints)
					{
						if ($nbPointPrev != $nbPoints) $rank++;
						echo "<tr>";
						echo "<td align='center'>".$rank."</td><td><a href='player_view.php?playerID=".$playerID."'>".$nickPlayer[$playerID]."</a></td><td align='center'>".$eloPlayer[$playerID]."</td><td align='center'>".$nbPoints."</td>";
						echo "</tr>";
						$nbPointPrev = $nbPoints;
					}
					
				?>
	            </table>
	        </div>
        </div>
        <? }?>
		<form name="games" action="game_board.php" method="post">
        	<?
        	$fmtlist = new IntlDateFormatter(getenv("LC_ALL"), IntlDateFormatter::SHORT, IntlDateFormatter::SHORT);
	
			$result = listTournamentGames($tournament['tournamentID']);
			$numGames = mysqli_num_rows($result);
			echo $numGames." "._("game(s) found");	
			while($tmpGame = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
				echo("<div class='activity' id='game".$tmpGame['gameID']."'>
						<div class='details' style='width:100%; '>
							<div class='content' style='font-size: 11px;'>");
						
						/* White */
						echo("<div style='float: left; padding-left: 5px;'><img style='vertical-align: middle' src='pgn4web/".$_SESSION['pref_theme']."/20/wp.png'><a href='player_view.php?playerID=".$tmpGame['whitePlayerID']."'><b>".$tmpGame['whiteNick']."</b></a></div> ");
					
						/* Black */
						echo("<div style='float: right; padding-right: 5px;'><img style='vertical-align: middle' src='pgn4web/".$_SESSION['pref_theme']."/20/bp.png'><a href='player_view.php?playerID=".$tmpGame['blackPlayerID']."'><b>".$tmpGame['blackNick']."</b></a></div> ");
						
						/* View button */
						echo("<div style='margin: auto; width: 15%;'><center><input type='button' value='"._("View")."' class='link' onclick='javascript:loadGame(".$tmpGame['gameID'].")'><center></div>");
						
						/* Type */
						echo("<div style='width: 100%;'><center>".getStrGameType($tmpGame['type'], $tmpGame['flagBishop'], $tmpGame['flagKnight'], $tmpGame['flagRook'], $tmpGame['flagQueen']));
						
						echo(" (<b>");
						
						/* Status */
						if (is_null($tmpGame['gameMessage']))
							echo("...");
						else
						{
							if (($tmpGame['gameMessage'] == "playerResigned") && ($tmpGame['messageFrom'] == "white"))
							{
								echo("<a href='javascript:loadGame(".$tmpGame['gameID'].")'>0-1</a>");
							}
							else if (($tmpGame['gameMessage'] == "playerResigned") && ($tmpGame['messageFrom'] == "black"))
							{
								echo("<a href='javascript:loadGame(".$tmpGame['gameID'].")'>1-0</a>");
							}
							else if (($tmpGame['gameMessage'] == "checkMate") && ($tmpGame['messageFrom'] == "white"))
							{
								echo("<a href='javascript:loadGame(".$tmpGame['gameID'].")'>1-0</a>");
							}
							else if ($tmpGame['gameMessage'] == "checkMate")
							{
								echo("<a href='javascript:loadGame(".$tmpGame['gameID'].")'>0-1</a>");
							}
							else if ($tmpGame['gameMessage'] == "draw")
							{
								echo("<a href='javascript:loadGame(".$tmpGame['gameID'].")'>1/2-1/2</a>");
							}
							else
								echo("...");
						}
						
						echo("</b>)</center></div>");
						
						/* ECO Code */
						if ($tmpGame['type'] == 0 && $tmpGame['eco']!="")
							echo ("<div style='width: 100%'><center>[".$tmpGame['eco']."] ".$tmpGame['ecoName']."</center></div>");
						
						$started = new DateTime($tmpGame['dateCreated']);
						$strStarted = $fmtlist->format($started);
						$lastMove = new DateTime($tmpGame['lastMove']);
						$strLastMove = $fmtlist->format($lastMove);
						
						/* Start Date */
						echo ("<span style='float: left; font-size: 11px; padding-left: 5px;'>"._("Started")." : "
							.$strStarted."</span>");
			
						/* Last Move */
						echo ("<span style='float: right; font-size: 11px; padding-right: 5px;'>"._("Last move")." : ".$strLastMove.("</span></div>"));
						
				echo ("</div></div>");
			}
			?>
        	<input type="hidden" name="gameID" value="">
        	<input type="hidden" name="from" value="tournament">
      	</form>
		
<?
require 'include/page_footer.php';
mysqli_close($dbh);
?>
