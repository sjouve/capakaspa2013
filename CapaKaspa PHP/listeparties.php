<?
	session_start();

	/* load settings */
	if (!isset($_CONFIG))
		require 'config.php';

	/* load external functions for setting up new game */
	require 'bwc_games.php';
	
	/* connect to database */
	require 'connectdb.php';

	$errMsg = "";

	/* check session status */
	require 'sessioncheck.php';

	/* set default playing mode to different PCs (as opposed to both players sharing a PC) */
	$_SESSION['isSharedPC'] = false;
	
	$titre_page = "Echecs en différé - Les autres parties en cours";
	$desc_page = "Jouer aux échecs en différé. Retrouvez la liste de toutes les parties en cours dans la zone de jeu en différé.";
    require 'page_header.php';
?>
    <script type="text/javascript">

		function loadGame(gameID)
		{
			
			document.existingGames.gameID.value = gameID;
			document.existingGames.submit();
		}

<? if ($CFG_USEEMAILNOTIFICATION) { ?>
		function testEmail()
		{
			document.preferences.ToDo.value = "TestEmail";
			document.preferences.submit();
		}
<? } ?>
	</script>
<?
    $image_bandeau = 'bandeau_capakaspa_zone.jpg';
    $barre_progression = "<a href='/'>Accueil</a> > Echecs en différé > Les autres parties en cours";
    require 'page_body.php';
?>
  <div id="contentlarge">
    <div class="blogbody">
	  <?
		if ($errMsg != "")
			echo("<div class='error'>".$errMsg."</div>");
		?>
		
      <form name="existingGames" action="partie.php" method="post">
        <h3> Toutes les parties en cours sur le site</h3>
        <div id="tabliste">
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
	$tmpGames = mysql_query("SELECT G.gameID, G.eco eco, W.playerID whitePlayerID, W.nick whiteNick, B.playerID blackPlayerID, B.nick blackNick, G.gameMessage, G.messageFrom, DATE_FORMAT(G.dateCreated, '%d/%m/%Y %T') dateCreatedF, DATE_FORMAT(G.lastMove, '%d/%m/%Y %T') lastMove
                            FROM games G, players W, players B
                            WHERE G.gameMessage = ''
                            AND (G.whitePlayer != ".$_SESSION['playerID']." AND G.blackPlayer != ".$_SESSION['playerID'].")
                            AND W.playerID = G.whitePlayer AND B.playerID = G.blackPlayer
                            ORDER BY G.dateCreated");
	
	if (mysql_num_rows($tmpGames) == 0)
		echo("<tr><td colspan='6'>Il n'y a aucune partie en cours sur le site</td></tr>\n");
	else
	{
		while($tmpGame = mysql_fetch_array($tmpGames, MYSQL_ASSOC))
		{
			/* White */
			echo("<tr><td>");
			echo("<a href='profil_consultation.php?playerID=".$tmpGame['whitePlayerID']."'>".$tmpGame['whiteNick']."</a>");
			
			/* Black */
			echo ("</td><td>");
			echo("<a href='profil_consultation.php?playerID=".$tmpGame['blackPlayerID']."'>".$tmpGame['blackNick']."</a>");
			
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
		
		<center><script type="text/javascript"><!--
    google_ad_client = "ca-pub-8069368543432674";
    /* CapaKaspa Liste parties Bandeau Haut */
    google_ad_slot = "2886115401";
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
<?
    require 'page_footer.php';
    mysql_close();
?>
