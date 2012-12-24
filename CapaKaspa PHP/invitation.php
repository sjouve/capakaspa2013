<?
	session_start(); 

	/* chess utils */
	require_once('chessutils.php');
	
	require 'gui_list.php';
	
	/* connect to the database */
	require 'connectdb.php';
		
	/* check session status */
	require 'sessioncheck.php';
	
	//require 'dac_players.php';
	
    $titre_page = "Echecs en différé - Proposition de partie";
    $desc_page = "Jouer aux échecs en différé. Recherchez un adversaire pour lui proposer une partie d'échecs en différé.";
    require 'page_header.php';
?>
	<script type="text/javascript">

		function loadPage(page)
		{
			document.searchPlayers.page.value = page;
			document.searchPlayers.submit();
		}
		
	</script>
<?
    $image_bandeau = 'bandeau_capakaspa_zone.jpg';
    $barre_progression = "<a href='/'>Accueil</a> > Echecs en différé > Les autres joueurs";
    require 'page_body.php';
?>
	<div id="contentlarge">
		<div class="blogbody">
  
		
		<form action="tableaubord.php" method="post">
		<h3>Proposez une nouvelle partie au joueur de votre choix <a href="manuel-utilisateur-jouer-echecs-capakaspa.pdf#page=10" target="_blank"><img src="images/point-interrogation.gif" border="0"/></a></h3>
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
				<td width="25%">
					<input type="submit" value="Inviter">
				</td>
			</tr>
		</table>
		</form>
		<br/>
		<table width="90%">
			<tr>
			<td valign="middle"><img src="images/ampoule.jpg"></td> 
			<td valign="middle">Un joueur est considéré passif s'il ne s'est plus connecté au site depuis 2 semaines, ramenez-le parmi nous : invitez le !</td>
	        </tr>
		</table>
    	<br/>
		<?
            $critEloStart = isset($_POST['critEloStart']) ? $_POST['critEloStart'] : "";
            $critEloEnd = isset($_POST['critEloEnd']) ? $_POST['critEloEnd'] : "";
            $critStatus = isset($_POST['critStatus']) ? $_POST['critStatus']:"actif";
            $critFavorite = isset($_POST['critFavorite']) ? $_POST['critFavorite']:"na";
            
			$pge = isset($_POST['page']) ? $_POST['page']:0;
            $limit = 25;
			$debut = $pge*$limit;
			$nb_tot = 0;
			$res_count = searchPlayers("count", 0, 0, $critFavorite, $critStatus, $critEloStart, $critEloEnd); 
			if ($res_count)
			{
				$count = mysql_fetch_array($res_count, MYSQL_ASSOC);
				$nb_tot = $count['nbPlayers'];
			}
			
			$nbpages = ceil($nb_tot/$limit); // ceil = plafond : pour arrondir à la valeur supérieure
			$resultats = searchPlayers("", $debut, $limit, $critFavorite, $critStatus, $critEloStart, $critEloEnd); 
			
		?>
		<h3>Rechercher un joueur </h3>
		<div>
			<form name="searchPlayers" action="invitation.php" method="post">
				<table border="0" width="650">
		          <tr>
		            <td width="180">Vos joueurs favoris :</td>
		            <td>
		              <input name="critFavorite" type="radio" value="na" <?if ($critFavorite=='na') echo('checked');?>>
		              Indifférent 
		            </td>
		            <td colspan="2">
		              <input name="critFavorite" type="radio" value="oui" <?if ($critFavorite=='oui') echo('checked');?>>
		              <img src='images/favori-etoile-icone.png' /> Oui  
		              
		            </td>
		            
		          </tr>
		          <tr>
		            <td width="180">Activité :</td>
		            <td>
		              <input name="critStatus" type="radio" value="tous" <?if ($critStatus=='tous') echo('checked');?>>
		              Tous
		            </td>
		            <td> 
		              <input name="critStatus" type="radio" value="actif" <?if ($critStatus=='actif') echo('checked');?>>
		              <img src='images/joueur_actif.gif' /> Actif
		            </td>
		            <td>
		              <input name="critStatus" type="radio" value="passif" <?if ($critStatus=='passif') echo('checked');?>>
		              <img src='images/joueur_passif.gif' /> Passif
		            </td>
		          </tr>
		          <tr>
		            <td width="180">Classement Elo :</td>
		            <td>
		              Entre <input name="critEloStart" type="text" size="4" maxlength="4" value="<? echo($critEloStart);?>">
		            </td>
		            <td>
		              et <input name="critEloEnd" type="text" size="4" maxlength="4" value="<? echo($critEloEnd);?>">
		            </td>
		            <td>
		            	<input name="Filter" type="submit" value="Filtrer">
		
		            </td>
		          </tr>
		          
		        </table>
		        
	        	<?
	         	displayPageNav($pge, $limit, $nb_tot, $nbpages);
	        	?>
	        	
	        	<input type="hidden" name="page" value="">
	        </form>
        </div>
        
		<div id="tabliste">  	
		<table border="0" width="100%">
		  	<tr>
			<th width="20%">Surnom</th>
			<th width="5%">Elo</th>
			<th width="5%">Age</th>
			<th width="15%">Localisation</th>
			<th width="35%">Profil</th>
			<th width="20%">Invitation</th>
		  	</tr>
			<?
				if ($nb_tot == 0)
					echo("<tr><td colspan='6'>Il n'y a pas de joueurs</td></tr>\n");
				else
				{
					while($tmpPlayer = mysql_fetch_array($resultats, MYSQL_ASSOC))
					{
						echo ("<tr valign='top'>");
						echo ("<form action='tableaubord.php' method='post'>");
						echo ("<input type='hidden' name='ToDo' value='InvitePlayer'>");
						echo ("<td>");
						echo ("<input type='hidden' name='opponent' value='".$tmpPlayer['playerID']."'><a href='profil_consultation.php?playerID=".$tmpPlayer['playerID']."'>".$tmpPlayer['nick']."</a><br/>");
						if ($tmpPlayer['lastActionTime'])
							echo("<img src='images/user_online.gif'/>");
						if (isNewPlayer($tmpPlayer['creationDate']))
							echo("<img src='images/user_new.gif'/>");
						echo ("</td>");
						echo ("<td>");
						echo ($tmpPlayer['elo']);
						echo ("</td>");
						echo ("<td align='right'>");
						echo (date("Y")-$tmpPlayer['anneeNaissance']);
						echo ("</td>");
						echo ("<td>");
						//echo ("<TEXTAREA NAME='txtProfil' COLS='13' ROWS='3' readonly='readonly'>".stripslashes($tmpPlayer['situationGeo'])."</TEXTAREA>");
						echo ("<div style='word-wrap: break-word;width: 90px;'>".stripslashes($tmpPlayer['situationGeo'])."</div>");
						echo ("</td>");
						echo ("<td>");
						//echo ("<TEXTAREA NAME='txtProfil' COLS='40' ROWS='3' readonly='readonly'>".stripslashes($tmpPlayer['profil'])."</TEXTAREA>");
						echo ("<div style='word-wrap: break-word;width: 220px;'>".stripslashes($tmpPlayer['profil'])."</div>");
						echo ("</td>");
						echo ("<td><input type='radio' name='color' value='white' checked> Blancs
								<input type='radio' name='color' value='black'> Noirs <br/>
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
