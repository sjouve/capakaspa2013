<?
session_start();
/* load settings */
if (!isset($_CONFIG))
	require '../config.php';

require '../connectdb.php';
require '../bwc_players.php';
require '../bwc_games.php';
require '../gui_rss.php';
	
/* Traitement des actions */
$err=1;
$ToDo = isset($_POST['ToDo']) ? $_POST['ToDo']:$_GET['ToDo'];

switch($ToDo)
{
	case 'Valider':
		$err = sendPassword($_POST['txtEmail']);
		break;
}	

$titre_page = "Echecs en différé (mobile) - Mot de passe oublié";
$desc_page = "Jouer aux échecs en différé sur votre smartphone. Retrouvez votre mot de passe afin d'accéder à la zone de jeu en différé et jouer des parties d'échecs à votre rythme.";
require 'page_header.php';

require 'page_body.php';
?>
  
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
    	<p>Vous disposez déjà d'un compte pour accéder à la zone de jeu en différé mais <b>vous avez oublié votre mot de passe</b>.</p>
    	<p>Saisissez l'adresse de messagerie que vous avez associé à ce compte. Un message sera envoyé à cette adresse. Il contiendra les informations nécessaires à la connexion.</p>
		<form name="userdata" method="post" action="jouer-echecs-differe-passe-oublie.php">
			<table align="center">
				<tr>
		            <td> Email : </td>
		            <td><input name="txtEmail" type="text" size="30" maxlength="50" value="<?echo($_POST['txtEmail']);?>">
		            </td>
		        </tr>
			</table>
	
			<center><input name="ToDo" value="Valider" type="submit"></center>
		</form>
      <?}?>
      <br/><br/><br/><br/>
		      
  
<?
    require 'page_footer.php';
    mysql_close();
?>
