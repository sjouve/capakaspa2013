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

$titre_page = _("Play chess against JChess");
$desc_page = _("Play a chess game against JChess");
require 'include/page_header.php';
?>
<script src="javascript/menu.js" type="text/javascript"></script>
<script type="text/javascript">
var g_flip = true;
function nouveau() { document.jchess.NewGame(); }
function jouer() { document.jchess.StartCompute(); }
function retourner() { document.jchess.FlipBoard(g_flip); g_flip = !g_flip; }
function set_level(type, temps, prof)
{
// setlevel avec la profondeur semble ne pas marcher ???
document.jchess.SetLevel(0, parseInt(temps)*1000, parseInt(prof))
}
</script>
<?
$attribut_body = "onload='highlightMenu(7)'";
require 'include/page_body.php';
?>
<div id="contentlarge">
	<div class="contentbody">
	<h3><? echo $titre_page?></h3>
		<center>
		<applet
			code=jchess.class archive="bin/jchess.zip"
			name=jchess width=700 height=400>
		  <PARAM NAME="bgcolor" VALUE="EEEEEE">
		  <PARAM NAME="color" VALUE="#000080">
		  <PARAM NAME="whtfld" VALUE="#F2A521">
		  <PARAM NAME="blkfld" VALUE="#9B6A15">
		  <PARAM NAME="but1txt" VALUE="<? echo _("Config")?>">
		  <PARAM NAME="but2txt" VALUE="<? echo _("Play !")?>">
		  <PARAM NAME="but3txt" VALUE="<? echo _("New game")?>">
		  <PARAM NAME="but4txt" VALUE="<? echo _("About")?>">
		</applet>
		<input type="button" class="button" value="<? echo _("Play with blacks")?>" onclick="nouveau();jouer()" />
		<input type="button" class="button" value="<? echo _("Flip the board")?>" onclick="retourner()" />
		</center>
		<br>
		<br>
		<br>
		<center>
			<script type="text/javascript"><!--
		    google_ad_client = "ca-pub-8069368543432674";
		    /* CapaKaspa JChess Bandeau Bas */
		    google_ad_slot = "8246377058";
		    google_ad_width = 468;
		    google_ad_height = 60;
		    //-->
		    </script>
		    <script type="text/javascript"
		    src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
		    </script>
		</center>
		<br>
		<br>
		<br>
		<br>
	</div>
</div>

<?
require 'include/page_footer.php';
?>
