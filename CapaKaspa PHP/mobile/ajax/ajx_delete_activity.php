<?
/*
 * Delete an activity
 * 
 */

// Parameters
if (!isset($_CONFIG))
	require '../../include/config.php';

// Connect DB
require '../../include/connectdb.php';
require '../../dac/dac_activity.php';

// Get comment Id
$activityID=$_GET["id"];

deleteActivity($activityID);
mysqli_close($dbh);
?>