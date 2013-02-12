<?
/*
 * send a private message
 * 
 */
session_start();
// Parameters
if (!isset($_CONFIG))
	require '../include/config.php';

// Connect DB
require '../include/connectdb.php';
require '../dac/dac_activity.php';

// Insert a private message
$fromPlayerID=$_GET["fromID"];
$toPlayerID=$_GET["toID"];
$message=urldecode($_GET["mes"]);

insertPrivateMessage($fromPlayerID, $toPlayerID, $message);
//TODO Email notification
mysql_close();
?>