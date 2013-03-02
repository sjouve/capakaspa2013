<?
session_start();

/* load settings */
if (!isset($_CONFIG))
	require '../include/config.php';

require '../dac/dac_players.php';
require '../dac/dac_games.php';
require '../bwc/bwc_common.php';
require '../bwc/bwc_chessutils.php';
require '../bwc/bwc_players.php';
require '../bwc/bwc_games.php';

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

$titre_page = _("CapaKaspa mobile");
$desc_page = _("Play chess and share your games");
require 'include/page_header.php';
    
require 'include/page_body.php';
?>

	
	<? if (!isset($_SESSION['playerID'])||$_SESSION['playerID']==-1) {?>
		<center>
		<p><? echo _("Play your chess games on your mobile.")?></p>
		<form method="post" action="game_in_progress.php">
        <br/>
        <? echo _("User name");?> : <input name="txtNick" type="text" size="13" maxlength="20"/><br/>
        <? echo _("Password");?> : <input name="pwdPassword" type="password" size="13" maxlength="16"/><br/>
        <input name="chkAutoConn" type="checkbox"/> <? echo _("Remember me")?><br/>
        <input name="ToDo" value="Login" type="hidden" /><input name="login" value="<? echo _("Sign in")?>" type="submit" />
        
        <?if (isset($_GET['err'])&&$_GET['err']=='login') {?>
        <div class='error'><? echo _("Invalid user name or password")?></div>
        <?}?>
		</form>
      	<br/>
      	<!-- <img src="/images/puce.gif"/> <a href="sign-up.php">S'inscrire</a>-->
	  	<p><? echo _("To sign-up on CapaKaspa access to the computer version by clicking on the link below.")?></p>
	  	
	  	</center>
	<? } else {?>
		<div id="onglet">
		<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td><div class="ongletdisable"><a href="game_in_progress.php"><? echo("Games")?></a></div></td>
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
	
	<br/>
	
<?
require 'include/page_footer.php';
mysql_close();
?>
