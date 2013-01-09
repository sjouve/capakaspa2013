<?
session_start();

/* load settings */
if (!isset($_CONFIG))
	require '../include/config.php';

require '../include/connectdb.php';
require '../bwc/bwc_players.php';
require '../bwc/bwc_games.php';

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

$titre_page = "Echecs en diff�r� (mobile) - Accueil";
$desc_page = "Jouer aux �checs en diff�r� sur votre smartphone. Inscrivez-vous � la zone de jeu en diff�r� et jouer des parties d'�checs � votre rythme.";
require 'page_header.php';
    
require 'page_body.php';
?>

	
	<? if (!isset($_SESSION['playerID'])||$_SESSION['playerID']==-1) {?>
		<center>
		<p>La zone de jeu d'�checs en diff�r� du site CapaKaspa vous permet de jouer vos parties sur votre smartphone avec le meilleur confort d'utilisation possible.</p>
		<form method="post" action="tableaubord.php">
        <br/>
        Surnom : <input name="txtNick" type="text" size="13" maxlength="20"/><br/>
        Passe : <input name="pwdPassword" type="password" size="13" maxlength="16"/><br/>
        <input name="chkAutoConn" type="checkbox"/> Se souvenir de moi<br/>
        <input name="ToDo" value="Login" type="hidden" /><input name="login" value="Entrer" type="submit" />
        
        <?if (isset($_GET['err'])&&$_GET['err']=='login') {?>
        <div class='error'>Surnom ou Passe invalide !</div>
        <?}?>
		</form>
      	<br/>
      	<!-- <img src="/images/puce.gif"/> <a href="jouer-echecs-differe-inscription.php">S'inscrire</a>-->
	  	<img src="/images/puce.gif"/> <a href="jouer-echecs-differe-passe-oublie.php">Mot de passe oubli�</a>
	  	<p>Pour vous inscrire et acc�der � la zone de jeu d'�checs en diff�r� du site CapaKaspa veuillez vous diriger vers la version pour ordinateur du site en cliquant sur le lien plus bas.</p>
	  	
	  	</center>
	<? } else {?>
		<div id="onglet">
		<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td><div class="ongletdisable"><a href="tableaubord.php">Parties</a></div></td>
			<td><div class="ongletdisable"><a href="invitation.php">Invitation</a></div></td>
			<td><div class="ongletdisable"><a href="profil.php">Mon profil</a></div></td>	
		</tr>
		</table>
		</div>
		
      	<form name="logout" action="tableaubord.php" method="post">
        <p>Bienvenue <b><? echo ($_SESSION['playerName'])?></b>,</p>
        <p>vous �tes connect� � la zone de jeu d'�checs en diff�r� optimis�e pour les smartphones du site CapaKaspa.</p>
        
        <input type="hidden" name="ToDo" value="Logout">
        <input type="submit" value="Deconnexion">
        <br/><br/>
		
      	</form>
	<? } ?>
	<div class="navlinks">
			<div class="title">Statistiques</div>
		  	<ul>
				<li><img src="images/hand.gif" />  Parties en cours : <? echo(getNbActiveGameForAll())?></li>
				<li><img src="images/joueur_actif.gif" />  Joueurs actifs : <? echo(getNbActivePlayers())?></li>
				<li><img src="images/joueur_passif.gif" />  Joueurs passifs : <? echo(getNbPassivePlayers())?></li>
				<li><img src="images/user_online.gif" /> Joueurs en ligne : <? echo(getNbOnlinePlayers())?></li>
		  	</ul> 	
	</div>
	<br/>
	
<?
require 'page_footer.php';
mysql_close();
?>
