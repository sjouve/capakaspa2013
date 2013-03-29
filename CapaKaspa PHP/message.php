<?
session_start();

/* load settings */
if (!isset($_CONFIG))
	require 'include/config.php';

require 'dac/dac_activity.php';
require 'dac/dac_players.php';
require 'bwc/bwc_players.php';
require 'dac/dac_games.php';
require 'bwc/bwc_games.php';

/* connect to database */
require 'include/connectdb.php';

$errMsg = "";

/* check session status */
require 'include/sessioncheck.php';

require 'include/localization.php';

$toPlayerIDInit = isset($_GET['pID'])?$_GET['pID']:0;
$toEmailInit = isset($_GET['pE'])?base64_decode($_GET['pE']):"";

$titre_page = _("Private messages");
$desc_page = _("Your private messages");
require 'include/page_header.php';
?>
<script src="javascript/pmessage.js" type="text/javascript"></script>
<script src="javascript/menu.js" type="text/javascript"></script>
<script type="text/javascript">
function sendPrivateMessage(playerID)
{
	toPlayerID = document.getElementById("toPlayerID").value;
	toEmail = document.getElementById("toEmail").value;
	insertPrivateMessage(playerID, toPlayerID, toEmail);
	displayPrivateMessage(playerID, toPlayerID, toEmail);
	document.getElementById("privateMessage").value = "";
}

</script>
<?
if ($toPlayerIDInit != 0)
	$attribut_body = "onload=\"javascript:highlightMenu(6);displayPrivateMessage(".$_SESSION['playerID'].", ".$toPlayerIDInit.", '".$toEmailInit."');\"";
else
	$attribut_body = "onload=\"javascript:highlightMenu(6);\"";

require 'include/page_body.php';
?>
<div id="content">
	<div class="contentbody">
  	<?
	if ($errMsg != "")
		echo("<div class='error'>".$errMsg."</div>");
	?>
		<div id="messages" style="display: block;overflow-y: auto;height: 500px;">
			<center><? echo _("No Conversation Selected")?></center>
		</div>
		<div id="messageForm" style="display: none;">
			<input id="toPlayerID" type="hidden" value="">
			<input id="toEmail" type="hidden" value="">
			<textarea id="privateMessage" style="width: 540px; font-size: 12px;" rows="4" placeholder="<? echo _("Your message...")?>"></textarea><br>
			<input type="button" class="button" value="<? echo _("Send")?>" onclick="sendPrivateMessage(<? echo($_SESSION['playerID'])?>)">
		</div>
	</div>
</div>
<div id="rightbar">
	<div id="contacts" style="display: block;overflow-y: auto;height: 500px;">
		<div class='navlinks'>
			<div class='title'>
				<? echo _("Contacts")?>
			</div>
		</div>
		<? 
		$result = listPMContact($_SESSION['playerID']);
		$nb_contacts = mysql_num_rows($result);
		if ($nb_contacts > 0)
			while($tmpPlayer = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				if ($tmpPlayer['playerID'] != $_SESSION['playerID'])
				{	
					echo("
							<div id='contact".$tmpPlayer['playerID']."' class='contact' onmouseover=\"this.style.cursor='pointer';\" onclick='javascript:displayPrivateMessage(".$_SESSION['playerID'].", ".$tmpPlayer['playerID'].", \"".$tmpPlayer['email']."\")'>
							<div id='picture' style='float: left; margin-right: 5px;'>
							<img src='".getPicturePath($tmpPlayer['socialNetwork'], $tmpPlayer['socialID'])."' width='32' height='32' border='0'/>
							</div>
							<span class='name'>".$tmpPlayer['firstName']." ".$tmpPlayer['lastName']." (".$tmpPlayer['nick'].")</span>");
					if ($tmpPlayer['lastActionTime'])
						echo("<img src='images/user_online.gif' style='vertical-align:bottom;' alt='"._("Player online")."'/>");
					if (isNewPlayer($tmpPlayer['creationDate']))
						echo("<br><span class='newplayer'>"._("New player")."</span>");
					if ($tmpPlayer['nbUnread'] > 0)
						echo("<br>".$tmpPlayer['nbUnread']." "._("Unread messages"));
					echo("</div>
							");
				}
			}
		else {
			echo ("<div class='contentbody'>"._("No contact")."</div>");
		}
		?>
	</div>
	<?require 'include/page_footer_right.php';?>
</div>
<?
require 'include/page_footer.php';
mysql_close();
?>
