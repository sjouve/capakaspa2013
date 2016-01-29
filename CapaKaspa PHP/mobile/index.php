<?
session_start();

/* load settings */
if (!isset($_CONFIG))
	require '../include/config.php';

require '../include/constants.php';
require '../dac/dac_players.php';
require '../dac/dac_common.php';
require '../dac/dac_games.php';
require '../dac/dac_tournament.php';

require '../bwc/bwc_chessutils.php';
require '../bwc/bwc_common.php';
require '../bwc/bwc_players.php';
require '../bwc/bwc_games.php';
require '../bwc/bwc_tournament.php';

require '../include/connectdb.php';

/* check session status */
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

// Localization after login
require '../include/localization.php';

if (isset($_SESSION['playerID']) && $_SESSION['playerID'] != -1)
{
	header("Location: game_in_progress.php");
	exit;
}

$titre_page = _("CapaKaspa mobile");
$desc_page = _("Play chess and share your games");
require 'include/page_header.php';
    
require 'include/page_body.php';
?>

	
	<? if (!isset($_SESSION['playerID'])||$_SESSION['playerID']==-1) {?>
		<center>
		<p>&nbsp;</p>
		<h4><? echo _("Play your chess games on your mobile !")?></h4>
		
		<div class="blockform">
			<br>
			<form method="post" action="game_in_progress.php">
	        <div id="homefieldnames" style="float: left; text-align: right; width: 50%;">
		        <? echo _("User name");?> : <br>
		        <? echo _("Password");?> : <br>
	        </div>
	        <div id="homefieldimputs" style="float: left; text-align: left; width: 50%;">
		        <input name="txtNick" type="text" size="13" maxlength="20"/><br>
		        <input name="pwdPassword" type="password" size="13" maxlength="16"/><br>
	        </div>
	        <input name="chkAutoConn" type="checkbox"> <? echo _("Remember me")?><br/>
	        <input name="ToDo" value="Login" type="hidden"><input name="login" value="<? echo _("Sign in")?>" type="submit" class="button">
	        
	        <?if (isset($_GET['err'])&&$_GET['err']=='login') {?>
	        <div class='error'><? echo _("Invalid user name or password")?></div>
	        <?}?>
			</form>
			<br>
		</div>
      	<div class="blockform" style="align: left;">
	   		<span class="newplayer" style="font-size: 12px;"><? echo(getNbActivePlayers()+getNbPassivePlayers()); ?></span> <?php echo _("players are waiting to play chess games");?><br>
	   		<span class="newplayer" style="font-size: 12px;"><? echo(getNbActiveGameForAll()); ?></span> <?php echo _("chess games in progress");?><br>
	   		<span class="newplayer" style="font-size: 12px;"><? echo(getNbIPTournament()); ?></span> <?php echo _("in progress chess tournaments");?>
	   		<br>
   		</div>
      	<!-- <img src="/images/puce.gif"/> <a href="sign-up.php">S'inscrire</a>-->
	  	<p><? echo _("To sign-up on CapaKaspa access to the computer version by clicking on the link below.")?></p>
	  	
	  	</center>
	<? } else {?>
		<div id="onglet">
		<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td><div class="ongletdisable"><a href="game_in_progress.php"><? echo _("Games")?></a></div></td>
			<td><div class="ongletdisable"><a href="tournament_list.php"><? echo _("Tournaments");?></a></div></td>
			<td><div class="ongletdisable"><a href="activity.php"><? echo _("News");?></a></div></td>
			<td><div class="ongletdisable"><a href="player_search.php"><? echo _("Players");?></a></div></td>
		</tr>
		</table>
		</div>
		
      	<form name="logout" action="game_in_progress.php" method="post">
        <p><? echo _("Welcome")?> <b><? echo ($_SESSION['playerName'])?></b>,</p>
        <p><? echo _("your are on the optimized version of CapaKaspa for your mobile.")?></p>
        
        <input type="hidden" name="ToDo" value="Logout">
        <input type="submit" value="<? echo _("Sign out")?>" class="button">
        <br/><br/>
		
      	</form>
	<? } ?>
	
<?
require 'include/page_footer.php';
mysqli_close($dbh);
?>
