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

$titre_page = _("Play chess against SparkChess");
$desc_page = _("Play a chess game against SparkChess");
require 'include/page_header.php';
?>
<script src="javascript/menu.js" type="text/javascript"></script>
<?
$attribut_body = "onload='highlightMenu(10)'";
require 'include/page_body.php';
?>
<div id="contentlarge">
    <div class="contentbody">
		<h3><? echo $titre_page?></h3>
		<p><?echo _("Play chess with sparkChess a Flash chess game with a 3D chessboard. 3 levels available.");?></p>
		<br/>
    	
    	<embed width="760" height="550" name="plugin" src="bin/sparkchess.swf" type="application/x-shockwave-flash">
		
		<br/>
		<br/>
		<br/>
	</div>
</div>
<?
require 'include/page_footer.php';
?>
