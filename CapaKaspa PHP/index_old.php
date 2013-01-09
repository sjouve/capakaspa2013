<?session_start();
require_once("include/localization.php");

/* load settings */
if (!isset($_CONFIG))
	require 'include/config.php';

/* Pour les statistiques */
require 'bwc/bwc_players.php';
require 'bwc/bwc_games.php';

/* connect to database */
require 'include/connectdb.php';
	
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
	
$titre_page = "Jouer aux échecs, apprendre et progresser - Accueil";
$desc_page = "Les échecs conviviaux sur le Net : découvrir les échecs, apprendre, jouer aux échecs en ligne et partager grâce au forum et au blog";
require 'page_header.php';
$image_bandeau = 'bandeau_capakaspa_global.jpg';
$barre_progression = "Accueil";
require 'page_body_no_menu.php';
?>
<div id="content">
	<div class="blogbody">
    	<div id="connexion">
	      <? if (!isset($_SESSION['playerID'])||$_SESSION['playerID']==-1) {?>
	      <form method="post" action="tableaubord.php">
	        <!--<table>
	          <tr>
	            <td><div class="item">Surnom :</div></td>
	            <td><input name="txtNick" type="text" size="13" maxlength="15"/></td>
	          </tr>
	          <tr>
	            <td><div class="item">Passe :</div></td>
	            <td><input name="pwdPassword" type="password" size="13" maxlength="15"/></td>
	          </tr>
	          
	          <tr>
	            <td colspan="2" align="center"><input name="ToDo" value="Login" type="hidden" /><input name="login" value="Entrer" type="submit" /></td>
	          </tr>
	        </table>-->
	        <center>
	        <div class="item">Surnom :&nbsp;<input name="txtNick" type="text" size="13" maxlength="20"/></div>
	        <div class="item">Passe :&nbsp;&nbsp;&nbsp;<input name="pwdPassword" type="password" size="13" maxlength="16"/></div>
	        <div class="item"><input name="chkAutoConn" type="checkbox"/> Se souvenir de moi</div>
	        <input name="ToDo" value="Login" type="hidden" /><input name="login" value="Entrer" type="submit" />
	        </center>
	        <?if (isset($_GET['err'])&&$_GET['err']=='login') {?>
	        <div class='error'>Surnom ou Passe invalide !</div>
	        <?}?>
	      </form>
	      <? } else {?>
	      
	        <div class="item"><a href="profil.php"><img src="<?echo(getPicturePath($_SESSION['socialNetwork'], $_SESSION['socialID']));?>" width="40" height="40" border="0" style="float: left;margin-right: 10px;"/></a> <b></b><a href="profil.php"><? echo (substr($_SESSION['nick'], 0, 17))?></a></b></div>
	        <div class="item">Elo : <? echo ($_SESSION['elo'])?></div>
	      <? } ?>
		</div>
    		<div class="block">
	      		<div class="blocktitle"><?php echo _("Play chess");?></div>
				
				<!-- AddThis Button BEGIN 
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
			   	AddThis Button END -->
			   	
				<img src="images/pos_initiale.jpg" alt="Echiquier position initiale" style="FLOAT: left; MARGIN: 3px 5px 5px 0px;"/>
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
				<td width="25%"><a href="http://www.youtube.com/user/CapaKaspaEchecs?feature=creators_cornier-http%253A%2F%2Fs.ytimg.com%2Fyt%2Fimg%2Fcreators_corner%2FYouTube%2Fyoutube_32x32.png"><img src="http://s.ytimg.com/yt/img/creators_corner/YouTube/youtube_32x32.png" alt="Abonnez-vous aux vid�os sur YouTube"/></a><img src="http://www.youtube-nocookie.com/gen_204?feature=creators_cornier-http%3A//s.ytimg.com/yt/img/creators_corner/YouTube/youtube_32x32.png" style="display: none"/>
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
    		
    	</div>
	</div>
<?require 'page_footer.php';?>