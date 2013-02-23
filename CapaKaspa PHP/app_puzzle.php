<?session_start();

if (!isset($_CONFIG))
	require 'include/config.php';

require 'dac/dac_players.php';
require 'dac/dac_activity.php';
require 'bwc/bwc_common.php';
require 'bwc/bwc_chessutils.php';
require 'bwc/bwc_players.php';

/* connect to database */
require 'include/connectdb.php';

/* check session status */
require 'include/sessioncheck.php';

require 'include/localization.php';

$titre_page = _("Puzzle of the day");
require 'include/page_header.php';
?>
<script src="javascript/menu.js" type="text/javascript"></script>
<?
$attribut_body = "onload='highlightMenu(9)'";
require 'include/page_body.php';
?>
<div id="contentlarge">
    <div class="contentbody">
		<center>
    	<iframe id="blockrandom" name="iframe" src="http://www.shredderchess.com/online/playshredder/dailytactics.php?lang=<? getLang();?>" width="410" height="440" scrolling="no" align="top" frameborder="0" class="wrapper">
		This option will not work correctly.  Unfortunately, your browser does not support Inline Frames
		</iframe>
		
		</center>
		<br>
		
		<center>
			<script type="text/javascript"><!--
			google_ad_client = "pub-8069368543432674";
			/* 468x60, FlashChess Bandeau Bas */
			google_ad_slot = "4819269420";
			google_ad_width = 468;
			google_ad_height = 60;
			//-->
			</script>
			<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
			</script>
		</center>
		<br/>
		<br/>
		<br/>
		<br/>
	</div>
</div>

<?
require 'include/page_footer.php';
?>
