<?	
require 'include/mobilecheck.php';
session_start();

/* load settings */
if (!isset($_CONFIG))
	require 'include/config.php';

/* load external functions for setting up new game */
require 'dac/dac_common.php';
require 'dac/dac_players.php';
require 'dac/dac_activity.php';
require 'bwc/bwc_chessutils.php';
require 'bwc/bwc_common.php';
require 'bwc/bwc_players.php';
require 'dac/dac_games.php';
require 'bwc/bwc_games.php';

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
		$socialID = $_POST['txtSocialID'];
		
		if (!empty ($_FILES['ifile']['tmp_name']))
		{
			/* Thumbnail class is required */
			include_once('phpthumb/ThumbLib.inc.php');
		
			/* GetImageSize() function pulls out valid info about image such as image type, height etc. If it fails
			 then it is not valid image. */
		
			if (!getimagesize($_FILES['ifile']['tmp_name']))
			{
				$err = 10;
				break;	
			}
		
			$imgtype = array('1' => '.gif', '2' => '.jpg' , '3' => '.png');
		
			// extract the width and height of image
			list($width, $height, $type, $attr) = getimagesize($_FILES['ifile']['tmp_name']);
		
			// Extract the image extension
			switch ($type)
			{
				case 1: $ext='.gif'; break;
				case 2: $ext = '.jpg';break;
				case 3: $ext='.png'; break;
			}
			// Dont allow gif files to upload as it may  contain harmful code
			if ( $ext == '.gif') {
				$err = 20;
				break;			
			}
		
			/* Specify maximum height and width of users uploading image */
			if ($width > 1000 || $height > 1000)
			{
				$err = 30;
				break;			 
			}
			/* Specify maximum file size here in bytes */
			if ($_FILES['ifile']['size'] > 500000 )
			{
				$err = 40;
				break;	
			}
			/******** IMAGE RESIZING *********************/
			// Before we start resizing, we first have to move the image file to server
			// save it there under a unique name and then do the final resizing and save the resized image.
		
			// Specify which directory you want to upload. It should be a subfolder where the script is present
			// We also generate a unique name for picture FILE-USERID-XXX where xxx is random number
			// The uploads folder must have writable permissions.
			$uploaddir = 'images/uploads/';
			$socialID = "img-".$_SESSION['nick']. $ext;
			$uploadfile =  $uploaddir . $socialID;
			
		
			if (!move_uploaded_file($_FILES['ifile']['tmp_name'], $uploadfile ))
			{
				$err = 50;
				break;
			}
		
			$thumb = PhpThumbFactory::create($uploadfile);
			//specify the height and width of avatar image to resize
			$thumb->resize(50, 50);
			$thumb->save($uploadfile);
		}
		
		$err = updateProfil($_SESSION['playerID'], $_POST['pwdPassword'], $_POST['pwdOldPassword'], strip_tags($_POST['txtFirstName']), strip_tags($_POST['txtLastName']), $_POST['txtEmail'], strip_tags($_POST['txtProfil']), strip_tags($_POST['txtSituationGeo']), $_POST['txtAnneeNaissance'], $_POST['rdoTheme'], $_POST['txtEmailNotification'], $_POST['txtLanguage'],$_POST['txtShareInvitation'],$_POST['txtShareResult'],$_POST['rdoSocialNetwork'], $socialID, $_POST['txtCountryCode'], $_POST['txtSex']);
		break;
		
	case 'CreateVacation':
	
		$err = createVacation($_SESSION['playerID'], $_POST['nbDays']);	
		break;
		
	case 'DisableAccount':
		$err = updateProfil($_SESSION['playerID'], "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "");
		if ($err == 1)
			header("Location: game_in_progress.php?ToDo=Logout");
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
	document.getElementById("firstname_format_error").style.display = "none";
	document.getElementById("lastname_format_error").style.display = "none";
	document.getElementById("confirm_password_error").style.display = "none";
	document.getElementById("old_password_error").style.display = "none";
	
	if (isEmpty(Trim(document.userdata.txtFirstName.value))
		|| isEmpty(Trim(document.userdata.txtLastName.value))
		//|| isEmpty(document.userdata.txtNick.value)
		|| isEmpty(document.userdata.txtEmail.value)
		|| isEmpty(document.userdata.txtAnneeNaissance.value)
		|| isEmpty(document.userdata.txtCountryCode.value))
	{
		document.getElementById("fields_required_error").style.display = "block";
		return;
	}

	/*if (!isAlphaNumeric(document.userdata.txtFirstName.value))
	{
		document.getElementById("firstname_format_error").style.display = "block";
		return;
	}
	
	if (!isAlphaNumeric(document.userdata.txtLastName.value))
	{
		document.getElementById("lastname_format_error").style.display = "block";
		return;
	}*/
	
	if (!isEmpty(document.userdata.pwdPassword.value) && (!isAlphaNumeric(document.userdata.pwdPassword.value) || document.userdata.pwdPassword.value.length < 6))
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
		document.Vacation.submit();
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

function deleteCKPicture()
{
	document.getElementById("currentSocialID").value = "";
	setCKPicture();
}

function setCKPicture()
{
	if(document.getElementById("rdoCK").checked)
	{
		document.getElementById("txtSocialID").value = "";
		if (document.getElementById("currentSocialNW").value == "CK")
			document.getElementById("txtSocialID").value = document.getElementById("currentSocialID").value;
		if (document.getElementById("txtSex").value == "M")
		{
			if (document.getElementById("txtSocialID").value == "")
				document.getElementById("txtSocialID").value = "avatar_homme.jpg";
			
		}
		else
		{
			if (document.getElementById("txtSocialID").value == "")
				document.getElementById("txtSocialID").value = "avatar_femme.jpg";
					
		}
	
		document.getElementById("pictureProfile").src = "images/uploads/"+document.getElementById("txtSocialID").value;		
		document.getElementById("socialID").style.display = "none";
		document.getElementById("uploadPicture").style.display = "block";
	}
}

function setNWPicture()
{
	if(document.getElementById("rdoFB").checked)
	{
		document.getElementById("pictureProfile").src = "https://graph.facebook.com/"+document.getElementById("txtSocialID").value+"/picture";
	}
	if(document.getElementById("rdoGP").checked)
	{
		document.getElementById("pictureProfile").src = "https://plus.google.com/s2/photos/profile/"+document.getElementById("txtSocialID").value+"?sz=32";
	}
	if(document.getElementById("rdoTW").checked)
	{
		document.getElementById("pictureProfile").src = "http://api.twitter.com/1/users/profile_image/"+document.getElementById("txtSocialID").value+".xml";
	}
}

function initNWPicture()
{
	document.getElementById("socialID").style.display = "block";
	document.getElementById("uploadPicture").style.display = "none";
	document.getElementById("txtSocialID").value = "";
	if(document.getElementById("rdoFB").checked && document.getElementById("currentSocialNW").value == "FB")
		document.getElementById("txtSocialID").value = document.getElementById("currentSocialID").value;
	if(document.getElementById("rdoGP").checked && document.getElementById("currentSocialNW").value == "GP")
		document.getElementById("txtSocialID").value = document.getElementById("currentSocialID").value;	
	if(document.getElementById("rdoTW").checked && document.getElementById("currentSocialNW").value == "TW")
		document.getElementById("txtSocialID").value = document.getElementById("currentSocialID").value;
	setNWPicture();
}

</script>
<?
$attribut_body = "onload='setCKPicture();'";
require 'include/page_body.php';
?>
<div id="contentlarge">
    <div class="contentbody">
	
	<?
	if ($err == 0)
		echo("<div class='error'>"._("A technical error has occured")."</div>");
	if ($err == 10)
		echo("<div class='error'>"._("Invalid image file for picture")."</div>");
	if ($err == 20)
		echo("<div class='error'>"._("GIF not allowed. Please use only PNG or JPEG formats")."</div>");
	if ($err == 30)
		echo("<div class='error'>"._("Maximum width and height exceeded (max 1000x1000 pixels)")."</div>");
	if ($err == 40)
		echo("<div class='error'>"._("Large file size (max 500kb)")."</div>");
	if ($err == 50)
		echo("<div class='error'>"._("Error moving the uploaded file")."</div>");
	
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
	<div class="error" id="password_format_error" style="display: none"><?echo _("Bad format for password : at least 6 caracters")?></div>
	<div class="error" id="email_format_error" style="display: none"><?echo _("Bad format for email")?></div>
	<div class="error" id="firstname_format_error" style="display: none"><?echo _("Bad format for first name")?></div>
	<div class="error" id="lastname_format_error" style="display: none"><?echo _("Bad format for last name")?></div>
	<div class="error" id="confirm_password_error" style="display: none"><?echo _("Password confirmation error")?></div>
	<div class="error" id="old_password_error" style="display: none"><?echo _("Old password is required")?></div>
	<!-- For translation in javascript -->
    <span id="#confirm_add_vacation_id" style="display: none"><?echo _("This postponement can not be canceled and all your games will be immediately postponed. Please confirm your absence ?")?></span>
    <span id="#confirm_disable_account_id" style="display: none"><?echo _("You want to disable your account. Please confirm ?")?></span>
    
	<form name="userdata" action="player_update.php" method="post" enctype="multipart/form-data">
	  <h3><?php echo _("Basic info");?></h3>
        <table border="0" width="100%">
          <tr>
            <td width="180"><?php echo _("I am");?> :</td>
            <td>
            	<select onChange="setCKPicture();" name="txtSex" id="txtSex">
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
            <td>
            	<? echo($_SESSION['email']); ?><input type="hidden" name="txtEmail" value="<? echo($_SESSION['email']); ?>"> 
            	<span onmouseout="document.getElementById('helpEmail').style.display = 'none';" onmouseover="document.getElementById('helpEmail').style.display = 'block';"><img src="images/point-interrogation.gif" border="0"/></span>
            	<div id="helpEmail" style="display: none;" class="help">
		      		<? echo _("You can update your email address.");?><br>
					<? echo _("First disable your account (see bottom of this page).");?><br>
					<? echo _("Sign in and follow the activation process.");?><br>
				</div>
            </td>
          </tr>
          <tr>
            <td><?echo _("Country");?> :</td>
            <td><select name="txtCountryCode" id="txtCountryCode">
	            <?
	            echo "\t",'<option value="">', _("Select your country") ,'</option>',"\n";
	            $tmpCountries = listCountriesByLang(getLang());
	            while($tmpCountry = mysqli_fetch_array($tmpCountries, MYSQLI_ASSOC))
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
            <td>
            <? echo($_SESSION['elo']); ?> 
            <span onmouseout="document.getElementById('helpElo').style.display = 'none';" onmouseover="document.getElementById('helpElo').style.display = 'block';"><img src="images/point-interrogation.gif" border="0"/></span>
            <div id="helpElo" style="display: none;" class="help">
            <? echo _("Elo ranking is calculated monthly and takes into account the classic games completed during the month past.");?>
            </div>
            </td>
          </tr>
          <tr>
            <td width="180"><?php echo _("Elo Chess960");?> : </td>
            <td>
            <? echo($_SESSION['elo960']); ?> 
            <span onmouseout="document.getElementById('helpElo960').style.display = 'none';" onmouseover="document.getElementById('helpElo960').style.display = 'block';"><img src="images/point-interrogation.gif" border="0"/></span>
            <div id="helpElo960" style="display: none;" class="help">
            <? echo _("Elo Chess960 ranking is calculated monthly and takes into account the Chess960 games completed during the month past.");?>
            </div>
            </td>
          </tr>
		  <tr>
            <td><?php echo _("About you");?> : </td>
            <td><textarea name="txtProfil" cols="50" rows="5"><? echo($_SESSION['profil']); ?></textarea></td>
          </tr>
          <tr>
            <td><?php echo _("Picture");?> : </td>
            <td>
            	<img id="pictureProfile" src="<?echo(getPicturePath($_SESSION['socialNetwork'], $_SESSION['socialID']));?>" width="50" height="50" style="float: left;margin-right: 30px;"/>
            	<? echo _("Display picture of your profile on")?> :<br/>
            	<input onclick="setCKPicture();" id="rdoCK" name="rdoSocialNetwork" type="radio" value="CK" <? if ($_SESSION['socialNetwork']=="CK") echo("checked");?>> <? echo _("CapaKaspa")?>
            	<input onclick="initNWPicture();" id="rdoFB" name="rdoSocialNetwork" type="radio" value="FB" <? if ($_SESSION['socialNetwork']=="FB") echo("checked");?>> <? echo _("Facebook")?>
            	<input onclick="initNWPicture();" id="rdoGP" name="rdoSocialNetwork" type="radio" value="GP" <? if ($_SESSION['socialNetwork']=="GP") echo("checked");?>> <? echo _("Google+")?>
            	<input onclick="initNWPicture();" id="rdoTW" name="rdoSocialNetwork" type="radio" value="TW" <? if ($_SESSION['socialNetwork']=="TW") echo("checked");?>> <? echo _("Twitter")?>
            	<input type="hidden" id="currentSocialNW" name="currentSocialNW" value="<? echo($_SESSION['socialNetwork']); ?>">
		            
            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>
            	<div id="socialID">
            		<input type="hidden" id="currentSocialID" name="currentSocialID" value="<? echo($_SESSION['socialID']); ?>">
		            <?php echo _("Social network ID");?> : <input onkeyup="setNWPicture();" id="txtSocialID" name="txtSocialID" type="text" size="50" maxlength="100" value="<? echo($_SESSION['socialID']); ?>"> 
		            <span onmouseout="document.getElementById('helpSocial').style.display = 'none';" onmouseover="document.getElementById('helpSocial').style.display = 'block';"><img src="images/point-interrogation.gif" border="0"/></span>
            	</div>
            	<div id="uploadPicture" style="display: none;">
		            <? echo _("Choose your picture");?> 
		            <input type="file" name="ifile">
		            <? if ($_SESSION['socialNetwork']=="CK" && substr($_SESSION['socialID'], 0, 3)=="img") {?>
		            <input type="button" value="<? echo _("Delete");?>" onclick="deleteCKPicture()" class="link">
		            <?  }?>
		            <br><? echo _("Max 500 Kb, JPEG/PNG only (1000x1000 pixels maximum)");?>
            	</div>
            	<div id="helpSocial" style="display: none;" class="help">
		      		1) <? echo _("Facebook");?><br>
					<? echo _("In Facebook, at the bottom of the Infos section of your account:");?><br>
					<? echo _("Facebook http://www.facebook.com/[ ID ]");?><br>
					<? echo _("Or");?><br>
					<? echo _("Facebook http://www.facebook.com/profile.php?id= [ ID ]");?><br>
					2) <? echo _("Twitter");?><br>
					<? echo _("It's the name of your account.");?><br>
					3) <? echo _("Google+");?><br>
					<? echo _("Display your profile page, the URL in the browser is something like that:");?><br>
					<? echo _("https://plus.google.com/u/0/[ ID ]/posts (it's a long number).");?><br>
				</div>
            </td>
          </tr>
          <tr>
            <td colspan="2" align="center">
            	<input class="button" name="Update" type="button" value="<?echo _("Save");?>" onClick="validatePersonalInfo()"> 
            	
            </td>
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
            <td>
					
              	<input name="rdoTheme" type="radio" value="merida" <? if ($_SESSION['pref_theme'] == 'merida') echo("checked");?>>
	              	<img src="pgn4web/merida/28/bk.png"/>
					<img src="pgn4web/merida/28/bq.png"/>
					<img src="pgn4web/merida/28/br.png"/>
					<img src="pgn4web/merida/28/bb.png"/>
					<img src="pgn4web/merida/28/bn.png"/>
					<img src="pgn4web/merida/28/bp.png"/> 
              	<br><input name="rdoTheme" type="radio" value="alpha" <? if ($_SESSION['pref_theme'] == 'alpha') echo("checked");?>>
					<img src="pgn4web/alpha/28/bk.png"/>
					<img src="pgn4web/alpha/28/bq.png"/>
					<img src="pgn4web/alpha/28/br.png"/>
					<img src="pgn4web/alpha/28/bb.png"/>
					<img src="pgn4web/alpha/28/bn.png"/>
					<img src="pgn4web/alpha/28/bp.png"/>
				<br><input name="rdoTheme" type="radio" value="uscf" <? if ($_SESSION['pref_theme'] == 'uscf') echo("checked");?>>
					<img src="pgn4web/uscf/28/bk.png"/>
					<img src="pgn4web/uscf/28/bq.png"/>
					<img src="pgn4web/uscf/28/br.png"/>
					<img src="pgn4web/uscf/28/bb.png"/>
					<img src="pgn4web/uscf/28/bn.png"/>
					<img src="pgn4web/uscf/28/bp.png"/>
              
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
	
      
      <h3><? echo _("Game postponement");?> </h3>
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
			$nbCurrentVacation = mysqli_num_rows($tmpVacations);
			if ($nbCurrentVacation == 0)
				echo _("You don't have vacation in progress.");
			else
			{
				$tmpVacation = mysqli_fetch_array($tmpVacations, MYSQLI_ASSOC);
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
		<form name="Vacation" action="player_update.php" method="post">
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
    mysqli_close($dbh);
?>
