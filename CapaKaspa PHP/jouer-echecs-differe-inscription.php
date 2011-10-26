<?
/* load settings */
if (!isset($_CONFIG))
	require 'config.php';

require 'connectdb.php';
require 'bwc_players.php';
		
/* Traitement des actions */
$err=false;
$ToDo = isset($_POST['ToDo']) ? $_POST['ToDo']:$_GET['ToDo'];

switch($ToDo)
{
	case 'NewUser':

		// Contrôle serveur du nick vide
		if ($_POST['txtNick'] == "")
		{
		  	$err = 'emptyNick';
	    	break;
		}
		
		// Contrôle existence joueur avec même surnom ou email
		$player = getPlayerByNickEmail($_POST['txtNick'], $_POST['txtEmail']);
		
		if (strtolower($player['nick']) == strtolower($_POST['txtNick']))
		{
			$err = 'existNick';
			break;
		}
				
		if (strtolower($player['email']) == strtolower($_POST['txtEmail']))
		{
			$err = 'existEmail';
			break;
		}
		
		// Création du joueur et envoi message confirmation
		if (!createPlayer())
		{
		  	// Erreur technique
			$err = 'db';
			break;	
		}
		
		break;
		
	case 'activer':
		// On vérifie si le compte n'est pas déjà activé
		$player = getPlayer($_GET['playerID']);
		if ($player && $player[activate] == 1)
		{
			header("Location: tableaubord.php");
			exit;
		}
		else if (!activatePlayer($_GET['playerID'], $_GET['nick']))
			$err = 'db';
		break;
		
}

$titre_page = "Echecs en différé - Inscription à la zone de jeu";
$desc_page = "Jouer aux échecs en différé. Inscrivez-vous à la zone de jeu en différé et jouer des parties d'échecs à votre rythme.";
require 'page_header.php';
    
?>
<script type="text/javascript" src="javascript/formValidation.js">
 /* fonctions de validation des champs d'un formulaire */
</script>
<script type="text/javascript">
	function validateForm()
	{
		
		if (isEmpty(document.userdata.txtFirstName.value)
			|| isEmpty(document.userdata.txtLastName.value)
			|| isEmpty(document.userdata.txtNick.value)
			|| isEmpty(document.userdata.pwdPassword.value)
			|| isEmpty(document.userdata.txtEmail.value)
			|| isEmpty(document.userdata.txtProfil.value)
			|| isEmpty(document.userdata.txtSituationGeo.value)
			|| isEmpty(document.userdata.txtAnneeNaissance.value))
		{
			alert("Toutes les informations personnelles sont obligatoires.");
			return;
		}
		
		if (!isAlphaNumeric(document.userdata.txtNick.value))
		{
			alert("Le surnom doit être alphanumérique.");
			return;
		}
		
		if (!isAlphaNumeric(document.userdata.pwdPassword.value))
		{
			alert("Le mot de passe doit être alphanumérique.");
			return;
		}
		
		if (!isEmailAddress(document.userdata.txtEmail.value))
		{
			alert("L'adresse de messagerie n'est pas au bon format.");
			return;
		}
		
		if (!isNumber(document.userdata.txtAnneeNaissance.value) || !isWithinRange(document.userdata.txtAnneeNaissance.value, 1900, 2100))
		{
			alert("L'année de naissance est un nombre à 4 chiffres compris entre 1900 et 2010.");
			return;
		}
		
		if (document.userdata.pwdPassword.value == document.userdata.pwdPassword2.value)
			document.userdata.submit();
		else
			alert("Vous avez fait une erreur de saisie de mot de passe.");
		
	}
</script>
<?
    $image_bandeau = 'bandeau_capakaspa_zone.jpg';
    $barre_progression = "<a href='/'>Accueil</a> > Echecs en différé > Inscription";
    require 'page_body.php';
