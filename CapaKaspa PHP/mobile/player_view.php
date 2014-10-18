<?	
session_start();

/* load settings */
if (!isset($_CONFIG))
	require '../include/config.php';

require '../dac/dac_players.php';
require '../dac/dac_games.php';
require '../dac/dac_activity.php';
require '../bwc/bwc_chessutils.php';
require '../bwc/bwc_common.php';
require '../bwc/bwc_players.php';
	
/* connect to database */
require '../include/connectdb.php';

/* check session status */
require '../include/sessioncheck.php';

require '../include/localization.php';
$fmt = new IntlDateFormatter(getenv("LC_ALL"), IntlDateFormatter::SHORT, IntlDateFormatter::SHORT);

/* Charger le profil */
$playerID = isset($_POST['playerID']) ? $_POST['playerID']:$_GET['playerID'];
$player = getPlayer($playerID);
	
/* Load following */
$favorite = getPlayerFavorite($_SESSION['playerID'], $player['playerID']);

$titre_page = _("View player profile");
$desc_page = _("View player profile");
require 'include/page_header.php';
?>
<link href="http://jouerauxechecs.capakaspa.info/pgn4web/fonts/pgn4web-font-ChessSansPiratf.css" type="text/css" rel="stylesheet" />
<script src="http://jouerauxechecs.capakaspa.info/javascript/player.js" type="text/javascript"></script>
<script src="http://jouerauxechecs.capakaspa.info/javascript/follow.js" type="text/javascript"></script>
<script src="http://jouerauxechecs.capakaspa.info/javascript/activity.js" type="text/javascript"></script>
<script src="http://jouerauxechecs.capakaspa.info/javascript/comment.js" type="text/javascript"></script>
<script src="http://jouerauxechecs.capakaspa.info/javascript/like.js" type="text/javascript"></script>
<script src="http://jouerauxechecs.capakaspa.info/javascript/pmessage.js" type="text/javascript"></script>
<script src="http://jouerauxechecs.capakaspa.info/javascript/css-pop.js" type="text/javascript"></script>
<script type="text/javascript">
function loadEndedGame(gameID)
{
	document.endedGames.gameID.value = gameID;
	document.endedGames.submit();
}
function loadGame(gameID)
{

	document.existingGames.gameID.value = gameID;
	document.existingGames.submit();
}
function loadGameActivity(gameID)
{

	document.activityGames.gameID.value = gameID;
	document.activityGames.submit();
}
function displayFeed (type, start)
{
	if (type == 'activity')
	{
		document.getElementById("players0").style.display = "none";
		document.getElementById("stat_news").style.backgroundColor = "#F2A521";
		document.getElementById("stat_wing").style.backgroundColor = "#FFFFFF";
		document.getElementById("stat_wers").style.backgroundColor = "#FFFFFF";
		document.getElementById("feedType").value = 'activity';
		displayActivity(start, 1, <? echo($playerID);?>);
	}
	if (type == 'wers')
	{
		document.getElementById("activities0").style.display = "none";
		document.getElementById("stat_news").style.backgroundColor = "#FFFFFF";
		document.getElementById("stat_wing").style.backgroundColor = "#FFFFFF";
		document.getElementById("stat_wers").style.backgroundColor = "#F2A521";
		document.getElementById("feedType").value = 'wers';
		displayPlayers(start, <? echo($playerID);?>, 'wers', '', '', '', '', '');
	}
	if (type == 'wing')
	{
		document.getElementById("activities0").style.display = "none";
		document.getElementById("stat_news").style.backgroundColor = "#FFFFFF";
		document.getElementById("stat_wing").style.backgroundColor = "#F2A521";
		document.getElementById("stat_wers").style.backgroundColor = "#FFFFFF";
		document.getElementById("feedType").value = 'wing';
		displayPlayers(start, <? echo($playerID);?>, 'wing', '', '', '', '', '');
	}
}
function getheight() 
{
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
			displayFeed(document.getElementById("feedType").value, document.getElementById("startPage").value);
	}
}

