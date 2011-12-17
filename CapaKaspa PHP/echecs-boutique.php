<?
	session_start();
	$titre_page = "La boutique jeu d'échecs";
	$desc_page = "La boutique du jeu d'échecs, découvrez une sélection de livres, logiciels et matériel.";
    require 'page_header.php';
    $image_bandeau = 'bandeau_capakaspa_global.jpg';
    $barre_progression = "<a href='/'>Accueil</a> > La boutique du jeu d'échecs";
    require 'page_body_no_menu.php';
?>
    <div id="contentxlarge">
    	<iframe src="http://astore.amazon.fr/capa0e-21" width="90%" height="4000" frameborder="0" scrolling="no"></iframe>
	</div>
<?
    require 'page_footer.php';
?>
