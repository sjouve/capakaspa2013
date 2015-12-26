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

$tournamentID = $type = isset($_POST['tournamentID']) ? $_POST['tournamentID']:Null;
$isLastPlayer = $type = isset($_POST['isLastPlayer']) ? $_POST['isLastPlayer']:Null;
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
$fmt = new IntlDateFormatter(getenv("LC_ALL"), IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);

$titre_page = _("Tournaments list");
$desc_page = _("Participate to a tournament");
require 'include/page_header.php';
?>
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
if (!$res) echo "Bong !!";
?>
<div id="content">
	<div class="contentbody">
		<h2><? echo _("Register for a tournament")?></h2>
		<? 	$tmpTournaments = listTournaments(0, 10, WAITING);
			while($tmpTournament = mysqli_fetch_array($tmpTournaments, MYSQLI_ASSOC))
			{
				$tournamentDate = new DateTime($tmpTournament['creationDate']);
				$strTournamentDate = $fmt->format($tournamentDate);
		?>
  		<div class="blockform">
  		
			<b><? echo $tmpTournament['name']." - ".$strTournamentDate;?></b>
			<p><? echo $tmpTournament['nbPlayers']." "._("players")." - ".$tmpTournament['timeMove']." "._("days per move");?></p>
			<p>
			<? 	$tmpPlayers = listTournamentPlayers($tmpTournament['tournamentID']);
				$nbRegisteredPlayers = mysqli_num_rows($tmpPlayers);
				$isLastPlayer = 0;
				if ($tmpTournament['nbPlayers'] == $nbRegisteredPlayers + 1)
					$isLastPlayer = 1;
					
				$registered = FALSE;
				
				while($tmpPlayer = mysqli_fetch_array($tmpPlayers, MYSQLI_ASSOC))
				{
					echo $tmpPlayer['playerID']." - ".$tmpPlayer['nick']." - ".$tmpPlayer['elo']."<br>";
					if ($_SESSION['playerID'] == $tmpPlayer['playerID']) $registered = TRUE;
				}
			?>
			</p>
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
		<? } ?>
		
		<h2><? echo _("Tournaments in progress")?></h2>
		<? 	$tmpTournaments = listTournaments(0, 10, INPROGRESS);
			while($tmpTournament = mysqli_fetch_array($tmpTournaments, MYSQLI_ASSOC))
			{
				$tournamentDate = new DateTime($tmpTournament['beginDate']);
				$strTournamentDate = $fmt->format($tournamentDate);
		?>
  		<div class="blockform">
  		
			<b><? echo $tmpTournament['name']." - ".$strTournamentDate;?></b>
			<p><? echo $tmpTournament['nbPlayers']." "._("players")." - ".$tmpTournament['timeMove']." "._("days per move");?></p>
			<p>
			<? 	$tmpPlayers = listTournamentPlayers($tmpTournament['tournamentID']);
				while($tmpPlayer = mysqli_fetch_array($tmpPlayers, MYSQLI_ASSOC))
				{
					echo $tmpPlayer['playerID']." - ".$tmpPlayer['nick']." - ".$tmpPlayer['elo']."<br>";
					if ($_SESSION['playerID'] == $tmpPlayer['playerID']) $registered = TRUE;
				}
			?>
			</p>
		</div>
		<? } ?>
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
