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

// Traitement des crit�res
$critCountry = isset($_POST['critCountryCode']) ? $_POST['critCountryCode'] : "";
$critGameType = isset($_POST['critGameTypeCode']) ? $_POST['critGameTypeCode'] : "0";
$critOrder = isset($_POST['critOrder']) ? $_POST['critOrder'] : "DESC";

$player = getPlayer($_SESSION['playerID']);
if ($critGameType == 0)
	$player_rank = $player['rank'];
else
	$player_rank = $player['rank960'];

$titre_page = _("Players ranking");
$desc_page = _("Players ranking by country and game type");
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
			displayPlayersRanking(document.getElementById("playerStartPage").value,<? echo($_SESSION['playerID']);?>,'<? echo($critCountry);?>','<? echo($critGameType);?>','<? echo($critOrder);?>');
	}
}

window.onscroll = getheight;
	
</script>
<?
$nb_tot=0;
$res_count = searchPlayersRanking("count", 0, 0, "", $critCountry, $critGameType, $critOrder); 
if ($res_count)
{
	$count = mysqli_fetch_array($res_count, MYSQLI_ASSOC);
	$nb_tot = $count['nbPlayers'];
}

$attribut_body = "onload=\"highlightMenu(15);displayPlayersRanking(0,".$_SESSION['playerID'].",'".$critCountry."','".$critGameType."','".$critOrder."')\"";
require 'include/page_body.php';
?>
<div id="content">
	<div class="contentbody">
		<div class="blockform">
		<h3><? echo _("Players ranking");?></h3>
		<p><a href="http://www.capakaspa.info/propos-contact/aide/" target="_blank"><? echo _("How is the ranking of players calculated?");?></a></p>
		<div id="searchForm">
			<form name="searchPlayers" action="player_ranking.php" method="post">
				<table border="0" width="100%">
		        	<tr>
			            <td><?echo _("Choose ranking");?> :</td>
			            <td>
			            	<select name="critGameTypeCode" id="critGameTypeCode">
				            	<option value="0" <?php if ($critGameType == 0) echo(" selected");?>><?echo _("Classic game")?></option>
				            	<option value="2" <?php if ($critGameType == 2) echo(" selected");?>><?echo _("Chess960")?></option>
			            	</select>
			            </td>
		        	</tr>
		        	<tr>
			            <td><?echo _("Order ranking");?> :</td>
			            <td>
			            	<select name="critOrder" id="critOrder">	            		
				            	<option value="DESC" <? if ($critOrder == "DESC") echo(" selected");?>><?echo _("From first to last")?></option>
				            	<option value="ASC" <? if ($critOrder == "ASC") echo(" selected");?>><?echo _("From last to first")?></option>
			            	</select>
			            </td>
		        	</tr>
		        	<tr>
			            <td><?echo _("Filter by country");?> :</td>
			            <td ><select name="critCountryCode" id="critCountryCode">
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
			            <td colspan="2" align="right">
			            	<input type="submit" name="Filter" value="<? echo _("Filter");?>" class="button">	
			            </td>
		          	</tr>	          
		        </table>
			</form>
			   	
        </div>
        </div>
        <?php echo(_("Your rank is")." : ".$player_rank);?><br><br>
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
