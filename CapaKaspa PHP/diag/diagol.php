<?
/*************************************************/
/*              DIAGOL, version 0.30             */
/*                                               */
/*           Auteur : Olivier Bouverot           */
/*          <webmaster@ajec-echecs.org>          */
/*                                               */
/*  Patch (prise en compte de la notation FEN) : */
/*                Patrice Pillot                 */
/*              <p.pillot@free.fr>               */
/*                                               */
/*  Ce programme est distribué sous licence GPL  */
/*               (voir ci-dessous)               */
/*                                               */
/*                  Homepage :                   */
/*    http://carabas.netliberte.org/diagol.html  */
/*************************************************/

/*
Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes de la Licence Publique Générale GNU publiée par la Free Software Foundation (version 2 ou bien toute autre version ultérieure choisie par vous).

Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE, ni explicite ni implicite, y compris les garanties de commercialisation ou d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU pour plus de détails.

Vous devez avoir reçu une copie de la Licence Publique Générale GNU en même temps que ce programme ; si ce n'est pas le cas, écrivez à la Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.
*/

require("includes.inc.php");
require("config.inc.php");
require("sub_function.inc.php");

/*$position = "B:Rf1/N:Pb2,c3,d4,e5/SV:e4/SR:a2";*/

$position = ereg_replace("[\n\r]","",$position);

/* l'heuristique est faible mais bon... */
if ( substr_count($position, "/") == 7 ) {
  $type = "FEN";
  $position = FEN2classic($position);
} else {
  $type = "CLASSIC";
  $position = ereg_replace("[ ]","",$position);
  $position = strtolower($position);
}

$table = explode("/",$position);
if (count($table)<2) {
  die("Erreur dans la syntaxe (explode table) !");
}
$chessboard = create_chessboard();

for ($i=0 ; $i<count($table) ; $i++) {
  if ( $table[$i] == "r" ) {
    $flip = 1;
  }
}

for ($i=0 ; $i<count($table) ; $i++) {
  $sub_table = split("[:,]",$table[$i]);
  switch($sub_table[0]) {
    case "b" : $side = "w";break;
    case "n" : $side = "b";break;
    case "sr" : $side = "-"; $hilite = "red"; break;
    case "sv" : $side = "-"; $hilite = "green"; break;
    case "sb" : $side = "-"; $hilite = "blue"; break;
    case "sj" : $side = "-"; $hilite = "yellow"; break;
    case "r" : $side = "-"; $flip = 1; break; /* Ne sert qu'a eviter des erreurs ;) */
    default : die("Erreur dans la syntaxe (couleur) !");
  }
  /* Ici j'ai change car sinon il est impossible de commencer la */
  /* description de la position par un pion sans 'p' */
  /* et cela pose probleme pour la partie hilite qui elle ne */
  /* comporte pas de piece */
  if (strlen($sub_table[1])==2) {
    $name = "p";
  } elseif (strlen($sub_table[1])==3) {
    $name = substr($sub_table[1],0,1);
  } else {
    if ( (strlen($sub_table[1])==0) && $sub_table[0]!="r" ) {
      echo "<p>$sub_table[1]</p>";
      die("Erreur dans la syntaxe (piece) !");
    }
  }

  for ($j=1 ; $j<count($sub_table) ; $j++) {
    switch(strlen($sub_table[$j])) {
      case 2 :
        $square = substr($sub_table[$j],0,2);
        break;
      case 3 :
        $name = substr($sub_table[$j],0,1);
        $square = substr($sub_table[$j],1,2);
        break;
      default :
        die("Erreur dans la syntaxe (pos) !");
    }
    if ($side!="-") {
      put_piece($chessboard,$side,$name,$square,$flip);
    } else {
      hilite_square($chessboard,$square,$hilite,$flip);
    }
  }
}

if ($hdr) {
  header($header);
}

if ($coords) {
  $big_chessboard = imagecreate($board_size+2*$border_size+$image_size,$board_size+2*$border_size+$image_size);
  $bg_color = imagecolorallocate($big_chessboard,$background_color[0],$background_color[1],$background_color[2]);
  imagecolortransparent($big_chessboard,$bg_color);
  imagecopy($big_chessboard,$chessboard,$image_size,0,0,0,$board_size+2*$border_size,$board_size+2*$border_size);
  $width = imagefontwidth($font);
  $height = imagefontheight($font);
  $center = intval($image_size/2);
  for ($i=1 ; $i<=8 ; $i++) {
    $empty_coord = imagecreate($image_size,$image_size);
    $bg_color = imagecolorallocate($empty_coord,$background_color[0],$background_color[1],$background_color[2]);
    imagecolortransparent($empty_coord,$bg_color);
    $font_color = imagecolorallocate($empty_coord,0,0,0);
    if ($flip == 0) {
      imagechar($empty_coord,$font,($image_size-$width)/2,($image_size-$height)/2+$border_size,9-$i,$font_color);
    } else {
      imagechar($empty_coord,$font,($image_size-$width)/2,($image_size-$height)/2+$border_size,$i,$font_color);
    }
    imagecopy($big_chessboard,$empty_coord,0,($i-1)*$image_size,0,0,$image_size,$image_size);
  }
  for ($i=1 ; $i<=8 ; $i++) {
    $empty_coord = imagecreate($image_size,$image_size);
    $bg_color = imagecolorallocate($empty_coord,$background_color[0],$background_color[1],$background_color[2]);
    imagecolortransparent($empty_coord,$bg_color);
    $font_color = imagecolorallocate($empty_coord,0,0,0);
    if ($flip == 0) {
      imagechar($empty_coord,$font,($image_size-$width)/2+$border_size,($image_size-$height)/2,$number2letter[$i],$font_color);
    } else {
      imagechar($empty_coord,$font,($image_size-$width)/2+$border_size,($image_size-$height)/2,$number2letter[9-$i],$font_color);
    }
    imagecopy($big_chessboard,$empty_coord,$i*$image_size,8*$image_size+2*$border_size,0,0,$image_size,$image_size);
  }
 $chessboard = $big_chessboard;
}

$img($chessboard);
?>
