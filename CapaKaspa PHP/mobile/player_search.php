<?
	session_start(); 

	/* external function */
	require '../dac/dac_players.php';
	require '../bwc/bwc_chessutils.php';
	require '../bwc/bwc_common.php';
	require '../bwc/bwc_players.php';
	
	/* connect to the database */
	require '../include/connectdb.php';
	
	/* check session status */
	require '../include/sessioncheck.php';
	
	
    $titre_page = "Echecs en diff�r� (mobile) - Proposition de partie";
    $desc_page = "Jouer aux �checs en diff�r� sur votre smartphone. Recherchez un adversaire pour lui proposer une partie d'�checs en diff�r�.";
    require 'include/page_header.php';
?>
	<script type="text/javascript">

		function loadPage(page)
		{
			document.searchPlayers.page.value = page;
			document.searchPlayers.submit();
		}
		
	</script>
<?
    require 'include/page_body.php';
?>
	<div id="onglet">
	<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td><div class="ongletdisable"><a href="game_list_inprogress.php">Parties</a></div></td>
		<td><div class="ongletenable">Invitation</div></td>
		<td><div class="ongletdisable"><a href="player_update.php">Mon profil</a></div></td>	
	</tr>
	</table>
	</div>
	
	<h3>Proposez une partie</h3> 
	<form action="game_list_inprogress.php" method="post">
	
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
            $critStatus = isset($_POST['critStatus']) ? $_POST['critStatus']:"actif";
            $critFavorite = isset($_POST['critFavorite']) ? $_POST['critFavorite']:"na";
            
			$pge = isset($_POST['page']) ? $_POST['page']:0;
            $limit = 25;
			$debut = $pge*$limit;
			$nb_tot = 0;
			$res_count = searchPlayers("count", 0, 0, $_POST['critFavorite'], $critStatus, $_POST['critEloStart'], $_POST['critEloEnd']); 
			if ($res_count)
			{
				$count = mysql_fetch_array($res_count, MYSQL_ASSOC);
				$nb_tot = $count['nbPlayers'];
			}
			$nbpages = ceil($nb_tot/$limit); // ceil = plafond : pour arrondir � la valeur sup�rieure
			$resultats = searchPlayers("", $debut, $limit, $_POST['critFavorite'], $critStatus, $_POST['critEloStart'], $_POST['critEloEnd']); 
			
		?>
		<h3>Rechercher un joueur</h3>
		
			<form name="searchPlayers" action="player_search.php" method="post">
				<table border="0" width="100%">
		          <tr>
		            <td width="20%">Favoris :</td>
		            <td width="24%">
		              <input name="critFavorite" type="radio" value="na" <?if ($critFavorite=='na') echo('checked');?>>
		              Indiff. 
		            </td>
		            <td width="26%">
		              <input name="critFavorite" type="radio" value="oui" <?if ($critFavorite=='oui') echo('checked');?>>
		              <img src='images/favori-etoile-icone.png' /> Oui  
		            </td>
		            <td width="30%">&nbsp;</td>
		          </tr>
		          <tr>
		            <td>Activit� :</td>
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
		            <td width="20%">Elo :</td>
		            <td>
		              Entre <input name="critEloStart" type="text" size="4" maxlength="4" value="<? echo($_POST['critEloStart']);?>">
		            </td>
		            <td>
		              et <input name="critEloEnd" type="text" size="4" maxlength="4" value="<? echo($_POST['critEloEnd']);?>">
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
	
	<div class="tabliste">
	<table border="0" width="100%">
	  <tr>
	  	
		<th width="25%">Surnom</th>
		<th width="10%">Elo</th>
		<th width="40%">Profil</th>
		<th width="25%">Invitation</th>
	  </tr>
		<?
			if (mysql_num_rows($resultats) == 0)
				echo("<tr><td colspan='6'>Il n'y a pas de joueurs</td></tr>\n");
			else
			{
				while($tmpPlayer = mysql_fetch_array($resultats, MYSQL_ASSOC))
				{
					echo ("<tr>");
					echo ("<form action='game_list_inprogress.php' method='post'>");
					echo ("<input type='hidden' name='ToDo' value='InvitePlayer'>");
					
					echo ("<td>");
					echo ("<input type='hidden' name='opponent' value='".$tmpPlayer['playerID']."'><a href='player_view.php?playerID=".$tmpPlayer['playerID']."'>".substr($tmpPlayer['nick'],0,15)."</a>");
					if ($tmpPlayer['lastActionTime'])
							echo("<br/><img src='images/user_online.gif'/>");
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
    require 'include/page_footer.php';
?>
