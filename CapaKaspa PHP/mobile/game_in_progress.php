<?	
session_start();
		
/* load settings */
if (!isset($_CONFIG))
	require '../include/config.php';

/* load external functions for setting up new game */
require '../include/constants.php';
require '../dac/dac_players.php';
require '../dac/dac_games.php';
require '../dac/dac_activity.php';
require '../bwc/bwc_common.php';
require '../bwc/bwc_chessutils.php';
require '../bwc/bwc_board.php';
require '../bwc/bwc_players.php';
require '../bwc/bwc_games.php';
	
/* connect to database */
require '../include/connectdb.php';
	
$tmpNewUser = false;
$errMsg = "";

$flagBishop = isset($_POST['flagBishop'])?$_POST['flagBishop']:"";
$flagKnight = isset($_POST['flagKnight'])?$_POST['flagKnight']:"";
$flagRook = isset($_POST['flagRook'])?$_POST['flagRook']:"";
$flagQueen = isset($_POST['flagQueen'])?$_POST['flagQueen']:"";
$type = isset($_POST['type']) ? $_POST['type']:0;
$ToDo = isset($_POST['ToDo']) ? $_POST['ToDo']:(isset($_GET['ToDo']) ? $_GET['ToDo']:Null);
$_SESSION['hideNotTurn'] = isset($_GET['turn']) ? $_GET['turn']:(isset($_SESSION['hideNotTurn']) ? $_SESSION['hideNotTurn']:0);
	
switch($ToDo)
{
	case 'Login':
		
		$res = loginPlayer($_POST['txtNick'], $_POST['pwdPassword'], isset($_POST['chkAutoConn']) ? $_POST['chkAutoConn']:"");
		if ($res == -1)
		{
			// TODO Passer le nick et password
			header("Location: activation.php");
			exit;
		}
		if ($res == 0)
		{
			header("Location: index.php?err=login");
			exit;
		}
		break;

	case 'Logout':
	
		if (isset($_COOKIE["capakaspacn"])) {
		   foreach ($_COOKIE["capakaspacn"] as $nom => $valeur) {
		     setcookie("capakaspacn[$nom]", 0, time()-3600*24);    
		  }
		}
		$_SESSION['playerID'] = -1;
		header("Location: index.php");
		exit;
		break;


	case 'InvitePlayer':
		
		$opponentColor="";
		$newGameID = createInvitation($_SESSION['playerID'], $_POST['opponent'], $_POST['color'], $type, $flagBishop, $flagKnight, $flagRook, $flagQueen, $opponentColor, $_POST['timeMove'], $_POST['chess960']);
		
		if ($newGameID) {
			// Notification
			chessNotification('invitation', $opponentColor, '', $_SESSION['nick'], $newGameID);
			if ($_SESSION['pref_shareinvitation'] == 'oui')
				insertActivity($_SESSION['playerID'], GAME, $newGameID, "", 'invitation');
		}
		break;

	case 'ResponseToInvite':

		// Attention la partie ne doit pas avoir �t� accept�e entre temps
		$game = getGame($_POST['gameID']);
		if ($game['gameMessage'] == 'playerInvited')
		{
			if ($_POST['response'] == 'accepted')
			{			
				
				/* update game data */
				$tmpQuery = "UPDATE games 
								SET gameMessage = DEFAULT, messageFrom = DEFAULT,
									whitePlayer = (SELECT CASE WHEN whitePlayer = 0 THEN ".$_SESSION['playerID']." ELSE whitePlayer END),
									blackPlayer = (SELECT CASE WHEN blackPlayer = 0 THEN ".$_SESSION['playerID']." ELSE blackPlayer END) 
								WHERE gameID = ".$_POST['gameID'];
				mysqli_query($dbh,$tmpQuery) or die (mysqli_error($dbh));
	
				/* setup new board */
				createNewGame($_POST['gameID']);
				saveGame();
				
				if ($_POST['whitePlayerID'] == 0)
					$oppColor = "black";
				else if ($_POST['whitePlayerID'] != $_SESSION['playerID'])
					$oppColor = "white";
				else 
				  	$oppColor = "black";
				
				/* Notification */
				chessNotification('accepted', $oppColor, $_POST['respMessage'], $_SESSION['nick'], $_POST['gameID']);
				if ($_SESSION['pref_shareinvitation'] == 'oui')
					insertActivity($_SESSION['playerID'], GAME, $_POST['gameID'], "", 'accepted');					
			}
			else
			{
				$tmpQuery = "UPDATE games SET gameMessage = 'inviteDeclined', messageFrom = '".$_POST['messageFrom']."' WHERE gameID = ".$_POST['gameID'];
				mysqli_query($dbh,$tmpQuery);
				
				if ($_POST['whitePlayerID'] != $_SESSION['playerID'])
					$oppColor = "white";
				else
				  	$oppColor = "black";
				
				/* Notification */
				chessNotification('declined', $oppColor, $_POST['respMessage'], $_SESSION['nick'], $_POST['gameID']);
				if ($_SESSION['pref_shareinvitation'] == 'oui')
					insertActivity($_SESSION['playerID'], GAME, $_POST['gameID'], "", 'declined');
			}
		}
		else $errMsg = _("A player accepted the game before you !");
		break;

	case 'WithdrawRequest':

		if ($_POST['whitePlayerID'] == $_SESSION['playerID'])
			$oppColor = "black";
		else
			$oppColor = "white";

		/* notify opponent of invitation via email */
		chessNotification('withdrawal', $oppColor, '', $_SESSION['nick'], $_POST['gameID']);
		if ($_SESSION['pref_shareinvitation'] == 'oui')
			insertActivity($_SESSION['playerID'], GAME, $_POST['gameID'], "", 'withdrawal');
		
		$tmpQuery = "DELETE FROM games WHERE gameID = ".$_POST['gameID'];
		mysqli_query($dbh,$tmpQuery);
		
		break;
}

