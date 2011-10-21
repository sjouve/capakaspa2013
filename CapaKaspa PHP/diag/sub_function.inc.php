<?

require("includes.inc.php");
require("config.inc.php");

function FEN2classic($fen) {
  global $english2french, $columns;
  $w = "b:";
  $b = "n:";

  $fen = explode(" ", $fen);
  /* FEN complète = 6, abrégée = 1 */
  if ( count($fen) != 6  && count($fen) != 1 ) {
    die("Erreur dans la syntaxe dans la position FEN (nb de champs = count($fen)) !");
  }
  $side = $fen[1];
  $fen = explode("/", $fen[0]);
  if ( count($fen) != 8  ) {
    die("Erreur dans la syntaxe dans la position FEN (nb de ligne) !");
  }
  
  for ($i = 0; $i < 8; $i++) {
    for ($j = 0, $col = 0; $j < strlen($fen[$i]), $col < 8; $j++ ) {
      if ( ereg("[KQBNRP]", $fen[$i][$j] ) ) {
        if (strlen($w)>2) {
          $w.=",";
        }
        $w .= ($english2french[$fen[$i][$j]] . ($columns[$col++]) . (8 - $i));
      }
      elseif ( ereg("[kqbnrp]", $fen[$i][$j] ) ) {
        if (strlen($b)>2) {
          $b.=",";
        }
        $b .= ($english2french[$fen[$i][$j]] . ($columns[$col++]) . (8 - $i));
      }
      elseif ( ereg("[1-8]", $fen[$i][$j] ) ) {
        $col += $fen[$i][$j] ;
     } else {
      die("Erreur dans la syntaxe dans la position FEN (caractere) !");
      } 
    }
  }
  if ($side == "w") {
    return ($w . "/" . $b);
  } else {
    /* C'est aux noirs de jouer on retourne l'echiquier ! */
    return ($w . "/" . $b . "/r" );
  }
}

function create_chessboard() {
  global $image_size, $board_size, $border_size, $light, $dark;
  $board = imagecreate($board_size,$board_size);
  $light_color = imagecolorallocate($board,$light[0],$light[1],$light[2]);
  imagefill($board,0,0,$light_color);
  $square = imagecreate($image_size,$image_size);
  $dark_color = imagecolorallocate($square,$dark[0],$dark[1],$dark[2]);
  imagefill($square,0,0,$dark_color);
  for ($i=1 ; $i<=8 ; $i++) {
    for ($j=1 ; $j<=8 ; $j++) {
      if (!is_integer(($i+$j)/2)) {
        imagecopy($board,$square,($i-1)*$image_size,($j-1)*$image_size,0,0,$image_size,$image_size);
      }
    }
  }
  $chessboard = imagecreate($board_size+2*$border_size,$board_size+2*$border_size);
  $black_color = imagecolorallocate($chessboard,0,0,0);
  imagefill($chessboard,0,0,$black_color);
  imagecopy($chessboard,$board,$border_size,$border_size,0,0,$board_size,$board_size);
  return($chessboard);
}

function put_piece($chessboard,$side,$name,$square,$flip) {
  global $base_url, $letter2number, $english_name, $image_size, $border_size, $img_create, $suffix;
  $letter = substr($square,0,1);
  $number = substr($square,1,1);
  if ((!(ereg("[a-h]",$letter,$match1))) or (!(ereg("[1-8]",$number,$match2)))) {
    die("Erreur dans la syntaxe (put_piece)!");
  }
  $url = $base_url.$side.$english_name[$name].$suffix;
  $file = $img_create($url);
  if ( $flip == 0 ) {
    imagecopy($chessboard,$file,($letter2number[$letter]-1)*$image_size+$border_size,(8-$number)*$image_size+$border_size,0,0,$image_size,$image_size);
  } else {
    imagecopy($chessboard,$file,(8-$letter2number[$letter])*$image_size+$border_size,($number-1)*$image_size+$border_size,0,0,$image_size,$image_size);
  }
}


function hilite_square($chessboard,$square,$hilite,$flip) {
  global $base_url, $letter2number, $english_name, $image_size, $border_size, $img_create, $suffix;
  $letter = substr($square,0,1);
  $number = substr($square,1,1);

  if ((!(ereg("[a-h]",$letter,$match1))) or (!(ereg("[1-8]",$number,$match2)))) {
    die("Erreur dans la syntaxe (hilite_square)!");
  }
  $url = $base_url.$hilite.$suffix;
  $file = $img_create($url);
  if ( $flip == 0 ) {
    imagecopymerge($chessboard,$file,($letter2number[$letter]-1)*$image_size+$border_size,(8-$number)*$image_size+$border_size,0,0,$image_size,$image_size,33);
  } else {
    imagecopymerge($chessboard,$file,(8-$letter2number[$letter])*$image_size+$border_size,($number-1)*$image_size+$border_size,0,0,$image_size,$image_size,33);
  }
}

?>
