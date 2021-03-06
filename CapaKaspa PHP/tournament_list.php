<?
session_start();

/* load settings */
if (!isset($_CONFIG))
	require 'include/config.php';

require 'include/constants.php';
require 'dac/dac_players.php';
require 'dac/dac_games.php';
require 'dac/dac_activity.php';
require 'dac/dac_tournament.php';
require 'bwc/bwc_common.php';
require 'bwc/bwc_chessutils.php';
require 'bwc/bwc_board.php';
require 'bwc/bwc_players.php';
require 'bwc/bwc_games.php';
require 'bwc/bwc_tournament.php';

/* connect to the database */
require 'include/connectdb.php';

$tournamentID = isset($_POST['tournamentID']) ? $_POST['tournamentID']:Null;
$isLastPlayer = isset($_POST['isLastPlayer']) ? $_POST['isLastPlayer']:Null;
$ToDo = isset($_POST['ToDo']) ? $_POST['ToDo']:(isset($_GET['ToDo']) ? $_GET['ToDo']:Null);
	
switch($ToDo)
{
	case 'Register':
		registerTournamentPlayer($tournamentID, $_SESSION['playerID']);
		break;
		
	case 'UnRegister':
		unregisterTournamentPlayer($tournamentID, $_SESSION['playerID']);
		break;
}
	
/* check session status */
require 'include/sessioncheck.php';

require 'include/localization.php';
$fmt = new IntlDateFormatter(getenv("LC_ALL"), IntlDateFormatter::SHORT, IntlDateFormatter::SHORT);

$titre_page = _("Tournaments list");
$desc_page = _("Participate to a tournament");
require 'include/page_header.php';
?>
<script src="javascript/menu.js" type="text/javascript"></script>
<?
$attribut_body = "onload=\"highlightMenu(16);\"";
require 'include/page_body.php';