/* check session status */
require '../include/sessioncheck.php';

// Localization after login
require '../include/localization.php';
$fmt = new IntlDateFormatter(getenv("LC_ALL"), IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);

$titre_page = _("My games in progress");
$desc_page = _("Play chess and share your games. My games in progress.");
require 'include/page_header.php';
?>
<script src="http://jouerauxechecs.capakaspa.info/javascript/comment.js" type="text/javascript"></script>
<script src="http://jouerauxechecs.capakaspa.info/javascript/like.js" type="text/javascript"></script>
<script type="text/javascript">
	function sendResponse(responseType, messageFrom, gameID, whitePlayerID)
	{
		document.responseToInvite.response.value = responseType;
		document.responseToInvite.messageFrom.value = messageFrom;
		document.responseToInvite.gameID.value = gameID;
		document.responseToInvite.whitePlayerID.value = whitePlayerID;
		document.responseToInvite.submit();
	}

	function sendResponseAll(responseType, messageFrom, gameID, whitePlayerID)
	{
		document.responseToInviteAll.response.value = responseType;
		document.responseToInviteAll.messageFrom.value = messageFrom;
		document.responseToInviteAll.gameID.value = gameID;
		document.responseToInviteAll.whitePlayerID.value = whitePlayerID;
		document.responseToInviteAll.submit();
	}

	function loadGame(gameID)
	{

		document.existingGames.gameID.value = gameID;
		document.existingGames.submit();
	}

	function withdrawRequest(gameID, whitePlayerID)
	{
		document.withdrawRequestForm.gameID.value = gameID;
		document.withdrawRequestForm.whitePlayerID.value = whitePlayerID;
		document.withdrawRequestForm.submit();
	}
