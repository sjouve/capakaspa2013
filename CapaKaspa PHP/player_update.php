<?	
require 'include/mobilecheck.php';
session_start();

/* load settings */
if (!isset($_CONFIG))
	require 'include/config.php';

/* load external functions for setting up new game */
require 'dac/dac_common.php';
require 'dac/dac_players.php';
require 'bwc/bwc_chessutils.php';
require 'bwc/bwc_common.php';
require 'bwc/bwc_players.php';

/* connect to database */
require 'include/connectdb.php';

/* check session status */
require 'include/sessioncheck.php';

require 'include/localization.php';

$err = 1;
$ToDo = isset($_POST['ToDo']) ? $_POST['ToDo']:Null;
switch($ToDo)
{
	
	case 'UpdateProfil':
		
		$err = updateProfil($_SESSION['playerID'], $_POST['pwdPassword'], $_POST['pwdOldPassword'], strip_tags($_POST['txtFirstName']), strip_tags($_POST['txtLastName']), $_POST['txtEmail'], strip_tags($_POST['txtProfil']), strip_tags($_POST['txtSituationGeo']), $_POST['txtAnneeNaissance'], $_POST['rdoTheme'], $_POST['txtEmailNotification'], $_POST['txtLanguage'],$_POST['txtShareInvitation'],$_POST['txtShareResult'],$_POST['rdoSocialNetwork'], $_POST['txtSocialID'], $_POST['txtCountryCode'], $_POST['txtSex']);
		break;
		
	case 'CreateVacation':
	
		$err = createVacation($_SESSION['playerID'], $_POST['nbDays'], $CFG_EXPIREGAME);	
		break;
		
	case 'DisableAccount':
		$err = updateProfil($_SESSION['playerID'], "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "");
		if ($err == 1)
			header("Location: index.php?ToDo=Logout");
		break;
		
}
		
$titre_page = _("Update your profile");
$desc_page = _("Update your profile");
require 'include/page_header.php';
?>
<script type="text/javascript" src="javascript/formValidation.js">
 /* fonctions de validation des champs d'un formulaire */
</script>
<script type="text/javascript">	
function validatePersonalInfo()
{
	var dayDate = new Date();
	var annee = dayDate.getFullYear();

	document.getElementById("fields_required_error").style.display = "none";
	document.getElementById("login_format_error").style.display = "none";
	document.getElementById("password_format_error").style.display = "none";
	document.getElementById("email_format_error").style.display = "none";
	document.getElementById("confirm_password_error").style.display = "none";
	document.getElementById("old_password_error").style.display = "none";
	
	if (isEmpty(document.userdata.txtFirstName.value)
		|| isEmpty(document.userdata.txtLastName.value)
		//|| isEmpty(document.userdata.txtNick.value)
		|| isEmpty(document.userdata.txtEmail.value)
		|| isEmpty(document.userdata.txtAnneeNaissance.value)
		|| isEmpty(document.userdata.txtCountryCode.value))
	{
		document.getElementById("fields_required_error").style.display = "block";
		return;
	}
	
	if (!isEmpty(document.userdata.pwdPassword.value) && !isAlphaNumeric(document.userdata.pwdPassword.value))
	{
		document.getElementById("password_format_error").style.display = "block";
		return;
	}

	if (!isEmpty(document.userdata.pwdPassword.value)
			&& isEmpty(document.userdata.pwdOldPassword.value))
	{
		document.getElementById("old_password_error").style.display = "block";
		return;
	}
	
	if (!isEmailAddress(document.userdata.txtEmail.value))
	{
		document.getElementById("email_format_error").style.display = "block";
		return;
	}
	
	if (document.userdata.pwdPassword.value == document.userdata.pwdPassword2.value)
		document.userdata.submit();
	else
		document.getElementById("confirm_password_error").style.display = "block";
}

function validateVacation()
{
	document.getElementById("number_days_error").style.display = "none";
	
	if (!isWithinRange(document.Vacation.nbDays.value, 1, 30))
	{
		document.getElementById("number_days_error").style.display = "block";
		return;
	}
	var vok=false;
	vok = confirm(document.getElementById('#confirm_add_vacation_id').innerHTML);
	if (vok)
	{
		document.vacation.submit();
	}
}

function disableAccount()
{	
	var vok=false;
	vok = confirm(document.getElementById('#confirm_disable_account_id').innerHTML);
	if (vok)
	{
		document.disable.submit();
	}
}
</script>
<?
require 'include/page_body.php';
?>
<div id="contentlarge">
    <div class="contentbody">
	
	<?
	if ($err == 0)
		echo("<div class='error'>"._("A technical error has occured")."</div>");
	if ($ToDo == 'UpdateProfil')
	{
		if ($err == -1)
			echo("<div class='error'>"._("Your old password is wrong")."</div>");
		if ($err == 1)
			echo("<div class='success'>"._("Profile changes have been saved successfully")."</div>");
	}
	?>
	<div class="error" id="fields_required_error" style="display: none"><?echo _("All fields are required")?></div>
	<div class="error" id="login_format_error" style="display: none"><?echo _("Bad format for login")?></div>
	<div class="error" id="password_format_error" style="display: none"><?echo _("Bad format for password")?></div>
	<div class="error" id="email_format_error" style="display: none"><?echo _("Bad format for email")?></div>
	<div class="error" id="confirm_password_error" style="display: none"><?echo _("Password confirmation error")?></div>
	<div class="error" id="old_password_error" style="display: none"><?echo _("Old password is required")?></div>
	<!-- For translation in javascript -->
    <span id="#confirm_add_vacation_id" style="display: none"><?echo _("This postponement can not be canceled and all your games will be immediately postponed. Please confirm your absence ?")?></span>
    <span id="#confirm_disable_account_id" style="display: none"><?echo _("You want to disable your account. Please confirm ?")?></span>
    
	<form name="userdata" action="player_update.php" method="post">
	  <h3><?php echo _("Basic info");?></h3>
        <table border="0" width="100%">
          <tr>
            <td width="180"><?php echo _("I am");?> :</td>
            <td>
            	<select name="txtSex" id="txtSex">
            		<option value="M" <?if ($_SESSION['playerSex'] == "M") echo("selected");?>><?echo _("Male");?></option>
            		<option value="F" <?if ($_SESSION['playerSex'] == "F") echo("selected");?>><?echo _("Female");?></option>
            	</select>
            </td>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td><?php echo _("User name");?> : </td>
            <td><? echo($_SESSION['nick']); ?></td>
            <td colspan="2"><?echo _("Enter data here to change password");?></td>
          </tr>
		  <tr>
            <td><?php echo _("First name");?> : </td>
            <td><input name="txtFirstName" type="text" size="20" maxlength="20" value="<? echo($_SESSION['firstName']); ?>"></td>
            <td><?php echo _("Actual password");?> : </td>
            <td><input name="pwdOldPassword" size="30" type="password" value=""></td>
          </tr>
          <tr>
            <td><?php echo _("Last name");?> : </td>
            <td><input name="txtLastName" type="text" size="20" maxlength="20" value="<? echo($_SESSION['lastName']); ?>"></td>
            <td><?php echo _("New password");?> : </td>
            <td><input name="pwdPassword" size="30" type="password" value=""></td>
          </tr>
          <tr>
            <td><?php echo _("Birth date (year)");?> :</td>
            <td><select name="txtAnneeNaissance" id="txtAnneeNaissance">
            	<?php
            	echo "\t",'<option value="">', _("Select a year") ,'</option>',"\n";
            	// Parcours du tableau
				$annee = $_SESSION['anneeNaissance'];
				for($i=1900; $i<=date('Y'); $i++)
				{
					$selected = "";
					// L'année est-elle celle postée ?
					if($i == $annee)
					{
						$selected = " selected";
					}
					// Affichage de la ligne
					echo "\t",'<option value="', $i ,'"', $selected ,'>', $i ,'</option>',"\n";
				}
				?>
            </select></td>
            <td><?php echo _("Confirm password");?>: </td>
            <td><input name="pwdPassword2" size="30" type="password" value=""></td>
        </tr>
        </table>
        
        <h3><?php echo _("Contact info");?></h3>
        <table border="0" width="650">
		  <tr>
            <td width="180"><?php echo _("Email");?> : </td>
            <td><? echo($_SESSION['email']); ?><input type="hidden" name="txtEmail" value="<? echo($_SESSION['email']); ?>"></td>
          </tr>
          <tr>
            <td><?echo _("Country");?> :</td>
            <td><select name="txtCountryCode" id="txtCountryCode">
	            <?
	            echo "\t",'<option value="">', _("Select your country") ,'</option>',"\n";
	            $tmpCountries = listCountriesByLang(getLang());
	            while($tmpCountry = mysql_fetch_array($tmpCountries, MYSQL_ASSOC))
	            {
	            	$selected = "";
	            	$countryCode = $_SESSION['countryCode'];
	            	if($tmpCountry['countryCode'] == $countryCode)
	            	{
	            		$selected = " selected";
	            	}
	            	echo "\t",'<option value="', $tmpCountry['countryCode'] ,'"', $selected ,'>', $tmpCountry['countryName'] ,'</option>',"\n";
	            }	
	            ?>
            </select></td>
        </tr>
		  <tr>
            <td><?php echo _("Localization");?> : </td>
            <td><input name="txtSituationGeo" type="text" size="50" maxlength="50" value="<? echo($_SESSION['situationGeo']); ?>"></td>
          </tr>
          </table>
          
		  <h3><?php echo _("More about you");?></h3>
		  <table border="0" width="100%">
		   <tr>
            <td width="180"><?php echo _("Elo CapaKaspa");?> : </td>
            <td><? echo($_SESSION['elo']); ?></td>
          </tr>
		  <tr>
            <td><?php echo _("About you");?> : </td>
            <td><TEXTAREA NAME="txtProfil" COLS="50" ROWS="5"><? echo($_SESSION['profil']); ?></TEXTAREA></td>
          </tr>
          <tr>
            <td><?php echo _("Picture");?> : </td>
            <td>
            	<img src="<?echo(getPicturePath($_SESSION['socialNetwork'], $_SESSION['socialID']));?>" width="50" height="50" style="float: left;margin-right: 30px;"/>
            	<? echo _("Display picture of your profile on")?> :<br/>
            	<input name="rdoSocialNetwork" type="radio" value="" <? if ($_SESSION['socialNetwork']=="") echo("checked");?>> <? echo _("No profile")?>
            	<input name="rdoSocialNetwork" type="radio" value="FB" <? if ($_SESSION['socialNetwork']=="FB") echo("checked");?>> <? echo _("Facebook")?>
            	<input name="rdoSocialNetwork" type="radio" value="GP" <? if ($_SESSION['socialNetwork']=="GP") echo("checked");?>> <? echo _("Google+")?>
            	<input name="rdoSocialNetwork" type="radio" value="TW" <? if ($_SESSION['socialNetwork']=="TW") echo("checked");?>> <? echo _("Twitter")?>
            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><?php echo _("Social network ID");?> : <input name="txtSocialID" type="text" size="50" maxlength="100" value="<? echo($_SESSION['socialID']); ?>"> <a href="manuel-utilisateur-jouer-echecs-capakaspa.pdf#page=14" target="_blank"><img src="images/point-interrogation.gif" border="0"/></a></td>
          </tr>
        </table>
      
      <h3><?echo _("Preferences");?></h3>
      
        <table border="0" width="100%">
          <tr>
            <td width="180"><?echo _("Email notification");?> :</td>
            <td><?
					if ($_SESSION['pref_emailnotification'] == 'oui')
					{
				?>
              <input name="txtEmailNotification" type="radio" value="oui" checked>
              <?echo _("Yes");?> 
              <input name="txtEmailNotification" type="radio" value="non">
              <?echo _("No");?> 
              <?
					}
					else
					{
				?>
              <input name="txtEmailNotification" type="radio" value="oui">
              <?echo _("Yes");?> 
              <input name="txtEmailNotification" type="radio" value="non" checked>
              <?echo _("No");?> 
              <?	}
				?>
				<?echo _("(Invitations, moves, results and private messages)");?>
            </td>
          </tr>
          <tr>
            <td width="180"><?echo _("Share invitations");?> :</td>
            <td><?
					if ($_SESSION['pref_shareinvitation'] == 'oui')
					{
				?>
              <input name="txtShareInvitation" type="radio" value="oui" checked>
              <?echo _("Yes");?> 
              <input name="txtShareInvitation" type="radio" value="non">
              <?echo _("No");?> 
              <?
					}
					else
					{
				?>
              <input name="txtShareInvitation" type="radio" value="oui">
              <?echo _("Yes");?> 
              <input name="txtShareInvitation" type="radio" value="non" checked>
              <?echo _("No");?> 
              <?	}
				?>
            </td>
          </tr>
          <tr>
            <td width="180"><?echo _("Share results");?> :</td>
            <td><?
					if ($_SESSION['pref_shareresult'] == 'oui')
					{
				?>
              <input name="txtShareResult" type="radio" value="oui" checked>
              <?echo _("Yes");?> 
              <input name="txtShareResult" type="radio" value="non">
              <?echo _("No");?> 
              <?
					}
					else
					{
				?>
              <input name="txtShareResult" type="radio" value="oui">
              <?echo _("Yes");?> 
              <input name="txtShareResult" type="radio" value="non" checked>
              <?echo _("No");?> 
              <?	}
				?>
            </td>
          </tr>
          <tr>
            <td><?echo _("Chess set");?> :</td>
            <td><?
					if ($_SESSION['pref_theme'] == 'merida')
					{
				?>
              <input name="rdoTheme" type="radio" value="merida" checked>
              	<img src="pgn4web/merida/28/wk.png"/>
				<img src="pgn4web/merida/28/wq.png"/>
				<img src="pgn4web/merida/28/wr.png"/>
				<img src="pgn4web/merida/28/wb.png"/>
				<img src="pgn4web/merida/28/wk.png"/>
				<img src="pgn4web/merida/28/wp.png"/>
              <input name="rdoTheme" type="radio" value="alpha">
             	<img src="pgn4web/alpha/28/wk.png"/>
				<img src="pgn4web/alpha/28/wq.png"/>
				<img src="pgn4web/alpha/28/wr.png"/>
				<img src="pgn4web/alpha/28/wb.png"/>
				<img src="pgn4web/alpha/28/wk.png"/>
				<img src="pgn4web/alpha/28/wp.png"/>
              <?
					}
					else
					{
				?>
              <input name="rdoTheme" type="radio" value="merida">
              	<img src="pgn4web/merida/28/wk.png"/>
				<img src="pgn4web/merida/28/wq.png"/>
				<img src="pgn4web/merida/28/wr.png"/>
				<img src="pgn4web/merida/28/wb.png"/>
				<img src="pgn4web/merida/28/wk.png"/>
				<img src="pgn4web/merida/28/wp.png"/> 
              <input name="rdoTheme" type="radio" value="alpha" checked>
				<img src="pgn4web/alpha/28/wk.png"/>
				<img src="pgn4web/alpha/28/wq.png"/>
				<img src="pgn4web/alpha/28/wr.png"/>
				<img src="pgn4web/alpha/28/wb.png"/>
				<img src="pgn4web/alpha/28/wk.png"/>
				<img src="pgn4web/alpha/28/wp.png"/>
              <?	}
				?>
            </td>
          </tr>
          <tr>
            <td><?echo _("Displaying language")?> :</td>
            <td>
            	<select name="txtLanguage" id="txtLanguage">
            		<option value="en_US" <?if ($_SESSION['pref_language'] == "en_US") echo("selected");?>><?echo _("English");?></option>
            		<option value="fr_FR" <?if ($_SESSION['pref_language'] == "fr_FR") echo("selected");?>><?echo _("French");?></option>
            	</select> 
            </td>
          </tr>
          <tr>
            <td colspan="2" align="center">
            	<input class="button" name="Update" type="button" value="<?echo _("Save");?>" onClick="validatePersonalInfo()"> 
            	
            </td>
          </tr>
        </table>
		<input type="hidden" name="ToDo" value="UpdateProfil">
      </form>
      
      <!-- 
      Gestion des absences
      Le joueur saisie la durée de son congé qui est effectif à partir du lendemain
      On demande confirmation car toute annulation est impossible
      La saisi du congé n'est plus possible pendant la durée d'un congé
      Le solde de congé du joueur est décrémenté du nombre de jour saisi
      Le système enregistre la date de début du congé (date du jour + 1), la durée et la date de fin (date de début + durée)
      
      Lors de la saisie du congé il faut modifier la date du dernier des parties du joueur :
      Pour chaque partie
      	Si pas de congé en cours pour l'adversaire on ajoute la durée du congé saisi +1 à la date du dernier coup
      	Sinon on ajoute la durée du congé saisi - (date de fin du congé en cours de l'adversaire - date de début du congé saisi)      
      
      Tant qu'un des joueurs d'une partie est en congé la partie est gelée (il est impossible de jouer un coup)
       -->
	
      
      <h3><? echo _("Game postponement");?> <a href="manuel-utilisateur-jouer-echecs-capakaspa.pdf#page=15" target="_blank"><img src="images/point-interrogation.gif" border="0"/></a></h3>
      	<? 
		if ($ToDo == 'CreateVacation')
		{
			if ($err == -100)
				echo("<div class='error'>"._("The number of days of vacation that you requested is not valid")."</div>");
			if ($err == 1)
				echo("<div class='success'>"._("Your request of vacation has been saved successfully")."</div>");
		}
		?>
      <div class="error" id="number_days_error" style="display: none"><?echo _("Number of days must be between 0 and 30")?></div>
      
      <? echo _("You have");?> <b><?echo(countAvailableVacation($_SESSION['playerID']));?></b> <? echo _("available days of vacation for year");?> <?echo(date('Y'))?> (<? echo _("days of vacation on 2 years are counted in the last year");?>).<br/>
      <br/>
      <?	
      		$fmt = new IntlDateFormatter(getenv("LC_ALL"), IntlDateFormatter::MEDIUM, IntlDateFormatter::NONE);
      		$tmpVacations = getCurrentVacation($_SESSION['playerID']);
			$nbCurrentVacation = mysql_num_rows($tmpVacations);
			if ($nbCurrentVacation == 0)
				echo _("You don't have vacation in progress.");
			else
			{
				$tmpVacation = mysql_fetch_array($tmpVacations, MYSQL_ASSOC);
				$beginDate = new DateTime($tmpVacation['beginDate']);
				$strBeginDate = $fmt->format($beginDate);
				$endDate = new DateTime($tmpVacation['endDate']);
				$strEndDate = $fmt->format($endDate);
				echo _("You have vacation from");
				echo(" <b>".$strBeginDate."</b> ");
    			echo("to" );
				echo(" <b>".$strEndDate."</b>.");
			}
    	
      		if ($nbCurrentVacation == 0)
      	{
      		
      	?>
      	<br/><br/>
		<form name="vacation" action="player_update.php" method="post">
	  	<?	$tomorrow  = mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"));
	  	?> 
	        <?echo _("You want to postpone your games for")?> 
	        <input name="nbDays" size="2" maxlength="2" type="text" value=""> <?echo _("day(s) from");?> <? echo($fmt->format($tomorrow));?> 
	        <input name="Validate" class="button" type="button" value="<?echo _("Postpone my games...");?>" onClick="validateVacation()"> <?echo _("(All your games will be postponed immediatly)");?>
	      	<input type="hidden" name="ToDo" value="CreateVacation">
    	</form>
    	<? }?>
    	<br>
    	<h3><?echo _("Disable my account")?></h3>
    	<?echo _("You can disable your account on CapaKaspa. Other players will no longer have interactions with your account. But it will viewable from games or news. You can reactivate it later if you want.")?>
    	<form name="disable" action="player_update.php" method="post">
    		<center>
    			<input class="button" name="Disable" type="button" value="<?echo _("Disable my account");?>" onClick="disableAccount()">
    		</center>
    		<input type="hidden" name="ToDo" value="DisableAccount">
    	</form>
    </div>
  </div>
<?
    require 'include/page_footer.php';
    mysql_close();
?>
