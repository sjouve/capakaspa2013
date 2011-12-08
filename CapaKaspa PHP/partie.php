<?	require 'mobilecheck.php';
	session_start();

	/* load settings */
	if (!isset($_CONFIG))
		require 'config.php';
	
	/* define constants */
	require 'chessconstants.php';

	/* include outside functions */
	require_once('chessutils.php');
	require 'gui_games.php';
	require 'bwc_games.php';
	require 'bwc_board.php';

	/* connect to database */
	require 'connectdb.php';
	
	/* check session status */
	require 'sessioncheck.php';
	
	/* check if loading game 
	if (isset($_POST['gameID']))
		$_SESSION['gameID'] = $_POST['gameID'];*/
	
	/* debug flag */
	define ("DEBUG", 0);

	/* ajoute un message au dialogue */
	$isMessage = isset($_POST['addMessage']) ? $_POST['addMessage']:Null;
	if ($isMessage == 'yes')
	{
		$tmpQuery = "UPDATE games SET dialogue = concat('[".date("d/m/y H:i")."][".$_SESSION['nick']."] ".strip_tags($_POST['message'])."\n', ifnull(dialogue, ' ')) WHERE gameID = ".$_POST['gameID'];
		mysql_query($tmpQuery);
	}
	
	/* load game */
	$Test = isset($_POST['isInCheck']) ? $_POST['isInCheck']:Null;
	$isInCheck = ($Test == 'true');
	$isCheckMate = false;
	$isPromoting = false;
	$isUndoing = false;
	
	loadHistory();
	$tmpGame = loadGame();
	processMessages();
	$pgnstring ="";
    $TestPromotion = isset($_POST['promotion']) ? $_POST['promotion']:Null;
    $TestFromRow = isset($_POST['fromRow']) ? $_POST['fromRow']:Null;
	
	if ($_SESSION['playerID'] == $tmpGame['whitePlayer'] || $_SESSION['playerID'] == $tmpGame['whitePlayer'])
	{
	    // Les absences de l'adversaires
		if ($_SESSION['playerID'] == $tmpGame['whitePlayer'])
		{	
			$res_adv_vacation = getCurrentVacation($tmpGame['blackPlayer']);
		}
		if ($_SESSION['playerID'] == $tmpGame['blackPlayer'])
		{
			$res_adv_vacation = getCurrentVacation($tmpGame['whitePlayer']);
		}
		
		
		// Les absences du joueur
		$res_vacation = getCurrentVacation($_SESSION['playerID']);
		
		global $nb_game_vacation;
		$nb_game_vacation = mysql_num_rows($res_adv_vacation) + mysql_num_rows($res_vacation);
	}
	
	// Pièces capturées
	// TODO Mettre cette requete dans dac_games
	$f=mysql_query("select curPiece,curColor,replaced from history where replaced > '' and gameID =  '".$_POST['gameID']."' order by curColor desc , replaced desc");
	
			
	if ($isUndoing)
	{
		@mysql_query("BEGIN");
		doUndo();
		saveGame();
		@mysql_query("COMMIT");
	}
	elseif (($TestPromotion != "") && ($_POST['toRow'] != "") && ($_POST['toCol'] != ""))
	{
		@mysql_query("BEGIN");
		savePromotion();
		$board[$_POST['toRow']][$_POST['toCol']] = $_POST['promotion'] | ($board[$_POST['toRow']][$_POST['toCol']] & BLACK);
		saveGame();
		@mysql_query("COMMIT");
	}
	elseif (($TestFromRow != "") && ($_POST['fromCol'] != "") && ($_POST['toRow'] != "") && ($_POST['toCol'] != ""))
	{
		/* ensure it's the current player moving				 */
		/* NOTE: if not, this will currently ignore the command...               */
		/*       perhaps the status should be instead?                           */
		/*       (Could be confusing to player if they double-click or something */
		$tmpIsValid = true;
		if (($numMoves == -1) || ($numMoves % 2 == 1))
		{
			/* White's move... ensure that piece being moved is white */
			if ((($board[$_POST['fromRow']][$_POST['fromCol']] & BLACK) != 0) || ($board[$_POST['fromRow']][$_POST['fromCol']] == 0))
				/* invalid move */
				$tmpIsValid = false;
		}
		else
		{
			/* Black's move... ensure that piece being moved is black */
			if ((($board[$_POST['fromRow']][$_POST['fromCol']] & BLACK) != BLACK) || ($board[$_POST['fromRow']][$_POST['fromCol']] == 0))
				/* invalid move */
				$tmpIsValid = false;
		}
		
		if ($tmpIsValid)
		{
			@mysql_query("BEGIN");
			
			$res = saveHistory();
			//echo(microtime()." history : ".$res);
			if (!$res)
				@mysql_query("ROLLBACK");
			
			doMove();
			//echo(microtime()." move : ");
			
			$res = saveGame();
			//echo(microtime()." game : ".$res);
			if (!$res)
			{
				@mysql_query("ROLLBACK");
				//echo(microtime()." game : ROLLBACK");
			}
				
			if ($res)
			{
				
				@mysql_query("COMMIT");
				//echo(microtime()." game : COMMIT");
				sendEmailNotification($history, $isPromoting, $numMoves, $isInCheck);
				//echo(microtime()." mail : ".$res);
			}
			
		}
	}

	//mysql_close();
	
	/* find out if it's the current player's turn */
	if (( (($numMoves == -1) || (($numMoves % 2) == 1)) && ($playersColor == "white"))
			|| ((($numMoves % 2) == 0) && ($playersColor == "black")))
		$isPlayersTurn = true;
	else
		$isPlayersTurn = false;

	if ($_SESSION['isSharedPC'])
		$titre_page = '';
	else if ($isPlayersTurn)
        $titre_page = "Echecs en différé - Votre coup";
	else
        $titre_page = "Echecs en différé - Le coup de l'adversaire";
	$desc_page = "Jouer aux échecs en différé. C'est votre partie, à vous de jouer.";
    require 'page_header.php';
    //echo("<meta HTTP-EQUIV='Pragma' CONTENT='no-cache'>\n");
