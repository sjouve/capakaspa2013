<?
	session_start();
	require 'bwc/bwc_players.php';
	$titre_page = 'Jouer aux échecs contre flashChess';
    require 'page_header.php';
?>

<?
    $image_bandeau = 'bandeau_capakaspa_zone.jpg';
    $barre_progression = "Jeux en ligne > Jouer contre flashChess";
    require 'page_body.php';
?>
  <div id="contentlarge">
    <div class="blogbody">
    <h3>Jouez une partie d'échecs en Flash contre un adversaire toujours disponible !</h3>
    
        
      <!-- AddThis Button BEGIN -->
      <div class="addthis_toolbox addthis_default_style ">
      <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
      <a class="addthis_button_tweet"></a>
      <a class="addthis_button_google_plusone" g:plusone:size="medium"></a>
      <a class="addthis_counter addthis_pill_style"></a>
      </div>
      <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4e7cb2a45be34669"></script>
      <!-- AddThis Button END -->
		<br/>
		<center>
		
    	<embed width="650" height="500" name="plugin" src="bin/FLChess.swf" type="application/x-shockwave-flash">

		</center>
		<br/>
		
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
    require 'page_footer.php';
?>
