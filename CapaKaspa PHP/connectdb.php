﻿<?
	/* load settings */
	if (!isset($_CONFIG))
		require 'config.php';
	
	/* connect to database */
	$dbh=mysql_connect ($CFG_SERVER, $CFG_USER, $CFG_PASSWORD)
		or die ('CapaKaspa cannot connect to the database.  Please check the database settings in your config : '.mysql_error());

	mysql_select_db ($CFG_DATABASE);
?>
