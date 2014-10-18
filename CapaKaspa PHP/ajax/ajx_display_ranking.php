<?
/*
 * Display ranking
 * 
 */
session_start();
// Parameters
if (!isset($_CONFIG))
	require '../include/config.php';

require '../dac/dac_players.php';
require '../bwc/bwc_common.php';
require '../bwc/bwc_players.php';

// Connect DB
require '../include/connectdb.php';

require '../include/localization.php';

// Load activities from 
$start = $_GET["start"];
$playerID = $_GET["player"];
$critCountry = $_GET["cc"];
$critType = $_GET["tp"];
$limit = 20;
$rank = $start;
$previousElo = "";
$currentElo = "";

$fmt = new IntlDateFormatter(getenv("LC_ALL"), IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);

$result = searchPlayersRanking("", $start, $limit, $playerID, $critCountry, $critType);
$numPlayers = mysqli_num_rows($result);
	
while($tmpPlayer = mysqli_fetch_array($result, MYSQLI_ASSOC))
{
	$lastConnection = new DateTime($tmpPlayer['lastConnection']);
	$strLastConnection = $fmt->format($lastConnection);
	
	$currentElo = $tmpPlayer['elo'];
	if ($currentElo != $previousElo)
		$rank = $rank + 1;
	$previousElo = $tmpPlayer['elo'];
	
	echo("
		<form action='game_new.php' method='post'>
		<div class='player'>
			<div class='leftbar'>
				<img src='".getPicturePath($tmpPlayer['socialNetwork'], $tmpPlayer['socialID'])."' width='40' height='40' border='0'/> 
			</div>
			<div class='details'>
				<div class='title'>
					<a href='player_view.php?playerID=".$tmpPlayer['playerID']."'><span class='name'>[".$rank."] ".$tmpPlayer['firstName']." ".$tmpPlayer['lastName']." (".$tmpPlayer['nick'].")</span></a>");  
					if (isNewPlayer($tmpPlayer['creationDate']))
						echo(" <span class='newplayer'>"._("New player")."</span>");
				echo("</div>
				<div class='content'>");
					
					if ($tmpPlayer['playerID'] != $_SESSION['playerID'])
						echo("<span style='float: right'><input type='submit' class='link' value='"._("New game")."'></span>");
					echo(stripslashes($tmpPlayer['situationGeo']).", ".$tmpPlayer['countryName']."
					<br><b>"._("Elo")." : ".$tmpPlayer['elo']."</b>
					<br><span class='date'>".nl2br(stripslashes($tmpPlayer['profil']))."</span>
				</div>
				<div class='footer'>
				</div>
			</div>
		</div>
		<input type='hidden' name='opponent' value='".$tmpPlayer['nick']."'>
		</form>
		");
}

if ($numPlayers == $limit)
{
?>
	<div id="players<?echo($start + $limit);?>" style="display: none;">
		<img src='images/ajaxloader.gif'/>
		<input type="hidden" id="playerStartPage" value="<?echo($start + $limit);?>"/>
	</div>
<?
}
mysqli_close($dbh);
?>