<?
require 'include/mobilecheck.php';

session_start();

/* load settings */
if (!isset($_CONFIG))
	require 'include/config.php';

/* load external functions for setting up new game */
require 'dac/dac_players.php';
require 'bwc/bwc_common.php';
require 'bwc/bwc_chessutils.php';
require 'bwc/bwc_games.php';
require 'bwc/bwc_players.php';
	
/* connect to database */
require 'include/connectdb.php';

$errMsg = "";

/* check session status */
require 'include/sessioncheck.php';

require 'include/localization.php';

$fmt = new IntlDateFormatter(getenv("LC_ALL"), IntlDateFormatter::SHORT, IntlDateFormatter::SHORT);

/* Id du joueur */
$playerID = isset($_GET['playerID']) ? $_GET['playerID']:$_SESSION['playerID'];
// Si le joueur n'est pas celui connecté on récupère ces infos
if (isset($_GET['playerID']))
    $player = getPlayer($playerID);
	
$titre_page = _("Ended games");
$desc_page = _("All ended chess games of a player");
require 'include/page_header.php';
?>
<script src="javascript/menu.js" type="text/javascript"></script>
<script type="text/javascript">
function loadEndedGame(gameID)
{
	document.endedGames.gameID.value = gameID;
	document.endedGames.submit();
}
</script>
<?
$attribut_body = "onload='highlightMenu(3)'";
require 'include/page_body.php';
?>
  <div id="contentlarge">
    <div class="contentbody">
	  <?
		if ($errMsg != "")
			echo("<div class='error'>".$errMsg."</div>");
		?>
    
      <form name="endedGames" action="game_board.php" method="post">
        <?
    	$tmpGames = mysql_query("SELECT G.gameID gameID, G.eco eco, E.name ecoName, W.playerID whitePlayerID, W.nick whiteNick, B.playerID blackPlayerID, B.nick blackNick, G.gameMessage gameMessage, G.messageFrom messageFrom, G.dateCreated, G.lastMove
                                FROM games G, players W, players B, eco E 
								WHERE (G.gameMessage <> '' AND G.gameMessage <> 'playerInvited' AND G.gameMessage <> 'inviteDeclined')
                                AND (G.whitePlayer = ".$playerID." OR G.blackPlayer = ".$playerID.")
                                AND ((G.gameMessage = 'playerResigned' AND G.messageFrom = 'white' AND G.whitePlayer = ".$playerID.")
                                    OR (G.gameMessage = 'playerResigned' AND G.messageFrom = 'black' AND G.blackPlayer = ".$playerID.")
                                    OR (G.gameMessage = 'checkMate' AND G.messageFrom = 'black' AND G.whitePlayer = ".$playerID.")
                                    OR (G.gameMessage = 'checkMate' AND G.messageFrom = 'white' AND G.blackPlayer = ".$playerID.")) 
								AND W.playerID = G.whitePlayer AND B.playerID = G.blackPlayer
								AND G.eco = E.eco
                                ORDER BY G.eco ASC, G.lastMove DESC");
		?>
        <A NAME="defaites"></A>
		<h3><?echo _("Lost games");?> (<?echo(mysql_num_rows($tmpGames));?>) <?if (isset($_GET['playerID'])) echo(_("of")." ".$player['nick']);?></h3>
        <div class="tabliste">
          <table border="0" width="650">
            <tr>
              <th width="17%"><?echo _("Whites");?></th>
              <th width="17%"><?echo _("Blacks");?></th>
              <th width="8%"><?echo _("Result");?></th>
              <th width="8%"><?echo _("ECO");?></th>
              <th width="25%"><?echo _("Started");?></th>
              <th width="25%"><?echo _("Last move");?></th>
            </tr>
            
	<?
	if (mysql_num_rows($tmpGames) == 0)
		echo("<tr><td colspan='6'>"._("No lost games")."</td></tr>\n");
	else
	{
		while($tmpGame = mysql_fetch_array($tmpGames, MYSQL_ASSOC))
		{
			/* White */
			echo("<tr><td>");
			echo("<a href='player_view.php?playerID=".$tmpGame['whitePlayerID']."'>".$tmpGame['whiteNick']."</a>");
			
			/* Black */
			echo ("</td><td>");
			echo("<a href='player_view.php?playerID=".$tmpGame['blackPlayerID']."'>".$tmpGame['blackNick']."</a>");
			
			/* Status */
			if (is_null($tmpGame['gameMessage']))
				echo("</td><td>&nbsp;");
			else
			{
				if (($tmpGame['gameMessage'] == "playerResigned") && ($tmpGame['messageFrom'] == "white"))
					echo("</td><td align=center><a href='javascript:loadEndedGame(".$tmpGame['gameID'].")'>0-1</a>");
				else if (($tmpGame['gameMessage'] == "playerResigned") && ($tmpGame['messageFrom'] == "black"))
					echo("</td><td align=center><a href='javascript:loadEndedGame(".$tmpGame['gameID'].")'>1-0</a>");
				else if (($tmpGame['gameMessage'] == "checkMate") && ($tmpGame['messageFrom'] == "white"))
					echo("</td><td align=center><a href='javascript:loadEndedGame(".$tmpGame['gameID'].")'>1-0</a>");
				else if ($tmpGame['gameMessage'] == "checkMate")
					echo("</td><td align=center><a href='javascript:loadEndedGame(".$tmpGame['gameID'].")'>0-1</a>");
				else
					echo("</td><td>&nbsp;");
			}
			
			/* ECO Code */
			echo ("</td><td align='center'><span title='".$tmpGame['ecoName']."'>".$tmpGame['eco']);
			
			$started = new DateTime($tmpGame['dateCreated']);
			$strStarted = $fmt->format($started);
			$lastMove = new DateTime($tmpGame['lastMove']);
			$strLastMove = $fmt->format($lastMove);
			
			/* Start Date */
			echo ("</span></td><td align='center'>".$strStarted);

			/* Last Move */
			echo ("</td><td align='center'>".$strLastMove."</td></tr>\n");
		}
	}
?>
          </table>
        </div>
        
        <?
	$tmpGames = mysql_query("SELECT G.gameID, G.eco eco, E.name ecoName, W.playerID whitePlayerID, W.nick whiteNick, B.playerID blackPlayerID, B.nick blackNick, G.gameMessage, G.messageFrom, G.dateCreated, G.lastMove
                                FROM games G, players W, players B, eco E
                                WHERE (G.gameMessage <> '' AND G.gameMessage <> 'playerInvited' AND G.gameMessage <> 'inviteDeclined')
                                AND (G.whitePlayer = ".$playerID." OR G.blackPlayer = ".$playerID.")
                                AND G.gameMessage = 'draw'
                                AND W.playerID = G.whitePlayer AND B.playerID = G.blackPlayer
                                AND G.eco = E.eco
                                ORDER BY E.eco ASC, G.lastMove DESC");?>
		
		<br/>
		
		<A NAME="nulles"></A>
		<h3><?echo _("Draw games");?> (<?echo(mysql_num_rows($tmpGames));?>) <?if (isset($_GET['playerID'])) echo(_("of")." ".$player['nick']);?></h3>
        <div class="tabliste">
          <table border="0" width="650">
            <tr>
              <th width="17%"><?echo _("Whites");?></th>
              <th width="17%"><?echo _("Blacks");?></th>
              <th width="8%"><?echo _("Result");?></th>
              <th width="8%"><?echo _("ECO");?></th>
              <th width="25%"><?echo _("Started");?></th>
              <th width="25%"><?echo _("Last move");?></th>
            </tr>
            
	<?
	if (mysql_num_rows($tmpGames) == 0)
		echo("<tr><td colspan='6'>"._("No draw games")."</td></tr>\n");
	else
	{
		while($tmpGame = mysql_fetch_array($tmpGames, MYSQL_ASSOC))
		{
			/* White */
			echo("<tr><td>");
			echo("<a href='player_view.php?playerID=".$tmpGame['whitePlayerID']."'>".$tmpGame['whiteNick']."</a>");
			
			/* Black */
			echo ("</td><td>");
			echo("<a href='player_view.php?playerID=".$tmpGame['blackPlayerID']."'>".$tmpGame['blackNick']."</a>");

			/* Status */
			if (is_null($tmpGame['gameMessage']))
				echo("</td><td>&nbsp;");
			else
			{

					echo("</td><td align=center><a href='javascript:loadEndedGame(".$tmpGame['gameID'].")'>1/2-1/2</a>");
			}
			/* ECO Code */
			echo ("</td><td align='center'><span title='".$tmpGame['ecoName']."'>".$tmpGame['eco']);
			
			$started = new DateTime($tmpGame['dateCreated']);
			$strStarted = $fmt->format($started);
			$lastMove = new DateTime($tmpGame['lastMove']);
			$strLastMove = $fmt->format($lastMove);
			
			/* Start Date */
			echo ("</td><td align='center'>".$strStarted);

			/* Last Move */
			echo ("</td><td align='center'>".$strLastMove."</td></tr>\n");
		}
	}
?>
          </table>
        </div>
         
          <?
	$tmpGames = mysql_query("SELECT G.gameID, G.eco eco, E.name ecoName, W.playerID whitePlayerID, W.nick whiteNick, B.playerID blackPlayerID, B.nick blackNick, G.gameMessage, G.messageFrom, G.dateCreated, G.lastMove
                                FROM games G, players W, players B, eco E WHERE (G.gameMessage <> '' AND G.gameMessage <> 'playerInvited' AND G.gameMessage <> 'inviteDeclined')
                                AND (G.whitePlayer = ".$playerID." OR G.blackPlayer = ".$playerID.")
                                AND ((G.gameMessage = 'playerResigned' AND G.messageFrom = 'white' AND G.blackPlayer = ".$playerID.")
                                    OR (G.gameMessage = 'playerResigned' AND G.messageFrom = 'black' AND G.whitePlayer = ".$playerID.")
                                    OR (G.gameMessage = 'checkMate' AND G.messageFrom = 'black' AND G.blackPlayer = ".$playerID.")
                                    OR (G.gameMessage = 'checkMate' AND G.messageFrom = 'white' AND G.whitePlayer = ".$playerID."))
                                AND W.playerID = G.whitePlayer AND B.playerID = G.blackPlayer
                                AND G.eco = E.eco
                                ORDER BY E.eco ASC, G.lastMove DESC");?>
                                

	<br/>
	
	<A NAME="victoires"></A>
	<h3><?echo _("Won games");?> (<?echo(mysql_num_rows($tmpGames));?>) <?if (isset($_GET['playerID'])) echo(_("of")." ".$player['nick']);?></h3>
	
        <div class="tabliste">
          <table border="0" width="650">
            <tr>
              <th width="17%"><?echo _("Whites");?></th>
              <th width="17%"><?echo _("Blacks");?></th>
              <th width="8%"><?echo _("Result");?></th>
              <th width="8%"><?echo _("ECO");?></th>
              <th width="25%"><?echo _("Started");?></th>
              <th width="25%"><?echo _("Last move");?></th>
            </tr>
           
	<?
	if (mysql_num_rows($tmpGames) == 0)
		echo("<tr><td colspan='6'>"._("No won games")."</td></tr>\n");
	else
	{
		while($tmpGame = mysql_fetch_array($tmpGames, MYSQL_ASSOC))
		{
			/* White */
			echo("<tr><td>");
			echo("<a href='player_view.php?playerID=".$tmpGame['whitePlayerID']."'>".$tmpGame['whiteNick']."</a>");
			
			/* Black */
			echo ("</td><td>");
			echo("<a href='player_view.php?playerID=".$tmpGame['blackPlayerID']."'>".$tmpGame['blackNick']."</a>");

			/* Status */
			if (is_null($tmpGame['gameMessage']))
				echo("</td><td>&nbsp;");
			else
			{

                if (($tmpGame['gameMessage'] == "playerResigned") && ($tmpGame['messageFrom'] == "white"))
					echo("</td><td align=center><a href='javascript:loadEndedGame(".$tmpGame['gameID'].")'>0-1</a>");
				else if (($tmpGame['gameMessage'] == "playerResigned") && ($tmpGame['messageFrom'] == "black"))
					echo("</td><td align=center><a href='javascript:loadEndedGame(".$tmpGame['gameID'].")'>1-0</a>");
				else if (($tmpGame['gameMessage'] == "checkMate") && ($tmpGame['messageFrom'] == "white"))
					echo("</td><td align=center><a href='javascript:loadEndedGame(".$tmpGame['gameID'].")'>1-0</a>");
				else if ($tmpGame['gameMessage'] == "checkMate")
					echo("</td><td align=center><a href='javascript:loadEndedGame(".$tmpGame['gameID'].")'>0-1</a>");
				else
					echo("</td><td>&nbsp;");
			}
			/* ECO Code */
			echo ("</td><td align='center'><span title='".$tmpGame['ecoName']."'>".$tmpGame['eco']);
			
			$started = new DateTime($tmpGame['dateCreated']);
			$strStarted = $fmt->format($started);
			$lastMove = new DateTime($tmpGame['lastMove']);
			$strLastMove = $fmt->format($lastMove);
			
			/* Start Date */
			echo ("</td><td align='center'>".$strStarted);

			/* Last Move */
			echo ("</td><td align='center'>".$strLastMove."</td></tr>\n");
		}
	}
?>
          </table>
        </div>
        
        <input type="hidden" name="gameID" value="">
        <input type="hidden" name="sharePC" value="no">
        <input type="hidden" name="from" value="archive">
      </form>

    </div>
  </div>
<?
require 'include/page_footer.php';
mysql_close();
?>
