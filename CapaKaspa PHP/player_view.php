<?	
require 'include/mobilecheck.php';
session_start();

/* load settings */
if (!isset($_CONFIG))
	require 'include/config.php';

require 'include/constants.php';
require 'dac/dac_players.php';
require 'dac/dac_games.php';
require 'dac/dac_activity.php';
require 'bwc/bwc_chessutils.php';
require 'bwc/bwc_common.php';
require 'bwc/bwc_players.php';
	
/* connect to database */
require 'include/connectdb.php';

/* check session status */
require 'include/sessioncheck.php';

require 'include/localization.php';
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
<link href="pgn4web/fonts/pgn4web-font-ChessSansPiratf.css" type="text/css" rel="stylesheet" />
<script src="javascript/player.js" type="text/javascript"></script>
<script src="javascript/follow.js" type="text/javascript"></script>
<script src="javascript/activity.js" type="text/javascript"></script>
<script src="javascript/comment.js" type="text/javascript"></script>
<script src="javascript/like.js" type="text/javascript"></script>
<script src="javascript/pmessage.js" type="text/javascript"></script>
<script src="javascript/css-pop.js" type="text/javascript"></script>
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
function displayElo ()
{
	document.getElementById("graphelo").style.display = "block";
	document.getElementById("graphelo960").style.display = "none";
}
function displayElo960 ()
{
	document.getElementById("graphelo").style.display = "none";
	document.getElementById("graphelo960").style.display = "block";
}
function displayFeed (type, start)
{
	if (type == 'activity')
	{
		document.getElementById("players0").style.display = "none";
		document.getElementById("stat_news").style.backgroundColor = "#F2A521";
		document.getElementById("stat_wing").style.backgroundColor = "#EEEEEE";
		document.getElementById("stat_wers").style.backgroundColor = "#EEEEEE";
		document.getElementById("feedType").value = 'activity';
		displayActivity(start, 1, <? echo($playerID);?>, "");
	}
	if (type == 'wers')
	{
		document.getElementById("activities0").style.display = "none";
		document.getElementById("stat_news").style.backgroundColor = "#EEEEEE";
		document.getElementById("stat_wing").style.backgroundColor = "#EEEEEE";
		document.getElementById("stat_wers").style.backgroundColor = "#F2A521";
		document.getElementById("feedType").value = 'wers';
		displayPlayers(start, <? echo($playerID);?>, 'wers', '', '', '', '', '');
	}
	if (type == 'wing')
	{
		document.getElementById("activities0").style.display = "none";
		document.getElementById("stat_news").style.backgroundColor = "#EEEEEE";
		document.getElementById("stat_wing").style.backgroundColor = "#F2A521";
		document.getElementById("stat_wers").style.backgroundColor = "#EEEEEE";
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
<div id="player_view_header">
	<div id="player_header">
		<div class="profile_picture">
			<img src="<?echo(getPicturePath($player['socialNetwork'], $player['socialID']));?>" width="50" height="50" style="vertical-align: middle"/>
		</div>
		<div id="player_name" style="float:left; display: block; padding: 5px;">
			<? 
			echo("<br><span class='player_name'>".getPlayerName(0, $player['nick'], $player['firstName'], $player['lastName'])."</span>"); 
	  		if (getOnlinePlayer($player['playerID'])) echo (" <img src='images/user_online.gif' title='"._("Player online")."' alt='"._("Player online")."'/>");
	  		if (isNewPlayer($player['creationDate'])) echo (" <span class='newplayer'>"._("New player")."</span>");
	  		?>
	  	</div>
	  	<div id="player_action" style="float: right;display: block;padding-top: 15px;padding-right: 5px;">
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
				<input type="button" name="message" id="message" class="link" value="<?echo _("Private message");?>" onclick="popup('popUpDiv')">
					
			<?}?>
			<? if ($_SESSION['playerID'] != $player['playerID']) {?>
				<input type="button" class="link" value="<? echo _("Ended games"); ?>" onclick="location.href='game_list_ended.php?playerID=<?php echo($player['playerID']);?>'">
			<?}?>
			<? if ($_SESSION['playerID'] == $player['playerID']) {?>
				<input id="btnUpdate" type="button" class="link" value="<?php echo _("Update my information")?>" onclick="location.href='player_update.php'">
			<? }?>
				<input type="button" class="link" value="<? echo _("X"); ?>" onclick="location.href='game_in_progress.php'">
			</form>
		</div>
	</div>
	<div id="player_info">
		<? echo _("Was born in ")?> <? if (strlen($player['anneeNaissance'])) echo(stripslashes($player['anneeNaissance'])); else echo("..."); ?><br>
		<? echo _("Lives in ")?> <? if (strlen($player['situationGeo'])) echo(stripslashes($player['situationGeo'])); else echo("..."); ?>, <? echo($player['countryName']); ?><br>
		<? echo _("About")." :"?>
		<div style="background-color: #EEEEEE; padding: 3px; height: 60px; overflow-y: auto; margin-bottom: 10px;">
			<? echo(nl2br(stripslashes($player['profil']))); ?>
		</div>
		<span><? echo _("Elo")." "._("Classic")." : ".$player['elo'];?></span><span style="float: right;"><?echo _("Elo Chess960")." : ".$player['elo960'];?></span><br>
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
		// Parties classiques
		$countLost = countLost($player['playerID'], $dateDeb, $dateFin, CLASSIC);
		$nbDefaites = $countLost['nbGames'];
		$countDraw = countDraw($player['playerID'], $dateDeb, $dateFin, CLASSIC);
		$nbNulles = $countDraw['nbGames'];
		$countWin = countWin($player['playerID'], $dateDeb, $dateFin, CLASSIC);
		$nbVictoires = $countWin['nbGames'];
		$nbParties = $nbDefaites + $nbNulles + $nbVictoires;
		// Parties Chess960
		$countLost960 = countLost($player['playerID'], $dateDeb, $dateFin, CHESS960);
		$nbDefaites960 = $countLost960['nbGames'];
		$countDraw960 = countDraw($player['playerID'], $dateDeb, $dateFin, CHESS960);
		$nbNulles960 = $countDraw960['nbGames'];
		$countWin960 = countWin($player['playerID'], $dateDeb, $dateFin, CHESS960);
		$nbVictoires960 = $countWin960['nbGames'];
		
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
		<div style="float: left">
			<div id="stat_won" class="block_stat" onmouseover="this.style.cursor='pointer';" onclick="location.href='game_list_ended.php?playerID=<?php echo($player['playerID']);?>&critType=0&critResult=W&critType=0'"><span class="label"><? echo _("Won");?></span><br><span class="number"> <? echo($nbVictoires); ?></span></div> 
			<div id="stat_draw" class="block_stat" onmouseover="this.style.cursor='pointer';" onclick="location.href='game_list_ended.php?playerID=<?php echo($player['playerID']);?>&critType=0&critResult=D&critType=0'"><span class="label"><? echo _("Draw");?></span><br><span class="number"> <? echo($nbNulles); ?></span></div> 
			<div id="stat_lost" class="block_stat" onmouseover="this.style.cursor='pointer';" onclick="location.href='game_list_ended.php?playerID=<?php echo($player['playerID']);?>&critType=0&critResult=L&critType=0'"><span class="label"><? echo _("Lost");?></span><br><span class="number"> <? echo($nbDefaites); ?></span></div>
		</div>
		<div style="float: right">
			<div id="stat_won" class="block_stat" onmouseover="this.style.cursor='pointer';" onclick="location.href='game_list_ended.php?playerID=<?php echo($player['playerID']);?>&critType=0&critResult=W&critType=2'"><span class="label"><? echo _("Won");?></span><br><span class="number"><? echo($nbVictoires960); ?></span></div> 
			<div id="stat_draw" class="block_stat" onmouseover="this.style.cursor='pointer';" onclick="location.href='game_list_ended.php?playerID=<?php echo($player['playerID']);?>&critType=0&critResult=D&critType=2'"><span class="label"><? echo _("Draw");?></span><br><span class="number"><? echo($nbNulles960); ?></span></div> 
			<div id="stat_lost" style="margin-right: 0px;" class="block_stat" onmouseover="this.style.cursor='pointer';" onclick="location.href='game_list_ended.php?playerID=<?php echo($player['playerID']);?>&critType=0&critResult=L&critType=2'"><span class="label"><? echo _("Lost");?></span><br><span class="number"><? echo($nbDefaites960); ?></span></div>
		</div>
		<div id="stat_news" class="block_social" onmouseover="this.style.cursor='pointer';" onclick="displayFeed('activity', 0)"><span class="label"><? echo _("News");?></span><br><span class="number"><? echo($nbNews);?></span></div>
		<div id="stat_wers" class="block_social" onmouseover="this.style.cursor='pointer';" onclick="displayFeed('wers', 0)"><span class="label"><? echo _("Followers");?></span><br><span class="number"><? echo($nbFollowers);?></span></div>
		<div id="stat_wing" class="block_social" onmouseover="this.style.cursor='pointer';" onclick="displayFeed('wing', 0)"><span class="label"><? echo _("Following");?></span><br><span class="number"><? echo($nbFollowing);?></span></div>
		
	</div>
	<div id="graphelo" style="float: right; display: block;">
		<img title="<?php echo _("Display 960 chess ranking history");?>" src="images/picto_echange_16.png" onmouseover="this.style.cursor='pointer';" onclick="displayElo960();"/> <? echo _("Elo ranking history")." ("._("Classic").")";?><br>
		<img src="graph_elo_progress.php?playerID=<?php echo($playerID);?>&elo=<?php echo($player['elo']);?>&type=<?php echo(CLASSIC);?>" width="600" height="250" />
	</div>
	<div id="graphelo960" style="float: right; display: none;">
		<img title="<?php echo _("Display classic chess ranking history");?>" src="images/picto_echange_16.png" onmouseover="this.style.cursor='pointer';" onclick="displayElo();"/> <? echo _("Elo ranking history")." ("._("Chess960").")";?><br>
		<img src="graph_elo_progress.php?playerID=<?php echo($playerID);?>&elo=<?php echo($player['elo960']);?>&type=<?php echo(CHESS960);?>" width="600" height="250" />
	</div>
</div>

<div id="rightbarlarge">
	<div class="navlinks">
		<div class="title">
		<? echo _("Achievements")?>
		</div>
	</div>
	<div class="blockform" style="height: 170px;">
		<? 
		$achievements = getAchievements($player['playerID']);
		$widthTotal = 150;
		for ($i=1; $i<7; $i++)
		{
			$achievement = $achievements[$i];
			$value = $achievement["VAL"];
			$level = $achievement["LVL"];
			$next = $achievement["NXT"];
			$picto = $achievement["PCT"];
			$name = $achievement["NAM"];
			$desc = $achievement["DSC"];
			if ($value < $next)
				$widthValue = intval($widthTotal*$value/$next); 
			else
				$widthValue = $widthTotal;
			$widthNext = $widthTotal - $widthValue;
			?>
			
			<div class="achievement" title="<?echo $name;?> (<?echo _("Level ")." ".$level;?>) : <?echo $desc;?>">
				<span style="width:150px; font-size: 10px;"><?echo $name;?></span><br/>
				<div class="picto" style="position: relative; float: left; background-image: url('images/<?echo($picto);?>'); width: 32px; height: 32px;">
				<span class="newplayer" style="position: absolute; left: 0px; bottom: 0px;"><? echo($level);?></span>
				</div>
				<div class="value" style="width: <? echo($widthValue);?>px;">
				<div style="position: relative; float: left;"><? echo($value);?></div>
				</div>
				<div class="next" style="width: <? echo($widthNext);?>px;">
				<div style="position: relative; float: right;"><? echo($next);?></div>
				</div>
			</div>
			<?
		}
		?>
		
	</div>

	<div class="navlinks">
		<div class="title">
		<? echo _("Games in progress")?>
		</div>
	</div>
	<div class="blockform">
		<form name="existingGames" action="game_board.php" method="post">

        <div class="tabliste">
          <table border="0" width="100%">
            <tr>
              <th width="35%"><? echo _("Whites")?></th>
              <th width="35%"><? echo _("Blacks")?></th>
              <th width="15%"><? echo _("Type")?></th>
              <th width="15%">&nbsp;</th>
            </tr>
            <?
				$tmpGames = mysqli_query($dbh,"SELECT G.gameID, G.eco eco, W.nick whiteNick, B.nick blackNick, G.gameMessage, G.messageFrom, G.type
			                            FROM games G, players W, players B
			                            WHERE (G.gameMessage IS NULL OR gameMessage = '')
			                            AND (G.whitePlayer = ".$player['playerID']." OR G.blackPlayer = ".$player['playerID'].")
			                            AND W.playerID = G.whitePlayer AND B.playerID = G.blackPlayer
			                            ORDER BY G.dateCreated");
				
				if (mysqli_num_rows($tmpGames) == 0)
					echo("<tr><td colspan='4'>"._("No games in progress")."</td></tr>\n");
				else
				{
					while($tmpGame = mysqli_fetch_array($tmpGames, MYSQLI_ASSOC))
					{
						/* White */
						echo("<tr><td>");
						echo($tmpGame['whiteNick']);
						
						/* Black */
						echo ("</td><td>");
						echo($tmpGame['blackNick']);
						
						/* ECO Code */
						if ($tmpGame['type'] == 2)
							echo ("</td><td align='center'>"._("Chess960"));
						else
							echo ("</td><td align='center'>".$tmpGame['eco']);
						
						/* Current Turn */
						echo ("</td><td align=center>");
						echo("<a title = '"._("Open the game")."' href='javascript:loadGame(".$tmpGame['gameID'].")'><img src='images/eye.gif' height='11' border=0 alt=\""._("View")."\"/></a></td></tr>");
			
					}									
				}
			?>
          </table>
        </div>
        <input type="hidden" name="gameID" value="">
        <input type="hidden" name="sharePC" value="no">
        <input type="hidden" name="from" value="toutes">
      </form>
      </div>
      <? if ($_SESSION['playerID'] != $player['playerID']) {?>
      	
		<div class="navlinks">
			<div class="title">
			<? echo _("My games against"); ?> <? echo($player['nick']); ?>
			</div>
		</div>
		<div class="blockform">
			<form name="endedGames" action="game_board.php" method="post">
	        <div class="tabliste">
	          <table border="0" width="100%">
	            <tr>
	              <th width="35%"><? echo _("Whites")?></th>
	              <th width="35%"><? echo _("Blacks")?></th>
	              <th width="15%"><? echo _("Type")?></th>
	              <th width="15%"><? echo _("Result")?></th>
	            </tr>
	            <?
					$tmpGames = mysqli_query($dbh,"SELECT G.gameID, G.eco eco, W.nick whiteNick, B.nick blackNick, G.gameMessage, G.messageFrom, G.type
				                            FROM games G, players W, players B
				                            WHERE (G.gameMessage <> '' AND G.gameMessage <> 'playerInvited' AND G.gameMessage <> 'inviteDeclined')
				                            AND ((G.whitePlayer = ".$player['playerID']." AND G.blackPlayer = ".$_SESSION['playerID'].") OR (G.blackPlayer = ".$player['playerID']." AND G.whitePlayer = ".$_SESSION['playerID']."))
				                            AND W.playerID = G.whitePlayer AND B.playerID = G.blackPlayer
				                            ORDER BY G.dateCreated");
					
					if (mysqli_num_rows($tmpGames) == 0)
						echo("<tr><td colspan='4'>"._("You've never played against this player")."</td></tr>\n");
					else
					{
						while($tmpGame = mysqli_fetch_array($tmpGames, MYSQLI_ASSOC))
						{
							/* White */
							echo("<tr><td>");
							echo($tmpGame['whiteNick']);
							
							/* Black */
							echo ("</td><td>");
							echo($tmpGame['blackNick']);
							
							/* ECO Code */
							if ($tmpGame['type'] == 2)
								echo ("</td><td align='center'>"._("Chess960"));
							else
								echo ("</td><td align='center'>".$tmpGame['eco']);
							
							/* Result */
							if (($tmpGame['gameMessage'] == "playerResigned") && ($tmpGame['messageFrom'] == "white"))
								echo("</td><td align=center><a title = '"._("Open the game")."' href='javascript:loadEndedGame(".$tmpGame['gameID'].")'>0-1</a></td></tr>");
							else if (($tmpGame['gameMessage'] == "playerResigned") && ($tmpGame['messageFrom'] == "black"))
								echo("</td><td align=center><a title = '"._("Open the game")."' href='javascript:loadEndedGame(".$tmpGame['gameID'].")'>1-0</a></td></tr>");
							else if (($tmpGame['gameMessage'] == "checkMate") && ($tmpGame['messageFrom'] == "white"))
								echo("</td><td align=center><a title = '"._("Open the game")."' href='javascript:loadEndedGame(".$tmpGame['gameID'].")'>1-0</a></td></tr>");
							else if ($tmpGame['gameMessage'] == "checkMate")
								echo("</td><td align=center><a title = '"._("Open the game")."' href='javascript:loadEndedGame(".$tmpGame['gameID'].")'>0-1</a></td></tr>");
							else
								echo("</td><td align=center><a title = '"._("Open the game")."' href='javascript:loadEndedGame(".$tmpGame['gameID'].")'>1/2-1/2</a></td></tr>");
						}					
					}
				?>
	          </table>
	        </div>
	        <input type="hidden" name="gameID" value="">
	        <input type="hidden" name="sharePC" value="no">
	        <input type="hidden" name="from" value="toutes">
	      	</form>
	     </div>
		
		<?}?>
</div>
<div id="content">
	<div class="contentactivity">
		<span id="#confirm_delete_activity" style="display: none"><?echo _("Are you sure you want to delete this news ?")?></span>
	    
		<input id="feedType" type="hidden" name="feedType" value="activity">
		<form name="activityGames" action="game_board.php" method="post">
			<div id="activities0" style="display: none;"><img src='images/ajaxloader.gif'/></div>
			<input type="hidden" name="gameID" value="">
			<input type="hidden" name="from" value="encours">
		</form>
		
		<div id="players0" style="display: none;"><img src='images/ajaxloader.gif'/></div>
		<br>		
	</div>
</div>
<?
require 'include/page_footer.php';
mysqli_close($dbh);
?>