window.onscroll = getheight;

</script>
<?
$attribut_body = "onload=\"displayFeed('activity', 0)\"";
$toPlayerID = $player['playerID'];
$toFirstName = $player['firstName'];
$toLastName = $player['lastName'];
$toNick = $player['nick'];
$toEmail = $player['email'];
require 'include/page_body.php';
/*
 * Parties en cours
 * Parties contre 
 * Statistiques parties
 * Abonnés
 * Abonnements
 * 
 */
?>
<div id="onglet">
	<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td><div class="ongletdisable"><a href="game_in_progress.php"><? echo _("Games")?></a></div></td>
			<td><div class="ongletdisable"><a href="activity.php"><? echo _("News");?></a></div></td>
			<td><div class="ongletdisable"><a href="player_search.php"><? echo _("Players");?></a></div></td>
		</tr>
	</table>
</div>
		
<div id="player_header">
	<div class="profile_picture">
		<img src="<?echo(getPicturePathM($player['socialNetwork'], $player['socialID']));?>" width="50" height="50" style="vertical-align: middle"/>
	</div>
	<div id="player_name" style="float:left; display: block; padding: 5px;">
		<? 
		echo("<span class='player_name'>".$player['firstName']." ".$player['lastName']."</span><br><span class='player_name'>(".$player['nick'].")</span>"); 
  		if (getOnlinePlayer($player['playerID'])) echo (" <img src='images/user_online.gif'/>");
  		if (isNewPlayer($player['creationDate'])) echo (" <span class='newplayer'>"._("New player")."</span>");
  		?>
  	</div>
  	<div id="player_action" style="float: right;display: block;padding-top: 15px;padding-right: 5px;">
	  	<? if ($_SESSION['playerID'] === $player['playerID']) {?>
	  	<form name="logout" action="game_in_progress.php" method="post">
		  	<div id="logout" style="display: inline;">
		        <input type="hidden" name="ToDo" value="Logout">
		        <input type="submit" value='<? echo _("Sign out")?>' class="button">
	        </div>
      	</form>
      	<?}?>
	  	<form action="game_new.php" method="post">
		<? if ($_SESSION['playerID'] != $player['playerID'] && !$favorite && $player['activate'] == 1) {?>	
			<div id="follow<?echo($player['playerID']);?>" style="display: inline;"><input id="btnFollow" value="<? echo _("Follow")?>" type="button" class="button" onclick="javascript:insertFav(<?echo($player['playerID']);?>,'<?echo($player['email']);?>');"></div>
		<? }?>
		<? if ($_SESSION['playerID'] != $player['playerID'] && $favorite && $player['activate'] == 1) {?>			
			<div id="follow<?echo($player['playerID']);?>" style="display: inline;"><input id="btnFollow" value="<? echo _("Unfollow")?>" type="button" class="button" onclick="javascript:deleteFav(<?echo($favorite['favoriteID']);?>, <?echo($player['playerID']);?>);"></div>
		<? }?>
		<? if ($_SESSION['playerID'] != $player['playerID'] && $player['activate'] == 1) {?>
			<div id="newgame" style="display: inline;">					
				<input type="submit" class="link" value="<? echo _("New game");?>">
				<input type="hidden" name="opponent" value="<? echo _($player['nick']);?>">						
			</div>	
		<?}?>
		</form>
	</div>
