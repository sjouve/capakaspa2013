<?
session_start();

/* load settings */
if (!isset($_CONFIG))
	require 'include/config.php';

/* load external functions for setting up new game */
require 'include/constants.php';
require 'dac/dac_players.php';
require 'dac/dac_activity.php';
require 'dac/dac_games.php';
require 'bwc/bwc_common.php';
require 'bwc/bwc_chessutils.php';
require 'bwc/bwc_games.php';
require 'bwc/bwc_players.php';

/* connect to database */
require 'include/connectdb.php';

$errMsg = "";

/* check session status */
require 'include/sessioncheck.php';

require 'include/localization.php';

// Traitement des critères
$critState = isset($_POST['critState']) ? $_POST['critState'] : "E";
$critColor = isset($_POST['critColor']) ? $_POST['critColor'] : "";
$critResult = isset($_POST['critResult']) ? $_POST['critResult'] : (isset($_GET['critResult']) ? $_GET['critResult'] : "");
$critType = isset($_POST['critType']) ? $_POST['critType'] : (isset($_GET['critType']) ? $_GET['critType'] : "0");
$critRank = isset($_POST['critRank']) ? $_POST['critRank'] : "0";
$critElo = isset($_POST['critElo']) ? $_POST['critElo'] : "";

$fmt = new IntlDateFormatter(getenv("LC_ALL"), IntlDateFormatter::SHORT, IntlDateFormatter::SHORT);

/* Id du joueur */
$playerID = isset($_GET['playerID']) ? $_GET['playerID'] : (isset($_POST['playerID']) ? $_POST['playerID'] : $_SESSION['playerID']);
// Si le joueur n'est pas celui connecté on récupère ses infos
if (isset($_GET['playerID']) || isset($_POST['playerID']))
    $player = getPlayer($playerID);

$dateDeb = date("Y-m-d", mktime(0,0,0, 1, 1, 1990));
$dateFin = date("Y-m-d", mktime(0,0,0, 12, 31, 2020));
$countLost = countLost($playerID, $dateDeb, $dateFin, CLASSIC);
$nbDefaites = $countLost['nbGames'];
$countDraw = countDraw($playerID, $dateDeb, $dateFin, CLASSIC);
$nbNulles = $countDraw['nbGames'];
$countWin = countWin($playerID, $dateDeb, $dateFin, CLASSIC);
$nbVictoires = $countWin['nbGames'];
$nbPartiesClassic = $nbDefaites + $nbNulles + $nbVictoires;

$countLost = countLost($playerID, $dateDeb, $dateFin, CHESS960);
$nbDefaites = $countLost['nbGames'];
$countDraw = countDraw($playerID, $dateDeb, $dateFin, CHESS960);
$nbNulles = $countDraw['nbGames'];
$countWin = countWin($playerID, $dateDeb, $dateFin, CHESS960);
$nbVictoires = $countWin['nbGames'];
$nbParties960 = $nbDefaites + $nbNulles + $nbVictoires;

$titre_page = _("Ended games");
$desc_page = _("All ended chess games of a player");
require 'include/page_header.php';
?>
<script src="javascript/menu.js" type="text/javascript"></script>
<script src="javascript/game.js" type="text/javascript"></script>
<script type="text/javascript">
function loadEndedGame(gameID)
{
	document.endedGames.gameID.value = gameID;
	document.endedGames.submit();
}

function getheight() {
	var myWidth = 0,
		myHeight = 0;
	if (typeof(window.innerWidth) == 'number') {
		//Non-IE
		myWidth = window.innerWidth;
		myHeight = window.innerHeight;
	} else if (document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)) {
		//IE 6+ in 'standards compliant mode'
		myWidth = document.documentElement.clientWidth;
		myHeight = document.documentElement.clientHeight;
	} else if (document.body && (document.body.clientWidth || document.body.clientHeight)) {
		//IE 4 compatible
		myWidth = document.body.clientWidth;
		myHeight = document.body.clientHeight;
	}
		var scrolledtonum = window.pageYOffset + myHeight + 2;
		var heightofbody = document.body.offsetHeight;
		if (scrolledtonum >= heightofbody && document.getElementById("gamesStartPage")) {
			displayGames(document.getElementById("gamesStartPage").value,<? echo($playerID);?>,'<? echo($critState);?>','<? echo($critColor);?>','<? echo($critResult);?>','<? echo($critType);?>','<? echo($critRank);?>','<? echo($critElo);?>');
	}
}

window.onscroll = getheight;
</script>
<?
if ($playerID == $_SESSION['playerID'])	
	$attribut_body = "onload=\"highlightMenu(3);displayGames(0,".$playerID.",'".$critState."','".$critColor."','".$critResult."','".$critType."','".$critRank."','".$critElo."')\"";
else
	$attribut_body = "onload=\"displayGames(0,".$playerID.",'".$critState."','".$critColor."','".$critResult."','".$critType."','".$critRank."','".$critElo."')\"";
