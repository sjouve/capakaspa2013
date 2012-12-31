<?
session_start();
/* load settings */
if (!isset($_CONFIG))
	require 'config.php';

require 'connectdb.php';
require 'bwc_players.php';
require 'bwc_games.php';

// Captcha
include_once  '/securimage/securimage.php';
$securimage = new Securimage();
		
/* Traitement des actions */
$err = false;
$ToDo = isset($_POST['ToDo']) ? $_POST['ToDo']:isset($_GET['ToDo']) ? $_GET['ToDo']:"" ;

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
		
		if ($securimage->check($_POST['captcha_code']) == false) {
		  $err = 'captcha';
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
			header("Location: index.php");
			exit;
		}
		else if (!activatePlayer($_GET['playerID'], $_GET['nick']))
			$err = 'db';
		break;
		
}

$titre_page = _("CapaKaspa");
$desc_page = _("Sign up for CapaKaspa, play chess and share your games.");
require 'page_header.php';
    
?>
<script type="text/javascript" src="javascript/formValidation.js">
 /* fonctions de validation des champs d'un formulaire */
</script>
<script type="text/javascript">
	function validateForm()
	{
		var dayDate = new Date();
		var annee = dayDate.getFullYear();

		document.getElementById("fields_required_error").style.display = "none";
		document.getElementById("login_format_error").style.display = "none";
		document.getElementById("password_format_error").style.display = "none";
		document.getElementById("email_format_error").style.display = "none";
		document.getElementById("year_format_error").style.display = "none";
		document.getElementById("confirm_password_error").style.display = "none";
		
		if (isEmpty(document.userdata.txtFirstName.value)
			|| isEmpty(document.userdata.txtLastName.value)
			|| isEmpty(document.userdata.txtNick.value)
			|| isEmpty(document.userdata.pwdPassword.value)
			|| isEmpty(document.userdata.txtEmail.value)
			|| isEmpty(document.userdata.txtSituationGeo.value)
			|| isEmpty(document.userdata.txtAnneeNaissance.value))
		{
			document.getElementById("fields_required_error").style.display = "block";
			return;
		}
		
		if (!isAlphaNumeric(document.userdata.txtNick.value))
		{
			document.getElementById("login_format_error").style.display = "block";
			return;
		}
		
		if (!isAlphaNumeric(document.userdata.pwdPassword.value))
		{
			document.getElementById("password_format_error").style.display = "block";
			return;
		}
		
		if (!isEmailAddress(document.userdata.txtEmail.value))
		{
			document.getElementById("email_format_error").style.display = "block";
			return;
		}
		
		if (!isNumber(document.userdata.txtAnneeNaissance.value) || !isWithinRange(document.userdata.txtAnneeNaissance.value, 1900, annee))
		{
			document.getElementById("year_format_error").style.display = "block";
			return;
		}
		
		if (document.userdata.pwdPassword.value == document.userdata.pwdPassword2.value)
			document.userdata.submit();
		else
			document.getElementById("confirm_password_error").style.display = "block";
		
	}
