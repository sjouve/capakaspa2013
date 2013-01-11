<?
	session_start();

	/* load settings */
	if (!isset($_CONFIG))
		require '../include/config.php';
	
	/* define constants */
	require '../include/constants.php';

	/* include outside functions */
	require_once('../bwc/bwc_chessutils.php');
	require '../bwc/bwc_games.php';
	require '../bwc/bwc_board.php';

	/* connect to database */
	require '../include/connectdb.php';
	
	/* check session status */
	require '../include/sessioncheck.php';
	
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
	
	// Pi�ces captur�es
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
        $titre_page = "Echecs en diff�r� (mobile) - Votre coup";
	else
        $titre_page = "Echecs en diff�r� (mobile) - Le coup de l'adversaire";
	
    $desc_page = "Jouer aux �checs en diff�r� sur votre smartphone. C'est votre partie, � vous de jouer.";
    require 'include/page_header.php';
    //echo("<meta HTTP-EQUIV='Pragma' CONTENT='no-cache'>\n");
?>
<script type="text/javascript">
/* transfer board data to javacripts */
<? writeJSboard(); ?>
<? writeJShistory(); ?>

if (DEBUG)
	alert("Game initilization complete!");
</script>
<script type="text/javascript" src="http://www.capakaspa.info/javascript/chessutils.js">
 /* these are utility functions used by other functions */
</script>
<script type="text/javascript" src="http://www.capakaspa.info/javascript/commands.js">
// these functions interact with the server
</script>
<script type="text/javascript" src="http://www.capakaspa.info/javascript/validation.js">
// these functions are used to test the validity of moves
</script>
<script type="text/javascript" src="http://www.capakaspa.info/javascript/isCheckMate.js">
// these functions are used to test the validity of moves
</script>
<script type="text/javascript" src="http://www.capakaspa.info/javascript/squareclicked.js">
// this is the main function that interacts with the user everytime they click on a square
</script>
<?
    require 'include/page_body.php';
?>
	<div id="onglet">
	<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td><div class="ongletdisable"><a href="tableaubord.php">Parties</a></div></td>
		<td><div class="ongletdisable"><a href="invitation.php">Invitation</a></div></td>
		<td><div class="ongletdisable"><a href="profil.php">Mon profil</a></div></td>	
	</tr>
	</table>
	</div>
<?
	if ($_SESSION['playerID'] == $tmpGame['whitePlayer'] || $_SESSION['playerID'] == $tmpGame['whitePlayer'])
	{
        if (mysql_num_rows($res_adv_vacation) > 0)
			echo("<div class='success'>Votre adversaire est absent en ce moment ! La partie est ajourn�e.</div>");
		else
			echo("<br/>");
	}
?>
    <center>   
	<form name="gamedata" method="post" action="partie.php">
		<table border="0">
        <tr valign="top" align="center">
        <td>
        	<? writeStatus(); ?>
			
			<input type="hidden" name="from" value="<? echo($_POST['from']) ?>" />
        </td>
        </tr>
        <tr valign="top" align="center">
        <td>
	        <?
			if ($isPromoting)
				writePromotion(true);
			?>
	        <?
			if ($isUndoRequested)
				writeUndoRequest(true);
			?>
	        <?
			if ($isDrawRequested)
				writeDrawRequest(true);
			?>
	        <? drawboard(false); ?>
              <nobr>
              <input type="button" name="btnUndo" value="Annuler le coup" <? if (isBoardDisabled()) echo("disabled='yes'"); else echo ("onClick='undo()'"); ?>>
              <!-- <input type="button" name="btnReload" value="Actualiser l'�chiquier" onClick="document.gamedata.submit();"> -->
              <input type="button" name="btnDraw" value="Proposer nulle" <? if (isBoardDisabled()) echo("disabled='yes'"); else echo ("onClick='draw()'"); ?>>
              <input type="button" name="btnResign" value="Abandonner" <? if (isBoardDisabled()) echo("disabled='yes'"); else echo ("onClick='resigngame()'"); ?>>
			  
              
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
          </tr>
          
        <tr>
        <td>
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
        <tr>
          <td valign="top" align="center">   
		  	<? 
				$listeCoups = writeHistory();
				$pgnstring = getPGN($tmpGame['whiteNick'], $tmpGame['blackNick'], $tmpGame['type'], $tmpGame['flagBishop'], $tmpGame['flagKnight'], $tmpGame['flagRook'], $tmpGame['flagQueen'], $listeCoups);
			?>
			
        </td>
        </tr>
      </table>
      
      
      <h3>Commentaires</h3>
 		<p>     
      	<input type="text" name="message" maxlength="255" size="30" />
		<input type="button" name="btnSend" value="Poster" <? echo ("onClick='send()'"); ?> />
		<br/>
      	<TEXTAREA NAME='dialogue' COLS='40' ROWS='8' readonly><? echo($dialogue); ?></TEXTAREA>
		</p>	
		<input type="hidden" name="addMessage" value="no" />
	  </form>
    </center>
<?
    require 'include/page_footer.php';
    mysql_close();
?>
