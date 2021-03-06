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
$typeList = 0;
$activityID = isset($_GET['ID']) ? $_GET['ID'] : "''";
if ($activityID != "''")
	$typeList = 2;

/* check session status */
require '../include/sessioncheck.php';
	
require '../include/localization.php';
	
$titre_page = _("News feed");
$desc_page = _("News feed of your following");
require 'include/page_header.php';
?>
<link href="http://jouerauxechecs.capakaspa.info/pgn4web/fonts/pgn4web-font-ChessSansPiratf.css" type="text/css" rel="stylesheet" />
<script src="http://jouerauxechecs.capakaspa.info/javascript/activity.js" type="text/javascript"></script>
<script src="http://jouerauxechecs.capakaspa.info/javascript/comment.js" type="text/javascript"></script>
<script src="http://jouerauxechecs.capakaspa.info/javascript/like.js" type="text/javascript"></script>
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
			displayActivity(document.getElementById("startPage").value, <? echo($typeList);?>, <? echo($_SESSION['playerID']);?>, <?echo($activityID);?>);
	}
}

window.onscroll = getheight;

</script>
<?
$attribut_body = "onload=\"displayActivity(0, ".$typeList.", ".$_SESSION['playerID'].", ".$activityID."); \"";
$activeMenu = 0;
if ($activityID == "''")
	$activeMenu = 30;
require 'include/page_body.php';

	if ($errMsg != "")
		echo("<div class='error'>".$errMsg."</div>");
	?>
		<form name="existingGames" action="game_board.php" method="post">
			<div id="activities0" style="display: none;"><img src='images/ajaxloader.gif'/></div>
			<input type="hidden" name="gameID" value="">
			<input type="hidden" name="from" value="encours">
		</form>
<?
updatePlayerDisplayNews($_SESSION['playerID']);
require 'include/page_footer.php';
mysqli_close($dbh);
?>
