<?
session_start();
	
/* load settings */
if (!isset($_CONFIG))
	require 'include/config.php';

require 'dac/dac_players.php';
require 'dac/dac_activity.php';
require 'dac/dac_common.php';
require 'bwc/bwc_common.php';
require 'bwc/bwc_chessutils.php';
require 'bwc/bwc_players.php';
require 'dac/dac_games.php';
require 'bwc/bwc_games.php';

/* connect to the database */
require 'include/connectdb.php';
		
/* check session status */
require 'include/sessioncheck.php';

require 'include/localization.php';

// Traitement des critères
$critCountry = isset($_POST['critCountryCode']) ? $_POST['critCountryCode'] : "";
$critName = isset($_POST['critName']) ? $_POST['critName'] : "";
$critEloStart = isset($_POST['critEloStart']) ? $_POST['critEloStart'] : "";
$critEloEnd = isset($_POST['critEloEnd']) ? $_POST['critEloEnd'] : "";
$critStatus = isset($_POST['critStatus']) ? $_POST['critStatus']:"actif";
$critFavorite = isset($_POST['critFavorite']) ? $_POST['critFavorite']:"na";

$titre_page = _("Search for players");
$desc_page = _("Search for players, play and share chess");
require 'include/page_header.php';
?>
<script src="javascript/menu.js" type="text/javascript"></script>
<script src="javascript/player.js" type="text/javascript"></script>
<script type="text/javascript">
function getheight() {
	var myWidth = 0,
		myHeight = 0;
	if (typeof(window.innerWidth) == 'number') {
		//Non-IE
		myWidth = window.innerWidth;
		myHeight = window.innerHeight;
	} else if (document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)) {
		//IE 6+ in 'standards compliant mode'
		myWidth = document.documentElement.clientWidth;
		myHeight = document.documentElement.clientHeight;
	} else if (document.body && (document.body.clientWidth || document.body.clientHeight)) {
		//IE 4 compatible
		myWidth = document.body.clientWidth;
		myHeight = document.body.clientHeight;
	}
		var scrolledtonum = window.pageYOffset + myHeight + 2;
		var heightofbody = document.body.offsetHeight;
		if (scrolledtonum >= heightofbody && document.getElementById("playerStartPage")) {
			displayPlayers(document.getElementById("playerStartPage").value,<? echo($_SESSION['playerID']);?>,'<? echo($critFavorite);?>','<? echo($critStatus);?>','<? echo($critEloStart);?>','<? echo($critEloEnd);?>','<? echo($critCountry);?>','<? echo($critName);?>');
	}
}

window.onscroll = getheight;
	
</script>
<?
$attribut_body = "onload=\"highlightMenu(5);displayPlayers(0,".$_SESSION['playerID'].",'".$critFavorite."','".$critStatus."','".$critEloStart."','".$critEloEnd."','".$critCountry."','".$critName."')\"";
require 'include/page_body.php';
?>
<div id="content">
	<div class="contentbody">
  
		<?
            $nb_tot=0;
			$res_count = searchPlayers("count", 0, 0, $_SESSION['playerID'], $critFavorite, $critStatus, $critEloStart, $critEloEnd, $critCountry, $critName); 
			if ($res_count)
			{
				$count = mysqli_fetch_array($res_count, MYSQLI_ASSOC);
				$nb_tot = $count['nbPlayers'];
			}
			
		?>
		<div class="blockform">
		<h3><? echo _("Players advanced search");?></h3>
		<div id="searchForm">
			<form name="searchPlayers" action="player_search.php" method="post">
				<table border="0" width="100%">
		        	<tr>
			            <td><?echo _("Country");?> :</td>
			            <td colspan="3"><select name="critCountryCode" id="critCountryCode">
				            <?
				            echo "\t",'<option value="">', _("All countries") ,'</option>',"\n";
				            $tmpCountries = listCountriesByLang(getLang());
				            while($tmpCountry = mysqli_fetch_array($tmpCountries, MYSQLI_ASSOC))
				            {
				            	$selected = "";
				            	if($tmpCountry['countryCode'] == $critCountry)
				            	{
				            		$selected = " selected";
				            	}
				            	echo "\t",'<option value="', $tmpCountry['countryCode'] ,'"', $selected ,'>', $tmpCountry['countryName'] ,'</option>',"\n";
				            }	
				            ?>
			            </select></td>
		        	</tr>
		        	<tr>
			            <td><?echo _("Name");?> :</td>
			            <td colspan="3">
			            	<input name="critName" type="text" size="40" maxlength="20" value="<? echo($critName);?>" placeholder="<? echo _("Part of user name, first name or last name");?>">
			            </td>
		        	</tr>
		        	<tr>
			            <td><? echo _("Last connection");?> :</td>
			            <td>
			              <input name="critStatus" type="radio" value="tous" <?if ($critStatus=='tous') echo('checked');?>>
			              <? echo _("All");?>
			            </td>
			            <td> 
			              <input name="critStatus" type="radio" value="actif" <?if ($critStatus=='actif') echo('checked');?>>
			               <? echo _("less than 15 days");?>
			            </td>
			            <td>
			              <input name="critStatus" type="radio" value="passif" <?if ($critStatus=='passif') echo('checked');?>>
			               <? echo _("more than 14 days");?>
			            </td>
		          	</tr>
		          	<tr>
			            <td ><? echo _("Elo ranking");?> :</td>
			            <td>
			              <? echo _("Between");?> <input name="critEloStart" type="text" size="4" maxlength="4" value="<? echo($critEloStart);?>">
			            </td>
			            <td>
			              <? echo _("and");?> <input name="critEloEnd" type="text" size="4" maxlength="4" value="<? echo($critEloEnd);?>">
			            </td>
			            <td align="right">
			            	<input type="submit" name="Filter" value="<? echo _("Filter");?>" class="button">	
			            </td>
		          	</tr>	          
		        </table>
			</form>
			</div>  	        	
        </div>
        <? echo($nb_tot." "._("player(s) found"));?>
        <div id="players0" style="display: none;"><img src='images/ajaxloader.gif'/></div>
        
	</div>
</div>
<div id="rightbar">
	<div id="suggestions">
		<? displaySuggestion();?>
	</div>
	<?require 'include/page_footer_right.php';?>
</div>
<?
require 'include/page_footer.php';
mysqli_close($dbh);
?>
