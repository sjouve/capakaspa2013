<?
session_start();

/* load settings */
if (!isset($_CONFIG))
	require '../include/config.php';

require '../dac/dac_players.php';
require '../dac/dac_games.php';
require '../bwc/bwc_common.php';
require '../bwc/bwc_chessutils.php';
require '../bwc/bwc_players.php';
require '../bwc/bwc_games.php';

require '../include/connectdb.php';

$titre_page = _("CapaKaspa mobile");
$desc_page = _("Play Chess on your smartphone");

require 'include/page_header.php';
    
require 'include/page_body.php';
?>

	
	<? if (!isset($_SESSION['playerID'])||$_SESSION['playerID']==-1) {?>
		<center>
		<p><? echo _("CapaKaspa mobile will be back soon !");?></p>
		
	  	<p><? echo _("Click on the bottom to access the computer version.");?></p>
	  	
	  	</center>
	<? } else {?>
		<div id="onglet">
		<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td><div class="ongletdisable"><a href="game_list_inprogress.php">Parties</a></div></td>
		</tr>
		</table>
		</div>
		
      	<form name="logout" action="game_list_inprogress.php" method="post">
        <p>Bienvenue <b><? echo ($_SESSION['playerName'])?></b>,</p>
        <p>vous �tes connect� � la zone de jeu d'�checs en diff�r� optimis�e pour les smartphones du site CapaKaspa.</p>
        
        <input type="hidden" name="ToDo" value="Logout">
        <input type="submit" value="Deconnexion" class="button">
        <br/><br/>
		
      	</form>
	<? } ?>
	
	<br/>
	
<?
require 'include/page_footer.php';
mysql_close();
?>
