<?
	session_start();

	/* load settings */
	if (!isset($_CONFIG))
		require 'include/config.php';

	/* load external functions for setting up new game */
	//require 'bwc/bwc_games.php';
	require 'bwc/bwc_common.php';
	
	/* connect to database */
	require 'include/connectdb.php';

	$errMsg = "";

	/* check session status */
	require 'include/sessioncheck.php';

	/* set default playing mode to different PCs (as opposed to both players sharing a PC) */
	$_SESSION['isSharedPC'] = false;
	
	$titre_page = "Echecs en différé - Les autres parties en cours";
	$desc_page = "Jouer aux échecs en différé. Retrouvez la liste de toutes les parties en cours dans la zone de jeu en différé.";
    require 'include/page_header.php';
?>
    <script type="text/javascript">

		function loadGame(gameID)
		{
			document.existingGames.gameID.value = gameID;
			document.existingGames.submit();
		}

		function loadPage(page)
		{
			document.searchGames.page.value = page;
			document.searchGames.submit();
		}
		
	</script>
<?
    $image_bandeau = 'bandeau_capakaspa_zone.jpg';
    $barre_progression = "<a href='/'>Accueil</a> > Echecs en différé > Les autres parties en cours";
    require 'include/page_body.php';
?>
	<div id="contentlarge">
    	<div class="blogbody">
	  	<?
		if ($errMsg != "")
			echo("<div class='error'>".$errMsg."</div>");
		?>
		<h3> Toutes les parties en cours sur le site</h3>
		<?
            $pge = isset($_POST['page']) ? $_POST['page']:0;
            $limit = 25;
			$requete = "SELECT G.gameID, G.eco eco, W.playerID whitePlayerID, W.nick whiteNick, B.playerID blackPlayerID, B.nick blackNick, G.gameMessage, G.messageFrom, DATE_FORMAT(G.dateCreated, '%d/%m/%Y %T') dateCreatedF, DATE_FORMAT(G.lastMove, '%d/%m/%Y %T') lastMove
                            FROM games G, players W, players B
                            WHERE G.gameMessage = ''
                            AND (G.whitePlayer != ".$_SESSION['playerID']." AND G.blackPlayer != ".$_SESSION['playerID'].")
                            AND W.playerID = G.whitePlayer AND B.playerID = G.blackPlayer
                            ORDER BY G.dateCreated DESC";
			
			$debut = $pge*$limit;
			$total = mysql_query($requete) or die("SQL1 : ".mysql_error()); 
			$nb_tot = mysql_num_rows($total);
			$nbpages = ceil($nb_tot/$limit); // ceil = plafond : pour arrondir à la valeur supérieure
			$resultats = mysql_query($requete." limit ".$debut.",".$limit) or die("SQL1 : ".mysql_error()); 
		?>
        
	        <form name="searchGames" action="listeparties.php" method="post">
	        	<?
	         	displayPageNav($pge, $limit, $nb_tot, $nbpages);
	        	?>
	        	<input type="hidden" name="page" value="">
	        </form>
        
        <div id="tabliste">
          	<form name="existingGames" action="partie.php" method="post">
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
			if (mysql_num_rows($resultats) == 0)
				echo("<tr><td colspan='6'>Il n'y a aucune partie en cours sur le site</td></tr>\n");
			else
			{
				while($tmpGame = mysql_fetch_array($resultats, MYSQL_ASSOC))
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
          	<input type="hidden" name="gameID" value="">
        	<input type="hidden" name="sharePC" value="no">
        	<input type="hidden" name="from" value="toutes">
      		</form>
         	
		</div>
        
		<br/>
		
		<center>
		<script type="text/javascript"><!--
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
    require 'include/page_footer.php';
    mysql_close();
?>
