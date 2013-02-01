<?	
require 'include/mobilecheck.php';
session_start();

/* load settings */
if (!isset($_CONFIG))
	require 'include/config.php';

require 'dac/dac_players.php';
require 'dac/dac_games.php';
require 'bwc/bwc_chessutils.php';
require 'bwc/bwc_common.php';
require 'bwc/bwc_players.php';
	
/* connect to database */
require 'include/connectdb.php';

/* check session status */
require 'include/sessioncheck.php';

require 'include/localization.php';
$fmt = new IntlDateFormatter(getenv("LC_ALL"), IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);

/* Charger le profil */
$playerID = isset($_POST['playerID']) ? $_POST['playerID']:$_GET['playerID'];
$player = getPlayer($playerID);
	
/* Load following */
$favorite = getPlayerFavorite($_SESSION['playerID'], $player['playerID']);

$titre_page = _("View player profile");
$desc_page = _("View player profile");
require 'include/page_header.php';
?>
<script src="javascript/follow.js" type="text/javascript"></script>
<script src="javascript/activity.js" type="text/javascript"></script>
<script src="javascript/comment.js" type="text/javascript"></script>
<script src="javascript/like.js" type="text/javascript"></script>
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
			displayActivity(document.getElementById("startPage").value, 1, <? echo($playerID);?>);
	}
}

window.onscroll = getheight;

</script>
<?
$attribut_body = "onload='displayActivity(0, 1, ".$playerID.")'";
require 'include/page_body_no_menu.php';
/*
 * Parties en cours
 * Parties contre 
 * Statistiques parties
 * Abonnés
 * Abonnements
 * 
 */
?>
<div id="player_header">
	<div id="player_name" style="float:left; display: block;padding: 5px;">
		<img src="<?echo(getPicturePath($player['socialNetwork'], $player['socialID']));?>" width="50" height="50" style="vertical-align: middle"/>
		<? 
		echo("<span class='player_name'>".$player['firstName']." ".$player['lastName']." (".$player['nick'].")</span>"); 
  		if (getOnlinePlayer($player['playerID'])) echo (" <img src='images/user_online.gif'/>");
  		if (isNewPlayer($player['creationDate'])) echo (" <img src='images/user_new.gif'/>");
  		?>
  	</div>
  	<div id="player_action" style="float: right;display: block;padding-top: 15px;padding-right: 5px;">
	  	<form action="game_new.php" method="post">
		<? if ($_SESSION['playerID'] != $player['playerID'] && !$favorite) {?>	
			<div id="follow<?echo($player['playerID']);?>" style="display: inline;"><input id="btnFollow" value="<? echo _("Follow")?>" type="button" class="button" onclick="javascript:insertFav(<?echo($player['playerID']);?>);"></div>
		<? }?>
		<? if ($_SESSION['playerID'] != $player['playerID'] && $favorite) {?>			
			<div id="follow<?echo($player['playerID']);?>" style="display: inline;"><input id="btnFollow" value="<? echo _("Unfollow")?>" type="button" class="button" onclick="javascript:deleteFav(<?echo($favorite['favoriteID']);?>, <?echo($player['playerID']);?>);"></div>
		<? }?>		
		<? if ($_SESSION['playerID'] != $player['playerID']) {?>
			<div id="newgame" style="display: inline;">					
				<input type="submit" class="link" value="<? echo _("New game");?>">
				<input type="hidden" name="opponent" value="<? echo _($player['nick']);?>">						
			</div>
		<?}?>
		<? if ($_SESSION['playerID'] != $player['playerID']) {?>
			<input type="button" class="link" value="<? echo _("Ended games"); ?>" onclick="location.href='game_list_ended.php?playerID=<?php echo($player['playerID']);?>'">
		<?}?>
		<? if ($_SESSION['playerID'] == $player['playerID']) {?>
			<input id="btnUpdate" type="button" class="link" value="<?php echo _("Update my information")?>" onclick="location.href='player_update.php'">
		<? }?>
		</form>
	</div>
