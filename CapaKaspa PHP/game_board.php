<?	require 'include/mobilecheck.php';
	session_start();

	/* load settings */
	if (!isset($_CONFIG))
		require 'include/config.php';
	
	/* define constants */
	require 'include/constants.php';

	/* include outside functions */
	require 'dac/dac_players.php';
	require 'dac/dac_games.php';
	require 'bwc/bwc_common.php';
	require 'bwc/bwc_chessutils.php';
	require 'bwc/bwc_games.php';
	require 'bwc/bwc_board.php';
	require 'bwc/bwc_players.php';
	
	/* connect to database */
	require 'include/connectdb.php';
	
	/* check session status */
	require 'include/sessioncheck.php';
	
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
	// TODO Mettre cette requete dans dac/dac_games
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
	
	// Localization
	require 'include/localization.php';
	
	/* find out if it's the current player's turn */
	if (( (($numMoves == -1) || (($numMoves % 2) == 1)) && ($playersColor == "white"))
			|| ((($numMoves % 2) == 0) && ($playersColor == "black")))
		$isPlayersTurn = true;
	else
		$isPlayersTurn = false;

	if ($_SESSION['isSharedPC'])
		$titre_page = '';
	else if ($isPlayersTurn)
        $titre_page = _("Play chess - Your move");
	else
        $titre_page = _("Play chess - Opponent move");
	$desc_page = _("Play chess and share your game. It's your game, it's up to you !");
    require 'include/page_header.php';
    //echo("<meta HTTP-EQUIV='Pragma' CONTENT='no-cache'>\n");
?>
<link href="css/pgn4web.css" type="text/css" rel="stylesheet" />
<link href="pgn4web/fonts/pgn4web-font-ChessSansPiratf.css" type="text/css" rel="stylesheet" />

<script src="pgn4web/pgn4web.js" type="text/javascript"></script>
<script type="text/javascript">
   SetImagePath ("pgn4web/alpha/37");
   SetImageType("png");
   SetCommentsOnSeparateLines(true);
   SetAutoplayDelay(2500); // milliseconds
   SetAutostartAutoplay(false);
   SetAutoplayNextGame(true);
   SetShortcutKeysEnabled(false);

	/* transfer board data to javacripts */
	<? writeJSboard(); ?>
	<? writeJShistory(); ?>

	function afficheplayer(){
	      document.getElementById("player").style.display = "block";
	      document.getElementById("viewer").style.display = "none";
	      document.getElementById("cache").style.display = "inline";
	      document.getElementById("voir").style.display = "none";
	    }
	function afficheviewer(){
		document.getElementById("player").style.display = "none";
	      document.getElementById("viewer").style.display = "block";
	      document.getElementById("cache").style.display = "none";
	      document.getElementById("voir").style.display = "inline";
	}
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
<?
require 'include/page_body.php';
?>
<div id="contentlarge">
	<div class="contentbody">
      
        <?
        if ($_SESSION['playerID'] == $tmpGame['whitePlayer'] || $_SESSION['playerID'] == $tmpGame['whitePlayer'])
		{
        	if (mysql_num_rows($res_adv_vacation) > 0)
				echo("<div class='success'>"._("Your opponent is absent at the moment. The game is postponed").".</div>");
			else
				echo("<br/>");
		}
		?>
    <span id="#confirm_cancel_move_id" style="display: none"><?echo _("Are you sure you want to cancel your last move ?")?></span>
    <span id="#confirm_draw_proposal_id" style="display: none"><?echo _("Confirm your draw proposal ?")?></span>
    <span id="#confirm_resign_game_id" style="display: none"><?echo _("Are you sure you want to resign ?")?></span>    
	<form name="gamedata" method="post" action="game_board.php">
	<table border="0">
        <tr valign="top">
			<td>
				<div id="player" style="display:block;">
				
				<? drawboard(false); ?>
	              <nobr>
	              <input type="button" name="btnUndo" class="button" value="<?php echo _("Cancel move")?>" <? if (isBoardDisabled()) echo("disabled='yes'"); else echo ("onClick='undo()'"); ?>>
	              <input type="button" name="btnDraw" class="button" value="<?php echo _("Draw proposal")?>" <? if (isBoardDisabled()) echo("disabled='yes'"); else echo ("onClick='draw()'"); ?>>
	              <input type="button" name="btnResign" class="button" value="<?php echo _("Resign")?>" <? if (isBoardDisabled()) echo("disabled='yes'"); else echo ("onClick='resigngame()'"); ?>>
				  
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
				</div>
				<div id="viewer" style="display:none;">
					<div id="GameBoard"></div>
					<div id="GameButtons"></div>
				</div>
			</td>
          	
          	<td width="15">&nbsp;</td>
          	
          	<td>
	          	<?
	          	writeStatus(); 
				$listeCoups = writeHistory();
				$pgnstring = getPGN($tmpGame['whiteNick'], $tmpGame['blackNick'], $tmpGame['type'], $tmpGame['flagBishop'], $tmpGame['flagKnight'], $tmpGame['flagRook'], $tmpGame['flagQueen'], $listeCoups);
				
				if ($isPromoting) writePromotion(false);
				if ($isUndoRequested) writeUndoRequest(false);
				if ($isDrawRequested) writeDrawRequest(false);
				?>
				<a href="javascript:afficheviewer();" id="cache" style="display:inline;">Viewer</a>
				<a href="javascript:afficheplayer();" id="voir" style="display:none;">Player</a>
				<form style="display: none;">
				<textarea style="display: none;" id="pgnText">
				<? echo($pgnstring); ?>
				</textarea>
				</form>
				<div id="GameText"></div>
				<input type="hidden" name="from" value="<? echo($_POST['from']) ?>" />
          	</td>
        </tr>
        
        <tr>
        <td colspan="3">
        	<table widht='100%'>
        	<tr><td>
					<?$c=0;
					$d=0;
					
					while($row=mysql_fetch_array($f, MYSQL_ASSOC)){
					
						if(preg_match("/white/", $row['curColor'])){
							$color="black_";
							$c++;
						}
						else {
							$color="white_";
							}
					
						if($c==1){
							$d=0;
						}
						$d++;
						echo "\n<img src=\"images/mosaique/".$color.$row['replaced'].".gif\">";
					
					} // End while
					?>
				</tr></td>
				</table>
            </td>
        </tr>
      </table>
	
      <h3><?php echo _("Comments")?></h3>
 		<p>     
      	<input type="text" name="message" maxlength="255" size="87" />
		<input type="button" name="btnSend" class="button" value="<?php echo _("Post")?>" <? echo ("onClick='send()'"); ?> />
      	<TEXTAREA NAME='dialogue' COLS='74' ROWS='8' readonly><? echo($dialogue); ?></TEXTAREA>
			</p>	
		<input type="hidden" name="addMessage" value="no" />
	  </form>
	  
		<center>
	      <script type="text/javascript"><!--
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
    </div>
  </div>
<?
    require 'include/page_footer.php';
    mysql_close();
?>