</script>
<?
require 'page_body.php';
?>
	<div id="contentlarge">
    <div class="blogbody">
    	
	<?if ($ToDo == 'activer' && !$err) {?>
	<b>Votre compte vient d'être activé.</b>
	<p>
	Vous pouvez maintenant vous connecter à la zone de jeu en différé.
	</p>
	<?} else if ($ToDo == 'activer' && $err == 'db') {?>
	Une erreur s'est produite lors de l'activation !!!
	<?} else if (!$err && $ToDo == 'NewUser') {?>
	<b>Un message de confirmation d'inscription a été envoyé à l'adresse de messagerie que vous avez choisi : <? echo($_POST['txtEmail']); ?> .</b>
	<p>En attendant, vous pouvez consulter le <a href="../manuel-utilisateur-jouer-echecs-capakaspa.pdf" target="_blank">manuel utilisateur</a> de la zone de jeu en différé.</p>
	<p>Si vous souhaitez discuter au sujet des échecs ou faire des remarques et suggestions concernant le site CapaKaspa, vous pouvez aussi vous <a href="http://forum.capakaspa.info/profile.php?mode=register">inscrire sur le forum</a> de CapaKaspa.</p><br/>
	<hr/>

	<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
	<?} else  {?>
	
	<p>
	<h1><?php echo _("Play Chess and share your Games !");?></h1>
	Play chess and share your games
	</p>
	<p><img src="images/icone_video.png"/> <a href="http://youtu.be/J6pMC2Ceaxw" target="_blank"><?php echo _("Demo video");?></a>
	</p>
	<p><img src="images/icone_pdf.gif"/> <a href="../manuel-utilisateur-jouer-echecs-capakaspa.pdf" target="_blank"><?php echo _("User manual");?></a>
	</p>
	<form name="userdata" method="post" action="jouer-echecs-differe-inscription.php?ToDo=NewUser">
	<h3><?php echo _("New on CapaKaspa ? Sign up");?></h3>
	<div class="error" id="fields_required_error" style="display: none"><?echo _("All fields are required")?></div>
	<div class="error" id="login_format_error" style="display: none"><?echo _("Bad format for login")?></div>
	<div class="error" id="password_format_error" style="display: none"><?echo _("Bad format for password")?></div>
	<div class="error" id="email_format_error" style="display: none"><?echo _("Bad format for email")?></div>
	<div class="error" id="year_format_error" style="display: none"><?echo _("Bad format for year")?></div>
	<div class="error" id="confirm_password_error" style="display: none"><?echo _("Password confirmation error")?></div>
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
		if ($err == 'captcha')
			echo("<div class='error'>Le code de vérification est érroné. Essayez de nouveau.</div>");
			
	?>
	<table>
		
		<tr>
			<td width="250">
				<?php echo _("Login");?> :
			</td>

			<td width="450">
				<input name="txtNick" type="text" size="20" maxlength="20" value="<? echo(isset($_POST['txtNick'])?$_POST['txtNick']:""); ?>">
			</td>
		</tr>

		<tr>
			<td>
				<?php echo _("Password");?> :
			</td>

			<td>
				<input name="pwdPassword" type="password" size="16" maxlength="16">
			</td>
		</tr>

		<tr>
			<td>
				<?php echo _("Confirm password");?> :
			</td>

			<td>
				<input name="pwdPassword2" type="password" size="16" maxlength="16">
			</td>
		</tr>
		<tr>
			<td >
				<?php echo _("First name");?> :
			</td>
			
			<td>
				<input name="txtFirstName" type="text" size="20" maxlength="20" value="<? echo(isset($_POST['txtFirstName'])?$_POST['txtFirstName']:""); ?>">
			</td>
		</tr>

		<tr>
			<td>
				<?php echo _("Last name");?> :
			</td>

			<td>
				<input name="txtLastName" type="text" size="20" maxlength="20" value="<? echo(isset($_POST['txtLastName'])?$_POST['txtLastName']:""); ?>">
			</td>
		</tr>
		<tr>
            <td> <?php echo _("Email");?> : </td>
            <td><input name="txtEmail" type="text" size="50" maxlength="50" value="<? echo(isset($_POST['txtEmail'])?$_POST['txtEmail']:""); ?>">
            </td>
          </tr>
		  <tr>
            <td> <?php echo _("Country");?> : </td>
            <td><input name="txtSituationGeo" type="text" size="50" maxlength="50" value="<? echo(isset($_POST['txtSituationGeo'])?$_POST['txtSituationGeo']:""); ?>">
            </td>
          </tr>
		  <tr>
            <td> <?php echo _("Year of birth");?> : </td>
            <td><input name="txtAnneeNaissance" type="text" size="4" maxlength="4" value="<? echo(isset($_POST['txtAnneeNaissance'])?$_POST['txtAnneeNaissance']:""); ?>">
            </td>
          </tr>
		  <!-- <tr valign="top">
            <td> A propos de vous : </td>
            <td><TEXTAREA NAME="txtProfil" COLS="50" ROWS="5" ><? echo(isset($_POST['txtProfil'])?$_POST['txtProfil']:""); ?></TEXTAREA>
            </td>
          </tr> -->
		
		<tr>
			<td colspan="2">&nbsp</td>
		</tr>
		</table>
		<!-- <h3>Vos préférences</h3>
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
		</table> -->
		
		<table>
		<tr>
			<td width="250">
				<img id="captcha" src="securimage/securimage_show.php" alt="<?php echo _("Captcha Image");?>" title="<?php echo _("Captcha Image");?>"/>
			</td>
			<td>
				<input type="text" name="captcha_code" size="10" maxlength="6" />
				<a href="#" onclick="document.getElementById('captcha').src = 'securimage/securimage_show.php?' + Math.random(); return false"><img src="images/icone_rafraichir.png" border="0" alt="<?php echo _("Other image");?>" title="<?php echo _("Other image");?>"/></a>
			</td>
		</tr>
		
		<tr>
			<td align="left" colspan="2">
				<input name="btnCreate" type="button" value="<?php echo _("Sign up for CapaKaspa");?>" onClick="validateForm()" class="button">
			</td>
		</tr>
		</table>

		<!-- <input name="ToDo" value="NewUser" type="hidden"> -->
	</form>
	<?}?>
	</div>
	</div>
<?
    require 'page_footer.php';
    mysql_close();
?>
