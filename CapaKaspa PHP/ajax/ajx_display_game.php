<?
/*
 * Display list of games
 * 
 */
session_start();
// Parameters
if (!isset($_CONFIG))
	require '../include/config.php';

require '../dac/dac_games.php';
require '../bwc/bwc_games.php';
require '../bwc/bwc_common.php';

// Connect DB
require '../include/connectdb.php';

require '../include/localization.php';

// Load activities from 
$start = $_GET["start"];
$playerID = $_GET["player"];
$critState = $_GET["cs"];
$critColor = $_GET["cc"];
$critResult = $_GET["cr"];
$critType = $_GET["ct"];
$critRank = $_GET["ck"];
$critElo = $_GET["ce"];
$limit = 20;

$fmt = new IntlDateFormatter(getenv("LC_ALL"), IntlDateFormatter::SHORT, IntlDateFormatter::SHORT);

$result = searchGames("", $start, $limit, $critState, $playerID, $critColor, $critResult, $critType, $critRank, $critElo);
$numGames = mysqli_num_rows($result);
	
while($tmpGame = mysqli_fetch_array($result, MYSQLI_ASSOC))
{
	$whiteElo = "";
	$blackElo = "";
	if ($tmpGame['type'] == 0) 
	{
		$whiteElo = "(".$tmpGame['whiteElo'].")";
		$blackElo = "(".$tmpGame['blackElo'].")";
	}
	if ($tmpGame['type'] == 2) 
	{
		$whiteElo = "(".$tmpGame['whiteElo960'].")";
		$blackElo = "(".$tmpGame['blackElo960'].")";
	}
	
	echo("<div class='activity' id='game".$tmpGame['gameID']."'>
			<div class='details' style='width:100%;'>
				<div class='content' style='font-size: 11px; padding-left: 5px;'>");
			
			/* White */
			echo("<div style='float:left; width: 250px; height: 25px;'><img style='vertical-align: middle' src='pgn4web/".$_SESSION['pref_theme']."/20/wp.png'><a href='player_view.php?playerID=".$tmpGame['whitePlayerID']."'><b>".$tmpGame['whiteNick']."</b></a> ".$whiteElo."</div> ");
			
			/* Type */
			echo("<div style='float:left; width: 400px; height: 25px;'>".getStrGameType($tmpGame['type'], $tmpGame['flagBishop'], $tmpGame['flagKnight'], $tmpGame['flagRook'], $tmpGame['flagQueen']));
			
			echo(" (<b>");
			
			/* Status */
			if (is_null($tmpGame['gameMessage']))
				echo("&nbsp;");
			else
			{
				if (($tmpGame['gameMessage'] == "playerResigned") && ($tmpGame['messageFrom'] == "white"))
					echo("<a href='javascript:loadEndedGame(".$tmpGame['gameID'].")'>0-1</a>");
				else if (($tmpGame['gameMessage'] == "playerResigned") && ($tmpGame['messageFrom'] == "black"))
					echo("<a href='javascript:loadEndedGame(".$tmpGame['gameID'].")'>1-0</a>");
				else if (($tmpGame['gameMessage'] == "checkMate") && ($tmpGame['messageFrom'] == "white"))
					echo("<a href='javascript:loadEndedGame(".$tmpGame['gameID'].")'>1-0</a>");
				else if ($tmpGame['gameMessage'] == "checkMate")
					echo("<a href='javascript:loadEndedGame(".$tmpGame['gameID'].")'>0-1</a>");
				else if ($tmpGame['gameMessage'] == "draw")
					echo("<a href='javascript:loadEndedGame(".$tmpGame['gameID'].")'>1/2-1/2</a>");
				else
					echo("&nbsp;");
			}
			
			echo("</b>)");
			
			if ($tmpGame['tournamentID'] != "")
									echo(" - <a href='tournament_view.php?ID=".$tmpGame['tournamentID']."'>"._("Tournament")." #".$tmpGame['tournamentID']."</a>");
								
			echo ("</div>");
			
			echo("<div style='float:right; height: 25px;padding-right: 10px;'><input type='button' value='"._("View")."' class='link' onclick='javascript:loadEndedGame(".$tmpGame['gameID'].")'></div>");
			
			/* Black */
			echo("<div style='float:left; width: 250px; height: 25px;'><img style='vertical-align: middle' src='pgn4web/".$_SESSION['pref_theme']."/20/bp.png'><a href='player_view.php?playerID=".$tmpGame['blackPlayerID']."'><b>".$tmpGame['blackNick']."</b></a> ".$blackElo."</div> ");
			
			/* ECO Code */
			if ($tmpGame['type'] == 0)
				echo ("<div style='float:left; width: 400px; height: 25px;'>[".$tmpGame['eco']."] ".$tmpGame['ecoName']."</div> ");
			
			echo("<div style='float:right; height: 25px;padding-right: 10px;'><input type='button' class='link' value='"._("Download PGN")."' onclick=\"location.href='game_pgn.php?id=".$tmpGame['gameID']."'\"></div>");
			
			$started = new DateTime($tmpGame['dateCreated']);
			$strStarted = $fmt->format($started);
			$lastMove = new DateTime($tmpGame['lastMove']);
			$strLastMove = $fmt->format($lastMove);
			
			/* Start Date */
			echo ("</div><div class='footer' style='padding-left: 5px;'>".("<span style='float: left;width: 250px;'>")._("Started")." : "
				.$strStarted."</span>");

			/* Last Move */
			echo ("<span style='float: left;width: 250px;'>")._("Last move")." : ".$strLastMove.("</span>");
			
	echo ("</div></div></div>");
}

if ($numGames == $limit)
{
?>
	<div id="games<?echo($start + $limit);?>" style="display: none;">
		<img src='images/ajaxloader.gif'/>
		<input type="hidden" id="gamesStartPage" value="<?echo($start + $limit);?>"/>
	</div>
<?
}
mysqli_close($dbh);
?>