?>
	<div id="contentlarge">
    <div class="blogbody">
	<?
		/* Traiter les erreurs */
		if ($err == 'existNick')
			echo("<div class='error'>Le surnom (".$_POST['txtNick'].") que vous avez choisi  est déjà utilisé.  Essayez un autre surnom.</div>");
		if ($err == 'existEmail')
			echo("<div class='error'>L'email (".$_POST['txtEmail'].") que vous avez choisi  est déjà utilisé.  Essayez un autre email.</div>");
		if ($err == 'emptyNick')
			echo("<div class='error'>Surnom vide</div>");
		if ($err == 'db')
			echo("<div class='error'>Une erreur technique s'est produite</div>");
		
	?>
	<?if ($ToDo == 'activer' && !$err) {?>
	<b>Votre compte vient d'être activé.</b>
	<p>
	Vous pouvez maintenant vous connecter à la zone de jeu en différé.
	</p>
	<?} else if ($ToDo == 'activer' && $err == 'db') {?>
	Une erreur s'est produite lors de l'activation !!!
	<?} else if (!$err && $ToDo == 'NewUser') {?>
	<b>Un message de confirmation d'inscription a été envoyé à l'adresse de messagerie que vous avez choisi.</b>
	<p>En attendant, vous pouvez consulter le <a href="../manuel-utilisateur-jouer-echecs-capakaspa.pdf" target="_blank">manuel utilisateur</a> de la zone de jeu en différé.</p>
	<p>Si vous souhaitez discuter au sujet des échecs ou faire des remarques et suggestions concernant le site CapaKaspa, vous pouvez aussi vous <a href="http://forum.capakaspa.info/profile.php?mode=register">inscrire sur le forum</a> de CapaKaspa.</p><br/>
	<hr/>
	
	

<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
	<?} else  {?>
	
	<b>L'accès à la zone de jeu en différé nécessite une inscription.</b>
	<p><ul><li>Cette inscription est complètement gratuite</li></ul>La seule contrainte est de nous fournir toutes les informations ci-dessous. <i>Cependant votre nom, prénom et adresse de messagerie ne seront pas connus des autres joueurs</i>. Les informations restantes sont publiées dans la liste des joueurs du site.</p>
	<p><ul><li>Cette inscription nécessite une validation par messagerie électronique</li></ul>L'adresse de messagerie associée à votre compte doit donc être valide.</p>
	
	<form name="userdata" method="post" action="jouer-echecs-differe-inscription.php">
	<h3>Vos informations personnelles</h3>
	<table>
		
		<tr>
			<td width="250">
				Surnom :
			</td>

			<td width="450">
				<input name="txtNick" type="text" size="20" maxlength="20" value="<? echo($_POST['txtNick']); ?>">
			</td>
		</tr>

		<tr>
			<td>
				Mot de passe :
			</td>

			<td>
				<input name="pwdPassword" type="password" size="16" maxlength="16">
			</td>
		</tr>

		<tr>
			<td>
				Mot de passe confirmation:
			</td>

			<td>
				<input name="pwdPassword2" type="password" size="16" maxlength="16">
			</td>
		</tr>
		<tr>
			<td >
				Prénom :
			</td>
			
			<td>
				<input name="txtFirstName" type="text" size="20" maxlength="20" value="<? echo($_POST['txtFirstName']); ?>">
			</td>
		</tr>

		<tr>
			<td>
				Nom :
			</td>

			<td>
				<input name="txtLastName" type="text" size="20" maxlength="20" value="<? echo($_POST['txtLastName']); ?>">
			</td>
		</tr>
		<tr>
            <td> Email : </td>
            <td><input name="txtEmail" type="text" size="50" maxlength="50" value="<? echo($_POST['txtEmail']); ?>">
            </td>
          </tr>
		  <tr>
            <td> Situation géographique : </td>
            <td><input name="txtSituationGeo" type="text" size="50" maxlength="50" value="<? echo($_POST['txtSituationGeo']); ?>">
            </td>
          </tr>
		  <tr>
            <td> Année de naissance : </td>
            <td><input name="txtAnneeNaissance" type="text" size="4" maxlength="4" value="<? echo($_POST['txtAnneeNaissance']); ?>">
            </td>
          </tr>
		  <tr>
            <td> Profil : </td>
            <td><TEXTAREA NAME="txtProfil" COLS="50" ROWS="5" ><? echo($_POST['txtProfil']); ?></TEXTAREA>
            </td>
          </tr>
		
		<tr>
			<td colspan="2">&nbsp</td>
		</tr>
		</table>
		<h3>Vos préférences</h3>
		<table>
		<tr valign="top">
			<td width="250">
				Notification par email :
			</td>

			<td width="450">
				
				<input name="txtEmailNotification" type="radio" value="oui" checked> Oui
				<br>
				<input name="txtEmailNotification" type="radio" value="non"> Non
			</td>
		</tr>

		<tr valign="top">
			<td>
				Thèmes :
			</td>

			<td>
				<input name="rdoTheme" type="radio" value="beholder" checked> <img src="images/beholder/white_king.gif" height="30" width="30"/>
																			<img src="images/beholder/white_queen.gif" height="30" width="30"/>
																			<img src="images/beholder/white_rook.gif" height="30" width="30"/>
																			<img src="images/beholder/white_bishop.gif" height="30" width="30"/>
																			<img src="images/beholder/white_knight.gif" height="30" width="30"/>
																			<img src="images/beholder/white_pawn.gif" height="30" width="30"/>
				<br>
				<input name="rdoTheme" type="radio" value="plain"> <img src="images/plain30x30/white_king.gif" />
																	<img src="images/plain30x30/white_queen.gif" />
																	<img src="images/plain30x30/white_rook.gif" />
																	<img src="images/plain30x30/white_bishop.gif" />
																	<img src="images/plain30x30/white_knight.gif" />
																	<img src="images/plain30x30/white_pawn.gif" />
				<br>
				
			</td>
		</tr>
		
		<tr>
			<td align="center" colspan="2">
				<input name="btnCreate" type="button" value="Valider" onClick="validateForm()">
			</td>
		</tr>
		</table>

		<input name="ToDo" value="NewUser" type="hidden">
	</form>
	<?}?>
	</div>
	</div>
<?
    require 'page_footer.php';
    mysql_close();
?>
