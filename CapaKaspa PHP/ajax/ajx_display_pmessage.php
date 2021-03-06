<?
/*
 * Display private messages
 * 
 */
session_start();
// Parameters
if (!isset($_CONFIG))
	require '../include/config.php';

require '../dac/dac_activity.php';
require '../dac/dac_players.php';
require '../bwc/bwc_common.php';
require '../bwc/bwc_players.php';

// Connect DB
require '../include/connectdb.php';

require '../include/localization.php';

// Load messages with 
$playerID = $_GET["pID"];
$withPlayerID = $_GET["wID"];
$withPlayer = getPlayer($withPlayerID);
$fmt = new IntlDateFormatter(getenv("LC_ALL"), IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);
$tmpMessages = listPrivateMessageWith($playerID, $withPlayerID);
$numMessages = mysqli_num_rows($tmpMessages);
updateUnreadPrivateMessage($playerID, $withPlayerID);

echo("<h3>
			 <a href='player_view.php?playerID=".$withPlayer['playerID']."'>".getPlayerName(0, $withPlayer['nick'], $withPlayer['firstName'], $withPlayer['lastName'])."</a>
		</h3>");
if ($numMessages > 0)
{

	while($tmpMessage = mysqli_fetch_array($tmpMessages, MYSQLI_ASSOC))
	{
		$sendDate = new DateTime($tmpMessage['sendDate']);
		$strSendDate = $fmt->format($sendDate);
		
		echo("
			<div class='activity'>
					<div class='leftbar'>
						<img src='".getPicturePath($tmpMessage['socialNetwork'], $tmpMessage['socialID'])."' width='40' height='40' border='0'/>
					</div>
					<div class='details' style='width: 85%; padding: 0px;'>
						<div class='title'>
							<a href='player_view.php?playerID=".$tmpMessage['playerID']."'><span class='name'>".getPlayerName(0, $tmpMessage['nick'], $tmpMessage['firstName'], $tmpMessage['lastName'])."</span></a>
							<span style='float: right;' class='date'>".$strSendDate."</span>
						</div>
						<div class='content'>
							".nl2br(stripslashes($tmpMessage['message']))."				
						</div>
					</div>
				</div>
		");
	}
}
else
	echo("<center>"._("No Messages")."</center>");

mysqli_close($dbh);
?>