<?
/* load settings */
if (!isset($_CONFIG))
	require 'include/config.php';

/* connect to database */
global $dbh;
$dbh=mysqli_connect($CFG_SERVER, $CFG_USER, $CFG_PASSWORD, $CFG_DATABASE)
	or die ('CapaKaspa cannot connect to the database.  Please check the database settings in your config : '.mysqli_connect_error());


mysqli_query($dbh, "SET NAMES UTF8");
?>