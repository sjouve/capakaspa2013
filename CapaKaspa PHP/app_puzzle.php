<?session_start();

if (!isset($_CONFIG))
	require 'include/config.php';

require 'dac/dac_players.php';
require 'dac/dac_activity.php';
require 'bwc/bwc_common.php';
require 'bwc/bwc_chessutils.php';
require 'bwc/bwc_players.php';
require 'dac/dac_games.php';
require 'bwc/bwc_games.php';

/* connect to database */
require 'include/connectdb.php';

/* check session status */
//require 'include/sessioncheck.php';

require 'include/localization.php';

$titre_page = _("Chess puzzle of the day");
$desc_page = _("Chess puzzle of the day");
require 'include/page_header.php';
?>
<script src="javascript/menu.js" type="text/javascript"></script>
<?
$attribut_body = "onload='highlightMenu(9)'";
require 'include/page_body.php';
?>
<div id="contentlarge">
    <div class="contentbody">
    	<h3><? echo $titre_page?></h3>
		<br>
		<center>
    	<iframe id="blockrandom" name="iframe" src="http://www.shredderchess.com/online/playshredder/dailytactics.php?lang=<? getLang();?>" width="410" height="440" scrolling="no" align="top" frameborder="0" class="wrapper">
		This option will not work correctly.  Unfortunately, your browser does not support Inline Frames
		</iframe>
		
		</center>
		<br>
		
		
		<br/>
		<br/>
		<br/>
		<br/>
	</div>
</div>

<?
require 'include/page_footer.php';
?>
