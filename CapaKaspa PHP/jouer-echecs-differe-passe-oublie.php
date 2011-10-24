<?
session_start();
/* load settings */
if (!isset($_CONFIG))
	require 'config.php';

require 'connectdb.php';
require 'bwc_players.php';
require 'bwc_games.php';
require 'gui_rss.php';
	
/* Traitement des actions */
$err=1;
$ToDo = isset($_POST['ToDo']) ? $_POST['ToDo']:$_GET['ToDo'];

switch($ToDo)
{
	case 'Valider':
		$err = sendPassword($_POST['txtEmail']);
		break;
}	

$titre_page = "Echecs en différé - Mot de passe oublié";
$desc_page = "Jouer aux échecs en différé. Retrouvez votre mot de passe afin d'accder à la zone de jeu en différé et jouer des parties d'échecs à votre rythme.";
require 'page_header.php';
$image_bandeau = 'bandeau_capakaspa_global.jpg';
$barre_progression = "<a href='/'>Accueil</a> > Echecs en différé > Mot de passe oublié";
require 'page_body.php';
?>
  <div id="content">
    <div class="blogbody">
    <?/* Traiter les erreurs */
		if ($err == 0)
			echo("<div class='error'>Il n'y a aucun compte associé à cette adresse de messagerie</div>");
		if ($err == -1)
			echo("<div class='error'>Un problème technique a empêché l'envoi du message</div>");
			
	?>
	<? if ($err == 1 && $ToDo == 'Valider') {?>
		<div class='success'>Un message a été envoyé à l'adresse de messagerie indiquée.</div>
	<? } else {?>
	<h3>Mot de passe oublié</h3>
    	Vous disposez déjà d'un compte pour accéder à la zone de jeu en différé mais <b>vous avez oublié votre mot de passe</b>.<br/>
    	<p>Saisissez l'adresse de messagerie que vous avez associé à ce compte. Un message sera envoyé à cette adresse. Il contiendra les informations nécessaires à la connexion.</p>
		<form name="userdata" method="post" action="passeoublie.php">
			<table align="center">
				<tr>
		            <td> Email : </td>
		            <td><input name="txtEmail" type="text" size="50" maxlength="50" value="<?echo($_POST['txtEmail']);?>">
		            </td>
		        </tr>
			</table>
	
			<center><input name="ToDo" value="Valider" type="submit"></center>
		</form>
      <?}?>
      <br/><br/><br/><br/>
		      
      
    </div>
  </div>
  <div id="rightbar">
    <div class="navlinks">
    	
      
      	<div class="title">Statistiques</div>
		  <ul>
			<li><img src="images/hand.gif" /> Parties en cours : <? echo(getNbActiveGameForAll())?></li>
			<li><img src="images/joueur_actif.gif" /> Joueurs actifs : <? echo(getNbActivePlayers())?></li>
			<li><img src="images/joueur_passif.gif" /> Joueurs passifs : <? echo(getNbPassivePlayers())?></li>
		  </ul>
		
		<br/><br/>
	

 	</div>
 	</div>
<?
    require 'page_footer.php';
    mysql_close();
?>
