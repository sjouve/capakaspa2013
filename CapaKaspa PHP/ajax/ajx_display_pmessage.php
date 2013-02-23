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
require '../bwc/bwc_players.php';

// Connect DB
require '../include/connectdb.php';

require '../include/localization.php';

// Load messages with 
$playerID = $_GET["pID"];
$withPlayerID = $_GET["wID"];

$fmt = new IntlDateFormatter(getenv("LC_ALL"), IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);
$tmpMessages = listPrivateMessageWith($playerID, $withPlayerID);
$numMessages = mysql_num_rows($tmpMessages);
updateUnreadPrivateMessage($playerID, $withPlayerID);

while($tmpMessage = mysql_fetch_array($tmpMessages, MYSQL_ASSOC))
{
	$sendDate = new DateTime($tmpMessage['sendDate']);
	$strSendDate = $fmt->format($sendDate);
	
	echo("
		<div class='activity'>
				<div class='leftbar'>
					<img src='".getPicturePath($tmpMessage['socialNetwork'], $tmpMessage['socialID'])."' width='40' height='40' border='0'/>
				</div>
				<div class='details'>
					<div class='title'>
						<a href='player_view.php?playerID=".$tmpMessage['playerID']."'><span class='name'>".$tmpMessage['firstName']." ".$tmpMessage['lastName']."</span></a>
						<span style='float: right;' class='date'>".$strSendDate."</span>
					</div>
					<div class='content'>
						".nl2br(stripslashes($tmpMessage['message']))."				
					</div>
				</div>
			</div>
	");
}

mysql_close();
?>