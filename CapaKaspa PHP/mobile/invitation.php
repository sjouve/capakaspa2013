<?
	session_start(); 

	/* chess utils */
	require_once('../chessutils.php');
	
	/* check session status */
	require '../sessioncheck.php';
	
	//require 'dac_players.php';
	
	$Mode = isset($_GET['mode']) ? $_GET['mode']:'actif';
	$level = isset($_GET['level']) ? $_GET['level']:'DEB';
	
    $titre_page = "Echecs en différé (mobile) - Proposition de partie";
    $desc_page = "Jouer aux échecs en différé sur votre smartphone. Recherchez un adversaire pour lui proposer une partie d'échecs en différé.";
    require 'page_header.php';
    require 'page_body.php';
?>
	<div id="onglet">
	<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td><div class="ongletdisable"><a href="tableaubord.php">Parties</a></div></td>
		<td><div class="ongletenable">Invitation</div></td>
		<td><div class="ongletdisable"><a href="profil.php">Mon profil</a></div></td>	
	</tr>
	</table>
	</div>
	
	<h3>Proposez une partie</h3> 
	<form action="tableaubord.php" method="post">
	
		<input type="hidden" name="ToDo" value="InvitePlayerByNick">
	
		<table width="100%">
			<tr>
				<td >
					Position : <input type="radio" name="type" value="0" checked> Normale
				</td>
			</tr>
			<tr>
				<td>
					<input type="radio" name="type" value="1"><img src="/images/mosaique/white_king.gif">,<img src="/images/mosaique/white_pawn.gif"> et
					<input type="checkbox" name="flagBishop" value="1"><img src="/images/mosaique/white_bishop.gif">
					<input type="checkbox" name="flagKnight" value="1"><img src="/images/mosaique/white_knight.gif">
					<input type="checkbox" name="flagRook" value="1"><img src="/images/mosaique/white_rook.gif">
					<input type="checkbox" name="flagQueen" value="1"><img src="/images/mosaique/white_quuen.gif">
				</td>
			</tr>
		</table>
		<table width="100%">
			<tr>
				<td>Surnom :
					<input name="txtNick" type="text" size="20" maxlength="20">
				</td>
			</tr>
			<tr>
				<td>Votre couleur :
					<input type="radio" name="color" value="white" checked> Blancs
					<input type="radio" name="color" value="black"> Noirs
				
					<input type="submit" value="Inviter">
				</td>
			</tr>  
		</table>
	</form>
	<br/>
	<?
	/* connect to the database */
	require '../connectdb.php';
	switch($Mode)
	{
		case 'passif':
		$tmpPlayers = listPlayersPassifs();
		break;
		
		case 'actif':
		$tmpPlayers = listPlayersActifs();
		break;
		
		case 'favoris':
		$tmpPlayers = listPlayersFavoris($_SESSION['playerID']);
		break;
		
		case 'elo':
		$tmpPlayers = listPlayersByLevel($_GET['level']);
		break;
	}	
	?>
	<h3>Tous les joueurs</h3>
	<table>
	<tr>
	<td>
		<img src='images/joueur_actif.gif' /><?if ($Mode != 'actif') echo("<a href='invitation.php?mode=actif'>");?> Actifs<?if ($Mode != 'actif') echo("</a>");?> | 
		<img src='images/joueur_passif.gif' /><?if ($Mode != 'passif') echo("<a href='invitation.php?mode=passif'>");?> Passifs<?if ($Mode != 'passif') echo("</a>");?> | 
		<img src='images/favori-etoile-icone.png' /><?if ($Mode != 'favoris') echo("<a href='invitation.php?mode=favoris'>");?> Favoris<?if ($Mode != 'favoris') echo("</a>");?>
	</td>
	</tr>
	<tr>
	<td>
		Elo : 
		<?if ($Mode != 'elo' || ($Mode == 'elo' && $level != 'DEB')) echo("<a href='invitation.php?mode=elo&level=DEB'>");?>< 1300<?if ($Mode != 'elo' || ($Mode == 'elo' && $level != 'DEB')) echo("</a>");?> | 
		<?if ($Mode != 'elo' || ($Mode == 'elo' && $level != 'MOY')) echo("<a href='invitation.php?mode=elo&level=MOY'>");?>= 1300<?if ($Mode != 'elo' || ($Mode == 'elo' && $level != 'MOY')) echo("</a>");?> | 
		<?if ($Mode != 'elo' || ($Mode == 'elo' && $level != 'COF')) echo("<a href='invitation.php?mode=elo&level=COF'>");?>1301 à 1400<?if ($Mode != 'elo' || ($Mode == 'elo' && $level != 'COF')) echo("</a>");?> | 
		<?if ($Mode != 'elo' || ($Mode == 'elo' && $level != 'MAI')) echo("<a href='invitation.php?mode=elo&level=MAI'>");?>> 1400<?if ($Mode != 'elo' || ($Mode == 'elo' && $level != 'MAI')) echo("</a>");?> 
	</td>
	</tr>
	</table>
	
	<div id="tabliste">
	<table border="0" width="100%">
	  <tr>
	  	
		<th width="25%">Surnom</th>
		<th width="10%">Elo</th>
		<th width="40%">Profil</th>
		<th width="25%">Invitation</th>
	  </tr>
		<?
			if (mysql_num_rows($tmpPlayers) == 0)
				echo("<tr><td colspan='6'>Il n'y a pas de joueurs</td></tr>\n");
			else
			{
				while($tmpPlayer = mysql_fetch_array($tmpPlayers, MYSQL_ASSOC))
				{
					echo ("<tr>");
					echo ("<form action='tableaubord.php' method='post'>");
					echo ("<input type='hidden' name='ToDo' value='InvitePlayer'>");
					
					echo ("<td>");
					echo ("<input type='hidden' name='opponent' value='".$tmpPlayer['playerID']."'><a href='profil_consultation.php?playerID=".$tmpPlayer['playerID']."'>".substr($tmpPlayer['nick'],0,15)."</a>");
					echo ("</td>");
					
					echo ("<td>");
					echo ($tmpPlayer['elo']);
					echo ("</td>");
					
					echo ("<td>");
					echo ("<TEXTAREA NAME='txtProfil' COLS='20' ROWS='5' readonly='readonly'>".stripslashes($tmpPlayer['profil'])."</TEXTAREA>");
					echo ("</td>");
					
					echo ("<td>");
					echo ("<input type='radio' name='color' value='white' checked> Blancs
							<input type='radio' name='color' value='black'> Noirs
							<input type='submit' value='Inviter'>");
					echo ("</td>");
					echo ("</form>"); 
					echo ("</tr>");
				}
			}
		?>
				
	</table>
	</div>
	<br/>
	<?
		mysql_close();
	?>

<?
    require 'page_footer.php';
?>
