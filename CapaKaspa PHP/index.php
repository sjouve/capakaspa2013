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

	
	$titre_page = "Découvrir les échecs, apprendre et jouer - Accueil";
	$desc_page = "Les échecs conviviaux sur le Net : découvrir les échecs, apprendre, jouer aux échecs en ligne et partager grâce au forum et au blog";
    require 'page_header.php';
    $image_bandeau = 'bandeau_capakaspa_global.jpg';
    $barre_progression = "Accueil";
    require 'page_body.php';
?>
  	<div id="content">
    	<div class="blogbody">
    		<div class="block">
	      		<div class="blocktitle">Découvrir les échecs, apprendre et jouer</div>
				
				<!-- AddThis Button BEGIN -->
			    <div class="addthis_toolbox addthis_default_style ">
			    <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
			    <span class="addthis_separator">|</span>
			    <a class="addthis_button_tweet"></a>
			    <span class="addthis_separator">|</span>
			    <a class="addthis_button_google_plusone" g:plusone:size="medium"></a>
			    <span class="addthis_separator">|</span>
			    <a class="addthis_counter addthis_pill_style"></a>
			    </div>
			    <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4e7cb2a45be34669"></script>
			   	<!-- AddThis Button END -->
			   	
				<img src="/images/pos_initiale.jpg" alt="Echiquier position initiale" style="FLOAT: left; MARGIN: 3px 5px 5px 0px;"/>
				CapaKaspa c'est un bon moyen de découvrir les échecs. Comment commencer ? Trouvez une réponse dans la section <a href="echecs-apprentissage.php">apprentissage</a>.<br/><br/>
				Puis vous pourrez mettre en pratique grâce à la <a href="tableaubord.php">zone de jeu en différé</a> pour jouer des parties à votre rythme. 
				<br/><br/>Trouvez les réponses à vos questions, faites profiter de votre expérience les autres membres sur le <a href="http://forum.capakaspa.info">forum</a>, et continuez votre découverte grâce aux articles du <a href="http://blog.capakaspa.info">blog</a>.
					
			</div>
		</div>
  	</div>
  	<div id="rightbarhome">
  	
    	<div class="navlinks">
    
			<div class="title">Statistiques</div>
		  		<ul>
					<li><img src="images/hand.gif" /> Parties en cours : <? echo(getNbActiveGameForAll())?></li>
					<li><img src="images/joueur_actif.gif" /> Joueurs actifs : <? echo(getNbActivePlayers())?></li>
					<li><img src="images/joueur_passif.gif" /> Joueurs passifs : <? echo(getNbPassivePlayers())?></li>
					<li><img src="images/user_online.gif" /> Joueurs en ligne : <? echo(getNbOnlinePlayers())?></li>
		  		</ul>
		</div>
		<div class="blockright" style="height:115px">
			
				Suivre l'actualité du site et des échecs<br/>
				<table width="100%">
				<tr>
				<td width="25%" height="50px"><a href="http://www.facebook.com/capakaspa"><img src="images/icone_facebook.png" alt="Suivre CapaKaspa sur Facebook" width="32" height="32" style="border: 0;"/></a>
				</td>
				<td width="25%"><a href="http://www.twitter.com/CapaKaspa"><img src="http://twitter-badges.s3.amazonaws.com/t_logo-a.png" alt="Suivre CapaKaspa sur Twitter" width="32" height="32" style="border: 0;"/></a></td>
				<td width="25%"><a href="https://plus.google.com/114694270583726807082/?prsrc=3" style="text-decoration: none;"><img src="https://ssl.gstatic.com/images/icons/gplus-32.png" alt="Suivre CapaKaspa sur Google+" width="32" height="32" style="border: 0;"/></a></td>
				<td width="25%"><a href="http://www.youtube.com/user/CapaKaspaEchecs?feature=creators_cornier-http%253A%2F%2Fs.ytimg.com%2Fyt%2Fimg%2Fcreators_corner%2FYouTube%2Fyoutube_32x32.png"><img src="http://s.ytimg.com/yt/img/creators_corner/YouTube/youtube_32x32.png" alt="Abonnez-vous aux vidéos sur YouTube"/></a><img src="http://www.youtube-nocookie.com/gen_204?feature=creators_cornier-http%3A//s.ytimg.com/yt/img/creators_corner/YouTube/youtube_32x32.png" style="display: none"/>
				</td>
				</tr>
				</table>	
		</div>
	</div>
		
	<div id="contentlarge">
    	<div class="blogbody">	
			<div class="blockvideo">
				<div class="blocktitle">Vidéo à la une - 07/10/11</div>
				<iframe width="330" height="232" src="http://www.youtube.com/embed/pfLM1mcX1k8" frameborder="0" allowfullscreen></iframe>
	      		<!--  <object width="330" height="232">
					<param name="movie" value="http://www.youtube.com/v/CxwKBNXd46M?fs=1&amp;hl=fr_FR"></param>
					<param name="wmode" value="transparent"></param>
					<embed src="http://www.youtube.com/v/CxwKBNXd46M?fs=1&amp;hl=fr_FR" type="application/x-shockwave-flash" wmode="transparent" width="330" height="232"></embed>
				</object>-->
			</div>
        
        	<div class="blockfacebook">
				<div id="fb-root"></div>
				<script>(function(d, s, id) {
				  var js, fjs = d.getElementsByTagName(s)[0];
				  if (d.getElementById(id)) {return;}
				  js = d.createElement(s); js.id = id;
				  js.src = "//connect.facebook.net/fr_FR/all.js#xfbml=1";
				  fjs.parentNode.insertBefore(js, fjs);
				}(document, 'script', 'facebook-jssdk'));</script>
				
				<div class="fb-like-box" data-href="http://www.facebook.com/capakaspa" data-width="295" data-show-faces="true" data-border-color="#FFFFFF" data-stream="false" data-header="false"></div>
			</div>
		
       	
    		<div class="blocklarge">
		      	<div class="blocktitle">Le fil du forum ... <?displayIconRSS(URL_RSS_FORUM);?></div>
				<table>
				<tr>
				<td valign="top" width="75%">
				<?
						displayBodyRSS(URL_RSS_FORUM, 4);
				?>
				</td>
				<td>
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
			 	</td>
			 	</tr>
				</table>
			</div>	
    
		    <!--<div class="block">
		    <div class="blocktitle">Casino en ligne</div>
		    Vous aimez les jeux de toutes sortes, jeux de société, jeux de table ? 
		    Alors pourquoi ne pas tenter de jouer au casino depuis votre ordinateur sur le <a href="http://www.spinpalace.com/francais/" target="_blank">casino français Spin Palace.com</a> ? <br>
		    Vous pourrez ainsi tenter votre en chance aux machines à sous, au black jack, ou encore au <a href="http://www.spinpalace.com/francais/craps/" target="_blank">craps en ligne</a>.
		    
		    </div>-->
    
    		<div class="blocklarge">  
    
			  	<div class="blocktitle">A la une sur le blog ... <?displayIconRSS(URL_RSS_BLOG);?></div>
	        	<?
					displayBodyRSS(URL_RSS_BLOG, 2);
				?>
    		</div>
    	</div>
	</div>
  
<?
    require 'page_footer.php';
?>
