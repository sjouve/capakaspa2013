<?
	session_start();
			
	/* load settings */
	if (!isset($_CONFIG))
		require '../include/config.php';

	/* load external functions for setting up new game */
	require_once('../bwc/bwc_chessutils.php');
	require '../include/constants.php';
	require '../bwc/bwc_board.php';
	require '../bwc/bwc_players.php';
	require '../bwc/bwc_games.php';
	
	/* connect to database */
	require '../include/connectdb.php';
	
	$tmpNewUser = false;
	$errMsg = "";
	$Test = isset($_POST['ToDo']) ? $_POST['ToDo']:Null;
	switch($Test)
	{
		case 'Login':
			
			$res = loginPlayer($_POST['txtNick'], $_POST['pwdPassword'], $_POST['chkAutoConn']);
			if ($res == -1)
			{
				// TODO Passer le nick et password
				header("Location: ../activation.php");
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

			/* prevent multiple pending requests between two players with the same originator */
			$tmpQuery = "SELECT gameID FROM games WHERE gameMessage = 'playerInvited'";
			$tmpQuery .= " AND ((messageFrom = 'white' AND whitePlayer = ".$_SESSION['playerID']." AND blackPlayer = ".$_POST['opponent'].")";
			$tmpQuery .= " OR (messageFrom = 'black' AND whitePlayer = ".$_POST['opponent']." AND blackPlayer = ".$_SESSION['playerID']."))";

			$tmpExistingRequests = mysql_query($tmpQuery);

			if (mysql_num_rows($tmpExistingRequests) == 0)
			{

				if ($_POST['color'] == 'random')
					$tmpColor = (mt_rand(0,1) == 1) ? "white" : "black";
				else
					$tmpColor = $_POST['color'];

				$flagBishop = $_POST['flagBishop'];
				$flagKnight = $_POST['flagKnight'];
				$flagRook = $_POST['flagRook'];
				$flagQueen = $_POST['flagQueen'];
				
				if ( $flagBishop == "1") {$flagBishop = 1;} else {$flagBishop = 0;};
				if ( $flagKnight == "1") {$flagKnight = 1;} else {$flagKnight = 0;};
				if ( $flagRook == "1") {$flagRook = 1;} else {$flagRook = 0;};
				if ( $flagQueen == "1") {$flagQueen = 1;} else {$flagQueen = 0;};
				
				$type = isset($_POST['type']) ? $_POST['type']:0;
				
				$tmpQuery = "INSERT INTO games (whitePlayer, blackPlayer, gameMessage, messageFrom, dateCreated, lastMove, type, flagBishop, flagKnight, flagRook, flagQueen) VALUES (";
				if ($tmpColor == 'white')
					$tmpQuery .= $_SESSION['playerID'].", ".$_POST['opponent'];
				else
					$tmpQuery .= $_POST['opponent'].", ".$_SESSION['playerID'];

				$tmpQuery .= ", 'playerInvited', '".$tmpColor."', NOW(), NOW(), ".$type.", ".$flagBishop.", ".$flagKnight.", ".$flagRook.", ".$flagQueen.")";
				
				mysql_query($tmpQuery);
				
				/* if email notification is activated... */
				if ($CFG_USEEMAILNOTIFICATION)
				{
					/* if opponent is using email notification... */
					$tmpOpponentEmail = mysql_query("SELECT email FROM players WHERE playerID = ".$_POST['opponent']);
					if (mysql_num_rows($tmpOpponentEmail) > 0)
					{
						$opponentEmail = mysql_result($tmpOpponentEmail, 0);
						$tmpNotifEmail = mysql_query("SELECT value FROM preferences WHERE playerID = ".$_POST['opponent']." AND preference = 'emailNotification'");
						$notifEmail = mysql_result($tmpNotifEmail, 0);
						if ($notifEmail == 'oui')
						{
							/* notify opponent of invitation via email */
							// TODO A modifier comme pour site normal
							chessNotification('invitation', $opponentEmail, '', $_SESSION['nick']);
						}
					}
				}
			}
			break;

		case 'InvitePlayerByNick':
			// R�cup�rer l'id du player dans le cas de l'invitation par saisie surnom
			if (isset($_POST['txtNick']) && $_POST['txtNick'] != $_SESSION['nick'])
			{
				$tmpQueryId = "SELECT playerID FROM players WHERE nick = '".$_POST['txtNick']."' AND activate=1";
				$tmpPlayers = mysql_query($tmpQueryId);
				if (mysql_num_rows($tmpPlayers) > 0)
				{
					$tmpPlayer = mysql_fetch_array($tmpPlayers, MYSQL_ASSOC);

					if ($tmpPlayer)
					{
						$tmpQuery = "SELECT gameID FROM games WHERE gameMessage = 'playerInvited'";
						$tmpQuery .= " AND ((messageFrom = 'white' AND whitePlayer = ".$_SESSION['playerID']." AND blackPlayer = ".$tmpPlayer['playerID'].")";
						$tmpQuery .= " OR (messageFrom = 'black' AND whitePlayer = ".$tmpPlayer['playerID']." AND blackPlayer = ".$_SESSION['playerID']."))";

						$tmpExistingRequests = mysql_query($tmpQuery);

						if (mysql_num_rows($tmpExistingRequests) == 0)
						{

							if ($_POST['color'] == 'random')
								$tmpColor = (mt_rand(0,1) == 1) ? "white" : "black";
							else
								$tmpColor = $_POST['color'];
							
							$flagBishop = $_POST['flagBishop'];
							$flagKnight = $_POST['flagKnight'];
							$flagRook = $_POST['flagRook'];
							$flagQueen = $_POST['flagQueen'];
							if ( $flagBishop == "1") {$flagBishop = 1;} else {$flagBishop = 0;};
							if ( $flagKnight == "1") {$flagKnight = 1;} else {$flagKnight = 0;};
							if ( $flagRook == "1") {$flagRook = 1;} else {$flagRook = 0;};
							if ( $flagQueen == "1") {$flagQueen = 1;} else {$flagQueen = 0;};
							
							$tmpQuery = "INSERT INTO games (whitePlayer, blackPlayer, gameMessage, messageFrom, dateCreated, lastMove, type, flagBishop, flagKnight, flagRook, flagQueen) VALUES (";
							if ($tmpColor == 'white')
								$tmpQuery .= $_SESSION['playerID'].", ".$tmpPlayer['playerID'];
							else
								$tmpQuery .= $tmpPlayer['playerID'].", ".$_SESSION['playerID'];

							$tmpQuery .= ", 'playerInvited', '".$tmpColor."', NOW(), NOW(), ".$_POST['type'].", ".$flagBishop.", ".$flagKnight.", ".$flagRook.", ".$flagQueen.")";
							mysql_query($tmpQuery);
							
							/* if email notification is activated... */
							if ($CFG_USEEMAILNOTIFICATION)
							{
								/* if opponent is using email notification... */
								$tmpOpponentEmail = mysql_query("SELECT email FROM players WHERE playerID = ".$tmpPlayer['playerID']);
								if (mysql_num_rows($tmpOpponentEmail) > 0)
								{
									$opponentEmail = mysql_result($tmpOpponentEmail, 0);
									$tmpNotifEmail = mysql_query("SELECT value FROM preferences WHERE playerID = ".$tmpPlayer['playerID']." AND preference = 'emailNotification'");
									$notifEmail = mysql_result($tmpNotifEmail, 0);
									if ($notifEmail == 'oui')
									{
										/* notify opponent of invitation via email */
										// TODO A modifier comme pour site normal
										chessNotification('invitation', $opponentEmail, '', $_SESSION['nick']);
									}
								}
							}
						}
					}
				}
			}
			break;

		case 'ResponseToInvite':

			if ($_POST['response'] == 'accepted')
			{
				
				/* update game data */
				$tmpQuery = "UPDATE games SET gameMessage = '', messageFrom = '' WHERE gameID = ".$_POST['gameID'];
				mysql_query($tmpQuery);

				/* setup new board */
				//$_SESSION['gameID'] = $_POST['gameID'];
				createNewGame($_POST['gameID']);
				saveGame();
				
					
				/* if opponent is using email notification... */
				$tmpPlayersEmail = mysql_query("SELECT G.whitePlayer whitePlayer, G.blackPlayer blackPlayer, WP.email whiteEmail, BP.email blackEmail FROM games G, players WP, players BP WHERE G.gameID = ".$_POST['gameID']." AND G.whitePlayer = WP.playerID AND G.blackPlayer = BP.playerID");
				if (mysql_num_rows($tmpPlayersEmail) > 0)
				{
					$playersEmail = mysql_fetch_array($tmpPlayersEmail, MYSQL_ASSOC);
					if ($playersEmail['whitePlayer'] != $_SESSION['playerID'])
					{
						$opponentEmail = $playersEmail['whiteEmail'];
						$opponentID = $playersEmail['whitePlayer'];
					} else {
					  	$opponentEmail = $playersEmail['blackEmail'];
					  	$opponentID = $playersEmail['blackPlayer'];
					}
					
					$tmpNotifEmail = mysql_query("SELECT value FROM preferences WHERE playerID = ".$opponentID." AND preference = 'emailNotification'");
					$notifEmail = mysql_result($tmpNotifEmail, 0);
					if ($notifEmail == 'oui')
					{
						/* notify opponent of invitation via email */
						// TODO A modifier comme pour site normal
						chessNotification('accepted', $opponentEmail, $_POST['respMessage'], $_SESSION['nick']);
					}
				}
					
			}
			else
			{

				$tmpQuery = "UPDATE games SET gameMessage = 'inviteDeclined', messageFrom = '".$_POST['messageFrom']."' WHERE gameID = ".$_POST['gameID'];
				mysql_query($tmpQuery);
				
				/* if opponent is using email notification... */
				$tmpPlayersEmail = mysql_query("SELECT G.whitePlayer whitePlayer, G.blackPlayer blackPlayer, WP.email whiteEmail, BP.email blackEmail FROM games G, players WP, players BP WHERE G.gameID = ".$_POST['gameID']." AND G.whitePlayer = WP.playerID AND G.blackPlayer = BP.playerID");
				if (mysql_num_rows($tmpPlayersEmail) > 0)
				{
					$playersEmail = mysql_fetch_array($tmpPlayersEmail, MYSQL_ASSOC);
					if ($playersEmail['whitePlayer'] != $_SESSION['playerID'])
					{
						$opponentEmail = $playersEmail['whiteEmail'];
						$opponentID = $playersEmail['whitePlayer'];
					} else {
					  	$opponentEmail = $playersEmail['blackEmail'];
					  	$opponentID = $playersEmail['blackPlayer'];
					}
					
					$tmpNotifEmail = mysql_query("SELECT value FROM preferences WHERE playerID = ".$opponentID." AND preference = 'emailNotification'");
					$notifEmail = mysql_result($tmpNotifEmail, 0);
					if ($notifEmail == 'oui')
					{
						/* notify opponent of invitation via email */
						// TODO A modifier comme pour site normal
						chessNotification('declined', $opponentEmail, $_POST['respMessage'], $_SESSION['nick']);
					}
				}
			}

			break;

		case 'WithdrawRequest':

			/* get opponent's player ID */
			$tmpOpponentID = mysql_query("SELECT whitePlayer FROM games WHERE gameID = ".$_POST['gameID']);
			if (mysql_num_rows($tmpOpponentID) > 0)
			{
				$opponentID = mysql_result($tmpOpponentID, 0);

				if ($opponentID == $_SESSION['playerID'])
				{
					$tmpOpponentID = mysql_query("SELECT blackPlayer FROM games WHERE gameID = ".$_POST['gameID']);
					$opponentID = mysql_result($tmpOpponentID, 0);
				}

				$tmpQuery = "DELETE FROM games WHERE gameID = ".$_POST['gameID'];
				mysql_query($tmpQuery);

				/* if email notification is activated... */
				if ($CFG_USEEMAILNOTIFICATION)
				{
					/* if opponent is using email notification... */
					$tmpOpponentEmail = mysql_query("SELECT email FROM players WHERE playerID = ".$opponentID);
					if (mysql_num_rows($tmpOpponentEmail) > 0)
					{
						$opponentEmail = mysql_result($tmpOpponentEmail, 0);
						if ($opponentEmail != '')
						{
							/* notify opponent of invitation via email */
							// TODO A modifier comme pour site normal
							chessNotification('withdrawal', $opponentEmail, '', $_SESSION['nick']);
						}
					}
				}
			}
			break;

	}

	/* check session status */
	require '../include/sessioncheck.php';

	/* set default playing mode to different PCs (as opposed to both players sharing a PC) */
	$_SESSION['isSharedPC'] = false;

    $titre_page = "Echecs en diff�r� (mobile) - Tableau de bord";
    $desc_page = "Jouer aux �checs en diff�r� sur votre smartphone. Retrouvez vos parties d'�checs en diff�r� en cours et vos invitations en attente de r�ponse";
    require 'include/page_header.php';
?>
<script type="text/javascript">

		function sendResponse(responseType, messageFrom, gameID)
		{
			document.responseToInvite.response.value = responseType;
			document.responseToInvite.messageFrom.value = messageFrom;
			document.responseToInvite.gameID.value = gameID;
			document.responseToInvite.submit();
		}

		function loadGame(gameID)
		{

			document.existingGames.gameID.value = gameID;
			document.existingGames.submit();
		}

		function withdrawRequest(gameID)

		{
			document.withdrawRequestForm.gameID.value = gameID;
			document.withdrawRequestForm.submit();
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
   
    require 'include/page_body.php';
?>

	<div id="onglet">
	<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td><div class="ongletenable">Parties</div></td>
		<td><div class="ongletdisable"><a href="player_search.php">Invitation</a></div></td>
		<td><div class="ongletdisable"><a href="player_update.php">Mon profil</a></div></td>	
	</tr>
	</table>
	</div>
	
	<?
	if ($errMsg != "")
		echo("<div class='error'>".$errMsg."</div>");
		
	$res_current_vacation = getCurrentVacation($_SESSION['playerID']);
	if (mysql_num_rows($res_current_vacation) > 0)
		echo("<div class='success'>Vous avez une absence en cours ! Vos parties sont ajourn�es.</div>");
	
	/* if game is marked playerInvited and the invite is from the current player */
	$tmpQuery = "SELECT * FROM games WHERE (gameMessage = 'playerInvited' AND ((whitePlayer = ".$_SESSION['playerID']." AND messageFrom = 'white') OR (blackPlayer = ".$_SESSION['playerID']." AND messageFrom = 'black'))";

	/* OR game is marked inviteDeclined and the response is from the opponent */
	$tmpQuery .= ") OR (gameMessage = 'inviteDeclined' AND ((whitePlayer = ".$_SESSION['playerID']." AND messageFrom = 'black') OR (blackPlayer = ".$_SESSION['playerID']." AND messageFrom = 'white')))  ORDER BY dateCreated";

	$tmpGames = mysql_query($tmpQuery);
	
	if (mysql_num_rows($tmpGames) > 0)
	{
	?>
		
		<form name="withdrawRequestForm" action="game_list_inprogress.php" method="post">
        <h3>Mes propositions de partie</h3>
        
        <div class="tabliste">
          <table border="0" width="100%">
            <tr>
              <th>Adversaire</th>
              <th>Votre couleur</th>
              <th>Type / Statut</th>
              <th>Action</th>
            </tr>

	<?
		while($tmpGame = mysql_fetch_array($tmpGames, MYSQL_ASSOC))
		{
			/* Opponent */
			echo("<tr><td>");
			
			/* Get opponent's nick */
			if ($tmpGame['whitePlayer'] == $_SESSION['playerID'])
				$tmpOpponent = mysql_query("SELECT nick FROM players WHERE playerID = ".$tmpGame['blackPlayer']);
			else
				$tmpOpponent = mysql_query("SELECT nick FROM players WHERE playerID = ".$tmpGame['whitePlayer']);
			$opponent = mysql_result($tmpOpponent,0);
			//echo($opponent);
			if ($tmpGame['whitePlayer'] == $_SESSION['playerID'])
				echo("<a href='player_view.php?playerID=".$tmpGame['blackPlayer']."'>".$opponent."</a>");
			else
				echo("<a href='player_view.php?playerID=".$tmpGame['whitePlayer']."'>".$opponent."</a>");
				
			/* Your Color */
			echo ("</td><td align='center'>");
			if ($tmpGame['whitePlayer'] == $_SESSION['playerID'])
				echo ("<img src='/images/white_pawn.gif'/>");
			else
				echo ("<img src='/images/black_pawn.gif'/>");
			
			/* Type de partie */
			echo ("</td><td>");
			if ($tmpGame['type'] == 0)
				echo("Normale");
			else
			{
				$pieces="";
				if ($tmpGame['flagBishop'] == 1)
					$pieces .= ", Fous";
				if ($tmpGame['flagKnight'] == 1)
					$pieces .= ", Cavaliers"; 
				if ($tmpGame['flagRook'] == 1)
					$pieces .= ", Tours";
				if ($tmpGame['flagQueen'] == 1)
					$pieces .= ", Dames";
					
				echo("Roi et Pions".$pieces);	 
			}
				
				
			/* Status */
			echo ("<br/>");
			if ($tmpGame['gameMessage'] == 'playerInvited')
				echo ("R�ponse en attente");
			else if ($tmpGame['gameMessage'] == 'inviteDeclined')
				echo ("Invitation d�clin�e");

			/* Withdraw Request */
			echo ("</td><td align='center'>");
			echo ("<input type='button' value='Annuler' onclick=\"withdrawRequest(".$tmpGame['gameID'].")\">");

			echo("</td></tr>\n");
		}
	?>
          </table>
        </div>
        <input type="hidden" name="gameID" value="">
        <input type="hidden" name="ToDo" value="WithdrawRequest">
      </form>
	<? }?>
	
	<?
	$tmpQuery = "SELECT * FROM games WHERE gameMessage = 'playerInvited' AND ((whitePlayer = ".$_SESSION['playerID']." AND messageFrom = 'black') OR (blackPlayer = ".$_SESSION['playerID']." AND messageFrom = 'white')) ORDER BY dateCreated";
	$tmpGames = mysql_query($tmpQuery);
	
	if (mysql_num_rows($tmpGames) > 0)
	{
	?>
      <form name="responseToInvite" action="game_list_inprogress.php" method="post">
        <h3> On me propose une partie</h3>
        
        <div class="tabliste">
          <table border="0" width="100%">
            <tr>
              <th>Adversaire</th>
              <th>Votre couleur</th>
              <th>Type / R�ponse</th>
              <th>Action</th>
            </tr>
            
            <?
		while($tmpGame = mysql_fetch_array($tmpGames, MYSQL_ASSOC))
		{
			/* Opponent */
			echo("<tr><td>");
			/* get opponent's nick */
			if ($tmpGame['whitePlayer'] == $_SESSION['playerID'])
				$tmpOpponent = mysql_query("SELECT nick FROM players WHERE playerID = ".$tmpGame['blackPlayer']);
			else
				$tmpOpponent = mysql_query("SELECT nick FROM players WHERE playerID = ".$tmpGame['whitePlayer']);
			$opponent = mysql_result($tmpOpponent,0);
			//echo($opponent);
			if ($tmpGame['whitePlayer'] == $_SESSION['playerID'])
				echo("<a href='player_view.php?playerID=".$tmpGame['blackPlayer']."'>".$opponent."</a>");
			else
				echo("<a href='player_view.php?playerID=".$tmpGame['whitePlayer']."'>".$opponent."</a>");

			/* Your Color */
			echo ("</td><td align='center'>");
			if ($tmpGame['whitePlayer'] == $_SESSION['playerID'])
			{
				echo ("<img src='/images/white_pawn.gif'/>");
				$tmpFrom = "white";
			}
			else
			{
				echo ("<img src='/images/black_pawn.gif'/>");
				$tmpFrom = "black";
			}
			
			/* Type de partie */
			echo ("</td><td>");
			if ($tmpGame['type'] == 0)
				echo("Normale");
			else
			{
				$pieces="";
				if ($tmpGame['flagBishop'] == 1)
					$pieces .= ", Fous";
				if ($tmpGame['flagKnight'] == 1)
					$pieces .= ", Cavaliers"; 
				if ($tmpGame['flagRook'] == 1)
					$pieces .= ", Tours";
				if ($tmpGame['flagQueen'] == 1)
					$pieces .= ", Dames";
					
				echo("Roi et Pions".$pieces);	 
			}
			
			/* Response */
			echo ("<br/>");
			echo ("<TEXTAREA NAME='respMessage' COLS='15' ROWS='3' style='background-color:white;border-color:#CCCCCC;'></TEXTAREA>");
			
			/* Action */
			echo ("</td><td align='center'>");
			echo ("<input type='button' value='Accepter' onclick=\"sendResponse('accepted', '".$tmpFrom."', ".$tmpGame['gameID'].")\">");
			echo ("<input type='button' value='Refuser' onclick=\"sendResponse('declined', '".$tmpFrom."', ".$tmpGame['gameID'].")\">");
			
			echo("</td></tr>\n");
		}
	?>
          </table>
        </div>
        <input type="hidden" name="response" value="">
        <input type="hidden" name="messageFrom" value="">
        <input type="hidden" name="gameID" value="">
        <input type="hidden" name="ToDo" value="ResponseToInvite">
      </form>
	<? }?>
	
      <form name="existingGames" action="game_board.php" method="post">
        <h3> Mes parties en cours <a href="game_list_inprogress.php"><img src="images/icone_rafraichir.png" border="0" alt="Rafra�chir" /></a></h3>
        
		<div id="mosaique">
        <?
        	
			$tmpGames = mysql_query("SELECT G.gameID gameID, G.eco eco, DATE_FORMAT(G.lastMove, '%d/%m/%Y %T') dateCreatedF, DATE_FORMAT(lastMove, '%Y-%m-%d') lastMove, G.whitePlayer whitePlayer, G.blackPlayer blackPlayer, G.position position, W.playerID whitePlayerID, W.nick whiteNick, B.playerID blackPlayerID, B.nick blackNick
                                        FROM games G, players W, players B
                                        WHERE gameMessage = ''
                                        AND (whitePlayer = ".$_SESSION['playerID']." OR blackPlayer = ".$_SESSION['playerID'].")
                                        AND W.playerID = G.whitePlayer AND B.playerID = G.blackPlayer
                                        ORDER BY dateCreated");

            $nbGame = mysql_num_rows($tmpGames);

            if ($nbGame > 0)
        	{
                
                $numGame = 1;
                
                echo("<table width='100%' border='0'>");
                while($tmpGame = mysql_fetch_array($tmpGames, MYSQL_ASSOC))
        		{
                     
                     echo("<tr>");
                     
                     echo("<td align='center'>");
                     drawboardGame($tmpGame['gameID'],$tmpGame['whitePlayer'],$tmpGame['blackPlayer'], $tmpGame['position']);

                     echo($numGame.". <a href='player_view.php?playerID=".$tmpGame['whitePlayerID']."'>".$tmpGame['whiteNick']."</a>-<a href='player_view.php?playerID=".$tmpGame['blackPlayerID']."'>".$tmpGame['blackNick']."</a> [".$tmpGame['eco']."] ");
                     if ($isPlayersTurn)
    				        echo("<a href='javascript:loadGame(".$tmpGame['gameID'].")'><img src='/images/hand.gif' border=0 alt='Jouer'/></a>");
                     else
    				        echo("<a href='javascript:loadGame(".$tmpGame['gameID'].")'><img src='/images/eye.gif' border=0 alt='Voir'/></a>");
    				 
					 list($year, $month, $day) = explode("-", $tmpGame['lastMove']);
					 
    				 $expireDate = date("d/m/Y", mktime(0,0,0, $month, $day + $CFG_EXPIREGAME, $year));
    				 echo("<br/>Expire le : ".$expireDate);
                     echo("</td>");
                     
                     echo("</tr>");
                     
                     $numGame = $numGame + 1;
                }
                echo("</table>");
            } else
            {
			  echo("<p>Vous n'avez aucune partie en cours...</p>");
			}
            
        ?>
        </div>
        <input type="hidden" name="gameID" value="">
        <input type="hidden" name="sharePC" value="no">
        <input type="hidden" name="from" value="encours">
      </form>
      <br/>
      
      
 
<?
    require 'include/page_footer.php';
    mysql_close();
?>
