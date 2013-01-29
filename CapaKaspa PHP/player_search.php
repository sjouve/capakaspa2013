<?
session_start();
	
/* load settings */
if (!isset($_CONFIG))
	require 'include/config.php';

require 'dac/dac_players.php';
require 'bwc/bwc_common.php';
require 'bwc/bwc_chessutils.php';
require 'bwc/bwc_players.php';
	
/* connect to the database */
require 'include/connectdb.php';
		
/* check session status */
require 'include/sessioncheck.php';
	
$titre_page = _("Search for players");
$desc_page = _("");
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
	<div id="contentlarge">
		<div class="contentbody">
  
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
		<h3><? echo _("Players advanced search");?></h3>
		<div>
			<form name="searchPlayers" action="player_search.php" method="post">
				<table border="0" width="650">
		          <tr>
		            <td width="180"><? echo _("Followings");?> :</td>
		            <td>
		              <input name="critFavorite" type="radio" value="na" <?if ($critFavorite=='na') echo('checked');?>>
		              <? echo _("Anyone");?>
		            </td>
		            <td colspan="2">
		              <input name="critFavorite" type="radio" value="oui" <?if ($critFavorite=='oui') echo('checked');?>>
		              <img src='images/favori-etoile-icone.png' /> <? echo _("Yes");?>  
		              
		            </td>
		            
		          </tr>
		          <tr>
		            <td width="180"><? echo _("Activity");?> :</td>
		            <td>
		              <input name="critStatus" type="radio" value="tous" <?if ($critStatus=='tous') echo('checked');?>>
		              <? echo _("All");?>
		            </td>
		            <td> 
		              <input name="critStatus" type="radio" value="actif" <?if ($critStatus=='actif') echo('checked');?>>
		              <img src='images/joueur_actif.gif' /> <? echo _("Active");?>
		            </td>
		            <td>
		              <input name="critStatus" type="radio" value="passif" <?if ($critStatus=='passif') echo('checked');?>>
		              <img src='images/joueur_passif.gif' /> <? echo _("Passive");?>
		            </td>
		          </tr>
		          <tr>
		            <td width="180"><? echo _("Elo ranking");?> :</td>
		            <td>
		              <? echo _("Between");?> <input name="critEloStart" type="text" size="4" maxlength="4" value="<? echo($critEloStart);?>">
		            </td>
		            <td>
		              <? echo _("and");?> <input name="critEloEnd" type="text" size="4" maxlength="4" value="<? echo($critEloEnd);?>">
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
        
		<div class="tabliste">  	
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
						echo ("<form action='game_new.php' method='post'>");
						echo ("<input type='hidden' name='ToDo' value='InvitePlayer'>");
						echo ("<td>");
						echo ("<input type='hidden' name='opponent' value='".$tmpPlayer['nick']."'><a href='player_view.php?playerID=".$tmpPlayer['playerID']."'>".$tmpPlayer['nick']."</a><br/>");
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
						echo ("<td>
								<input type='submit' class='link' value='"._("New game")."'>");
						echo ("</td>");
						echo ("</form>"); 
						echo ("</tr>");
					}
				}
			?>
					
		</table>
		</div>
		</div>
	</div>
<?
require 'include/page_footer.php';
mysql_close();
?>
