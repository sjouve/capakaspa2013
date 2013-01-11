<?
session_start();
/* load settings */
if (!isset($_CONFIG))
	require '../include/config.php';

require '../include/connectdb.php';
require '../bwc/bwc_players.php';
require '../bwc/bwc_games.php';
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

$titre_page = "Echecs en diff�r� (mobile) - Mot de passe oubli�";
$desc_page = "Jouer aux �checs en diff�r� sur votre smartphone. Retrouvez votre mot de passe afin d'acc�der � la zone de jeu en diff�r� et jouer des parties d'�checs � votre rythme.";
require 'include/page_header.php';

require 'include/page_body.php';
?>
  
    <?/* Traiter les erreurs */
		if ($err == 0)
			echo("<div class='error'>Il n'y a aucun compte associ� � cette adresse de messagerie</div>");
		if ($err == -1)
			echo("<div class='error'>Un probl�me technique a emp�ch� l'envoi du message</div>");
			
	?>
	<? if ($err == 1 && $ToDo == 'Valider') {?>
		<div class='success'>Un message a �t� envoy� � l'adresse de messagerie indiqu�e.</div>
	<? } else {?>
	<h3>Mot de passe oubli�</h3>
    	<p>Vous disposez d�j� d'un compte pour acc�der � la zone de jeu en diff�r� mais <b>vous avez oubli� votre mot de passe</b>.</p>
    	<p>Saisissez l'adresse de messagerie que vous avez associ� � ce compte. Un message sera envoy� � cette adresse. Il contiendra les informations n�cessaires � la connexion.</p>
		<form name="userdata" method="post" action="password.php">
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
    require 'include/page_footer.php';
    mysql_close();
?>