?>
<div id="content">
	<div class="contentbody">
		
		<div class="blockform">
			<h3><? echo _("Register for a Round-robin tournament")?></h3>
			<p>
			<img src="images/picto_cup_20.png" width="40" height="40" border="0" style="float: left; margin-right: 10px; margin-top: 5px;">
			<?
			echo _("In the Round-robin tournaments you play classic games against every participant with whites and with blacks. For a tournament with four participants so you'll play 6 games.");
			echo "<br>"._("As a tournament did not start it is possible to withdraw. A tournament starts when the last player registers. So the last player can't withdraw !");
			?>
			</p>
		</div>
			<? 	$tmpTournaments = listTournaments(0, 10, WAITING);
			while($tmpTournament = mysqli_fetch_array($tmpTournaments, MYSQLI_ASSOC))
			{
				$tournamentDate = new DateTime($tmpTournament['creationDate']);
				$strTournamentDate = $fmt->format($tournamentDate);
				$strType = "";
				if ($tmpTournament['type'] == CLASSIC)
					$strType = _("Classic game");
		?>
  		<div class="blockform">
  			<div id="tourninfos">
			<b><? echo _("Tournament")." #".$tmpTournament['tournamentID']." - ".$tmpTournament['name']." - "._("Registration");?></b>
			
			<br><? echo _("Created")." ".$strTournamentDate;?>
			<p><? if ($tmpTournament['playerID'] == $_SESSION['playerID']) echo("<img title='"._("Your are registered")."' src='images/hand.gif'/> ");?><? echo $strType." - ".$tmpTournament['nbPlayers']." "._("players")." - ".$tmpTournament['timeMove']." "._("days per move");
				if ($tmpTournament['eloMin'] > 0) echo " - "."Elo "._("from")." ".$tmpTournament['eloMin']." "._("to")." ".$tmpTournament['eloMax'];
			?></p>
			</div>
			<div class="tabliste">
			<? 	$tmpPlayers = listTournamentPlayers($tmpTournament['tournamentID']);
				$nbRegisteredPlayers = mysqli_num_rows($tmpPlayers);
				$isLastPlayer = 0;
				$registered = FALSE;
				
				if ($nbRegisteredPlayers > 0)
				{
			?>
					<table border="0" width="60%">
		            <tr>
		              <th width="80%"><? echo _("Player")?></th>
		              <th width="20%"><? echo _("Elo")?></th>
		            </tr>
			<?
					if ($tmpTournament['nbPlayers'] == $nbRegisteredPlayers + 1)
						$isLastPlayer = 1;
						
					while($tmpPlayer = mysqli_fetch_array($tmpPlayers, MYSQLI_ASSOC))
					{
						echo "<tr><td><a href='player_view.php?playerID=".$tmpPlayer['playerID']."'>".$tmpPlayer['nick']."</a></td><td align='center'>".$tmpPlayer['elo']."</td></tr>";
						if ($_SESSION['playerID'] == $tmpPlayer['playerID']) $registered = TRUE;
					}
			?>
					</table>
			<?	
				}
			?>
				
			</div>
			
				<? 
				if ($tmpTournament['eloMin'] == 0 || ($tmpTournament['eloMin'] > 0 && $_SESSION['elo'] >= $tmpTournament['eloMin'] && $_SESSION['elo'] <= $tmpTournament['eloMax']))
				{
					if ($registered) {?>
					<form action="tournament_list.php" method="post">
						<input type="hidden" name="tournamentID" value="<?echo $tmpTournament['tournamentID'];?>">
						<input type="submit" value="<?echo _("UnRegister");?>" class="button">
						<input type="hidden" name="ToDo" value="UnRegister">
					</form>
					<? } else {?>
					<form action="tournament_list.php" method="post">
						<input type="hidden" name="tournamentID" value="<?echo $tmpTournament['tournamentID'];?>">
						<input type="hidden" name="isLastPlayer" value="<?echo $isLastPlayer;?>">
						<input type="submit" value="<?echo _("Register");?>" class="button">
						<input type="hidden" name="ToDo" value="Register">
					</form>
					<? }
				} ?>
			
		</div>
		<? }
			
			$tmpTournaments = listTournaments(0, 10, INPROGRESS);
			$nbIPTournaments = mysqli_num_rows($tmpTournaments);
			if ($nbIPTournaments > 0)
			{
		?>
		<br>
		<h3><? echo _("Your in progress tournaments")?></h3>
		
		<?		while($tmpTournament = mysqli_fetch_array($tmpTournaments, MYSQLI_ASSOC))
				{
					$tournamentDate = new DateTime($tmpTournament['beginDate']);
					$strTournamentDate = $fmt->format($tournamentDate);
					$strType = "";
					if ($tmpTournament['type'] == CLASSIC)
						$strType = _("Classic game");
		?>
  		<div class="blockform">
			<b><? echo _("Tournament")." #".$tmpTournament['tournamentID']." - ".$tmpTournament['name']." - "._("In progress");?></b>
			<div style='float:right;'><input type="button" class="link" value="<? echo _("View")?>" onclick="location.href='tournament_view.php?ID=<?echo $tmpTournament['tournamentID'];?>'"></div>
			<br><? echo _("Started")." ".$strTournamentDate;?>
			<p><? if ($tmpTournament['playerID'] == $_SESSION['playerID']) echo("<img title='"._("Your are registered")."' src='images/hand.gif'/> ");?><? echo $strType." - ".$tmpTournament['nbPlayers']." "._("players")." - ".$tmpTournament['timeMove']." "._("days per move");
				if ($tmpTournament['eloMin'] > 0) echo " - "."Elo "._("from")." ".$tmpTournament['eloMin']." "._("to")." ".$tmpTournament['eloMax'];
			?></p>
		</div>
		<? 		} 
			}
			
			$tmpTournaments = listTournaments(0, 10, ENDED);
			$nbEDTournaments = mysqli_num_rows($tmpTournaments);
			if ($nbEDTournaments > 0)
			{
		?>
		<br>
		<h3><? echo _("Your completed tournaments")?></h3>
		
		<?		while($tmpTournament = mysqli_fetch_array($tmpTournaments, MYSQLI_ASSOC))
				{
					$tournamentDate = new DateTime($tmpTournament['beginDate']);
					$strTournamentDate = $fmt->format($tournamentDate);
					$tournamentEndDate = new DateTime($tmpTournament['endDate']);
					$strTournamentEndDate = $fmt->format($tournamentEndDate);
					$strType = "";
					if ($tmpTournament['type'] == CLASSIC)
						$strType = _("Classic game");
		?>
  		<div class="blockform">
			<b><? echo _("Tournament")." #".$tmpTournament['tournamentID']." - ".$tmpTournament['name']." - "._("Completed ");?></b>
			<div style='float:right;'><input type="button" class="link" value="<? echo _("View")?>" onclick="location.href='tournament_view.php?ID=<?echo $tmpTournament['tournamentID'];?>'"></div>
			<br><? echo _("Started")." ".$strTournamentDate." - "._("Completed")." ".$strTournamentEndDate;?>
			<p><? if ($tmpTournament['playerID'] == $_SESSION['playerID']) echo("<img title='"._("Your are registered")."' src='images/hand.gif'/> ");?><? echo $strType." - ".$tmpTournament['nbPlayers']." "._("players")." - ".$tmpTournament['timeMove']." "._("days per move");
				if ($tmpTournament['eloMin'] > 0) echo " - "."Elo "._("from")." ".$tmpTournament['eloMin']." "._("to")." ".$tmpTournament['eloMax'];
			?></p>
			
		</div>
		<? 		} 
			}?>
			
	</div>
</div>
<div id="rightbar">
	<div id="suggestions">
		<? displaySuggestion();?>
	</div>
	<?require 'include/page_footer_right.php';?>
</div>
<?
require 'include/page_footer.php';
mysqli_close($dbh);
?>
