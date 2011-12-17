<?
	session_start();

	/* load settings */
	if (!isset($_CONFIG))
		require '../config.php';
	
	/* connect to database */
	require '../connectdb.php';
	/* check session status */
	require_once('../chessutils.php');
	
	require '../dac_players.php';
	require '../dac_games.php';

	require '../sessioncheck.php';

	/* Charger le profil */
	$playerID = isset($_POST['playerID']) ? $_POST['playerID']:$_GET['playerID'];
    $player = getPlayer($playerID);
    
    /* Action */
    $ToDo = isset($_POST['ToDo']) ? $_POST['ToDo']:$_GET['ToDo'];

	switch($ToDo)
	{
		case 'AddFavorite':
			insertFavPlayer($_SESSION['playerID'], $player['playerID']);
			break;
		
		case 'DelFavorite':
			$favorite = getPlayerFavorite($_SESSION['playerID'], $player['playerID']);
			deleteFavPlayer($favorite['favoriteID']);
			break;
	}
	
	/* Charger le favori */
    $favorite = getPlayerFavorite($_SESSION['playerID'], $player['playerID']);
    
 	$titre_page = "Echecs en différé (mobile) - Consulter un profil";
 	$desc_page = "Jouez aux échecs en différé sur votre smartphone. Consulter le profil d'un jouer de la zone de jeu d'échecs en différé : son classement Elo, sa description, ses parties...";
    require 'page_header.php';
?>

<script type="text/javascript">

		function loadEndedGame(gameID)
		{
			document.endedGames.gameID.value = gameID;
			document.endedGames.submit();
		}
		
		function loadGame(gameID)
		{
			
			document.existingGames.gameID.value = gameID;
			document.existingGames.submit();
		}

</script>

<?     
    require 'page_body.php';
