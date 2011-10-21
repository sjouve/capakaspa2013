<?
	session_start();
	$titre_page = 'Jouer aux échecs contre JChess';
    require 'page_header.php';
?>
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
    $image_bandeau = 'bandeau_capakaspa_zone.jpg';
    $barre_progression = "Jeux en ligne > Jouer contre JChess";
    require 'page_body.php';
?>
  <div id="content">
    <div class="blogbody">
    <table>
		<tr>
		<td valign="middle"><img src="images/ampoule.jpg"></td> 
		<td valign="middle">Exercez-vous, jouez une partie d'échecs contre un adversaire toujours disponible !</td>
        </tr>
        </table>
        <br/>
        <!-- AddThis Button BEGIN -->
      <div class="addthis_toolbox addthis_default_style ">
      <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
      <a class="addthis_button_tweet"></a>
      <a class="addthis_button_google_plusone" g:plusone:size="medium"></a>
      <a class="addthis_counter addthis_pill_style"></a>
      </div>
      <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4e7cb2a45be34669"></script>
      <!-- AddThis Button END -->
		<center>
		<applet
			code=jchess.class archive="jchess.zip"
			name=jchess width=480 height=400>
		  <PARAM NAME="bgcolor" VALUE="#FFFFFF">
		  <PARAM NAME="color" VALUE="#000080">
		  <PARAM NAME="whtfld" VALUE="#CCBBBB">
		  <PARAM NAME="blkfld" VALUE="#AA7777">
		  <PARAM NAME="but1txt" VALUE="Configurer">
		  <PARAM NAME="but2txt" VALUE="Joue !">
		  <PARAM NAME="but3txt" VALUE="Nouvelle">
		  <PARAM NAME="but4txt" VALUE="A propos">
		</applet>
		<input type="button" value="Jouer avec les noirs" onclick="nouveau();jouer()" />
		<input type="button" value="Retourner l'échiquier" onclick="retourner()" />
		</center>
		<br/>
		
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
		<br/>
		<br/>
		<br/>
		<br/>
	</div>
</div>

<div id="rightbar">
  
  
  <script type="text/javascript"><!--
  google_ad_client = "ca-pub-8069368543432674";
  /* CapaKaspa JChess Droite */
  google_ad_slot = "3613220032";
  google_ad_width = 160;
  google_ad_height = 600;
  //-->
  </script>
  <script type="text/javascript"
  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
  </script>
      
      
  
</div>
<?
    require 'page_footer.php';
?>
