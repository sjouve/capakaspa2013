<?	require 'include/mobilecheck.php';
	
	session_start();
			
	/* load settings */
	if (!isset($_CONFIG))
		require 'include/config.php';
	
	/* load external functions for setting up new game */
	require 'include/constants.php';
	require 'dac/dac_players.php';
	require 'dac/dac_games.php';
	require 'dac/dac_activity.php';
	require 'bwc/bwc_common.php';
	require 'bwc/bwc_chessutils.php';
	require 'bwc/bwc_board.php';
	require 'bwc/bwc_players.php';
	require 'bwc/bwc_games.php';
	
	/* connect to database */
	require 'include/connectdb.php';
	
	$tmpNewUser = false;
	$errMsg = "";
	$Test = isset($_POST['ToDo']) ? $_POST['ToDo']:Null;
	$flagBishop = isset($_POST['flagBishop'])?$_POST['flagBishop']:"";
	$flagKnight = isset($_POST['flagKnight'])?$_POST['flagKnight']:"";
	$flagRook = isset($_POST['flagRook'])?$_POST['flagRook']:"";
	$flagQueen = isset($_POST['flagQueen'])?$_POST['flagQueen']:"";
	$type = isset($_POST['type']) ? $_POST['type']:0;
	
	switch($Test)
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
				header("Location: sign-up.php?err=login");
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
			header("Location: sign-up.php");
			exit;
			break;
	

		case 'InvitePlayer':
			
			$oppColor="";
			$newGameID = createInvitation($_SESSION['playerID'], $_POST['opponent'], $_POST['color'], $type, $flagBishop, $flagKnight, $flagRook, $flagQueen, $oppColor);
			
			if ($newGameID) {
				/* Notification */
				chessNotification('invitation', $oppColor, '', $_SESSION['nick'], $newGameID);
				insertActivity($_SESSION['playerID'], GAME, $newGameID, "", 'invitation');
			}
			break;

		case 'InvitePlayerByNick':
			
			// Récupérer l'id du player dans le cas de l'invitation par saisie surnom
			if (isset($_POST['txtNick']) && $_POST['txtNick'] != $_SESSION['nick'])
			{
				$tmpQueryId = "SELECT playerID FROM players WHERE nick = '".$_POST['txtNick']."' AND activate=1";
				$tmpPlayers = mysql_query($tmpQueryId);
				if (mysql_num_rows($tmpPlayers) > 0)
				{
					$tmpPlayer = mysql_fetch_array($tmpPlayers, MYSQL_ASSOC);

					if ($tmpPlayer)
					{
						$oppColor="";
						$newGameID = createInvitation($_SESSION['playerID'], $tmpPlayer['playerID'], $_POST['color'], $_POST['type'], $_POST['flagBishop'], $_POST['flagKnight'], $_POST['flagRook'], $_POST['flagQueen'], $oppColor);
			
						if ($newGameID) {
							
							/* Notification */
							chessNotification('invitation', $oppColor, '', $_SESSION['nick'], $newGameID);
							insertActivity($_SESSION['playerID'], GAME, $newGameID, "", 'invitation');
						}
					}
				}
			}
			break;

		case 'ResponseToInvite':

			if ($_POST['response'] == 'accepted')
			{			
				/* update game data */
				$tmpQuery = "UPDATE games SET gameMessage = DEFAULT, messageFrom = DEFAULT WHERE gameID = ".$_POST['gameID'];
				mysql_query($tmpQuery) or die (mysql_error());

				/* setup new board */
				createNewGame($_POST['gameID']);
				saveGame();
				
				if ($_POST['whitePlayerID'] != $_SESSION['playerID'])
					$oppColor = "white";
				else 
				  	$oppColor = "black";
				
				/* Notification */
				chessNotification('accepted', $oppColor, $_POST['respMessage'], $_SESSION['nick'], $_POST['gameID']);
				insertActivity($_SESSION['playerID'], GAME, $_POST['gameID'], "", 'accepted');					
			}
			else
			{
				$tmpQuery = "UPDATE games SET gameMessage = 'inviteDeclined', messageFrom = '".$_POST['messageFrom']."' WHERE gameID = ".$_POST['gameID'];
				mysql_query($tmpQuery);
				
				if ($_POST['whitePlayerID'] != $_SESSION['playerID'])
					$oppColor = "white";
				else
				  	$oppColor = "black";
				
				/* Notification */
				chessNotification('declined', $oppColor, $_POST['respMessage'], $_SESSION['nick'], $_POST['gameID']);
				insertActivity($_SESSION['playerID'], GAME, $_POST['gameID'], "", 'declined');
			}

			break;

		case 'WithdrawRequest':

			if ($_POST['whitePlayerID'] == $_SESSION['playerID'])
				$oppColor = "black";
			else
				$oppColor = "white";

			/* notify opponent of invitation via email */
			chessNotification('withdrawal', $oppColor, '', $_SESSION['nick'], $_POST['gameID']);
			insertActivity($_SESSION['playerID'], GAME, $_POST['gameID'], "", 'withdrawal');
			// TODO Prévoir le cas où une activité n'a plus de partie associée
			$tmpQuery = "DELETE FROM games WHERE gameID = ".$_POST['gameID'];
			mysql_query($tmpQuery);
			
			break;

	}

	/* check session status */
	require 'include/sessioncheck.php';

	/* set default playing mode to different PCs (as opposed to both players sharing a PC) */
	$_SESSION['isSharedPC'] = false;
	
	// Localization after login
	require 'include/localization.php';
	
    $titre_page = _("My games in progress");
    $desc_page = _("Play chess and share your games. My games in progress.");
    require 'include/page_header.php';
