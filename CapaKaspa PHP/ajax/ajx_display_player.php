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
$critFavorite = $_GET["cf"];
$critStatus = $_GET["cs"];
$critEloStart = $_GET["ces"];
$critEloEnd = $_GET["cee"];
$critCountry = $_GET["cc"];
$critName = $_GET["cn"];
$limit = 20;

$fmt = new IntlDateFormatter(getenv("LC_ALL"), IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);

$result = searchPlayers("", $start, $limit, $critFavorite, $critStatus, $critEloStart, $critEloEnd, $critCountry, $critName);
$numPlayers = mysql_num_rows($result);
	
while($tmpPlayer = mysql_fetch_array($result, MYSQL_ASSOC))
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
						echo("<img src='images/user_online.gif' style='vertical-align:bottom;' alt='"._("Player online")."'/>");
					if (isNewPlayer($tmpPlayer['creationDate']))
						echo("<img src='images/user_new.gif' style='vertical-align:bottom;'/>");
				echo("</div>
				<div class='content'><span style='float: right'><input type='submit' class='link' value='"._("New game")."'></span>".
					stripslashes($tmpPlayer['situationGeo']).", ".$tmpPlayer['countryName']."
					<br>"._("Elo")." : ".$tmpPlayer['elo']."
					<br><span class='date'>".stripslashes($tmpPlayer['profil'])."</span>
				</div>
				<div class='footer'>
				</div>
			</div>
		</div>
		<input type='hidden' name='opponent' value='".$tmpPlayer['nick']."'>
		</form>
		");
		/*echo ("<tr valign='top'>");
		echo ("<form action='game_new.php' method='post'>");
		echo ("<input type='hidden' name='ToDo' value='InvitePlayer'>");
		echo ("<td>");
		echo ("<input type='hidden' name='opponent' value='".$tmpPlayer['nick']."'><a href='player_view.php?playerID=".$tmpPlayer['playerID']."'>".$tmpPlayer['nick']."</a><br/>");
		if ($tmpPlayer['lastActionTime'])
			echo("<img src='images/user_online.gif'/>");
		if (isNewPlayer($tmpPlayer['creationDate']))
			echo("<img src='images/user_new.gif'/>");
		echo ("</td>");
		echo ("<td>");
		echo ($tmpPlayer['elo']);
		echo ("</td>");
		echo ("<td align='right'>");
		echo (date("Y")-$tmpPlayer['anneeNaissance']);
		echo ("</td>");
		echo ("<td>");
		echo ("<div style='word-wrap: break-word;width: 90px;'>".stripslashes($tmpPlayer['situationGeo'])."</div>");
		echo ("</td>");
		echo ("<td>");
		echo ("<div style='word-wrap: break-word;width: 220px;'>".stripslashes($tmpPlayer['profil'])."</div>");
		echo ("</td>");
		echo ("<td>
				<input type='submit' class='link' value='"._("New game")."'>");
		echo ("</td>");
		echo ("</form>");
		echo ("</tr>");*/
}

if ($numPlayers == $limit)
{
?>
	<div id="players<?echo($start + $limit);?>" style="display: none;">
		<img src='images/ajaxloader.gif'/>
		<input type="hidden" id="startPage" value="<?echo($start + $limit);?>"/>
	</div>
<?
}
mysql_close();
?>