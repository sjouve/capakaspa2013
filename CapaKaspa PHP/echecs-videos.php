<?
	session_start();
	$titre_page = 'Découvrir les échecs - Les vidéos au sujet des échecs';
	$desc_page = "Découvrir les échecs. Une sélection de vidéos au sujet des échecs pour découvrir les différents aspects du jeu.";
    require 'page_header.php';

    $image_bandeau = 'bandeau_capakaspa_global.jpg';
    $barre_progression = "<a href='/'>Accueil</a> > Découvrir les échecs > Vidéos";
    require 'page_body.php';
?>
  <div id="contentlarge">
    <div class="blogbody">
    	
        <h3>Vidéos sur les échecs</h3>
        <!-- AddThis Button BEGIN -->
        <div class="addthis_toolbox addthis_default_style ">
        <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
        <a class="addthis_button_tweet"></a>
        <a class="addthis_button_google_plusone" g:plusone:size="medium"></a>
        <a class="addthis_counter addthis_pill_style"></a>
        </div>
        <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4e7cb2a45be34669"></script>
        <!-- AddThis Button END -->
        <p>Une sélection de vidéos pour revivre l'histoire du jeu d'échecs, découvrir quelques curiosités... 
        <br/>(Extrait de la chaîne <a href="http://www.youtube.com/user/CapaKaspaEchecs?feature=creators_cornier-http%253A%2F%2Fs.ytimg.com%2Fyt%2Fimg%2Fcreators_corner%2FYouTube%2Fyoutube_32x32.png"><img src="http://s.ytimg.com/yt/img/creators_corner/YouTube/youtube_32x32.png" alt="Abonnez-vous aux vidéos sur YouTube" width="16px" height=16px"/></a><img src="http://www.youtube-nocookie.com/gen_204?feature=creators_cornier-http%3A//s.ytimg.com/yt/img/creators_corner/YouTube/youtube_32x32.png" style="display: none"/>)
        </p>
        
       	<center>
        <iframe width="640" height="360" src="http://www.youtube.com/embed/videoseries?list=PL817884959CB02260&amp;hl=fr_FR" frameborder="0" allowfullscreen></iframe>
        
		<br/><br/>
		<script type="text/javascript"><!--
	    google_ad_client = "ca-pub-8069368543432674";
	    /* CapaKaspa Video Bandeau */
	    google_ad_slot = "6995231836";
	    google_ad_width = 468;
	    google_ad_height = 60;
	    //-->
	    </script>
	    <script type="text/javascript"
	    src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
	    </script>
	    </center>
	</div>
</div>
<?
    require 'page_footer.php';
?>
