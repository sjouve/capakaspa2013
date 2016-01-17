<?php // content="text/plain; charset=utf-8"
require_once ("jpgraph/jpgraph.php");
include ("jpgraph/jpgraph_bar.php");

session_start();
// Paramètres
if (!isset($_CONFIG))
	require 'include/config.php';

// Connexion BDD
require 'include/connectdb.php';
require 'bwc/bwc_common.php';
require 'dac/dac_games.php';

require 'include/localization.php';

// Data
$gamesPerEco = countGamesByEco($_GET['playerID']);
$tabNbGames = array();
$tabEco = array();

if (mysqli_num_rows($gamesPerEco)>0)
{
	$i = 0;
	while(($tmpGamesEco = mysqli_fetch_array($gamesPerEco, MYSQLI_ASSOC)) && ($i < 25))
	{

		$tabNbGames[$i] = $tmpGamesEco['nb'];
		$tabEco[$i] = $tmpGamesEco['eco'];
		$i++;
	}
}
else
{
	
}


// Create the graph. These two calls are always required
// On spécifie la largeur et la hauteur du graph
// Construction du conteneur
// Spécification largeur et hauteur
$graph = new Graph(775,180);

// Réprésentation linéaire
$graph->SetScale("textlin");

// Ajouter une ombre au conteneur
//$graph->SetShadow();

// Fixer les marges
$graph->img->SetMargin(40,30,25,40);

// Création du graphique histogramme
$bplot = new BarPlot($tabNbGames);

// Spécification des couleurs des barres
//$bplot->SetFillColor(array('red', 'green', 'blue'));
// Une ombre pour chaque barre
$bplot->SetShadow();

// Afficher les valeurs pour chaque barre
$bplot->value->Show();
// Fixer l'aspect de la police
//$bplot->value->SetFont(FF_ARIAL,FS_NORMAL,9);
// Modifier le rendu de chaque valeur
$bplot->value->SetFormat("%d");

// Ajouter les barres au conteneur
$graph->Add($bplot);

// Le titre
$graph->title->Set(_("Classic games per ECO Code"));
$graph->title->SetFont(FF_FONT1,FS_BOLD);

// Titre pour l'axe horizontal(axe x) et vertical (axe y)
$graph->xaxis->title->Set(_("ECO Code"));
$graph->yaxis->title->Set(_("Number of games"));

$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

// Légende pour l'axe horizontal
$graph->xaxis->SetTickLabels($tabEco);
$graph->SetMarginColor('#FFFFFF');
$graph->SetFrame(true,'#CCCCCC',0);

// Afficher le graphique
$graph->Stroke();

?>