?>
<script type="text/javascript">
/* transfer board data to javacripts */
<? writeJSboard(); ?>
<? writeJShistory(); ?>

if (DEBUG)
	alert("Game initilization complete!");
</script>
<script type="text/javascript" src="javascript/chessutils.js">
 /* these are utility functions used by other functions */
</script>
<script type="text/javascript" src="javascript/commands.js">
// these functions interact with the server
</script>
<script type="text/javascript" src="javascript/validation.js">
// these functions are used to test the validity of moves
</script>
<script type="text/javascript" src="javascript/isCheckMate.js">
// these functions are used to test the validity of moves
</script>
<script type="text/javascript" src="javascript/squareclicked.js">
// this is the main function that interacts with the user everytime they click on a square
</script>
<script type="text/javascript" src="iechecs/js/action.js">
 /* Actions échiquier en ligne */
</script>
<?
    $image_bandeau = 'bandeau_capakaspa_zone.jpg';

    if ($_POST['from'] == "encours" )
        $barre_progression = "<a href='/'>Accueil</a> > Echecs en différé > <a href='tableaubord.php'>Mes parties</a> > Une partie";
    else if ($_POST['from'] == "toutes")
        $barre_progression = "<a href='/'>Accueil</a> > Echecs en différé > <a href='listeparties.php'>Les autres parties en cours</a> > Une partie";
    else if ($_POST['from'] == "archive")
        $barre_progression = "<a href='/'>Accueil</a> > Echecs en différé > <a href='partiesterminees.php'>Les parties terminées</a> > Une partie";

    require 'page_body.php';
