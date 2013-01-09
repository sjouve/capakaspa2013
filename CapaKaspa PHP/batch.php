﻿<?
/* load settings */
if (!isset($_CONFIG))
	require 'include/config.php';

/* connect to database */
require 'include/connectdb.php';
require 'bwc/bwc_batch.php';

/* Traitement des actions */
$err=1;
$ToDo = isset($_POST['ToDo']) ? $_POST['ToDo']:$_GET['ToDo'];

switch($ToDo)
{
	case 'activation':
		$err = batchActivation();
		break;
	case 'position':
		$err = batchPosition();
		break;
	case 'eco':
		$err = batchEco();
		break;
	case 'elo':
		$err = calculerElo();
		break;
}

mysql_close();
?>	