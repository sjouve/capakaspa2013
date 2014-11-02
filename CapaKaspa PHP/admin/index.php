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
require '../bwc/bwc_batch.php';

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
<form name="userdata" action="index.php" method="post">
<input id="ToDo" name="ToDo" type="radio" value="save_elo">Historiser Elo
<input id="ToDo" name="ToDo" type="radio" value="elo">Calculer Elo (<?php echo($dateDeb);?> -> <?php echo($dateFin);?>)

<input type="submit">
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
		$err = calculerElo();
		break;
	case 'save_elo':
		$err = createEloHistory();
		break;
}

echo("<br>Retour : ".$err);
?>

<?php mysqli_close($dbh);?>
</body>
</html>