</div>
<div id="player_info">
        <br><? echo _("Elo")?> : <? echo($player['elo']); 
			if ($player['eloProgress'] == 0)
				echo (" (=)");
			else if ($player['eloProgress'] == 1)
				echo (" (-)");
			else echo (" (+)");
			?>
	<br><? echo _("Localization")?> : <? echo(stripslashes($player['situationGeo'])); ?>, <? echo($player['countryName']); ?>
	<br><? echo _("Birth date")?> : <? echo($player['anneeNaissance']); ?>
	<br><? echo _("About")?> : <? echo(stripslashes($player['profil'])); ?>
	<br><? echo _("Sign-up")?> : <?	$creationDate = new DateTime($player['creationDate']);
							$strCreationDate = $fmt->format($creationDate);
							echo($strCreationDate);?>
	<br><? echo _("Last connection")?> : <?	$lastConnection = new DateTime($player['lastConnection']);
									$strlastConnection = $fmt->format($lastConnection);
									echo($strlastConnection);?>
	<?
	$dateDeb = date("Y-m-d", mktime(0,0,0, 1, 1, 1990));
	$dateFin = date("Y-m-d", mktime(0,0,0, 12, 31, 2020));
	$countLost = countLost($player['playerID'], $dateDeb, $dateFin);
	$nbDefaites = $countLost['nbGames'];
	$countDraw = countDraw($player['playerID'], $dateDeb, $dateFin);
	$nbNulles = $countDraw['nbGames'];
	$countWin = countWin($player['playerID'], $dateDeb, $dateFin);
	$nbVictoires = $countWin['nbGames'];
	$nbParties = $nbDefaites + $nbNulles + $nbVictoires;
	$nbFollowers = 0;
	$nbFollowing = 0;
	$res_count = searchPlayers("count", 0, 0, $player['playerID'], "wers", "", "", "", "", "");
	if ($res_count)
	{
		$count = mysql_fetch_array($res_count, MYSQL_ASSOC);
		$nbFollowers = $count['nbPlayers'];
	}
	$res_count = searchPlayers("count", 0, 0, $player['playerID'], "wing", "", "", "", "", "");
	if ($res_count)
	{
		$count = mysql_fetch_array($res_count, MYSQL_ASSOC);
		$nbFollowing = $count['nbPlayers'];
	}
	?>
	<br><? echo _("Won")?> : <? echo($nbVictoires); ?>
	<br><? echo _("Draw")?> : <? echo($nbNulles); ?>
	<br><? echo _("Lost")?> : <? echo($nbDefaites); ?>
	<br><? echo _("Followers")?> : <? echo($nbFollowers); ?>
	<br><? echo _("Following")?> : <? echo($nbFollowing); ?>
	
</div>
<div id="graphelo" style="float: left;display: block;">
	<img src="graph_elo_progress.php?playerID=<?php echo($playerID);?>&elo=<?php echo($player['elo']);?>" width="650" height="250" />
</div>

<div id="content">
	<div class="contentbody">
		<form name="activityGames" action="game_board.php" method="post">
			<div id="activities0" style="display: none;"><img src='images/ajaxloader.gif'/></div>
			<input type="hidden" name="gameID" value="">
			<input type="hidden" name="from" value="encours">
		</form>		
		<center>
			<script type="text/javascript"><!--
			google_ad_client = "pub-8069368543432674";
			/* 468x60, Profil consultation bandeau */
			google_ad_slot = "3062307582";
			google_ad_width = 468;
			google_ad_height = 60;
			//-->
			</script>
			<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
			</script>
		</center>		
	</div>