require 'include/page_body.php';
?>
  <div id="contentlarge">
    <div class="contentbody">
	  <?
		if ($errMsg != "")
			echo("<div class='error'>".$errMsg."</div>");
		
		if ($nbPartiesClassic > 0 || $nbParties960 > 0)
		{
		?>
		<h3><?echo _("Statistics");?></h3>
		<div id="games_statistics">
			<?php if ($nbPartiesClassic > 0) {?>
			<img id="graphcountclassic" style="border: 1px solid; border-color: #e9eaed #dfe0e4 #d0d1d5;"
				src="graph_results_perc.php?playerID=<?php echo($playerID);?>&type=<?php echo(CLASSIC);?>">
			<? }
			if ($nbParties960 > 0) {?>
			<img style="border: 1px solid; border-color: #e9eaed #dfe0e4 #d0d1d5; float: right;" 
				src="graph_results_perc.php?playerID=<?php echo($playerID);?>&type=<?php echo(CHESS960);?>">
			<? }
			if ($nbPartiesClassic > 0) {?>
			<img id="graphecoclassic" style="border: 1px solid; border-color: #e9eaed #dfe0e4 #d0d1d5;" 
				src="graph_eco_games.php?playerID=<?php echo($playerID);?>">
			<? }?>
      	</div>
    <? }?>
	<?
		$nb_tot=0;
		$res_count = searchGames("count", 0, 0, $critState, $playerID, $critColor, $critResult, $critType, $critRank, $critElo); 
		if ($res_count)
		{
			$count = mysqli_fetch_array($res_count, MYSQLI_ASSOC);
			$nb_tot = $count['nbGames'];
		}
	?>
	<div class="blockform">
		<h3><? echo _("Ended games search");?></h3>
		<div id="searchForm">
			<form name="searchGames" action="game_list_ended.php" method="post">
				<table border="0" width="100%">
		        	<? if ($playerID != $_SESSION['playerID']) {?>
		        	<tr>
			            <td><? echo _("Player");?> :</td>
			            <td colspan="3"><? echo $player['firstName']." ".$player['lastName']." (".$player['nick'].")";?><input type="hidden" name="playerID" value="<? echo $playerID;?>"></td>
		          	</tr>
		          	<?}?>
		          	<tr>
			            <td colspan="2"><? echo _("Type");?> :</td>
			            <td>
			              <input name="critType" type="radio" value="0" <?if ($critType=="0") echo('checked');?>>
			              <? echo _("Classic game");?>
			            </td>
			            <td>
			              <input name="critType" type="radio" value="2" <?if ($critType=="2") echo('checked');?>>
			               <? echo _("Chess960 game");?>
			            </td>
			            <td> 
			              <input name="critType" type="radio" value="1" <?if ($critType=="1") echo('checked');?>>
			               <? echo _("Beginner game");?>
			            </td>
			            
		          	</tr>
		          	<tr>
			            <td><? echo _("Color");?> :</td>
			            <td>
			              <input name="critColor" type="radio" value="" <?if ($critColor=="") echo('checked');?>>
			              <? echo _("All");?>
			            </td>
			            <td> 
			              <input name="critColor" type="radio" value="W" <?if ($critColor=="W") echo('checked');?>>
			               <? echo _("Whites");?>
			            </td>
			            <td colspan="2">
			              <input name="critColor" type="radio" value="B" <?if ($critColor=="B") echo('checked');?>>
			               <? echo _("Blacks");?>
			            </td>
		          	</tr>
		          	<tr>
			            <td><? echo _("Result");?> :</td>
			            <td>
			              <input name="critResult" type="radio" value="" <?if ($critResult=="") echo('checked');?>>
			              <? echo _("All");?>
			            </td>
			            <td>
			              <input name="critResult" type="radio" value="W" <?if ($critResult=="W") echo('checked');?>>
			              <? echo _("Won");?>
			            </td>
			            <td> 
			              <input name="critResult" type="radio" value="L" <?if ($critResult=="L") echo('checked');?>>
			               <? echo _("Lost");?>
			            </td>
			            <td>
			              <input name="critResult" type="radio" value="D" <?if ($critResult=="D") echo('checked');?>>
			               <? echo _("Draw");?>
			            </td>
		          	</tr>
		          	<? if ($playerID == $_SESSION['playerID']) {?>
		          	<tr>
			            <td><? echo _("Ranking");?> :</td>
			            <td>
			              <input name="critRank" type="radio" value="0" <?if ($critRank=="0") echo('checked');?>>
			              <? echo _("All");?>
			            </td>
			            <td colspan="3"> 
			              <input name="critRank" type="radio" value="1" <?if ($critRank=="1") echo('checked');?>>
			               <? echo _("To take in account for next Elo ranking");?>
			            </td>
		          	</tr>
		          	<? } ?>
		          	<tr>
			            <td><? echo _("ECO");?> :</td>
			            <td colspan="4">
			              <input name="critElo" type="text" size="3" maxlength="3" value="<? echo($critElo); ?>">
			            </td>
		          	</tr>
		          	<tr>
			            <td colspan="4"><? echo _("Sorted by last move");?></td>
			            <td align="right">
			            	<input type="submit" name="Filter" value="<? echo _("Filter");?>" class="button">	
			            </td>
		          	</tr>	   	          
		        </table>
			</form>
			</div>  	        	
        </div>
       	<form name="endedGames" action="game_board.php" method="post">
       		<? echo($nb_tot." "._("game(s) found"));?>
        	<div id="games0" style="display: none;"><img src='images/ajaxloader.gif'/></div>
        	
        	<input type="hidden" name="gameID" value="">
        	<input type="hidden" name="from" value="archive">
      	</form>
    </div>
</div>
<?
require 'include/page_footer.php';
mysqli_close($dbh);
?>
