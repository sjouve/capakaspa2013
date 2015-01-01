<?
/* load settings */
if (!isset($_CONFIG))
	require '../include/config.php';

/* connect to database */
require '../include/connectdb.php';

require '../include/constants.php';
require '../dac/dac_players.php';
require '../dac/dac_games.php';
require '../bwc/bwc_chessutils.php';
require '../bwc/bwc_board.php';
require '../bwc/bwc_games.php';
require '../bwc/bwc_players.php';
require '../bwc/bwc_batch.php';
require '../bwc/bwc_common.php';


/* Traitement des actions */
$err="N/A";

$ToDo = isset($_POST['ToDo']) ? $_POST['ToDo']:(isset($_GET['ToDo']) ? $_GET['ToDo']:"N/A");

$dateDeb = date('Y-m-d', mktime(0,0,0,date('m')-1,1,date('Y')));
$dateFin = date('Y-m-d', mktime(0,0,0,date('m'),0,date('Y')));
	
?>
<html>
<head>
</head>
<body>
<h2>Période active : (<?php echo($dateDeb);?> - <?php echo($dateFin);?>)</h2>

<form name="userdata" action="index.php" method="post">
<h3>Classique</h3>
<input id="ToDo" name="ToDo" type="radio" value="save_elo"> 1) Historiser Elo
<input id="ToDo" name="ToDo" type="radio" value="elo">3) Calculer Elo
<input id="ToDo" name="ToDo" type="radio" value="rank">5) Classement

<h3>Chess960</h3>
<input id="ToDo" name="ToDo" type="radio" value="save_elo960">2) Historiser Elo
<input id="ToDo" name="ToDo" type="radio" value="elo960">4) Calculer Elo
<input id="ToDo" name="ToDo" type="radio" value="rank960">6) Classement

<p><input type="submit"></p>
</form>

<?php 
echo("Action : ".$ToDo."<br>");
switch($ToDo)
{
	/*case 'activation':
		$err = batchActivation();
		break;
	case 'position':
		$err = batchPosition();
		break;
	case 'eco':
		$err = batchEco();
		break;*/
	case 'elo':
		$err = calculerElo(0);
		break;
	case 'elo960':
		$err = calculerElo(2);
		break;
	case 'save_elo':
		$err = createEloHistory();
		break;
	case 'save_elo960':
		$err = createElo960History();
		break;
	case 'rank':
		$err = computeRank();
		break;
	case 'rank960':
		$err = computeRank960();
		break;
}

echo("<br>Retour : ".$err);
?>

<?php mysqli_close($dbh);?>
</body>
</html>