<?php // content="text/plain; charset=utf-8"
require_once ("jpgraph/jpgraph.php");
require_once ("jpgraph/jpgraph_line.php");

// Paramètres
if (!isset($_CONFIG))
	require 'config.php';

// Connexion BDD
require 'connectdb.php';

require 'dac_players.php';
	
// Data
$eloProgress = listEloProgress($_GET['playerID']);
$tableauEloPprogress = array();
$eloDates = array();

if (mysql_numrows($eloProgress)>0)
{
	$i = 0;
	$eloDates[$i] = '-';
	$tableauEloPprogress[$i] = '1300';
	while($tmpElo = mysql_fetch_array($eloProgress, MYSQL_ASSOC))
	{
		
		$tableauEloPprogress[$i] = $tmpElo['elo'];
		$i++;
		$eloDates[$i] = $tmpElo['eloDateF'];
	}
	
	$tableauEloPprogress[$i] = $_GET['elo'];
}
else
{
	$tableauEloPprogress[0] = '1300';
	$eloDates[0] = '1';
	$tableauEloPprogress[1] = '1300';
	$eloDates[1] = '2';
}

// Create the graph. These two calls are always required
$graph = new Graph(650,250);
$graph->SetScale('textlin');
$graph->title->Set('Progression classement Elo');
$graph->SetMarginColor('#EEEEEE');
$graph->SetFrame(true,'#CCCCCC',1);
 
// Create the linear plot
$lineplot=new LinePlot($tableauEloPprogress);
$lineplot->SetColor('blue');
$lineplot->value->Show();
$lineplot->value->SetFormat('%d');

// Chaque point de la courbe ****
// Type de point
$lineplot->mark->SetType(MARK_FILLEDCIRCLE);
// Couleur de remplissage
$lineplot->mark->SetFillColor("green");
// Taille
$lineplot->mark->SetWidth(3);

// Libellé axe des mois
$graph->xaxis->SetTickLabels($eloDates);
// Add the plot to the graph
$graph->Add($lineplot);
 
// Display the graph
$graph->Stroke();

?>
