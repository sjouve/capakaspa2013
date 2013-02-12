<?
require 'include/mobilecheck.php';
session_start();

/* load settings */
if (!isset($_CONFIG))
	require 'include/config.php';

/* define constants */
require 'include/constants.php';

/* include outside functions */
require 'dac/dac_players.php';
require 'dac/dac_games.php';
require 'dac/dac_activity.php';
require 'bwc/bwc_common.php';
require 'bwc/bwc_chessutils.php';
require 'bwc/bwc_games.php';
require 'bwc/bwc_board.php';
require 'bwc/bwc_players.php';
	
/* connect to database */
require 'include/connectdb.php';

/* check session status */
require 'include/sessioncheck.php';

/* debug flag */
define ("DEBUG", 0);
	
/* load game */
$Test = isset($_POST['isInCheck']) ? $_POST['isInCheck']:Null;
$isInCheck = ($Test == 'true');
$isCheckMate = false;
$isPromoting = false;
$isUndoing = false;
	
loadHistory($_POST['gameID']);
$tmpGame = loadGame($_POST['gameID'], $numMoves);
processMessages($tmpGame);
$pgnstring ="";
$TestPromotion = isset($_POST['promotion']) ? $_POST['promotion']:Null;
$TestFromRow = isset($_POST['fromRow']) ? $_POST['fromRow']:Null;
	
if ($_SESSION['playerID'] == $tmpGame['whitePlayer'] || $_SESSION['playerID'] == $tmpGame['blackPlayer'])
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
		
