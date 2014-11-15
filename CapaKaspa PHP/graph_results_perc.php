<?php // content="text/plain; charset=utf-8"
require_once ("jpgraph/jpgraph.php");
include ("jpgraph/jpgraph_pie.php");
include ("jpgraph/jpgraph_pie3d.php");
session_start();
// Paramètres
if (!isset($_CONFIG))
	require 'include/config.php';

// Connexion BDD
require 'include/connectdb.php';

require 'dac/dac_games.php';

require 'include/localization.php';

// Data
$tabResult[] = utf8_decode(_("Won"));
$tabResult[] = _("Draw");
$tabResult[] = _("Lost");

$dateDeb = date("Y-m-d", mktime(0,0,0, 1, 1, 1990));
$dateFin = date("Y-m-d", mktime(0,0,0, 12, 31, 2020));
$countLost = countLost($_GET['playerID'], $dateDeb, $dateFin, 0);
$nbDefaites = $countLost['nbGames'];
$countDraw = countDraw($_GET['playerID'], $dateDeb, $dateFin, 0);
$nbNulles = $countDraw['nbGames'];
$countWin = countWin($_GET['playerID'], $dateDeb, $dateFin, 0);
$nbVictoires = $countWin['nbGames'];

$tabNbGames[] = $nbVictoires;
$tabNbGames[] = $nbNulles;
$tabNbGames[] = $nbDefaites;

// Create the graph. These two calls are always required
// On spécifie la largeur et la hauteur du graph
$graph = new PieGraph(380,250);

// Ajouter une ombre au conteneur
//$graph->SetShadow();

// Donner un titre
$graph->title->Set(utf8_decode(_("Games per results")));
$graph->title->SetFont(FF_FONT1,FS_BOLD);

// Quelle police et quel style pour le titre
// Prototype: function SetFont($aFamily,$aStyle=FS_NORMAL,$aSize=10)
// 1. famille
// 2. style
// 3. taille
//$graph->title->SetFont(FF_GEORGIA,FS_BOLD, 12);

// Créer un camembert 
$pie = new PiePlot3D($tabNbGames);

// Quelle partie se détache du reste
$pie->ExplodeSlice(0);

// Spécifier des couleurs personnalisées... #FF0000 ok
$pie->SetSliceColors(array('green', 'blue', 'red'));

// Légendes qui accompagnent le graphique, ici chaque année avec sa couleur
$pie->SetLegends($tabResult);

// Position du graphique (0.5=centré)
$pie->SetCenter(0.4);

// Type de valeur (pourcentage ou valeurs)
$pie->SetValueType(PIE_VALUE_ABS);

// Personnalisation des étiquettes pour chaque partie
$pie->value->SetFormat("%d");

// Personnaliser la police et couleur des étiquettes
$pie->value->SetFont(FF_FONT1,FS_BOLD);
//$pie->value->SetColor('blue');

// ajouter le graphique PIE3D au conteneur 
$graph->Add($pie);

$graph->SetFrame(true,'#CCCCCC',0);

// Provoquer l'affichage
$graph->Stroke();

?>