?>
  <div id="contentlarge">
    <div class="blogbody">
      <table>
      		<tr>
	      		<td valign="middle"><img src="images/ampoule.jpg"></td> 
	      		<td valign="middle">Utilisez l'échiquier en ligne pour manipuler votre partie mais aussi pour revoir les règles du jeu...</td>
        	</tr>
        </table>
        <?
        if ($_SESSION['playerID'] == $tmpGame['whitePlayer'] || $_SESSION['playerID'] == $tmpGame['whitePlayer'])
		{
        	if (mysql_num_rows($res_adv_vacation) > 0)
				echo("<div class='success'>Votre adversaire est absent en ce moment ! La partie est ajournée.</div>");

			else
				echo("<br/>");
		}
		?>
        
		<form name="gamedata" method="post" action="partie.php">
	  <table border="0">
        <tr valign="top" align="center">
          <td>
              <?
		if ($isPromoting)
			writePromotion(false);
	?>
              <?
		if ($isUndoRequested)
			writeUndoRequest(false);
	?>
              <?
		if ($isDrawRequested)
			writeDrawRequest(false);
	?>
              <? drawboard(true); ?>
              <nobr>
              <input type="button" name="btnUndo" value="Annuler le coup" <? if (isBoardDisabled()) echo("disabled='yes'"); else echo ("onClick='undo()'"); ?>>
              <!-- <input type="button" name="btnReload" value="Actualiser l'échiquier" onClick="document.gamedata.submit();"> -->
              <input type="button" name="btnDraw" value="Proposer nulle" <? if (isBoardDisabled()) echo("disabled='yes'"); else echo ("onClick='draw()'"); ?>>
              <input type="button" name="btnResign" value="Abandonner" <? if (isBoardDisabled()) echo("disabled='yes'"); else echo ("onClick='resigngame()'"); ?>>
			  <a href="manuel-utilisateur-jouer-echecs-capakaspa.pdf#page=6" target="_blank"><img src="images/point-interrogation.gif" border="0"/></a>
              
              </nobr>
              
			  
              
              <input type="hidden" name="gameID" value="<? echo ($_POST['gameID']); ?>">
              <input type="hidden" name="requestUndo" value="no">
              <input type="hidden" name="requestDraw" value="no">
              <input type="hidden" name="resign" value="no">
              <input type="hidden" name="fromRow" value="<? if ($isPromoting) echo ($TestFromRow); ?>">
              <input type="hidden" name="fromCol" value="<? if ($isPromoting) echo ($_POST['fromCol']); ?>">
              <input type="hidden" name="toRow" value="<? if ($isPromoting) echo ($_POST['toRow']); ?>">
              <input type="hidden" name="toCol" value="<? if ($isPromoting) echo ($_POST['toCol']); ?>">
              <input type="hidden" name="isInCheck" value="false">
              <input type="hidden" name="isCheckMate" value="false">
            
            </td>
          <td width="15">&nbsp;</td>
          <td><? writeStatus(); ?>
            
           
		   
		    <? 
				$listeCoups = writeHistory();
				$pgnstring = getPGN($tmpGame['whiteNick'], $tmpGame['blackNick'], $tmpGame['type'], $tmpGame['flagBishop'], $tmpGame['flagKnight'], $tmpGame['flagRook'], $tmpGame['flagQueen'], $listeCoups);
			?>
			<img src="images/puce.gif"/> <a href="javascript:void(0)" onclick='window.open("http://www.iechecs.com/iechecs.htm?app,<? if ($playersColor == "black") echo("p=t,"); ?> pgn=<? echo($pgnstring); ?>","iechecs","height=413,width=675,status=no,toolbar=no,menubar=no,location=no,resizable=yes")' >Ouvrir la partie dans l'échiquier en ligne</a>
			
			<br />
			
			<input type="hidden" name="from" value="<? echo($_POST['from']) ?>" />
          </td>
        </tr>
        <tr>
        <td colspan="3">
        <?
					
					echo "<TABLE widht='100%'>";
					echo "<TR><TD>";
					$c=0;
					$d=0;
					
					while($row=mysql_fetch_array($f, MYSQL_ASSOC)){
					
						if(ereg("white",$row['curColor'])){
							$color="black_";
							$c++;
						}
						else {
							$color="white_";
							}
					
						if($c==1){
							//echo"\n</TD></TR><TR><TD>";
							$d=0;
						}
					
						$d++;
					
						echo"\n<img src=\"images/mosaique/".$color.$row['replaced'].".gif\">";
						/*if(($d%8)==0){
							echo "<BR>\n";
						}*/
					
					} // End while
					
					echo "</TD></TR></TABLE>";
					
              
              ?>
            </td>
        </tr>
      </table>
      
      <center><script type="text/javascript"><!--
      google_ad_client = "ca-pub-8069368543432674";
      /* CapaKaspa Partie Bandeau Discussion */
      google_ad_slot = "9888264481";
      google_ad_width = 468;
      google_ad_height = 60;
      //-->
      </script>
      <script type="text/javascript"
      src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
      </script>
		</center>
      <h3>Commentaires</h3>
 		<p>     
      	<input type="text" name="message" maxlength="255" size="87" />
		<input type="button" name="btnSend" value="Poster" <? echo ("onClick='send()'"); ?> />
      	<TEXTAREA NAME='dialogue' COLS='74' ROWS='8' readonly><? echo($dialogue); ?></TEXTAREA>
			</p>	
		<input type="hidden" name="addMessage" value="no" />
	  </form>
    </div>
  </div>
<?
    require 'page_footer.php';
    mysql_close();
?>
