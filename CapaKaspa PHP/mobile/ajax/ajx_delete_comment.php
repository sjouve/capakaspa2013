<?
/*
 * Delete a comment for an entity
 * 
 */

// Parameters
if (!isset($_CONFIG))
	require '../../include/config.php';

// Connect DB
require '../../include/connectdb.php';
require '../../dac/dac_activity.php';

// Get comment Id
$commentID=$_GET["id"];

deleteComment($commentID);
mysqli_close($dbh);
?>