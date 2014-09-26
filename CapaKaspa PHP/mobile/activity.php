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
	
$titre_page = _("News feed");
$desc_page = _("News feed of your following");
require 'include/page_header.php';
?>
<link href="http://www.capakaspa.info/pgn4web/fonts/pgn4web-font-ChessSansPiratf.css" type="text/css" rel="stylesheet" />
<script src="http://www.capakaspa.info/javascript/menu.js" type="text/javascript"></script>
<script src="http://www.capakaspa.info/javascript/activity.js" type="text/javascript"></script>
<script src="http://www.capakaspa.info/javascript/comment.js" type="text/javascript"></script>
<script src="http://www.capakaspa.info/javascript/like.js" type="text/javascript"></script>
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
			displayActivity(document.getElementById("startPage").value, 0, <? echo($_SESSION['playerID']);?>);
	}
}

window.onscroll = getheight;

</script>
<?
$attribut_body = "onload=\"displayActivity(0, 0, ".$_SESSION['playerID']."); \"";
require 'include/page_body.php';
?>

	<div id="onglet">
		<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td><div class="ongletdisable"><a href="game_in_progress.php"><? echo _("Games")?></a></div></td>	
			<td><div class="ongletenable"><? echo _("News");?></div></td>
			<td><div class="ongletdisable"><a href="player_search.php"><? echo _("Players");?></a></div></td>
		</tr>
		</table>
	</div>
  	<?
	if ($errMsg != "")
		echo("<div class='error'>".$errMsg."</div>");
	?>
		<form name="existingGames" action="game_board.php" method="post">
			<div id="activities0" style="display: none;"><img src='images/ajaxloader.gif'/></div>
			<input type="hidden" name="gameID" value="">
			<input type="hidden" name="from" value="encours">
		</form>
<?
require 'include/page_footer.php';
mysqli_close($dbh);
?>