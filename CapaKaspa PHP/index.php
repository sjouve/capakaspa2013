<?
	session_start();
	
	/* load settings */
	if (!isset($_CONFIG))
		require 'config.php';

	/* Pour les statistiques */
	require 'bwc_players.php';
	require 'bwc_games.php';
	require 'gui_rss.php';

	/* connect to database */
	require 'connectdb.php';
	
	/* check session status */
	// Si cookie alors connexion auto
	if ((!isset($_SESSION['playerID'])||$_SESSION['playerID'] == -1) && isset($_COOKIE['capakaspacn']['nick']))
	{
		loginPlayer($_COOKIE['capakaspacn']['nick'], $_COOKIE['capakaspacn']['password'], 0);
	}
	
	if (!isset($_SESSION['playerID']))
	{
	  	$_SESSION['playerID'] = -1;
	}
		
	if ($_SESSION['playerID'] != -1)
	{
		if (time() - $_SESSION['lastInputTime'] >= $CFG_SESSIONTIMEOUT)
		{
		  $_SESSION['playerID'] = -1;
		}
		else if (!isset($_GET['autoreload']))	
		{
		  	$_SESSION['lastInputTime'] = time();
		}
	}

	
	$titre_page = "Découvrir les échecs - Accueil";
	$desc_page = "Les échecs conviviaux sur le Net : jouer aux échecs en différé, découvrir et partager grâce au forum et au blog";
    require 'page_header.php';
    $image_bandeau = 'bandeau_capakaspa_global.jpg';
    $barre_progression = "Accueil";
    require 'page_body.php';
?>
  <div id="content">
    <div class="blogbody">
    	<div class="block">
	      	<div class="blocktitle">Découvrir les échecs</div>
			
			<img src="/images/pos_initiale.jpg" alt="Echiquier position initiale" style="FLOAT: left; MARGIN: 0px 5px 5px 0px;"/>
			CapaKaspa c'est un bon moyen de découvrir les échecs. Comment commencer ? Trouvez une réponse dans la section <a href="echecs-apprentissage.php">apprentissage</a>.<br/><br/>
			Puis vous pourrez mettre en pratique grâce à la <a href="tableaubord.php">zone de jeu en différé</a>. 
			Et continuez votre découverte grâce au <a href="http://forum.capakaspa.info">forum</a>, au <a href="http://blog.capakaspa.info">blog</a>.
			<br/><br/><br/>
			<!-- AddThis Button BEGIN -->
		    <div class="addthis_toolbox addthis_default_style ">
		    <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
		    <a class="addthis_button_tweet"></a>
		    <a class="addthis_button_google_plusone" g:plusone:size="medium"></a>
		    <a class="addthis_counter addthis_pill_style"></a>
		    </div>
		    <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4e7cb2a45be34669"></script>
		   	<!-- AddThis Button END -->
		</div>	
		<div class="blockvideo">
			<div class="blocktitle">Vidéo à la une - 07/10/11</div>
			<iframe width="247" height="160" src="http://www.youtube.com/embed/pfLM1mcX1k8" frameborder="0" allowfullscreen></iframe>
      		<!--  <object width="247" height="160">
				<param name="movie" value="http://www.youtube.com/v/CxwKBNXd46M?fs=1&amp;hl=fr_FR"></param>
				<param name="wmode" value="transparent"></param>
				<embed src="http://www.youtube.com/v/CxwKBNXd46M?fs=1&amp;hl=fr_FR" type="application/x-shockwave-flash" wmode="transparent" width="247" height="160"></embed>
			</object>-->
		</div>
        
        <div class="blockpubcarre">	
			<script type="text/javascript"><!--
      google_ad_client = "ca-pub-8069368543432674";
      /* CapaKaspa Accueil Carré Centre */
      google_ad_slot = "1181101877";
      google_ad_width = 200;
      google_ad_height = 200;
      //-->
      </script>
      <script type="text/javascript"
      src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
      </script>
		</div>
		
    
       	
    <div class="block">
	      	<div class="blocktitle">Le fil du forum ... <?displayIconRSS(URL_RSS_FORUM);?></div>
			<?
					displayBodyRSS(URL_RSS_FORUM, 3);
			?>
		</div>	
    
    <!--<div class="block">
    <div class="blocktitle">Casino en ligne</div>
    Vous aimez les jeux de toutes sortes, jeux de société, jeux de table ? 
    Alors pourquoi ne pas tenter de jouer au casino depuis votre ordinateur sur le <a href="http://www.spinpalace.com/francais/" target="_blank">casino français Spin Palace.com</a> ? <br>
    Vous pourrez ainsi tenter votre en chance aux machines à sous, au black jack, ou encore au <a href="http://www.spinpalace.com/francais/craps/" target="_blank">craps en ligne</a>.
    
    </div>-->
    
    <div class="block">  
    
		  	<div class="blocktitle">A la une sur le blog ... <?displayIconRSS(URL_RSS_BLOG);?></div>
        <?
				displayBodyRSS(URL_RSS_BLOG, 2);
			?>
    </div>
    </div>
  </div>
  
  <div id="rightbar">
  	
    <div class="navlinks">
    
		<div class="title">Statistiques</div>
		  <ul>
			<li><img src="images/hand.gif" /> Parties en cours : <? echo(getNbActiveGameForAll())?></li>
			<li><img src="images/joueur_actif.gif" /> Joueurs actifs : <? echo(getNbActivePlayers())?></li>
			<li><img src="images/joueur_passif.gif" /> Joueurs passifs : <? echo(getNbPassivePlayers())?></li>
		  </ul>
	</div>	  
	
	<div class="blockright">
		<div id="fb-root"></div>
		<script>(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) {return;}
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/fr_FR/all.js#xfbml=1";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));</script>
		
		<div class="fb-like-box" data-href="http://www.facebook.com/capakaspa" data-width="190" data-show-faces="true" data-border-color="#FFFFFF" data-stream="false" data-header="false"></div>
	</div>
	
	<div class="blockright">
		<script type="text/javascript"><!--
	    google_ad_client = "ca-pub-8069368543432674";
	    /* CapaKaspa Accueil Bandeau Droite */
	    google_ad_slot = "2254640927";
	    google_ad_width = 160;
	    google_ad_height = 600;
	    //-->
	    </script>
	    <script type="text/javascript"
	    src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
	    </script>
	</div>
	

	</div>
  
<?
    require 'page_footer.php';
?>
