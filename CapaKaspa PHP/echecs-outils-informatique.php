<?
	session_start();
	require 'gui_rss.php';
	$titre_page = 'Les outils informatique pour les échecs';
    require 'page_header.php';
    $image_bandeau = 'bandeau_capakaspa_global.jpg';
    $barre_progression = "Découvrir les échecs > Informatique";
    require 'page_body.php';
?>
  <div id="content">
    <div class="blogbody">
        <h3>Utiliser les outils informatiques (Extrait du <a href="http://blog.capakaspa.info">Blog</a>)</h3>
        <!-- AddThis Button BEGIN -->
        <div class="addthis_toolbox addthis_default_style ">
        <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
        <a class="addthis_button_tweet"></a>
        <a class="addthis_button_google_plusone" g:plusone:size="medium"></a>
        <a class="addthis_counter addthis_pill_style"></a>
        </div>
        <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4e7cb2a45be34669"></script>
        <!-- AddThis Button END -->
      <p>Les outils informatiques ont pris une grande importance dans l'apprentissage et la pratique des échecs. On peut les classer comme suit :
      <ul>
        <li>les moteurs,</li>
        <li>les bases de données,</li>
        <li>Internet.</li>
      </ul>
      </p>
      <p>Des standards sont même nés de cet effervescence : PGN, FEN, EPD, UCI. Nous y reviendrons. Je veux vous faire partager ma petite expérience dans ce domaine.</p>
      <center>
        <img src="images/chess_10.jpg" />
      </center>
      <p> Commençons par les moteurs : les cerveaux des logiciels de jeu d'échecs. Ils sont capables de calculer des coups à partir d'une position et éventuellement d'autres paramètres. On en trouve beaucoup de gratuits dont le plus célèbre est Crafty.</p>
      <p>Pour exploiter ces moteurs il faut une interface graphique capable de communiquer avec les moteurs grâce à un protocole tel que UCI (Universal Chess Interface) ou WinBoard. <a href="http://www.playwitharena.com" target="_blank">Arena</a> est une interface gratuite de ce type. Mais il existe aussi des logiciels commerciaux plus complets tel que Fritz de Chessbase qui fournit d'autres fonctionnalités d'apprentissage ou de conseils. L'intérêt de ces logiciels est triple :
      <ul>
        <li>jouer pour le plaisir contre un fort adversaire toujours disponible,</li>
        <li>s'entraîner en jouant sur un thème ou à des cadences bien choisies,</li>
        <li>analyser ses parties pour déceler les ressources tactiques oubliées.</li>
      </ul>
      </p>
      <p> Pour ma part j'ai commencé à jouer aux échecs contre des logiciels. Il est vrai qu'on se lasse vite d'enchaîner les parties contre un ordinateur. Maintenant le véritable intérêt des moteurs pour moi est plus l'analyse et l'entraînement.</p>
      <p>Dans cet optique il est important de conserver ses parties pour pouvoir les analyser. On peut aussi avoir besoin de retrouver un ensemble de parties pour la même ouverture, classer ses parties selon leur ouverture, leur finale ou bien d'autres thèmes. Les logiciels de base de données vous aident dans cette tâche : <a href="http://www.chessbase.com" target="_blank">ChessBase</a>, <a href="http://www.convekta.com" target="_blank">ChessAssistant</a> pour les produits commerciaux ou bien <a href="http://scid.sourceforge.net" target="_blank">Scid</a> pour les logiciels gratuits.<br />
        De plus un standard s'est imposé pour permettre l'échange de parties au format électronique : PGN (Portable Game Notation). Tous les logiciels permettent de lire une partie dans ce format. C'est le format principal d'échange de parties sur Internet.<br /> J'ai beaucoup utilisé ces logiciels et je conserve toutes mes parties pour pouvoir les analyser et les classer. </p>
      <center>
        <img src="images/chess_11.jpg"/>
      </center>
      <p>Lorsqu'on commence à progresser aux échecs on se trouve très vite en manque d'adversaire. Internet permet de pouvoir jouer contre des adversaires humains sans forcément aller dans un club. On y trouve différentes formes de jeu : en direct ou en différé.<br />
        Je pense que le jeu en direct est intéressant pour s'amuser. Cette forme de jeu est surtout utilisée pour jouer en blitz. Quant au jeu en différé il est peut être mieux adapté pour jouer des parties plus consistantes avec pour objectif de progresser. </p>
    </div>
  </div>
  <div id="rightbar">
  	<br/><br/>
  		<script type="text/javascript"><!--
      google_ad_client = "ca-pub-8069368543432674";
      /* CapaKaspa Informatique Droite */
      google_ad_slot = "0466263608";
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
