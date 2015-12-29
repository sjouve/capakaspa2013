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
		registerTournamentPlayer($tournamentID, $_SESSION['playerID'], $isLastPlayer);
		break;
		
	case 'UnRegister':
		deleteTournamentPlayer($tournamentID, $_SESSION['playerID']);
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
<script type="text/javascript">
function registerForTournament()
{
	document.registerForm.submit();

}
function unRegisterForTournament()
{
	document.unRegisterForm.submit();

}
</script>
<?
$attribut_body = "onload=\"highlightMenu(16);\"";
require 'include/page_body.php';

$res = createTournamentAuto();

?>
<div id="content">
	<div class="contentbody">
		<h2><? echo _("Register for a Round-robin tournament")?></h2>
		<div class="blockform">
			<?
			echo _("In the Round-robin tournaments you play classic games against every participant with whites and with blacks. For a tournament with four participants so you'll play 6 games.");
			echo "<br>"._("As a tournament did not start it is possible to withdraw. A tournament starts when the last player registers. So the last player can't withdraw !");
			?>
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
			<b><? echo _("Tournament")." #".$tmpTournament['tournamentID']." - ".$tmpTournament['name']." - "._("Registration");?></b>
			<br><? echo _("Created")." ".$strTournamentDate;?>
			<p><? echo $strType." - ".$tmpTournament['nbPlayers']." "._("players")." - ".$tmpTournament['timeMove']." "._("days per move");?></p>
			<div class="tabliste">
			<? 	$tmpPlayers = listTournamentPlayers($tmpTournament['tournamentID']);
				$nbRegisteredPlayers = mysqli_num_rows($tmpPlayers);
				$isLastPlayer = 0;
				$registered = FALSE;
				
				if ($nbRegisteredPlayers > 0)
				{
			?>
					<table border="0" width="50%">
		            <tr>
		              <th width="80%"><? echo _("Player")?></th>
		              <th width="20%"><? echo _("Elo")?></th>
		            </tr>
			<?
					if ($tmpTournament['nbPlayers'] == $nbRegisteredPlayers + 1)
						$isLastPlayer = 1;
						
					while($tmpPlayer = mysqli_fetch_array($tmpPlayers, MYSQLI_ASSOC))
					{
						echo "<tr><td>".$tmpPlayer['nick']."</td><td align='center'>".$tmpPlayer['elo']."</td></tr>";
						if ($_SESSION['playerID'] == $tmpPlayer['playerID']) $registered = TRUE;
					}
			?>
					</table>
			<?
					
				}
			?>
			</div>
			<? if ($registered) {?>
			<form name="unRegisterForm" action="tournament_list.php" method="post">
				<input type="hidden" name="tournamentID" value="<?echo $tmpTournament['tournamentID'];?>">
				<input type="button" value="<?echo _("UnRegister")?>" class="button" onclick="javascript:unRegisterForTournament();">
				<input type="hidden" name="ToDo" value="UnRegister">
			</form>
			<? } else {?>
			<form name="registerForm" action="tournament_list.php" method="post">
				<input type="hidden" name="tournamentID" value="<?echo $tmpTournament['tournamentID'];?>">
				<input type="hidden" name="isLastPlayer" value="<?echo $isLastPlayer;?>">
				<input type="button" value="<?echo _("Register")?>" class="button" onclick="javascript:registerForTournament();">
				<input type="hidden" name="ToDo" value="Register">
			</form>
			<? } ?>
			
		</div>
		<? }
			
			$tmpTournaments = listTournaments(0, 10, INPROGRESS);
			$nbIPTournaments = mysqli_num_rows($tmpTournaments);
			if ($nbIPTournaments > 0)
			{
		?>
		<h2><? echo _("Tournaments in progress")?></h2>
		
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
			<br><? echo _("Started")." ".$strTournamentDate;?>
			<p><? echo $strType." - ".$tmpTournament['nbPlayers']." "._("players")." - ".$tmpTournament['timeMove']." "._("days per move");?></p>
			<input type="button" class="link" value="<? echo _("View")?>" onclick="location.href='tournament_view.php?ID=<?echo $tmpTournament['tournamentID'];?>'">
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
