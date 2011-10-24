<?
	session_start();
	$titre_page = "Découvrir les échecs - Apprentissage des échecs";
	$desc_page = "Découvrir les échecs. Comment faire son apprentissage du jeu d'échecs ? Présentation des différentes phases de la progression du joueur.";
    require 'page_header.php';
    $image_bandeau = 'bandeau_capakaspa_global.jpg';
    $barre_progression = "<a href='/'>Accueil</a> > Découvrir les échecs > Apprentissage";
    require 'page_body.php';
?>
<div id="contentlarge">
	<div class="blogbody">
	<div id="onglet">
        <table width="680" cellpadding="0">
          <tr>
		  	<td ><div class="ongletdisable">Phases</div></td>
			<td ><div class="ongletenable"><a href="echecs-ouvertures.php">Ouvertures</a></div></td>
			<td ><div class="ongletenable">Tactique</div></td>
			<td ><div class="ongletenable">Stratégie</div></td>
			<td ><div class="ongletenable">Finales</div></td>
			<td width="100%"><div class="ongletend">&nbsp</td>		
          </tr>
        </table>
      </div>
      <!-- AddThis Button BEGIN -->
      <div class="addthis_toolbox addthis_default_style ">
      <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
      <a class="addthis_button_tweet"></a>
      <a class="addthis_button_google_plusone" g:plusone:size="medium"></a>
      <a class="addthis_counter addthis_pill_style"></a>
      </div>
      <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4e7cb2a45be34669"></script>
      <!-- AddThis Button END -->
	<p>A travers mon expérience, et avec un peu de recul, je pense que nous pouvons voir la progression d'un joueur d'échecs en 3 phases : </p>
      
	  <h3>Phase 1 : Assimiler (Extrait du <a href="http://blog.capakaspa.info">Blog</a>)</h3> 
      <p>Cette phase concerne bien évidemment une personne ne connaissant rien à ce jeu et désireuse de commencer à jouer. Comme dans tous les jeux il va d'abord falloir répondre à 2 questions :</p>
      <ul>
        <li>Quel est le but du jeu ? Le Mat.
        <li>Quels sont les règles ? Les pièces, leurs déplacements, leurs prises...
      </ul>
      <table width="680" border="0">
        <tr>
          <td width="217"><img src="images/pos_initiale.jpg" alt="Echiquier position initiale questions" /><div class="itemfooter"><center>Bien des questions se posent au débutant...</center></div></td>
          <td width="453" valign="top"><p>Je n'ai pas pour objectif de répondre complètement à ces questions aujourd'hui. Je voudrais juste essayé de comprendre ce qui passe dans la tête d'un joueur lors de ses toutes premières parties. Dans quelle direction s'oriente sa réflexion ? Quels sont les éléments qu'il assimile lors de ces premiers coups qui lui permettront de passer à un stade supérieur ? </p>
            <p>D'après mon expérience assez récente, le joueur, lorsqu'il débute, se demande tout le temps quels sont les coups possibles ? Il imagine péniblement où ses pièces peuvent bien aller, quelles autres pièces adverses elles peuvent "manger" ? Il veut éviter à tout pris de perdre une pièce bêtement. Il ne reste donc plus beaucoup de place pour autre chose dans sa réflexion. </p></td>
        </tr>
      </table>
      <p>Il me semble que toutes les parties jouées à ce stade n'ont donc pour objectif que <b>"d'oublier les règles"</b> ou plutôt les faire siennes. Il restera alors plus de place pour autre chose, et il pourra, s'il le souhaite, entrevoir la phase 2...</p>
      
      <center><script type="text/javascript"><!--
      google_ad_client = "ca-pub-8069368543432674";
      /* CapaKaspa Apprentissage Haut */
      google_ad_slot = "4937557499";
      google_ad_width = 468;
      google_ad_height = 60;
      //-->
      </script>
      <script type="text/javascript"
      src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
      </script></center>
	<br/>
	  <h3>Phase 2 : Calculer (Extrait du <a href="http://blog.capakaspa.info">Blog</a>)</h3>
      <p>La phase 2 de la progression d'un joueur d'échecs commence donc lorsqu'il est parvenu à assimiler les règles du jeu. Il a intégré les mouvements des pièces ainsi que le but du jeu. Il entrevoit d'autres horizons.</p>
      <p>L'objectif devient bien déterminé : il faut mater ce roi adverse au plus vite. L'adversaire n'offre plus ses pièces comme avant et il faut <b>calculer</b> plus profondément, construire ses premières combinaisons. Pour cela il faut entrevoir les premières notions de <b>tactique</b> : clouage, attaque double, la déviation, les mats simples.</p>
	  <table width="680" border="0">
        <tr>
          <td height="246" align="center"><img src="images/pos_debut.jpg" alt="Echiquier position initiale mouvements"/>
          <div class="itemfooter"><center>Comment commencer ?</center></div></td>
          <td align="center"><img src="images/pos_finale.jpg" alt="Echiquier position finale" /><div class="itemfooter"><center>Un pion de plus !</center></div></td>
        </tr>
      </table>
      <p>De plus une question taraude toujours le joueur devant la position initiale : quel coup jouer en premier ? Il faut donc répondre à cette question et entrevoir les premières notions d'<b>ouverture</b>. En tout cas celles qui lui permettront de répondre à son objectif : attaquer le roi adverse.
      <p>Le dernier point pour ce joueur avide de calculs est de percevoir les particularités de chaque pièce et de peser leur importance. Pourquoi un pion de plus peut faire gagner une partie ? C'est l'étude des <b>finales</b> les plus simples qui lui apporteront des éléments de réponses.
      
      <center><script type="text/javascript"><!--
      google_ad_client = "ca-pub-8069368543432674";
      /* CapaKaspa Apprentissage Bas */
      google_ad_slot = "3449481112";
      google_ad_width = 468;
      google_ad_height = 60;
      //-->
      </script>
      <script type="text/javascript"
      src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
      </script></center>
	<br/>
	  <h3>Phase 3 : Prévoir (Extrait du <a href="http://blog.capakaspa.info">Blog</a>)</h3>
      <p>Pour commencer le joueur a assimilé les règles du jeu et la marche des pièces. Par la suite il a pu se mettre à calculer avec un objectif précis : attaquer le roi adverse. Maintenant il est confronté à de bonnes défenses et les limites du « mat à tout prix » se font sentir. </p>
      <table width="680" border="0">
  <tr>
    <td><p>Il va falloir trouver d'autres plans ! Pour cela la tactique doit être complétée par des notions de <b>stratégie</b> : la paire de fous, bon et mauvais fou, les colonnes ouvertes, le pion passé, arriéré, isolé, le centre. </p>
   
	  <p>Afin de tout <b>prévoir</b> :</p>
      <ul>
        <li>il est nécessaire de construire un répertoire d'ouvertures plus conséquent et adapté à son style de jeu,</li>
        <li>il faut maîtriser tout les types de finales.</li>
      </ul></td>
    <td><img src="images/pos_fous.jpg" alt="Echiquier position finale fous"/><div class="itemfooter"><center>Bon ou mauvais fou ?</center></div></td>
  </tr>
</table>

	  
      <p>La phase 3 de la progression du joueur demandera un important travail personnel. J'essayerais de vous faire partager ma petite expérience dans ces domaines. Pour ma part je n'ai réalisé qu'une infime partie du travail nécessaire. </p>
      <p>Celui qui arrivera au stade que je décris ne sera alors qu'un bon joueur de club. Je n'ai pas la prétention de connaître tout ce qui permet de faire les très grands joueurs. Je souhaite seulement témoigner de mon apprentissage des échecs avec le recul de ces années passées. </p>
      <br/>
	</div>
</div>
<?
    require 'page_footer.php';
?>
