﻿</head>
<body <?echo(isset($attribut_body) ? $attribut_body:"")?>>
<?
require_once("localization.php");

function getJour($day) 
{
	$jour["Monday"] = "Lundi";
	$jour["Tuesday"] = "Mardi";
	$jour["Wednesday"] = "Mercredi";
	$jour["Thursday"] = "Jeudi";
	$jour["Friday"] = "Vendredi";
	$jour["Saturday"] = "Samedi";
	$jour["Sunday"] = "Dimanche";
	
	return $jour[$day];
}

function getMois($month)
{
	$mois["January"] = "Janvier";
	$mois["February"] = "Février";
	$mois["March"] = "Mars";
	$mois["April"] = "Avril";
	$mois["May"] = "Mai";
	$mois["June"] = "Juin";
	$mois["July"] = "Juillet";
	$mois["August"] = "Août";
	$mois["September"] = "Septembre";
	$mois["October"] = "Octobre";
	$mois["November"] = "Novembre";
	$mois["December"] = "Décembre";
	
	return $mois[$month];
}

$month = Date('F');
$day = Date('l');

?>

<div id="container">
	
  <div id="topbar">
  	<table cellpadding="0" cellspacing="0" width="900" style="background-image: url(./images/<?echo($image_bandeau)?>) ">
  		<tr height="60">
			<td width="430" height="60"><a href="http://www.capakaspa.info" style="display: block; height: 100%; width: 100%;">&nbsp;</a></td>
			<td width="470" colspan="4">
        		<h3>Rechercher dans tout CapaKaspa (dont Forum et Blog)</h3>
	          	<form action="http://www.capakaspa.info/recherche.php" id="cse-search-box">
				  <div>
				  	
				    <input type="hidden" name="cx" value="partner-pub-8069368543432674:7445098107" />
				    <input type="hidden" name="cof" value="FORID:10" />
				    <input type="hidden" name="ie" value="ISO-8859-1" />
				    <input type="text" name="q" size="31" />
				    <input type="submit" name="sa" value="Rechercher" />
				  </div>
				</form>
				
				<script type="text/javascript" src="http://www.google.fr/coop/cse/brand?form=cse-search-box&amp;lang=fr"></script>

        	</td>
		</tr>
    	<tr height="20">
			<td width="400">
				<h1><?
				print getJour($day);
				print Date('d')." ";
				print getMois($month);
				print Date('Y');?>
				</h1>
			</td>
			<td width="170"><h2><img src="images/point.png"/>&nbsp;<a href="tableaubord.php">Jouer aux Echecs</a></h2></td>
			<td width="130"><h2><img src="images/point.png"/>&nbsp;<a href="echecs-boutique.php">La boutique</a></h2></td>
			<td width="100"><h2><img src="images/point.png"/>&nbsp;<a href="http://forum.capakaspa.info">Le Forum</a></h2></td>
			<td width="100"><h2><img src="images/point.png"/>&nbsp;<a href="http://blog.capakaspa.info">Le Blog</a></h2></td>
			
		</tr>
    </table>
  </div>
  
	<div id="progressbar">
		<img src="images/puce.gif"/>  <? echo($barre_progression) ?>
	</div>
  
  <div id="contentxlarge">
    <center>
      <script type="text/javascript"><!--
      google_ad_client = "ca-pub-8069368543432674";
      /* CapaKaspa Leaderboard */
      google_ad_slot = "6207727366";
      google_ad_width = 728;
      google_ad_height = 90;
      //-->
      </script>
      <script type="text/javascript"
      src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
      </script>
    </center>
    <br/>
    <div class="skyscraper">
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
  
  <div id="leftbar">
    <div class="navlinks">
    	<div class="title">Jouer aux Echecs <img src="images/lock.gif" /></div>
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
		
      
      <ul>
      <? if (!isset($_SESSION['playerID'])||$_SESSION['playerID']==-1) {?>
      	<li><img src="images/puce.gif"/> <a href="jouer-echecs-differe-inscription.php">S'inscrire</a></li>
      	<li><img src="images/puce.gif"/> <a href="jouer-echecs-differe-passe-oublie.php">Mot de passe oublié</a></li>
      	<li><img src="images/icone_video.png"/> <a href="http://youtu.be/J6pMC2Ceaxw" target="_blank">Démonstration vidéo</a></li>
      <? } else {?>
        <li><img src="images/puce.gif"/> <a href="tableaubord.php">Mes parties en cours</a></li>
		<li><img src="images/puce.gif"/> <a href="partiesterminees.php">Mes parties terminées</a></li>
        <li><img src="images/puce.gif"/> <a href="listeparties.php">Les autres parties</a></li>
		<li><img src="images/puce.gif"/> <a href="invitation.php">Les autres joueurs</a></li>
	<? } ?>
        <li><img src="images/icone_pdf.gif"/> <a href="../manuel-utilisateur-jouer-echecs-capakaspa.pdf" target="_blank">Manuel utilisateur</a></li>
        <!-- <li><img src="images/icone-mobile.png"/> <a href="http://mobile.capakaspa.info">Version mobile</a></li> -->
      </ul>
      <? if (isset($_SESSION['playerID'])&&$_SESSION['playerID']!=-1) {?>
      <form name="logout" action="tableaubord.php" method="post">
	        <center>
	        <input type="hidden" name="ToDo" value="Logout">
	        <input type="submit" value="Deconnexion">
	        </center>
	  </form>
	  <? } ?>
      </div>
      <div class="navlinks">
	  <div class="title">Jeux en ligne</div>
      <ul>
        <li><img src="images/puce.gif"/> <a href="jouer-echecs-jchess.php">Jouer contre JChess</a></li>
        <li><img src="images/puce.gif"/> <a href="jouer-echecs-flashchess.php">Jouer contre flashChess</a></li>
        <li><img src="images/puce.gif"/> <a href="jouer-echecs-crazychess.php">Jouer à Crazy Chess</a></li>
        <li><img src="images/puce.gif"/> <a href="jouer-jeux-flash.php">Autres jeux</a></li>
      </ul>
	</div>
      <div class="navlinks">
      <div class="title">Découvrir les échecs</div>
      <ul>
	  	<li><img src="images/puce.gif"/> <a href="echecs-videos.php">Vidéos sur les échecs</a></li>
		<li><img src="images/puce.gif"/> <a href="echecs-apprentissage.php">Apprentissage des échecs</a></li>
		<li><img src="images/puce.gif"/> <img src="images/puce.gif"/> <a href="echecs-ouvertures.php">Ouvertures</a></li>
	  	<li><img src="images/puce.gif"/> <img src="images/puce.gif"/> <a href="echecs-sites-livres.php">Sites & Livres</a></li>
        <li><img src="images/puce.gif"/> <a href="echecs-outils-informatique.php">Echecs et informatique</a></li>
        
      </ul>
      </div>
      <div class="navlinks">
       <div class="title">Outils <img src="images/tools.gif"/></div>
      <ul>
		<li><img src="images/puce.gif"/> <a href="epd2diag.php">EPD/FEN en diagramme</a></li>
		<li><img src="images/puce.gif"/> <a href="javascript:void(0)" onclick='window.open("http://www.iechecs.com/iechecs.htm?app","iechecs","height=413,width=675,status=no,toolbar=no,menubar=no,location=no,resizable=yes")' >iEchecs : l'échiquier en ligne</a></li>
      </ul>	
    
	</div>
	
    </div>
    

