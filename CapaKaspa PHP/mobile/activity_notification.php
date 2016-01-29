<?
session_start();

/* load settings */
if (!isset($_CONFIG))
	require '../include/config.php';
	
require '../include/constants.php';
require '../dac/dac_players.php';
require '../dac/dac_activity.php';
require '../bwc/bwc_common.php';
require '../bwc/bwc_chessutils.php';
require '../bwc/bwc_players.php';
require '../dac/dac_games.php';
require '../bwc/bwc_games.php';
	
/* connect to database */
require '../include/connectdb.php';

$errMsg = "";

/* check session status */
require '../include/sessioncheck.php';
	
require '../include/localization.php';
	
$titre_page = _("Notifications feed");
$desc_page = _("All your notifications");
require 'include/page_header.php';
?>
<script src="http://jouerauxechecs.capakaspa.info/javascript/notification.js" type="text/javascript"></script>
<script type="text/javascript">
function loadGameActivity(gameID)
{
	document.existingGames.gameID.value = gameID;
	document.existingGames.submit();
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
		if (scrolledtonum >= heightofbody && document.getElementById("startPage")) {
			displayNotification(document.getElementById("startPage").value, <? echo($_SESSION['playerID']);?>);
	}
}

window.onscroll = getheight;

</script>
<?
$attribut_body = "onload=\"displayNotification(0, ".$_SESSION['playerID']."); \"";
$activeMenu = 0;
require 'include/page_body.php';
?>

  	<?
	if ($errMsg != "")
		echo("<div class='error'>".$errMsg."</div>");
	?>
	<h3><?echo(_("Notifications"));?> <a href="activity_notification.php"><img src="images/icone_rafraichir.png" border="0" title="<?php echo _("Refresh list")?>" alt="<?php echo _("Refresh list")?>" /></a></h3>
		<form name="existingGames" action="game_board.php" method="post">
			<div id="notifications0" style="display: none;"><img src='images/ajaxloader.gif'/></div>
			<input type="hidden" name="gameID" value="">
			<input type="hidden" name="from" value="notification">
		</form>
	
<?
require 'include/page_footer.php';
mysqli_close($dbh);
?>
