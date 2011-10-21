<?

require("includes.inc.php");
require("config.inc.php");
require("sub_function.inc.php");

/*$position = "B:Rf1/N:Pb2,c3,d4,e5/SV:e4/SR:a2";*/

$position = ereg_replace("[\n\r]","",$position);

/* l'heuristique est faible mais bon... */
if ( substr_count($position, "/") == 7 ) {
  $classic = FEN2classic($position);
  print("La position est : $classic");
} else {
  die("$position ne semble pas etre une position au format FEN.");
}

?>
