<?
	session_start(); 

	/* chess utils */
	require_once('chessutils.php');
	
	/* check session status */
	require 'sessioncheck.php';
	
	//require 'dac_players.php';
	
	$Mode = isset($_GET['mode']) ? $_GET['mode']:'actif';
	$level = isset($_GET['level']) ? $_GET['level']:'DEB';
	
    $titre_page = "Echecs en différé - Proposition de partie";
    $desc_page = "Jouer aux échecs en différé. Recherchez un adversaire pour lui proposer une partie d'échecs en différé.";
    require 'page_header.php';
    $image_bandeau = 'bandeau_capakaspa_zone.jpg';
    $barre_progression = "<a href='/'>Accueil</a> > Echecs en différé > Les autres joueurs";
    require 'page_body.php';
?>
   <div id="contentlarge">
	<div class="blogbody">
  
	<table>
		<tr>
		<td valign="middle"><img src="images/ampoule.jpg"></td> 
		<td valign="middle">Un joueur passif ne s'est plus connecté au site depuis 2 semaines : invitez le !</td>
		
        </tr>
        </table>
        <br/>
	<form action="tableaubord.php" method="post">
	<h3>Proposez une nouvelle partie au joueur de votre choix</h3>
		<input type="hidden" name="ToDo" value="InvitePlayerByNick">
	
		<table width="100%">
			<tr>
				<td width="35%">
					Position : <input type="radio" name="type" value="0" checked> Normale
				</td>
				<td width="65%">
					<input type="radio" name="type" value="1"> Roi, Pions et
					<input type="checkbox" name="flagBishop" value="1"> Fous
					<input type="checkbox" name="flagKnight" value="1"> Cavaliers
					<input type="checkbox" name="flagRook" value="1"> Tours
					<input type="checkbox" name="flagQueen" value="1"> Dames
				</td>
			</tr>
		</table>
		<table width="100%">
			<tr>
				<td width="35%">Surnom :
					<input name="txtNick" type="text" size="20" maxlength="20">
				</td>
				<td width="40%">Votre couleur :
					<input type="radio" name="color" value="white" checked> Blancs
					<input type="radio" name="color" value="black"> Noirs
				</td>
				<td width="25%" >
					<input type="submit" value="Inviter">
				</td>
			</tr>
	        <!--<tr>
				<td colspan="2">
                    <TEXTAREA NAME="txtMessage" COLS="50" ROWS="5"></TEXTAREA>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" value="Inviter">
				</td>
			</tr>-->
		</table>
	</form>
	<br/>
	<?
	/* connect to the database */
	require 'connectdb.php';
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
	<div id="tabliste">
	
	<img src='images/joueur_actif.gif' /> <?if ($Mode != 'actif') echo("<a href='invitation.php?mode=actif'>");?>Les joueurs actifs<?if ($Mode != 'actif') echo("</a>");?> - 
	<img src='images/joueur_passif.gif' /> <?if ($Mode != 'passif') echo("<a href='invitation.php?mode=passif'>");?>Les joueurs passifs<?if ($Mode != 'passif') echo("</a>");?> - 
	<img src='images/favori-etoile-icone.png' /> <?if ($Mode != 'favoris') echo("<a href='invitation.php?mode=favoris'>");?>Mes joueurs favoris<?if ($Mode != 'favoris') echo("</a>");?>
	<br/>
	Les joueurs dont le Elo est : 
	<?if ($Mode != 'elo' || ($Mode == 'elo' && $level != 'DEB')) echo("<a href='invitation.php?mode=elo&level=DEB'>");?>inférieur à 1300<?if ($Mode != 'elo' || ($Mode == 'elo' && $level != 'DEB')) echo("</a>");?> - 
	<?if ($Mode != 'elo' || ($Mode == 'elo' && $level != 'MOY')) echo("<a href='invitation.php?mode=elo&level=MOY'>");?>égal à 1300<?if ($Mode != 'elo' || ($Mode == 'elo' && $level != 'MOY')) echo("</a>");?> - 
	<?if ($Mode != 'elo' || ($Mode == 'elo' && $level != 'COF')) echo("<a href='invitation.php?mode=elo&level=COF'>");?>entre 1301 et 1400<?if ($Mode != 'elo' || ($Mode == 'elo' && $level != 'COF')) echo("</a>");?> - 
	<?if ($Mode != 'elo' || ($Mode == 'elo' && $level != 'MAI')) echo("<a href='invitation.php?mode=elo&level=MAI'>");?>supérieur à 1400<?if ($Mode != 'elo' || ($Mode == 'elo' && $level != 'MAI')) echo("</a>");?> 
	
	<table border="0" width="680">
	  <tr>
	  	
		<th width="20%">Surnom</th>
		<th width="5%">Elo</th>
		<th width="5%">Age</th>
		<th width="15%">Localisation</th>
		<th width="35%">Profil</th>
		<th width="20%">Invitation</th>
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
					echo ("<input type='hidden' name='opponent' value='".$tmpPlayer['playerID']."'><a href='profil_consultation.php?playerID=".$tmpPlayer['playerID']."'>".$tmpPlayer['nick']."</a>");
					echo ("</td>");
					echo ("<td>");
					echo ($tmpPlayer['elo']);
					echo ("</td>");
					echo ("<td align='right'>");
					echo (date("Y")-$tmpPlayer['anneeNaissance']);
					echo ("</td>");
					echo ("<td>");
					echo ("<TEXTAREA NAME='txtProfil' COLS='15' ROWS='3' readonly='readonly'>".stripslashes($tmpPlayer['situationGeo'])."</TEXTAREA>");
					echo ("</td>");
					
					echo ("<td>");
					echo ("<TEXTAREA NAME='txtProfil' COLS='45' ROWS='3' readonly='readonly'>".stripslashes($tmpPlayer['profil'])."</TEXTAREA>");
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
	<?
		mysql_close();
	?>
</div>
</div>
<?
    require 'page_footer.php';
?>
