<?
session_start();

/* load settings */
if (!isset($_CONFIG))
	require 'include/config.php';

/* load external functions for setting up new game */
require 'dac/dac_players.php';
require 'dac/dac_activity.php';
require 'dac/dac_games.php';
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

$tmpForEloGames = listEndedGamesForElo($playerID, 0);
$tmpForElo960Games = listEndedGamesForElo($playerID, 2);
$tmpLostGames = listLostGames($playerID);
$tmpDrawGames = listDrawGames($playerID);
$tmpWonGames = listWonGames($playerID);

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
if ($playerID == $_SESSION['playerID'])	
	$attribut_body = "onload='highlightMenu(3)'";
require 'include/page_body.php';
?>
  <div id="contentlarge">
    <div class="contentbody">
	  <?
		if ($errMsg != "")
			echo("<div class='error'>".$errMsg."</div>");
		
		if (mysqli_num_rows($tmpLostGames)+mysqli_num_rows($tmpDrawGames)+mysqli_num_rows($tmpWonGames) > 0)
		{
		?>
		<h2><?echo _("Statistics (no Chess960)");?></h2>
		<div id="games_statistics">
			<img style="border: 1px solid;
	border-color: #e9eaed #dfe0e4 #d0d1d5;" src="graph_results_perc.php?playerID=<?php echo($playerID);?>">
			<img style="border: 1px solid;
	border-color: #e9eaed #dfe0e4 #d0d1d5;float: right;" src="graph_eco_games.php?playerID=<?php echo($playerID);?>">
      	</div>
      	<br>
      	<? }?>
      	<? if ($playerID == $_SESSION['playerID']) {?>
      	<form name="endedGames" action="game_board.php" method="post">
		<h2><?echo _("To take in account for next Elo ranking");?></h2>
        <div class="tabliste">
          <table border="0" width="100%">
            <tr>
              <th width="20%"><?echo _("Whites");?></th>
              <th width="20%"><?echo _("Blacks");?></th>
              <th width="10%"><?echo _("Result");?></th>
              <th width="10%"><?echo _("ELO");?></th>
              <th width="20%"><?echo _("Started");?></th>
              <th width="20%"><?echo _("Last move");?></th>
            </tr>
            
				<?
				if (mysqli_num_rows($tmpForEloGames) == 0)
					echo("<tr><td colspan='6'>"._("No games to take in account")."</td></tr>\n");
				else
				{
					while($tmpGame = mysqli_fetch_array($tmpForEloGames, MYSQLI_ASSOC))
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
							else if ($tmpGame['gameMessage'] == "draw")
								echo("</td><td align=center><a href='javascript:loadEndedGame(".$tmpGame['gameID'].")'>1/2-1/2</a>");
							else
								echo("</td><td>&nbsp;");
						}
						
						/* Opponent ELO */
						$opponentElo = $tmpGame['whiteElo'];
						if ($tmpGame['whitePlayerID'] == $playerID)
							$opponentElo = $tmpGame['blackElo'];
						echo ("</td><td align='center'>".$opponentElo);
						
						/* Start Date */
						$started = new DateTime($tmpGame['dateCreated']);
						$strStarted = $fmt->format($started);
						echo ("</span></td><td align='center'>".$strStarted);
			
						/* Last Move */
						$lastMove = new DateTime($tmpGame['lastMove']);
						$strLastMove = $fmt->format($lastMove);
						echo ("</td><td align='center'>".$strLastMove."</td></tr>\n");
					}
				}
			?>
          </table>
        </div>
        		
		<br/>
		<? }?>
		
      	<? if ($playerID == $_SESSION['playerID']) {?>
      	<form name="endedGames" action="game_board.php" method="post">
		<h2><?echo _("To take in account for next Chess960 Elo ranking");?></h2>
        <div class="tabliste">
          <table border="0" width="100%">
            <tr>
              <th width="20%"><?echo _("Whites");?></th>
              <th width="20%"><?echo _("Blacks");?></th>
              <th width="10%"><?echo _("Result");?></th>
              <th width="10%"><?echo _("ELO");?></th>
              <th width="20%"><?echo _("Started");?></th>
              <th width="20%"><?echo _("Last move");?></th>
            </tr>
            
				<?
				if (mysqli_num_rows($tmpForElo960Games) == 0)
					echo("<tr><td colspan='6'>"._("No games to take in account")."</td></tr>\n");
				else
				{
					while($tmpGame = mysqli_fetch_array($tmpForElo960Games, MYSQLI_ASSOC))
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
							else if ($tmpGame['gameMessage'] == "draw")
								echo("</td><td align=center><a href='javascript:loadEndedGame(".$tmpGame['gameID'].")'>1/2-1/2</a>");
							else
								echo("</td><td>&nbsp;");
						}
						
						/* Opponent ELO */
						$opponentElo = $tmpGame['whiteElo960'];
						if ($tmpGame['whitePlayerID'] == $playerID)
							$opponentElo = $tmpGame['blackElo960'];
						echo ("</td><td align='center'>".$opponentElo);
						
						/* Start Date */
						$started = new DateTime($tmpGame['dateCreated']);
						$strStarted = $fmt->format($started);
						echo ("</span></td><td align='center'>".$strStarted);
			
						/* Last Move */
						$lastMove = new DateTime($tmpGame['lastMove']);
						$strLastMove = $fmt->format($lastMove);
						echo ("</td><td align='center'>".$strLastMove."</td></tr>\n");
					}
				}
			?>
          </table>
        </div>
        		
		<br/>
		<? }?>
				
    	<form name="endedGames" action="game_board.php" method="post">
        
        <A NAME="defaites"></A>
		<h2><?echo _("Lost classic games");?> (<?echo(mysqli_num_rows($tmpLostGames));?>) <?if (isset($_GET['playerID'])) echo(_("of")." ".$player['nick']);?></h2>
        <div class="tabliste">
          <table border="0" width="100%">
            <tr>
              <th width="20%"><?echo _("Whites");?></th>
              <th width="20%"><?echo _("Blacks");?></th>
              <th width="10%"><?echo _("Result");?></th>
              <th width="10%"><?echo _("ECO");?></th>
              <th width="20%"><?echo _("Started");?></th>
              <th width="20%"><?echo _("Last move");?></th>
            </tr>
            
	<?
	if (mysqli_num_rows($tmpLostGames) == 0)
		echo("<tr><td colspan='6'>"._("No lost games")."</td></tr>\n");
	else
	{
		while($tmpGame = mysqli_fetch_array($tmpLostGames, MYSQLI_ASSOC))
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
			echo ("</td><td align='center'><span title=\"".$tmpGame['ecoName']."\">".$tmpGame['eco']);
			
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
        		
		<br/>
		
		<A NAME="nulles"></A>
		<h2><?echo _("Draw classic games");?> (<?echo(mysqli_num_rows($tmpDrawGames));?>) <?if (isset($_GET['playerID'])) echo(_("of")." ".$player['nick']);?></h2>
        <div class="tabliste">
          <table border="0" width="100%">
            <tr>
              <th width="20%"><?echo _("Whites");?></th>
              <th width="20%"><?echo _("Blacks");?></th>
              <th width="10%"><?echo _("Result");?></th>
              <th width="10%"><?echo _("ECO");?></th>
              <th width="20%"><?echo _("Started");?></th>
              <th width="20%"><?echo _("Last move");?></th>
            </tr>
            
	<?
	if (mysqli_num_rows($tmpDrawGames) == 0)
		echo("<tr><td colspan='6'>"._("No draw games")."</td></tr>\n");
	else
	{
		while($tmpGame = mysqli_fetch_array($tmpDrawGames, MYSQLI_ASSOC))
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
			echo ("</td><td align='center'><span title=\"".$tmpGame['ecoName']."\">".$tmpGame['eco']);
			
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

	<br/>
	
	<A NAME="victoires"></A>
	<h2><?echo _("Won classic games");?> (<?echo(mysqli_num_rows($tmpWonGames));?>) <?if (isset($_GET['playerID'])) echo(_("of")." ".$player['nick']);?></h2>
	
        <div class="tabliste">
          <table border="0" width="100%">
            <tr>
              <th width="20%"><?echo _("Whites");?></th>
              <th width="20%"><?echo _("Blacks");?></th>
              <th width="10%"><?echo _("Result");?></th>
              <th width="10%"><?echo _("ECO");?></th>
              <th width="20%"><?echo _("Started");?></th>
              <th width="20%"><?echo _("Last move");?></th>
            </tr>
           
	<?
	if (mysqli_num_rows($tmpWonGames) == 0)
		echo("<tr><td colspan='6'>"._("No won games")."</td></tr>\n");
	else
	{
		while($tmpGame = mysqli_fetch_array($tmpWonGames, MYSQLI_ASSOC))
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
			echo ("</td><td align='center'><span title=\"".$tmpGame['ecoName']."\">".$tmpGame['eco']);
			
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
        <input type="hidden" name="from" value="archive">
      </form>

    </div>
</div>
<?
require 'include/page_footer.php';
mysqli_close($dbh);
?>
