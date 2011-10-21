<?
/* Chemin (relatif ou absolu) du répertoire où sont stockées les images */
/* Mettre un slash à la fin */

$base_url = "./pieces29/";

/* Taille des images en pixels (29 ou 35) */

$image_size = 29;

/* Couleur des cases "blanches" */
/* Couleurs prédéfinies : */
/* white,black,grey,green,blue,brown,lightyellow,lightbrown */
/* Pour définir d'autres couleurs, éditer le fichier includes.inc.php */

$light = $white;

/* Couleur des cases "noires" */
/* Couleurs prédéfinies : */
/* white,black,grey,green,blue,brown,lightyellow,lightbrown */
/* Pour définir d'autres couleurs, éditer le fichier includes.inc.php */

$dark = $blue;

/* Couleur de fond de la page web */

$background_color = $white;

/* Epaisseur de la bordure de l'échiquier, en pixels */

$border_size = 2;

/* Code de la police utilisée pour les coordonnées */
/* Entier compris entre 1 et 5, à modifier éventuellement */

$font = 5;

/* Faut-il retourner l'echiquier ?? */
/* Par defaut non ! 0 = pas de flip */

$flip = 0;

/* Mais au fait, veut-on afficher les coordonnées ? (true ou false) */

$coords = true;

/* Faut-il envoyer un en-tête (cas d'une image "nue"), ou l'image
est-elle destinée à être incluse dans une page Web ? Laisser la
variable à "true" dans le premier cas, la mettre à "false" dans le
second */

$hdr = true;

/* C'est tout pour l'instant ... */
/* Ne modifier ce qui suit qu'à vos risques et périls ! */

/* ----------------------------------------------------------------*/

$board_size = $image_size*8;

/* Pour certaines installation de php-gd il peut être nécessaire
   de décommenter la ligne suivante */

/* dl("gd.so"); */

if (function_exists("imagepng")) {
   $suffix = ".png";
   $img_create = 'imagecreatefrompng';
   $header = "Content-type: image/png";
   $img = 'imagepng';
}
elseif (function_exists("imagegif")) {
   $suffix = ".gif";
   $img_create = 'imagecreatefromgif';
   $header = "Content-type: image/gif";
   $img = 'imagegif';
}
else {
   die("Il est impossible d'utiliser le script (fonctions graphiques
   absentes) !");
}

/* Le package php3-gd de la debian potato semble buggé et nécessite
   de remplacer le bloc précedent par celui-ci */


/*  if (function_exists("imagegif")) { */
/*     $suffix = ".gif"; */
/*     $img_create = 'imagecreatefromgif'; */
/*     $header = "Content-type: image/gif"; */
/*     $img = 'imagegif'; */
/*  } */
/*  else { */
/*     die("Il est impossible d'utiliser le script (fonctions graphiques */
/*     absentes) !"); */
/*  } */


/* La fonction substr_count() n'étant disponible que 
   depuis php4 >= 4.0RC2, le code suivant permet d'assurer la 
   compatibilité avec les installations php3. */

if (!function_exists("substr_count")) {
  function substr_count($haystack, $needle) {
    $lh = strlen($haystack);
    $ln = strlen($needle);
    $count = 0;
    
    for ($i = 0; $i < ($lh-$ln+1) ; $i++)
      {
	if ( $needle == substr($haystack, $i, $ln))
	  {
	    $count++;
	  }
      }
    
    return $count;
  }
}
?>