if (($TestFromRow != "") && ($_POST['fromCol'] != "") && ($_POST['toRow'] != "") && ($_POST['toCol'] != ""))
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
<script src="javascript/css-pop.js" type="text/javascript"></script>
<script src="pgn4web/pgn4web.js" type="text/javascript"></script>
<script src="javascript/comment.js" type="text/javascript"></script>
<script src="javascript/like.js" type="text/javascript"></script>
<script src="javascript/pmessage.js" type="text/javascript"></script>
<script type="text/javascript">
	// pgn4web parameter
   	SetImagePath ("pgn4web/<?echo($_SESSION['pref_theme']);?>/37");
   	SetImageType("png");
   	SetCommentsOnSeparateLines(true);
  	SetAutoplayDelay(2500); // milliseconds
   	SetAutostartAutoplay(false);
   	SetAutoplayNextGame(true);
   	SetShortcutKeysEnabled(false);

	/* transfer board data to javacripts */
	<? writeJSboard($board, $numMoves); ?>
	<? writeJSHistory($history, $numMoves); ?>

	function afficheplayer(){
      document.getElementById("player").style.display = "block";
      document.getElementById("viewer").style.display = "none";
      document.getElementById("hide").style.display = "inline";
      document.getElementById("show").style.display = "none";
	}
	
	function afficheviewer(){
		document.getElementById("player").style.display = "none";
		document.getElementById("viewer").style.display = "block";
		document.getElementById("hide").style.display = "none";
		document.getElementById("show").style.display = "inline";
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
$attribut_body = "onload=\"displayComment('".GAME."',".$_POST['gameID'].")\"";
if ($_SESSION['playerID'] == $tmpGame['whitePlayer'])
{
	$toPlayerID = $tmpGame['blackPlayer'];
	$toFirstName = $tmpGame['blackFirstName'];
	$toLastName = $tmpGame['blackLastName'];
	$toNick = $tmpGame['blackNick'];
	$toEmail = $tmpGame['blackEmail'];
}
else
{
	$toPlayerID = $tmpGame['whitePlayer'];
	$toFirstName = $tmpGame['whiteFirstName'];
	$toLastName = $tmpGame['whiteLastName'];
	$toNick = $tmpGame['whiteNick'];
	$toEmail = $tmpGame['whiteEmail'];;
}
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
		<!-- For translation in javascript -->
	    <span id="#confirm_cancel_move_id" style="display: none"><?echo _("Are you sure you want to cancel your last move ?")?></span>
	    <span id="#confirm_draw_proposal_id" style="display: none"><?echo _("Confirm your draw proposal ?")?></span>
	    <span id="#confirm_resign_game_id" style="display: none"><?echo _("Are you sure you want to resign ?")?></span>
	    <span id="#alert_invalid_move_id" style="display: none"><?echo _("Invalid move")?></span>
	    <span id="#alert_color_play_id" style="display: none"><?echo _("You are playing with")?></span>
	    <span id="#alert_color_white_id" style="display: none"><?echo _("white")?></span>
	    <span id="#alert_color_black_id" style="display: none"><?echo _("black")?></span>
	    <span id="#alert_err_move_check_id" style="display: none"><?echo _("Cannot move into check.")?></span>
	    <span id="#alert_err_move_king_id" style="display: none"><?echo _("Kings cannot move like that.")?></span>
	    <span id="#alert_err_move_pawn_id" style="display: none"><?echo _("Pawns cannot move backwards, only forward.")?></span>
	    <span id="#alert_err_move_passant_id" style="display: none"><?echo _("Pawns can only move en passant immediately after an opponent played his pawn.")?></span>
	    <span id="#alert_err_move_knight_id" style="display: none"><?echo _("Knights cannot move like that.")?></span>
	    <span id="#alert_err_move_bishop_id" style="display: none"><?echo _("Bishops cannot move like that.")?></span>
	    <span id="#alert_err_move_bishop_jump_id" style="display: none"><?echo _("Bishops cannot jump over other pieces.")?></span>
	    <span id="#alert_err_move_rook_id" style="display: none"><?echo _("Rooks cannot move like that.")?></span>
	    <span id="#alert_err_move_rook_jump_id" style="display: none"><?echo _("Rooks cannot jump over other pieces.")?></span>
	    <span id="#alert_err_move_queen_id" style="display: none"><?echo _("Queens cannot move like that.")?></span>
	    <span id="#alert_err_move_queen_jump_id" style="display: none"><?echo _("Queens cannot jump over other pieces.")?></span>
	    <span id="#alert_err_castle_king_id" style="display: none"><?echo _("Can only castle if king has not moved yet.")?></span>
	    <span id="#alert_err_castle_rook_id" style="display: none"><?echo _("Can only castle if rook has not moved yet.")?></span>
	    <span id="#alert_err_castle_pieces_id" style="display: none"><?echo _("Can only castle if there are no pieces between the rook and the king.")?></span>
	    <span id="#alert_err_castle_attack_id" style="display: none"><?echo _("When castling, the king cannot move over a square that is attacked by an ennemy piece.")?></span>
	    <span id="#alert_draw_stalemate_id" style="display: none"><?echo _("Stalemate - The game ends with a draw.")?></span>
	    <span id="#alert_draw_material_id" style="display: none"><?echo _("Insufficient material to checkmate - The game ends with a draw.")?></span>
	    <span id="#alert_draw_3_times_id" style="display: none"><?echo _("Draw (this position has occurred three times) - The game ends with a draw.")?></span>
	    <span id="#alert_draw_50_moves_id" style="display: none"><?echo _("Draw (50 moves rule) - The game ends with a draw.")?></span>
	    
	    
		<div id="gamedata">
			<form name="gamedata" method="post" action="game_board.php">
			<div id="gamerequest">
				<div id="promoting" style="display: none;">
					<table border="0" cellspacing="0" cellpadding="0">
					<tr><td align="center" bgcolor="#F2A521">
						<?echo _("Promote the pawn in")?> :
						<br>
						<input type="radio" name="promotion" value="<? echo (QUEEN); ?>"> <?echo _("Queen")?>
						<input type="radio" name="promotion" value="<? echo (ROOK); ?>"> <?echo _("Rook")?>
						<input type="radio" name="promotion" value="<? echo (KNIGHT); ?>"> <?echo _("Knight")?>
						<input type="radio" name="promotion" value="<? echo (BISHOP); ?>"> <?echo _("Bishop")?>
						<input type="button" name="btnPromote" value="<? echo _("OK")?>" class="button" onClick="promotepawn()" />
					</td></tr>
					</table>
				</div>
				<?
				if ($isUndoRequested) writeUndoRequest(false);
				if ($isDrawRequested) writeDrawRequest(false);
				?>
			</div>
			<div id="gameplayer">
				
				<div id="player" style="display:block;">				
					<? drawboard(false); ?>
					<nobr>
					<input type="button" id="btnUndo" name="btnUndo" class="button" style="visibility: hidden" value="<?php echo _("Cancel")?>" onClick="javascript:undo();">
					<input type="button" id="btnPlay" name="btnPlay" class="button" style="visibility: hidden" value="<?php echo _("Valid")?>" onClick="javascript:play();">
					<div id="requestDraw" style="display: none"><input type="checkbox" name="requestDraw" value="yes"> <?echo _("Draw")?></div>
					<div id="shareMove" style="display: none"><input type="checkbox" name="chkShareMove" value="share"> <?echo _("Share")?></div>
					</nobr>
					<input type="hidden" name="gameID" value="<? echo ($_POST['gameID']); ?>">
					<!-- <input type="hidden" name="requestDraw" value="no"> -->
					<input type="hidden" name="resign" value="no">
					<input type="hidden" name="fromRow" value="">
					<input type="hidden" name="fromCol" value="">
					<input type="hidden" name="toRow" value="">
					<input type="hidden" name="toCol" value="">
					<input type="hidden" name="isInCheck" value="false">
					<input type="hidden" name="isCheckMate" value="false">
					<input type="hidden" id="drawResult" name="drawResult" value="false">
				</div>
				
				<div id="viewer" style="display:none;">				
					<div id="GameBoard"></div>
					<div id="GameButtons"></div>
				</div>
			</div>
			<div id="gamestatus">
				<?writeStatus($tmpGame);?>
          	</div>
          	
			
			<div id="gamemoves">
				<?
				$listeCoups = writeHistoryPGN($history, $numMoves);
				$pgnstring = getPGN($tmpGame['whiteNick'], $tmpGame['blackNick'], $tmpGame['type'], $tmpGame['flagBishop'], $tmpGame['flagKnight'], $tmpGame['flagRook'], $tmpGame['flagQueen'], $listeCoups);
				?>
				<form style="display: none;">
					<textarea style="display: none;" id="pgnText">
					<? echo($pgnstring); ?>
					</textarea>
				</form>
				<div id="GameText"></div>
			</div>			
			
	        <div id="gamecaptured">
				<?
				// List of captured pieces
				$listPieces = listCapturedPieces($_POST['gameID']);
				
				while($row=mysql_fetch_array($listPieces, MYSQL_ASSOC)){
				
					if(preg_match("/white/", $row['curColor']))
						$color="b";
					else
						$color="w";

					echo "\n<img src=\"pgn4web/".$_SESSION['pref_theme']."/20/".$color.getPieceCharForImage(getPieceCode($color, $row['replaced'])).".png\">";				
				} // End while
				?>
			</div>
			<div id="gameaction">
				<input type="button" name="hide" id="hide" class="link" style="display:inline;" value="<?echo _("Show viewer");?>" onclick="javascript:afficheviewer();">
				<input type="button" name="show" id="show" class="link" style="display:none;" value="<?echo _("Show player");?>" onclick="javascript:afficheplayer();">
				<input type="button" name="pgn" id="pgn" class="link" value="<?echo _("Download PGN");?>" onclick="location.href='game_pgn.php?id=<? echo($_POST['gameID'])?>'">
				<input type="button" name="message" id="message" class="link" value="<?echo _("Send message");?>" onclick="popup('popUpDiv')">
				<? if (!isBoardDisabled()) {
				?>
				<input type="button" name="btnResign" class="button" value="<?php echo _("Resign")?>" <? if (isBoardDisabled()) echo("disabled='yes'"); else echo ("onClick='resigngame()'"); ?>>
				<? } ?>
				<input type="hidden" name="from" value="<? echo($_POST['from']) ?>" />
				
			</div>
			</form>
		</div>
		<div id="gamefooter">
			<a href=""><? echo _("It's a good game");?></a> - <span class="date"><?
			$fmt = new IntlDateFormatter(getenv("LC_ALL"), IntlDateFormatter::SHORT, IntlDateFormatter::SHORT);
	
			$startDate = new DateTime($tmpGame['dateCreated']);
			$lastMove = new DateTime($tmpGame['lastMove']);
			$strStartDate = $fmt->format($startDate);
			$strLastMove = $fmt->format($lastMove);
			echo _("Started")?> : <? echo($strStartDate);?> - <?echo _("Last move")?> : <? echo($strLastMove);?></span>
		</div>
	 	<div id="comment<?echo($_POST['gameID']);?>" class="comment">
			<img src="images/ajaxloader.gif"/>
		</div>
		
		<div id="adsbottom" style="width: 700px;">
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
	    </div>
	    
		<?if (strlen($tmpGame['dialogue']) > 0) {?>
		<div id="oldComment" style="display: solid;text-align: center;">
			<?echo _("Here are old existing comments on this game");?> :
			<TEXTAREA NAME='dialogue' COLS='74' ROWS='8' readonly><? echo($tmpGame['dialogue']); ?></TEXTAREA>
		</div>
		<?}?>
	  
	</div>
</div>
<?
require 'include/page_footer.php';
mysql_close();
?>
