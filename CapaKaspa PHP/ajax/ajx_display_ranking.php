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
$critOrder = $_GET["od"];
$limit = 20;

$fmt = new IntlDateFormatter(getenv("LC_ALL"), IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);

$result = searchPlayersRanking("rank", $start, $limit, $playerID, $critCountry, $critType, $critOrder);
$numPlayers = mysqli_num_rows($result);
	
while($tmpPlayer = mysqli_fetch_array($result, MYSQLI_ASSOC))
{
	$lastConnection = new DateTime($tmpPlayer['lastConnection']);
	$strLastConnection = $fmt->format($lastConnection);
	if ($critType == 0)
	{
		$player_elo = $tmpPlayer['elo'];
		$rank = $tmpPlayer['rank'];
	}
	else
	{
		$player_elo = $tmpPlayer['elo960'];
		$rank = $tmpPlayer['rank960'];
	}
		
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
					if (strlen(stripslashes($tmpPlayer['situationGeo'])) > 0)
						echo(stripslashes($tmpPlayer['situationGeo']).", ");
						echo($tmpPlayer['countryName']."
					<br><b>"._("Elo")." : ".$player_elo."</b>
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