<?
/*
 * Display private messages
 * 
 */
session_start();
// Parameters
if (!isset($_CONFIG))
	require '../../include/config.php';

require '../../dac/dac_activity.php';
require '../../dac/dac_players.php';
require '../../bwc/bwc_common.php';
require '../../bwc/bwc_players.php';

// Connect DB
require '../../include/connectdb.php';

require '../../include/localization.php';

// Load messages with 
$playerID = $_GET["pID"];
$withPlayerID = $_GET["wID"];
$withPlayer = getPlayer($withPlayerID);
$fmt = new IntlDateFormatter(getenv("LC_ALL"), IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);
$tmpMessages = listPrivateMessageWith($playerID, $withPlayerID);
$numMessages = mysqli_num_rows($tmpMessages);
updateUnreadPrivateMessage($playerID, $withPlayerID);


echo("<h3>
			<input type=\"button\" class=\"link\" value=\"  <  \" onclick=\"location.href='message.php'\">
			  <a href='player_view.php?playerID=".$withPlayer['playerID']."'>".$withPlayer['firstName']." ".$withPlayer['lastName']."</a>
		</h3>");
if ($numMessages > 0)
{
	
	while($tmpMessage = mysqli_fetch_array($tmpMessages, MYSQLI_ASSOC))
	{
		$sendDate = new DateTime($tmpMessage['sendDate']);
		$strSendDate = $fmt->format($sendDate);
		
		echo("
			<div class='activity'"); 
				if ($tmpMessage['playerID'] == $_SESSION['playerID'])
						echo(" style='background-color: #FFF0C4;'");
						echo(">
				<div class='leftbar'>");  
					
					if ($tmpMessage['playerID'] != $_SESSION['playerID'])
						echo("
							<img src='".getPicturePathM($tmpMessage['socialNetwork'], $tmpMessage['socialID'])."' width='40' height='40' border='0'/>
						");
					else
						echo("
							<div style='height: 40px; width: 40px;'></div>
						");
			echo("</div>
					<div class='details' style='width: 80%; padding: 0px;'>
						<div class='title'>
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