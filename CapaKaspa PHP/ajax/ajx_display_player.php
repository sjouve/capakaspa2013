<?
/*
 * Display list of players
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
$critFavorite = $_GET["cf"];
$critStatus = $_GET["cs"];
$critEloStart = $_GET["ces"];
$critEloEnd = $_GET["cee"];
$critCountry = $_GET["cc"];
$critName = $_GET["cn"];
$limit = 20;

$fmt = new IntlDateFormatter(getenv("LC_ALL"), IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);

$result = searchPlayers("", $start, $limit, $playerID, $critFavorite, $critStatus, $critEloStart, $critEloEnd, $critCountry, $critName);
$numPlayers = mysqli_num_rows($result);
	
while($tmpPlayer = mysqli_fetch_array($result, MYSQLI_ASSOC))
{
	$lastConnection = new DateTime($tmpPlayer['lastConnection']);
	$strLastConnection = $fmt->format($lastConnection);
	
	echo("
		<form action='game_new.php' method='post'>
		<div class='player'>
			<div class='leftbar'>
				<img src='".getPicturePath($tmpPlayer['socialNetwork'], $tmpPlayer['socialID'])."' width='40' height='40' border='0'/> 
			</div>
			<div class='details'>
				<div class='title'>
					<a href='player_view.php?playerID=".$tmpPlayer['playerID']."'><span class='name'>".$tmpPlayer['firstName']." ".$tmpPlayer['lastName']." (".$tmpPlayer['nick'].")</span></a>");  
					if ($tmpPlayer['lastActionTime'])
						echo("<img src='images/user_online.gif' style='vertical-align:bottom;' title='"._("Player online")."' alt='"._("Player online")."'/>");
					if (isNewPlayer($tmpPlayer['creationDate']))
						echo(" <span class='newplayer'>"._("New player")."</span>");
				echo("</div>
				<div class='content'>");
					
					if ($tmpPlayer['playerID'] != $_SESSION['playerID'])
						echo("<span style='float: right'><input type='submit' class='link' value='"._("New game")."'></span>");
					if (strlen(stripslashes($tmpPlayer['situationGeo'])) > 0)
						echo(stripslashes($tmpPlayer['situationGeo']).", ");
					echo($tmpPlayer['countryName']."
					<br>"._("Elo")." : ".$tmpPlayer['elo']." - "._("Chess960")." : ".$tmpPlayer['elo960']."
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