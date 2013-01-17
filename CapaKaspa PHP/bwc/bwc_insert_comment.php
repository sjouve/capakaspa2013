<?
/*
 * Insert a comment for an entity
 * 
 */
session_start();
// Parameters
if (!isset($_CONFIG))
	require '../include/config.php';

// Connect DB
require '../include/connectdb.php';
require '../dac/dac_activity.php';

// Insert a comment for an entity
$entityType=$_GET["type"];
$entityID=$_GET["id"];
$message=$_GET["mes"];

insertComment($_SESSION['playerID'], $entityType, $entityID, $message);
?>