</div>
<div id="player_info">
	<span style="margin-left: 3px"><? echo _("Was born in ")?> <? echo($player['anneeNaissance']); ?></span><br>
	<span style="margin-left: 3px"><? echo _("Lives in ")?> <? echo(stripslashes($player['situationGeo'])); ?>, <? echo($player['countryName']); ?></span>
	<br><br>
	<span style="margin-left: 3px"><? echo _("About")?></span>
		<div style="background-color: #FFFFFF;padding: 3px;height: 60px;overflow-y: auto;">
			<? echo(nl2br(stripslashes($player['profil']))); ?>
		</div>
		<? 
			/*echo _("Sign-up")." : ";
			$creationDate = new DateTime($player['creationDate']);
			$strCreationDate = $fmt->format($creationDate);
			echo($strCreationDate);
			echo _("Last connection")." : ";
			$lastConnection = new DateTime($player['lastConnection']);
			$strlastConnection = $fmt->format($lastConnection);
			echo($strlastConnection);*/
	
	$dateDeb = date("Y-m-d", mktime(0,0,0, 1, 1, 1990));
	$dateFin = date("Y-m-d", mktime(0,0,0, 12, 31, 2020));
	$countLost = countLost($player['playerID'], $dateDeb, $dateFin);
	$nbDefaites = $countLost['nbGames'];
	$countDraw = countDraw($player['playerID'], $dateDeb, $dateFin);
	$nbNulles = $countDraw['nbGames'];
	$countWin = countWin($player['playerID'], $dateDeb, $dateFin);
	$nbVictoires = $countWin['nbGames'];
	$nbParties = $nbDefaites + $nbNulles + $nbVictoires;
	$nbNews = 0;
	$nbFollowers = 0;
	$nbFollowing = 0;
	$nbNews = countActivityForPlayer($player['playerID']);	
	$res_count = searchPlayers("count", 0, 0, $player['playerID'], "wers", "", "", "", "", "");
	if ($res_count)
	{
		$count = mysqli_fetch_array($res_count, MYSQLI_ASSOC);
		$nbFollowers = $count['nbPlayers'];
	}
	$res_count = searchPlayers("count", 0, 0, $player['playerID'], "wing", "", "", "", "", "");
	if ($res_count)
	{
		$count = mysqli_fetch_array($res_count, MYSQLI_ASSOC);
		$nbFollowing = $count['nbPlayers'];
	}
	?>
	<br>
	<div id="stat_won" class="block_stat"><span class="label"><? echo _("Won");?></span><br><span class="number"><? echo($nbVictoires); ?></span></div> 
	<div id="stat_draw" class="block_stat"><span class="label"><? echo _("Draw");?></span><br><span class="number"><? echo($nbNulles); ?></span></div> 
	<div id="stat_lost" class="block_stat"><span class="label"><? echo _("Lost");?></span><br><span class="number"><? echo($nbDefaites); ?></span></div>
	<div id="stat_news" class="block_stat" onmouseover="this.style.cursor='pointer';" onclick="displayFeed('activity', 0)"><span class="label"><? echo _("News");?></span><br><span class="number"><? echo($nbNews);?></span></div>
	<div id="stat_wers" class="block_stat" onmouseover="this.style.cursor='pointer';" onclick="displayFeed('wers', 0)"><span class="label"><? echo _("Followers");?></span><br><span class="number"><? echo($nbFollowers);?></span></div>
	<div id="stat_wing" class="block_stat" onmouseover="this.style.cursor='pointer';" onclick="displayFeed('wing', 0)"><span class="label"><? echo _("Following");?></span><br><span class="number"><? echo($nbFollowing);?></span></div>
	
</div>
		
<span id="#confirm_delete_activity" style="display: none"><?echo _("Are you sure you want to delete this news ?")?></span>
    
	<input id="feedType" type="hidden" name="feedType" value="activity">
	<form name="activityGames" action="game_board.php" method="post">
		<div id="activities0" style="display: none;"><img src='images/ajaxloader.gif'/></div>
	<input type="hidden" name="gameID" value="">
	<input type="hidden" name="from" value="encours">
</form>

<div id="players0" style="display: none;"><img src='images/ajaxloader.gif'/></div>
			

<?
require 'include/page_footer.php';
mysqli_close($dbh);
?>
