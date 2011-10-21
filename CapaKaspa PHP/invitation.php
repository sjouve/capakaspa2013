<?
	session_start(); 

	/* chess utils */
	require_once('chessutils.php');
	
	/* check session status */
	require 'sessioncheck.php';
	
	//require 'dac_players.php';
	
	$Mode = isset($_GET['mode']) ? $_GET['mode']:Null;

    $titre_page = 'Proposition de partie';
    require 'page_header.php';
    $image_bandeau = 'bandeau_capakaspa_zone.jpg';
    $barre_progression = "Echecs en différé > Les autres joueurs";
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
	if ($Mode == 'passif')
	{
		$tmpPlayers = listPlayersPassifs();
	}
	else
	{
		$tmpPlayers = listPlayersActifs();
	}	
	?>
	<div id="tabliste">
	<?
	if ($Mode == 'passif')
	{
		echo ("<img src='images/joueur_actif.gif' /> <a href='invitation.php?mode=actif'>Voir les joueurs actifs</a>");
	}
	else
	{
		echo ("<img src='images/joueur_passif.gif' /> <a href='invitation.php?mode=passif'>Voir les joueurs passifs</a>");
	}
	?>
	
	<table border="0" width="680">
	  <tr>
	  	
		<th>Surnom</th>
		<th>Elo</th>
		<th>Age</th>
		<th>Localisation</th>
		<th>Profil</th>
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
					echo (stripslashes($tmpPlayer['situationGeo']));
					echo ("</td>");
					
					echo ("<td>");
					echo (stripslashes($tmpPlayer['profil']));
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