</script>
<?
$activeMenu = 10;
require 'include/page_body.php';
    
	if ($errMsg != "")
		echo("<div class='error'>".$errMsg."</div>");
    
	$res_current_vacation = getCurrentVacation($_SESSION['playerID']);
	if (mysqli_num_rows($res_current_vacation) > 0)
		echo("<div class='success'>"._("You have a current vacation ! Your games are postponed").".</div>");
	$tmpGames = listInProgressGames($_SESSION['playerID']);
	$tmpGamesFrom = listInvitationFrom($_SESSION['playerID']);
	$tmpGamesFor = listInvitationFor($_SESSION['playerID']);
	if (mysqli_num_rows($tmpGamesFrom) > 0 || mysqli_num_rows($tmpGamesFor) > 0)
	{
	?>		
		<h3><?php echo _("My pending requests");?></h3>
		<form name="withdrawRequestForm" action="game_in_progress.php" method="post">
		<?
		if (mysqli_num_rows($tmpGamesFrom) > 0)
			while($tmpGame = mysqli_fetch_array($tmpGamesFrom, MYSQLI_ASSOC))
			{
				/* Get opponent's nick and ID*/
				if ($tmpGame['whitePlayer'] == $_SESSION['playerID']) {
					$opponent = $tmpGame['blackNick'];
					$opponentID = $tmpGame['blackPlayerID'];
					$opponentSocialID = $tmpGame['blackSocialID'];
					$opponentSocialNW = $tmpGame['blackSocialNetwork'];
				}
				else {
					$opponent = $tmpGame['whiteNick'];
					$opponentID = $tmpGame['whitePlayerID'];
					$opponentSocialID = $tmpGame['whiteSocialID'];
					$opponentSocialNW = $tmpGame['whiteSocialNetwork'];
				}
				
				// Elo
				if ($tmpGame['type'] == 2)
				{
					$whiteElo = $tmpGame['whiteElo960'];
					$blackElo = $tmpGame['blackElo960'];
				}
				else
				{
					$whiteElo = $tmpGame['whiteElo'];
					$blackElo = $tmpGame['blackElo'];
				}
				
				$postDate = new DateTime($tmpGame['dateCreated']);
				$strPostDate = $fmt->format($postDate);
				
				echo("
				<div class='activity'>
					<div class='details'>
						<div class='content'>
							<div class='gameboard'>");
								drawboardGame($tmpGame['gameID'], $tmpGame['whitePlayerID'], $tmpGame['blackPlayerID'], $tmpGame['position'], $tmpGame['nbMoves']);
							echo("</div>
							<div class='gamedetails'>");
							echo(getStrGameType($tmpGame['type'], $tmpGame['flagBishop'], $tmpGame['flagKnight'], $tmpGame['flagRook'], $tmpGame['flagQueen']));
							echo("<br>"._("Time per move").": ".$tmpGame['timeMove']." "._("days"));
							echo("<br>
									<span style='float: left'><img src='pgn4web/".$_SESSION['pref_theme']."/20/wp.png'> ".$tmpGame['whiteNick']."<br>".$whiteElo."</span>
									<span style='float: right'><img src='pgn4web/".$_SESSION['pref_theme']."/20/bp.png'> ".$tmpGame['blackNick']."<br>".$blackElo."</span><br><br><br>");
							
							echo ("<span style='float: right'>");
							if ($tmpGame['gameMessage'] == 'playerInvited')
								echo _("Response waiting");
							else if ($tmpGame['gameMessage'] == 'inviteDeclined')
								echo _("Request declined");
							echo (" <input type='button' value='"._("Cancel")."' class='button' onclick=\"withdrawRequest(".$tmpGame['gameID'].",".$tmpGame['whitePlayerID'].")\"></span>");
							echo("</div>
						</div>
						<div class='footer'>
							<span class='date'>".$strPostDate."</span>
						</div>
					</div>
				</div>");
			}
		?>			
			<input type="hidden" name="gameID" value="">
			<input type="hidden" name="whitePlayerID" value="">
			<input type="hidden" name="ToDo" value="WithdrawRequest">
		</form>
      
		<form name="responseToInvite" action="game_in_progress.php" method="post">
		<?
		if (mysqli_num_rows($tmpGamesFor) > 0)
			while($tmpGame = mysqli_fetch_array($tmpGamesFor, MYSQLI_ASSOC))
			{
				/* Get opponent's nick and ID*/
				if ($tmpGame['whitePlayer'] == $_SESSION['playerID']) {
					$opponent = $tmpGame['blackNick'];
					$opponentID = $tmpGame['blackPlayerID'];
					$opponentSocialID = $tmpGame['blackSocialID'];
					$opponentSocialNW = $tmpGame['blackSocialNetwork'];
				}
				else {
					$opponent = $tmpGame['whiteNick'];
					$opponentID = $tmpGame['whitePlayerID'];
					$opponentSocialID = $tmpGame['whiteSocialID'];
					$opponentSocialNW = $tmpGame['whiteSocialNetwork'];
				}
				
				// Elo
				if ($tmpGame['type'] == 2)
				{
					$whiteElo = $tmpGame['whiteElo960'];
					$blackElo = $tmpGame['blackElo960'];
				}
				else
				{
					$whiteElo = $tmpGame['whiteElo'];
					$blackElo = $tmpGame['blackElo'];
				}
				
				$postDate = new DateTime($tmpGame['dateCreated']);
				$strPostDate = $fmt->format($postDate);
				
				echo("
				<div class='activity'>
					<div class='details'>
						<div class='content'>
							<div class='gameboard'>");
								drawboardGame($tmpGame['gameID'], $tmpGame['whitePlayerID'], $tmpGame['blackPlayerID'], $tmpGame['position'], $tmpGame['nbMoves']);
							echo("</div>
							<div class='gamedetails'>");
							if ($tmpGame['whitePlayer'] == $_SESSION['playerID']) {
								$tmpFrom = "white";
							}
							else {
								$tmpFrom = "black";
							}
							echo(getStrGameType($tmpGame['type'], $tmpGame['flagBishop'], $tmpGame['flagKnight'], $tmpGame['flagRook'], $tmpGame['flagQueen']));
							echo("<br>"._("Time per move").": ".$tmpGame['timeMove']." "._("days"));
							echo("<br>
									<span style='float: left'><img src='pgn4web/".$_SESSION['pref_theme']."/20/wp.png'> ".$tmpGame['whiteNick']."<br>".$whiteElo."</span>
									<span style='float: right'><img src='pgn4web/".$_SESSION['pref_theme']."/20/bp.png'> ".$tmpGame['blackNick']."<br>".$blackElo."</span><br><br><br>");
							
							/* Response */
							echo ("<span style='float: left'><TEXTAREA name='respMessage' rows='3' placeholder='"._("Your message...")."' style='background-color: white;border-color: #CCCCCC;width: 260px;'></TEXTAREA></span>");
							
							/* Action */
							echo ("<span style='float: right'><input type='button' value='"._("Accept")."' class='button' onclick=\"sendResponse('accepted', '".$tmpFrom."', ".$tmpGame['gameID'].", ".$tmpGame['whitePlayerID'].")\"><br>");
							echo ("<input type='button' value='"._("Decline")."' class='button' onclick=\"sendResponse('declined', '".$tmpFrom."', ".$tmpGame['gameID'].", ".$tmpGame['whitePlayerID'].")\"></span>");
							echo("</div>
						</div>
						<div class='footer'>
							<span class='date'>".$strPostDate."</span>
						</div>
					</div>
				</div>");
			}
		?>

			<input type="hidden" name="response" value="">
			<input type="hidden" name="messageFrom" value="">
			<input type="hidden" name="gameID" value="">
			<input type="hidden" name="whitePlayerID" value="">
			<input type="hidden" name="ToDo" value="ResponseToInvite">
		</form>
		&nbsp;<br>
	<? }
	
	$tmpGamesFor = listInvitationForAll($_SESSION['playerID']);
	if (mysqli_num_rows($tmpGamesFor) > 0)
	{
	?>
	
		<h3><?php echo _("Other pending requests");?> (<a href="#" onclick="javascript:document.getElementById('requests').style.display = 'block';"><?php echo(mysqli_num_rows($tmpGamesFor));?></a>) <a href="game_in_progress.php"><img src="images/icone_rafraichir.png" border="0" title="<?php echo _("Refresh list")?>" alt="<?php echo _("Refresh list")?>" /></a></h3>
	<? if (mysqli_num_rows($tmpGames) > 0) {?>
	<div id="requests" style="display: none;">
	<?} else {?>
	<div id="requests">
	<?php }?>
		<form name="responseToInviteAll" action="game_in_progress.php" method="post">
		<?
		if (mysqli_num_rows($tmpGamesFor) > 0)
			while($tmpGame = mysqli_fetch_array($tmpGamesFor, MYSQLI_ASSOC))
			{
				/* Get opponent's nick and ID*/
				if ($tmpGame['whitePlayer'] == 0) {
					$opponent = $tmpGame['blackNick'];
					$opponentID = $tmpGame['blackPlayerID'];
					$opponentSocialID = $tmpGame['blackSocialID'];
					$opponentSocialNW = $tmpGame['blackSocialNetwork'];
				}
				else {
					$opponent = $tmpGame['whiteNick'];
					$opponentID = $tmpGame['whitePlayerID'];
					$opponentSocialID = $tmpGame['whiteSocialID'];
					$opponentSocialNW = $tmpGame['whiteSocialNetwork'];
				}
				
				// Elo
				$whiteElo = "";
				$blackElo = "";
				if ($tmpGame['type'] == 2)
				{
					if ($tmpGame['whitePlayerID'] !=0) $whiteElo = $tmpGame['whiteElo960'];
					if ($tmpGame['blackPlayerID'] !=0) $blackElo = $tmpGame['blackElo960'];
				}
				else
				{
					if ($tmpGame['whitePlayerID'] !=0) $whiteElo = $tmpGame['whiteElo'];
					if ($tmpGame['blackPlayerID'] !=0) $blackElo = $tmpGame['blackElo'];
				}
				
				$postDate = new DateTime($tmpGame['dateCreated']);
				$strPostDate = $fmt->format($postDate);
				
				echo("
				<div class='activity'>
					<div class='details'>
						<div class='content'>
							<div class='gameboard'>");
								drawboardGame($tmpGame['gameID'], $tmpGame['whitePlayerID'], $tmpGame['blackPlayerID'], $tmpGame['position'], $tmpGame['nbMoves']);
							echo("</div>
							<div class='gamedetails'>");
							if ($tmpGame['whitePlayer'] == $_SESSION['playerID']) {
								$tmpFrom = "white";
							}
							else {
								$tmpFrom = "black";
							}
							echo(getStrGameType($tmpGame['type'], $tmpGame['flagBishop'], $tmpGame['flagKnight'], $tmpGame['flagRook'], $tmpGame['flagQueen']));
							echo("<br>"._("Time per move").": ".$tmpGame['timeMove']." "._("days"));
							echo("<br>
									<span style='float: left'><img src='pgn4web/".$_SESSION['pref_theme']."/20/wp.png'> ".$tmpGame['whiteNick']."<br>".$whiteElo."</span>
									<span style='float: right'><img src='pgn4web/".$_SESSION['pref_theme']."/20/bp.png'> ".$tmpGame['blackNick']."<br>".$blackElo."</span><br><br><br>");
							
							/* Response */
							echo ("<span style='float: left'><TEXTAREA name='respMessage' rows='3' placeholder='"._("Your message...")."' style='background-color: white;border-color: #CCCCCC;width: 260px;'></TEXTAREA></span>");
							
							/* Action */
							echo ("<span style='float: right'><input type='button' value='"._("Accept")."' class='button' onclick=\"sendResponseAll('accepted', '".$tmpFrom."', ".$tmpGame['gameID'].", ".$tmpGame['whitePlayerID'].")\"><br>");
							
							echo("</div>
						</div>
						<div class='footer'>
							<span class='date'>".$strPostDate."</span>
						</div>
					</div>
				</div>");
			}
		?>

			<input type="hidden" name="response" value="">
			<input type="hidden" name="messageFrom" value="">
			<input type="hidden" name="gameID" value="">
			<input type="hidden" name="whitePlayerID" value="">
			<input type="hidden" name="ToDo" value="ResponseToInvite">
		</form>
	</div>
	&nbsp;<br>
	<? } ?>	
			
		<h3><?php echo _("My games in progress")?> 
		<? if (mysqli_num_rows($tmpGames) > 0) 
			if ($_SESSION['hideNotTurn'] == 1) {?>
		<a href="game_in_progress.php?turn=0"><img style="margin-right: 5px; margin-left: 5px;" src="images/picto_plus.png" border="0" title="<?php echo _("Show all your games in progress")?>" alt="<?php echo _("Show all your games in progress")?>" /></a>
		<? } else {?>		
		<a href="game_in_progress.php?turn=1"><img style="margin-right: 5px; margin-left: 5px;" src="images/picto_moins.png" border="0" title="<?php echo _("Show only your moves to do")?>" alt="<?php echo _("Show only your moves to do")?>" /></a>
		<? } ?>
		<a href="game_in_progress.php?turn=<? echo $_SESSION['hideNotTurn'];?>"><img src="images/icone_rafraichir.png" border="0" title="<?php echo _("Refresh list")?>" alt="<?php echo _("Refresh list")?>" /></a>
		</h3>
		<form name="existingGames" action="game_board.php" method="post">
		<?
		
		if (mysqli_num_rows($tmpGames) > 0)
			while($tmpGame = mysqli_fetch_array($tmpGames, MYSQLI_ASSOC))
			{
				$isTurn = FALSE;
				$nbMoves = $tmpGame['nbMoves'] - 1;
				/* Get opponent's nick and ID*/
				if ($tmpGame['whitePlayer'] == $_SESSION['playerID']) {
					$opponent = $tmpGame['blackNick'];
					$opponentID = $tmpGame['blackPlayerID'];
					$opponentSocialID = $tmpGame['blackSocialID'];
					$opponentSocialNW = $tmpGame['blackSocialNetwork'];
					if (($nbMoves == -1) || (($nbMoves % 2) == 1))
						$isTurn = TRUE;
				}
				else {
					$opponent = $tmpGame['whiteNick'];
					$opponentID = $tmpGame['whitePlayerID'];
					$opponentSocialID = $tmpGame['whiteSocialID'];
					$opponentSocialNW = $tmpGame['whiteSocialNetwork'];
					if (($nbMoves % 2) == 0)
						$isTurn = TRUE;
				}
				
				$postDate = new DateTime($tmpGame['lastMove']);
				$strPostDate = $fmt->format($postDate);
				$startedDate = new DateTime($tmpGame['dateCreated']);
				$strStartedDate = $fmt->format($startedDate);
				$expirationDate = new DateTime($tmpGame['expirationDate']);
				$strExpirationDate = $fmt->format($expirationDate);
				
				echo("
				<div id='".$tmpGame['gameID']."' class='activity' "); 
				if (!$isTurn && $_SESSION['hideNotTurn'] == 1) echo("style='visibility: hidden; height: 0px; margin-bottom: 0px; margin-top: 0px; padding-top: 0px; padding-bottom: 0px;'");
				echo(">
					<div class='details'>
						<div class='content'>
							<div class='gameboard'>");
								drawboardGame($tmpGame['gameID'],$tmpGame['whitePlayer'],$tmpGame['blackPlayer'], $tmpGame['position'], $tmpGame['nbMoves']);
							echo("</div>
							<div class='gamedetails'");
							if ($tmpGame['tournamentID'] != "") echo(" style='background-color: #FFF0C4;'"); 
							echo(">
								<span style='float: left'><img src='pgn4web/".$_SESSION['pref_theme']."/20/wp.png'> <b>".$tmpGame['whiteNick']."</b></span>
								<span style='float: right'><img src='pgn4web/".$_SESSION['pref_theme']."/20/bp.png'> <b>".$tmpGame['blackNick']."</b></span>");
								
								if ($isPlayersTurn)
									echo ("<br><br><span style='float: right'><input type='button' value='"._("Play")."' class='link_highlight' onclick='javascript:loadGame(".$tmpGame['gameID'].")'></span>");
								else
									echo ("<br><br><span style='float: right'><input type='button' value='"._("View")."' class='link' onclick='javascript:loadGame(".$tmpGame['gameID'].")'></span>");
								if ($tmpGame['tournamentID'] != "")
									echo(_("Tournament")." #".$tmpGame['tournamentID']." - ");
								echo(_("Time per move").": ".$tmpGame['timeMove']." "._("days"));
								echo("<br>"._("Expiration")." : <b>".$strExpirationDate."</b>");
							echo("</div>
						</div>
						<div class='footer'>");
							if (isset($tmpGame['likeID'])){?> 
							<span style="margin-right: 15px;" id="like<?echo(GAME.$tmpGame['gameID']);?>" ><a title="<? echo _("Stop liking this item")?>" href="javascript:deleteLike('<?echo(GAME);?>', <?echo($tmpGame['gameID']);?>, <?echo($tmpGame['likeID']);?>);"><?echo _("Unlike");?></a></span>
							<?} else {?>
							<span style="margin-right: 15px;" id="like<?echo(GAME.$tmpGame['gameID']);?>"><a title="<? echo _("I like this item")?>" href="javascript:insertLike('<?echo(GAME);?>', <?echo($tmpGame['gameID']);?>);"><?echo _("Like");?></a></span>
							<?}?>
							<a style="margin-right: 15px;" title="<? echo _("Comment this game")?>" href="javascript:displayComment('<?echo(GAME);?>', <?echo($tmpGame['gameID']);?>);"><?echo _("Comment");?></a>
							
							<? 
							if ($tmpGame['nbLike'] > 0 || $tmpGame['nbComment'] > 0 )
								echo("<span onmouseover=\"this.style.cursor='pointer';\" onclick=\"javascript:displayComment('".GAME."', ".$tmpGame['gameID'].");\">");
							if ($tmpGame['nbLike'] > 0) 
								echo("<img src='images/like.gif'><span class='socialcounter'>".$tmpGame['nbLike'])."</span> ";
							if ($tmpGame['nbComment'] > 0)
								echo("<img src='images/comment.jpg'><span class='socialcounter'>".$tmpGame['nbComment']."</span>");
							if ($tmpGame['nbLike'] > 0 || $tmpGame['nbComment'] > 0 )
								echo("</span>");
							echo("
						</div>
						<div class='comment' id='comment".$tmpGame['gameID']."'>
							<img src='images/ajaxloader.gif'/>
						</div>
					</div>
				</div>");
			}
			else {
				echo _("No games in progress...");
				echo ("<br><br>");
				?>
				<div class="blockform">
				<h3><? echo _("Begin to play")?></h3>
				<?php 
				echo _("Submit a new game for all players or a specific player");
				?>
				<br>
				<input type="button" class="link" value="<? echo _("New game")?>" onclick="location.href='game_new.php'">
				<br><br>
				<?php 
				echo _("Find a player with advanced search and invite him to play a new game.");
				?>
				<input type="button" class="link" value="<? echo _("Search players")?>" onclick="location.href='player_search.php'">
				</div>
				<?php 
				
			}
		?>
        
			<input type="hidden" name="gameID" value="">
			<input type="hidden" name="sharePC" value="no">
			<input type="hidden" name="from" value="encours">
		</form>	
		<div id="ads_bottom">
			<center>
			<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
			<!-- CapaKaspa Mobile Bottom -->
			<ins class="adsbygoogle"
			     style="display:inline-block;width:320px;height:100px"
			     data-ad-client="ca-pub-8069368543432674"
			     data-ad-slot="7983531266"></ins>
			<script>
			(adsbygoogle = window.adsbygoogle || []).push({});
			</script>
			</center>
		</div>
<?
require 'include/page_footer.php';
mysqli_close($dbh);
?>