?>
<script type="text/javascript">
	function sendResponse(responseType, messageFrom, gameID, whitePlayerID)
	{
		document.responseToInvite.response.value = responseType;
		document.responseToInvite.messageFrom.value = messageFrom;
		document.responseToInvite.gameID.value = gameID;
		document.responseToInvite.whitePlayerID.value = whitePlayerID;
		document.responseToInvite.submit();
	}

	function loadGame(gameID)
	{

		document.existingGames.gameID.value = gameID;
		document.existingGames.submit();
	}

	function withdrawRequest(gameID, whitePlayerID)
	{
		document.withdrawRequestForm.gameID.value = gameID;
		document.responseToInvite.whitePlayerID.value = whitePlayerID;
		document.withdrawRequestForm.submit();
	}
</script>
<?
require 'include/page_body.php';
?>
<div id="content">
    <div class="contentbody">
    <?
    if ($errMsg != "")
		echo("<div class='error'>".$errMsg."</div>");
		
	$res_current_vacation = getCurrentVacation($_SESSION['playerID']);
	if (mysql_num_rows($res_current_vacation) > 0)
		echo("<div class='success'>"._("You have a current vacation ! Your games are postponed").".</div>");
	
	$tmpGamesFrom = listInvitationFrom($_SESSION['playerID']);
	$tmpGamesFor = listInvitationFor($_SESSION['playerID']);
	if (mysql_num_rows($tmpGamesFrom) > 0 || mysql_num_rows($tmpGamesFor) > 0)
	{
	?>		
		<h2><?php echo _("My pending requests");?></h2>
		<form name="withdrawRequestForm" action="index.php" method="post">
		<?
		if (mysql_num_rows($tmpGamesFrom) > 0)
			while($tmpGame = mysql_fetch_array($tmpGamesFrom, MYSQL_ASSOC))
			{
				/* Get opponent's nick and ID*/
				if ($tmpGame['whitePlayer'] == $_SESSION['playerID']) {
					$opponent = $tmpGame['blackNick'];
					$opponentID = $tmpGame['blackPlayerID'];
				}
				else {
					$opponent = $tmpGame['whiteNick'];
					$opponentID = $tmpGame['whitePlayerID'];
				}
				
				echo("
				<div class='activity'>
					<div class='leftbar'>
						<img src='".getPicturePath("", "")."' width='40' height='40' border='0'/>
					</div>
					<div class='details'>
						<div class='title'>
							<span class='name'>"._("You")."</span> "._("invite a player to play a new game")." <a href='player_view.php?playerID=".$opponentID."'><span class='name'>".$opponent."</span></a>
						</div>
						<div class='content'>
							<div class='gameboard'>");
								drawboardGame($tmpGame['gameID'], $tmpGame['whitePlayerID'], $tmpGame['blackPlayerID'], $tmpGame['position']);
							echo("</div>
							<div class='gamedetails'>");
							if ($tmpGame['whitePlayer'] == $_SESSION['playerID'])
								echo ("<img src='images/white_pawn.gif'/>");
							else
								echo ("<img src='images/black_pawn.gif'/>");
							
							echo(getStrGameType($tmpGame['type'], $tmpGame['flagBishop'], $tmpGame['flagKnight'], $tmpGame['flagRook'], $tmpGame['flagQueen']));
							
							if ($tmpGame['gameMessage'] == 'playerInvited')
								echo _("Response waiting");
							else if ($tmpGame['gameMessage'] == 'inviteDeclined')
								echo _("Request declined");
							echo ("<input type='button' value='"._("Cancel")."' class='button' onclick=\"withdrawRequest(".$tmpGame['gameID'].",".$tmpGame['whitePlayerID'].")\">");
							echo("</div>
						</div>
					</div>
				</div>");
			}
		?>			
			<input type="hidden" name="gameID" value="">
			<input type="hidden" name="whitePlayerID" value="">
			<input type="hidden" name="ToDo" value="WithdrawRequest">
		</form>
      
		<form name="responseToInvite" action="index.php" method="post">
		<?
		if (mysql_num_rows($tmpGamesFor) > 0)
			while($tmpGame = mysql_fetch_array($tmpGamesFor, MYSQL_ASSOC))
			{
				/* Get opponent's nick and ID*/
				if ($tmpGame['whitePlayer'] == $_SESSION['playerID']) {
					$opponent = $tmpGame['blackNick'];
					$opponentID = $tmpGame['blackPlayerID'];
				}
				else {
					$opponent = $tmpGame['whiteNick'];
					$opponentID = $tmpGame['whitePlayerID'];
				}
				
				echo("
				<div class='activity'>
					<div class='leftbar'>
						<img src='".getPicturePath("", "")."' width='40' height='40' border='0'/>
					</div>
					<div class='details'>
						<div class='title'>
							<a href='player_view.php?playerID=".$opponentID."'><span class='name'>".$opponent."</span></a> "._("invite you to play a new game")."
						</div>
						<div class='content'>
							<div class='gameboard'>");
								drawboardGame($tmpGame['gameID'], $tmpGame['whitePlayerID'], $tmpGame['blackPlayerID'], $tmpGame['position']);
							echo("</div>
							<div class='gamedetails'>");
							if ($tmpGame['whitePlayer'] == $_SESSION['playerID']) {
								echo ("<img src='images/white_pawn.gif'/>");
								$tmpFrom = "white";
							}
							else {
								echo ("<img src='images/black_pawn.gif'/>");
								$tmpFrom = "black";
							}
							
							echo(getStrGameType($tmpGame['type'], $tmpGame['flagBishop'], $tmpGame['flagKnight'], $tmpGame['flagRook'], $tmpGame['flagQueen']));
							
							/* Response */
							echo ("<TEXTAREA NAME='respMessage' COLS='33' ROWS='3' style='background-color:white;border-color:#CCCCCC;'></TEXTAREA>");
							
							/* Action */
							echo ("<input type='button' value='"._("Accept")."' class='button' onclick=\"sendResponse('accepted', '".$tmpFrom."', ".$tmpGame['gameID'].", ".$tmpGame['whitePlayerID'].")\">");
							echo ("<input type='button' value='"._("Decline")."' class='button' onclick=\"sendResponse('declined', '".$tmpFrom."', ".$tmpGame['gameID'].", ".$tmpGame['whitePlayerID'].")\">");
							echo("</div>
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
		<br>
	<? }?>
	
		<h2><?php echo _("My games in progress")?> <a href="index.php"><img src="images/icone_rafraichir.png" border="0" title="<?php echo _("Refresh list")?>" alt="<?php echo _("Refresh list")?>" /></a></h2>
		<form name="existingGames" action="game_board.php" method="post">
		<?
		$tmpGames = listInProgressGames($_SESSION['playerID']);
		if (mysql_num_rows($tmpGames) > 0)
			while($tmpGame = mysql_fetch_array($tmpGames, MYSQL_ASSOC))
			{
				/* Get opponent's nick and ID*/
				if ($tmpGame['whitePlayer'] == $_SESSION['playerID']) {
					$opponent = $tmpGame['blackNick'];
					$opponentID = $tmpGame['blackPlayer'];
				}
				else {
					$opponent = $tmpGame['whiteNick'];
					$opponentID = $tmpGame['whitePlayer'];
				}
				
				echo("
				<div class='activity'>
					<div class='leftbar'>
						<img src='".getPicturePath("", "")."' width='40' height='40' border='0'/>
					</div>
					<div class='details'>
						<div class='title'>						
							<a href='player_view.php?playerID=".$opponentID."'><span class='name'>".$opponent."</span></a> "._("invite you to play a new game")."
						</div>
						<div class='content'>
							<div class='gameboard'>");
								drawboardGame($tmpGame['gameID'],$tmpGame['whitePlayer'],$tmpGame['blackPlayer'], $tmpGame['position']);
							echo("</div>
							<div class='gamedetails'>");
								echo("<a href='player_view.php?playerID=".$tmpGame['whitePlayerID']."'>".$tmpGame['whiteNick']."</a>-<a href='player_view.php?playerID=".$tmpGame['blackPlayerID']."'>".$tmpGame['blackNick']."</a> [".$tmpGame['eco']."] ");
								if ($isPlayersTurn)
									echo("<a href='javascript:loadGame(".$tmpGame['gameID'].")'><img src='images/hand.gif' border=0 alt='Jouer'/></a>");
								else
									echo("<a href='javascript:loadGame(".$tmpGame['gameID'].")'><img src='images/eye.gif' border=0 alt='Voir'/></a>");
		    				 	
								list($year, $month, $day) = explode("-", $tmpGame['lastMove']);
								$expireDate = date("d/m/Y", mktime(0,0,0, $month, $day + $CFG_EXPIREGAME, $year));
								echo("<br/>Expire le : ".$expireDate);
							echo("</div>
						</div>
					</div>
				</div>");
			}
			else {
				echo _("No games in progress...");
			}
		?>
        
			<input type="hidden" name="gameID" value="">
			<input type="hidden" name="sharePC" value="no">
			<input type="hidden" name="from" value="encours">
		</form>
		<br>
		<center><script type="text/javascript"><!--
	        google_ad_client = "ca-pub-8069368543432674";
	        /* CapaKaspa Tableau bord Bandeau Partie */
	        google_ad_slot = "3190675956";
	        google_ad_width = 468;
	        google_ad_height = 60;
	        //-->
	        </script>
	        <script type="text/javascript"
	        src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
	        </script>
		</center>
		<br>
	</div>
</div>
<?
require 'include/page_footer.php';
mysql_close();
?>