</div>
<div id="rightbarlarge">
	<div class="contentbody">
		<h3>Parties en cours de <? echo($player['nick']); ?></h3>
		
		<form name="existingGames" action="game_board.php" method="post">

        <div class="tabliste">
          <table border="0" width="650">
            <tr>
              <th width="17%">Blancs</th>
              <th width="17%">Noirs</th>
              <th width="8%">Résultat</th>
              <th width="8%">ECO</th>
              <th width="25%">Début</th>
              <th width="25%">Dernier coup</th>
            </tr>
            <?
					$tmpGames = mysql_query("SELECT G.gameID, G.eco eco, W.nick whiteNick, B.nick blackNick, G.gameMessage, G.messageFrom, DATE_FORMAT(G.dateCreated, '%d/%m/%Y %T') dateCreatedF, DATE_FORMAT(G.lastMove, '%d/%m/%Y %T') lastMove
				                            FROM games G, players W, players B
				                            WHERE G.gameMessage = ''
				                            AND (G.whitePlayer = ".$player['playerID']." OR G.blackPlayer = ".$player['playerID'].")
				                            AND W.playerID = G.whitePlayer AND B.playerID = G.blackPlayer
				                            ORDER BY G.dateCreated");
					
					if (mysql_num_rows($tmpGames) == 0)
						echo("<tr><td colspan='6'>Aucune partie en cours</td></tr>\n");
					else
					{
						while($tmpGame = mysql_fetch_array($tmpGames, MYSQL_ASSOC))
						{
							/* White */
							echo("<tr><td>");
							echo($tmpGame['whiteNick']);
							
							/* Black */
							echo ("</td><td>");
							echo($tmpGame['blackNick']);
							
							/* Current Turn */
							echo ("</td><td align=center>");
							echo("<a href='javascript:loadGame(".$tmpGame['gameID'].")'><img src='images/eye.gif' border=0 alt='Voir'/></a>");
				
							/* ECO Code */
							echo ("</td><td align='center'>".$tmpGame['eco']);
							/* Start Date */
							echo ("</td><td align='center'>".$tmpGame['dateCreatedF']);
				
							/* Last Move */
							echo ("</td><td align='center'>".$tmpGame['lastMove']."</td></tr>\n");
						}
											
					}
				?>
          </table>
        </div>
        <input type="hidden" name="gameID" value="">
        <input type="hidden" name="sharePC" value="no">
        <input type="hidden" name="from" value="toutes">
      </form>
      
      <? if ($_SESSION['playerID'] != $player['playerID']) {?>
		<h3>Mes parties contre <? echo($player['nick']); ?></h3>
		
		<form name="endedGames" action="game_board.php" method="post">

        <div class="tabliste">
          <table border="0" width="650">
            <tr>
              <th width="17%">Blancs</th>
              <th width="17%">Noirs</th>
              <th width="8%">Résultat</th>
              <th width="8%">ECO</th>
              <th width="25%">Début</th>
              <th width="25%">Dernier coup</th>
            </tr>
            <?
					$tmpGames = mysql_query("SELECT G.gameID, G.eco eco, W.nick whiteNick, B.nick blackNick, G.gameMessage, G.messageFrom, DATE_FORMAT(G.dateCreated, '%d/%m/%Y %T') dateCreatedF, DATE_FORMAT(G.lastMove, '%d/%m/%Y %T') lastMove
				                            FROM games G, players W, players B
				                            WHERE (G.gameMessage <> '' AND G.gameMessage <> 'playerInvited' AND G.gameMessage <> 'inviteDeclined')
				                            AND ((G.whitePlayer = ".$player['playerID']." AND G.blackPlayer = ".$_SESSION['playerID'].") OR (G.blackPlayer = ".$player['playerID']." AND G.whitePlayer = ".$_SESSION['playerID']."))
				                            AND W.playerID = G.whitePlayer AND B.playerID = G.blackPlayer
				                            ORDER BY G.dateCreated");
					
					if (mysql_num_rows($tmpGames) == 0)
						echo("<tr><td colspan='6'>Vous n'avez joué aucune partie contre ce joueur</td></tr>\n");
					else
					{
						while($tmpGame = mysql_fetch_array($tmpGames, MYSQL_ASSOC))
						{
							/* White */
							echo("<tr><td>");
							echo($tmpGame['whiteNick']);
							
							/* Black */
							echo ("</td><td>");
							echo($tmpGame['blackNick']);
							
							/* Current Turn */
						
							if (($tmpGame['gameMessage'] == "playerResigned") && ($tmpGame['messageFrom'] == "white"))
								echo("</td><td align=center><a href='javascript:loadEndedGame(".$tmpGame['gameID'].")'>0-1</a>");
							else if (($tmpGame['gameMessage'] == "playerResigned") && ($tmpGame['messageFrom'] == "black"))
								echo("</td><td align=center><a href='javascript:loadEndedGame(".$tmpGame['gameID'].")'>1-0</a>");
							else if (($tmpGame['gameMessage'] == "checkMate") && ($tmpGame['messageFrom'] == "white"))
								echo("</td><td align=center><a href='javascript:loadEndedGame(".$tmpGame['gameID'].")'>1-0</a>");
							else if ($tmpGame['gameMessage'] == "checkMate")
								echo("</td><td align=center><a href='javascript:loadEndedGame(".$tmpGame['gameID'].")'>0-1</a>");
							else
								echo("</td><td align=center><a href='javascript:loadEndedGame(".$tmpGame['gameID'].")'>1/2-1/2</a>");
				
							/* ECO Code */
							echo ("</td><td align='center'>".$tmpGame['eco']);
							/* Start Date */
							echo ("</td><td align='center'>".$tmpGame['dateCreatedF']);
				
							/* Last Move */
							echo ("</td><td align='center'>".$tmpGame['lastMove']."</td></tr>\n");
						}					
					}
				?>
          </table>
        </div>
        <input type="hidden" name="gameID" value="">
        <input type="hidden" name="sharePC" value="no">
        <input type="hidden" name="from" value="toutes">
      	</form>
		<br/>
		<?}?>
	</div>
</div>
<?
require 'include/page_footer.php';
mysql_close();
?>
