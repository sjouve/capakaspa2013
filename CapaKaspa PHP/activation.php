<?
session_start();
/* load settings */
if (!isset($_CONFIG))
	require 'include/config.php';

require 'dac/dac_players.php';
require 'dac/dac_games.php';
require 'bwc/bwc_common.php';
require 'bwc/bwc_chessutils.php';
require 'bwc/bwc_players.php';
require 'bwc/bwc_games.php';

/* connect to database */
require 'include/connectdb.php';
	
/* Traitement des actions */
$err=1;
$ToDo="";
$ToDo = isset($_POST['ToDo']) ? $_POST['ToDo']:(isset($_GET['ToDo']) ? $_GET['ToDo']:"");

switch($ToDo)
{
	case 'Valider':
		$err = activationRequest($_POST['txtNick'], $_POST['txtPassword'], $_POST['txtEmail']);
		break;
}	

$titre_page = "Echecs en différé - Activation du compte";
$desc_page = "Activez votre compte pour accéder à la zone de jeu d'échecs en différé et jouer des parties à votre rythme.";
require 'include/page_header.php';
require 'include/page_body.php';
?>
  <div id="contentlarge">
    <div class="contentbody">
    <?/* Traiter les erreurs */
		if ($err == 0)
			echo("<div class='error'>Le compte n'existe pas</div>");
		if ($err == -1)
			echo("<div class='error'>Un problème technique a empêché l'envoi du message</div>");
		if ($err == -2)
			echo("<div class='error'>L'adresse de messagerie est déjà utilisée</div>");
		if ($err == -3)
			echo("<div class='error'>L'adresse de messagerie est invalide</div>");
			
	?>
	<? if ($err == 1 && $ToDo == 'Valider') {?>
		<b>Un message d'activation a été envoyé à l'adresse de messagerie indiquée.</b>
	<? } else {?>
	<b>Votre compte n'est pas activé.</b>
	<p>Vous êtes sûrement dans l'un des cas suivant :
		<ul>
		<li>Vous avez reçu le message contenant le lien d'activation de votre compte mais vous ne l'avez pas encore utilisé,</li>
		<li>vous n'avez pas reçu de message contenant le lien d'activation.</li>
		</ul>
     </p>
	 <p>Dans ce dernier cas, vous pouvez utiliser le formulaire ci-dessous pour mettre à jour votre adresse de messagerie associée à votre compte et relancer le processus d'activation.</p>
	 <br/>
	 <form name="userdata" method="post" action="activation.php">
			<table align="center">
				<tr>
		            <td> Surnom : </td>
		            <td><input name="txtNick" type="text" size="20" maxlength="20" value="<?echo(isset($_POST['txtNick'])?$_POST['txtNick']:"");?>">
		            </td>
		        </tr>
		        <tr>
					<td> Passe : </td>
		            <td><input name="txtPassword" type="password" size="16" maxlength="16" value="<?echo(isset($_POST['txtPassword'])?$_POST['txtPassword']:"");?>">
		            </td>
		        </tr>
		        <tr>
					<td> Email : </td>
		            <td><input name="txtEmail" type="text" size="50" maxlength="50" value="<?echo(isset($_POST['txtEmail'])?$_POST['txtEmail']:"");?>">
		            </td>
		        </tr>
			</table>
	
			<center><input name="ToDo" value="Valider" type="submit" class="button"></center>
		</form> 
		<br/>
		<? } ?>
		<br/>
		<br/>
      <br />
    </div>
  </div>

<?
    require 'include/page_footer.php';
    mysql_close();
?>
