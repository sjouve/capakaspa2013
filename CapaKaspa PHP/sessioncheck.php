<?
	require_once('bwc/bwc_players.php');
	
	/* load settings */
	if (!isset($_CONFIG))
		require 'config.php';
	
	// Si cookie alors connexion auto
	if ((!isset($_SESSION['playerID'])||$_SESSION['playerID'] == -1) && isset($_COOKIE['capakaspacn']['nick']))
	{
		loginPlayer($_COOKIE['capakaspacn']['nick'], $_COOKIE['capakaspacn']['password'], 0);
	}
	
	if (!isset($_SESSION['playerID']))
	{
	  	$_SESSION['playerID'] = -1;
	}
		
	if ($_SESSION['playerID'] != -1)
	{
		if (time() - $_SESSION['lastInputTime'] >= $CFG_SESSIONTIMEOUT)
		{
		  $_SESSION['playerID'] = -1;
		}
		else if (!isset($_GET['autoreload']))	
		{
		  	$_SESSION['lastInputTime'] = time();
		}
	}
	
	if ($_SESSION['playerID'] == -1)
	{
		header('Location: jouer-echecs-differe-inscription.php');
		exit;
	}
	
	// Gestion des joueurs en ligne
	if ($_SESSION['playerID'] != -1)
	{
		// Insert or Update
		$online_player = getOnlinePlayer($_SESSION['playerID']);
		if ($online_player)
			updateOnlinePlayer($_SESSION['playerID']);
		else
			insertOnlinePlayer($_SESSION['playerID']);
			
		// Supprimer les joueurs hors ligne
		deleteOnlinePlayers();
	}
?>