?>
  
	<div id="onglet">
		<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td><div class="ongletdisable"><a href="javascript:history.go(-1)">< Retour</a></div></td>	
		</tr>
		</table>
	</div>
	
	<h3><img src="<?echo(getPicturePath($player['socialNetwork'], $player['socialID']));?>" width="50" height="50"/> Profil de <? echo($player['nick']); if ($favorite) echo("<img src='images/favori-etoile-icone.png'/>"); if (getOnlinePlayer($player['playerID'])) echo (" <img src='images/user_online.gif'/>"); if (isNewPlayer($player['creationDate'])) echo (" <img src='images/user_new.gif'/>");?></h3>
		
        <table border="0" width="100%">
          <tr>
            <td width="30%"> Elo : </td>
            <td width="70%"><? 	echo($player['elo']); 
					if ($player['eloProgress'] == 0)
					{echo (" (=)");}
					else if ($player['eloProgress'] == 1)
					{echo (" (-)");}
					else echo (" (+)");
				?>
			</td>
          </tr>
		  <tr>
            <td> Situation géographique : </td>
            <td><? echo(stripslashes($player['situationGeo'])); ?></td>
          </tr>
		  <tr>
            <td> Année de naissance : </td>
            <td><? echo($player['anneeNaissance']); ?></td>
          </tr>
		  <tr>
            <td> Profil : </td>
            <td><TEXTAREA NAME="txtProfil" COLS="30" ROWS="5" readonly="readonly"><? echo(stripslashes($player['profil'])); ?></TEXTAREA></td>
          </tr>
          <tr>
            <td> Dernière connexion le : </td>
            <td><? 	list($annee, $mois, $jour) = explode("-", substr($player['lastConnection'], 0,10)); 
					echo($jour.'/'.$mois.'/'.$annee);
				?>
			</td>
          </tr>
        </table>
        <? if ($_SESSION['playerID']!=$player['playerID'] && !$favorite) {?>
			<form action="profil_consultation.php" method="post">
				<input type="hidden" name="ToDo" value="AddFavorite">
				<input type="hidden" name="playerID" value="<? echo($player['playerID']);?>">
				<input type="submit" value="Ajouter aux favoris">
			</form>
			<? }?>
			<? if ($_SESSION['playerID']!=$player['playerID'] && $favorite) {?>
			<form action="profil_consultation.php" method="post">
				<input type="hidden" name="ToDo" value="DelFavorite">
				<input type="hidden" name="playerID" value="<? echo($player['playerID']);?>">
				<input type="submit" value="Retirer des favoris">
			</form>
		<? }?>
		<br/>
		
		<? if ($_SESSION['playerID']!=$player['playerID']) {?>
		<form action="tableaubord.php" method="post">
			<h3>Proposez une nouvelle partie à <? echo($player['nick']); ?></h3>
			<input type="hidden" name="ToDo" value="InvitePlayer">
			<input type="hidden" name="opponent" value="<? echo($player['playerID']);?>">
			<table width="100%">
				<tr>
					<td width="30%">
						Position : 
					</td>
					<td width="70%">
						<input type="radio" name="type" value="0" checked> Normale
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="radio" name="type" value="1"><img src="/images/mosaique/white_king.gif">,<img src="/images/mosaique/white_pawn.gif"> et
						<input type="checkbox" name="flagBishop" value="1"><img src="/images/mosaique/white_bishop.gif">
						<input type="checkbox" name="flagKnight" value="1"><img src="/images/mosaique/white_knight.gif">
						<input type="checkbox" name="flagRook" value="1"><img src="/images/mosaique/white_rook.gif">
						<input type="checkbox" name="flagQueen" value="1"><img src="/images/mosaique/white_quuen.gif">
					</td>
				</tr>
				<tr>
					<td>
					Votre couleur :
					</td>
					<td>
						<input type="radio" name="color" value="white" checked> Blancs
						<input type="radio" name="color" value="black"> Noirs 
						<input type="submit" value="Inviter">
					</td>
				</tr>
			</table>
		</form>
		<br/>
		<?}?>
		
		
		<h3>Parties en cours de <? echo($player['nick']); ?></h3>
		
		<form name="existingGames" action="partie.php" method="post">

        <div id="tabliste">
          <table border="0" width="100%">
            <tr>
              <th width="25%">Blancs</th>
              <th width="25%">Noirs</th>
              <th width="10%">Résultat</th>
              <th width="10%">ECO</th>
              <th width="30%">Dernier coup</th>
            </tr>
            <?
					$tmpGames = mysql_query("SELECT G.gameID, G.eco eco, W.nick whiteNick, B.nick blackNick, G.gameMessage, G.messageFrom, DATE_FORMAT(G.dateCreated, '%d/%m/%Y %T') dateCreatedF, DATE_FORMAT(G.lastMove, '%d/%m/%Y %T') lastMove
				                            FROM games G, players W, players B
				                            WHERE G.gameMessage = ''
				                            AND (G.whitePlayer = ".$player['playerID']." OR G.blackPlayer = ".$player['playerID'].")
				                            AND W.playerID = G.whitePlayer AND B.playerID = G.blackPlayer
				                            ORDER BY G.dateCreated");
					
					if (mysql_num_rows($tmpGames) == 0)
						echo("<tr><td colspan='6'>Aucune partie en cours</td></tr>\n");
					else
					{
						while($tmpGame = mysql_fetch_array($tmpGames, MYSQL_ASSOC))
						{
							/* White */
							echo("<tr><td>");
							echo($tmpGame['whiteNick']);
							
							/* Black */
							echo ("</td><td>");
							echo($tmpGame['blackNick']);
							
							/* Current Turn */
							echo ("</td><td align=center>");
							echo("<a href='javascript:loadGame(".$tmpGame['gameID'].")'><img src='images/eye.gif' border=0 alt='Voir'/></a>");
				
							/* ECO Code */
							echo ("</td><td align='center'>".$tmpGame['eco']);
				
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
		<br/>
		
		<? if ($_SESSION['playerID']!=$player['playerID']) {?>
		<h3>Mes parties contre <? echo($player['nick']); ?></h3>
		
		<form name="endedGames" action="partie.php" method="post">

        <div id="tabliste">
          <table border="0" width="100%">
            <tr>
              <th width="25%">Blancs</th>
              <th width="25%">Noirs</th>
              <th width="10%">Résultat</th>
              <th width="10%">ECO</th>
              <th width="30%">Dernier coup</th>
            </tr>
            <?
					$tmpGames = mysql_query("SELECT G.gameID, G.eco eco, W.nick whiteNick, B.nick blackNick, G.gameMessage, G.messageFrom, DATE_FORMAT(G.dateCreated, '%d/%m/%Y %T') dateCreatedF, DATE_FORMAT(G.lastMove, '%d/%m/%Y %T') lastMove
				                            FROM games G, players W, players B
				                            WHERE (G.gameMessage <> '' AND G.gameMessage <> 'playerInvited' AND G.gameMessage <> 'inviteDeclined')
				                            AND ((G.whitePlayer = ".$player['playerID']." AND G.blackPlayer = ".$_SESSION['playerID'].") OR (G.blackPlayer = ".$player['playerID']." AND G.whitePlayer = ".$_SESSION['playerID']."))
				                            AND W.playerID = G.whitePlayer AND B.playerID = G.blackPlayer
				                            ORDER BY G.dateCreated");
					
					if (mysql_num_rows($tmpGames) == 0)
						echo("<tr><td colspan='6'>Vous n'avez joué aucune partie contre ce joueur</td></tr>\n");
					else
					{
						while($tmpGame = mysql_fetch_array($tmpGames, MYSQL_ASSOC))
						{
							/* White */
							echo("<tr><td>");
							echo($tmpGame['whiteNick']);
							
							/* Black */
							echo ("</td><td>");
							echo($tmpGame['blackNick']);
							
							/* Current Turn */
						
							if (($tmpGame['gameMessage'] == "playerResigned") && ($tmpGame['messageFrom'] == "white"))
								echo("</td><td align=center><a href='javascript:loadEndedGame(".$tmpGame['gameID'].")'>0-1</a>");
							else if (($tmpGame['gameMessage'] == "playerResigned") && ($tmpGame['messageFrom'] == "black"))
								echo("</td><td align=center><a href='javascript:loadEndedGame(".$tmpGame['gameID'].")'>1-0</a>");
							else if (($tmpGame['gameMessage'] == "checkMate") && ($tmpGame['messageFrom'] == "white"))
								echo("</td><td align=center><a href='javascript:loadEndedGame(".$tmpGame['gameID'].")'>1-0</a>");
							else if ($tmpGame['gameMessage'] == "checkMate")
								echo("</td><td align=center><a href='javascript:loadEndedGame(".$tmpGame['gameID'].")'>0-1</a>");
							else
								echo("</td><td align=center><a href='javascript:loadEndedGame(".$tmpGame['gameID'].")'>1/2-1/2</a>");
				
							/* ECO Code */
							echo ("</td><td align='center'>".$tmpGame['eco']);
							
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
		<br/>
		<?}?>
		
		<h3>Statistiques de <? echo($player['nick']); ?></h3>
		<?
		$dateDeb = date("Y-m-d", mktime(0,0,0, 1, 1, 1990));
		$dateFin = date("Y-m-d", mktime(0,0,0, 12, 31, 2020));
		$countLost = countLost($player['playerID'], $dateDeb, $dateFin);
		$nbDefaites = $countLost['nbGames'];
		$countDraw = countDraw($player['playerID'], $dateDeb, $dateFin);
		$nbNulles = $countDraw['nbGames'];
		$countWin = countWin($player['playerID'], $dateDeb, $dateFin);
		$nbVictoires = $countWin['nbGames'];
		$nbParties = $nbDefaites + $nbNulles + $nbVictoires;
		?>
		<table border="0" width="100%">
          <tr>
            <td width="30%"> Victoires : </td>
            <td><? echo($nbVictoires); ?></td>
          </tr>
		  <tr>
            <td> Nulles : </td>
            <td><? echo($nbNulles); ?></td>
          </tr>
		  <tr>
            <td> Défaites : </td>
            <td><? echo($nbDefaites); ?></td>
          </tr>
		 </table>
		 <br/>	
		
<?
    require 'page_footer.php';
    mysql_close();
?>
