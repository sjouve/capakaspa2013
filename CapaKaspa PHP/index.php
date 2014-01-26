<?
require 'include/mobilecheck.php';
session_start();

/* load settings */
if (!isset($_CONFIG))
	require 'include/config.php';

require 'dac/dac_players.php';
require 'dac/dac_common.php';
require 'dac/dac_games.php';

require 'bwc/bwc_chessutils.php';
require 'bwc/bwc_common.php';
require 'bwc/bwc_players.php';
require 'bwc/bwc_games.php';

// Captcha
include_once  'securimage/securimage.php';
$securimage = new Securimage();

require 'include/connectdb.php';

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

require 'include/localization.php';

/* Traitement des actions */
$err = false;
$ToDo = isset($_POST['ToDo']) ? $_POST['ToDo']:(isset($_GET['ToDo']) ? $_GET['ToDo']:"");

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
		if ($player && $player['activate'] == 1)
		{
			header("Location: game_in_progress.php");
			exit;
		}
		else if (!activatePlayer($_GET['playerID'], $_GET['nick']))
			$err = 'db';
		break;
		
}

if (isset($_SESSION['playerID']) && $_SESSION['playerID'] != -1)
{
	header('Location: game_in_progress.php');
	exit;
}

$titre_page = _("Play chess and share your games");
$desc_page = _("Sign up for CapaKaspa, play chess and share your games.");
require 'include/page_header.php';
    
?>
<script type="text/javascript">
var fb_param = {};
fb_param.pixel_id = '6006417340813';
fb_param.value = '0.00';
(function(){
  var fpw = document.createElement('script');
  fpw.async = true;
  fpw.src = '//connect.facebook.net/en_US/fp.js';
  var ref = document.getElementsByTagName('script')[0];
  ref.parentNode.insertBefore(fpw, ref);
})();
</script>
<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/offsite_event.php?id=6006417340813&amp;value=0" /></noscript>
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
	document.getElementById("firstname_format_error").style.display = "none";
	document.getElementById("lastname_format_error").style.display = "none";
	document.getElementById("confirm_password_error").style.display = "none";
	
	if (isEmpty(Trim(document.userdata.txtFirstName.value))
		|| isEmpty(Trim(document.userdata.txtLastName.value))
		|| isEmpty(document.userdata.txtNick.value)
		|| isEmpty(document.userdata.pwdPassword.value)
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
	
	if (!isAlphaNumeric(document.userdata.txtNick.value)||document.userdata.txtNick.value.length < 2)
	{
		document.getElementById("login_format_error").style.display = "block";
		return;
	}
	
	if (!isAlphaNumeric(document.userdata.pwdPassword.value)||document.userdata.pwdPassword.value.length < 6)
	{
		document.getElementById("password_format_error").style.display = "block";
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
</script>
<?
require 'include/page_body_home.php';
?>
<div id="contenthome">
    <div class="contentbody">
	    
	    <div style="float: left"><img src="images/pawn_128.jpg" style="vertical-align: middle" height="128" width="128" alt="CapaKaspa" title="CapaKaspa">
		</div>
		<div style="float: left;height: 128px;width: 800px;">
			<h1> 
			<?php echo _("Play Chess");?>
			</h1>
			<h1 style="text-align: right;"> 
			<?php echo _("and Share your Games !");?>
			</h1>
		</div>
		
		<div style="width: 290px; height: 192px; padding: 5px; float:left; margin-top: 15px; margin-bottom: 15px; background-image: url('images/home_capakaspa_board.jpg');">
			<span style="position: relative; top: 110px; font-size: 16px; text-shadow: 0.1em 0.1em 0.2em black; color: #FFFFFF;">
				<?php echo _("Play chess games, choose your cadence and type.");?><br>
				<?php echo _("Improve your Elo chess ranking.");?>
			</span>
		</div>
		<div style="width: 290px; height: 192px; padding: 5px; float:left; margin-top: 15px; margin-bottom: 15px; margin-left: 30px; background-image: url('images/home_capakaspa_news.jpg');">
			<span style="position: relative; top: 110px; font-size: 16px; text-shadow: 0.1em 0.1em 0.2em black; color: #FFFFFF;">
				<?php echo _("Share your moves, results and invitations with your followers.");?><br>
				<?php echo _("Follow players, comment news...");?>
			</span>
		</div>
		<div style="width: 290px; height: 192px; padding: 5px; float:left; margin-top: 15px; margin-bottom: 15px; margin-left: 30px; background-image: url('images/home_capakaspa_profil.jpg');">
			<span style="position: relative; top: 110px; font-size: 16px; text-shadow: 0.1em 0.1em 0.2em black; color: #FFFFFF;">
				<?php echo _("View detailed profile of players.");?><br>
				<?php echo _("Discuss with them in private and comment their public news.");?>
			</span>
		</div>
	</div>
</div>
<div id="content">
	<div class="contentbody">
		<h3><?php echo _("New on CapaKaspa ? Sign up");?></h3>
		<?if ($ToDo == 'activer' && !$err) {?>
		<div class="success"><? echo _("Your account is actived.")?></div>
		<p>
		<? echo _("You can now sign in, play chess and share your games.")?>
		</p>
		<?} else if ($ToDo == 'activer' && $err == 'db') {?>
		<div class="error"><? echo _("An error has occured during activation !")?></div>
		<?} else if (!$err && $ToDo == 'NewUser') {?>
		<div class="success"><? echo _("A confirmation message has been sent at this email address")?> : <? echo($_POST['txtEmail']); ?> .</div>
		<?} else  {?>
		
		<div class="error" id="fields_required_error" style="display: none"><?echo _("All fields are required")?></div>
		<div class="error" id="login_format_error" style="display: none"><?echo _("Bad format for user name : at least 2 caracters")?></div>
		<div class="error" id="password_format_error" style="display: none"><?echo _("Bad format for password : at least 6 caracters")?></div>
		<div class="error" id="email_format_error" style="display: none"><?echo _("Bad format for email")?></div>
		<div class="error" id="firstname_format_error" style="display: none"><?echo _("Bad format for first name")?></div>
		<div class="error" id="lastname_format_error" style="display: none"><?echo _("Bad format for last name")?></div>
		<div class="error" id="confirm_password_error" style="display: none"><?echo _("Password confirmation error")?></div>
		<?
			/* Traiter les erreurs */
			if ($err == 'existNick')
				echo("<div class='error'>"._("User name")." (".$_POST['txtNick'].") "._("you have choosen already exists. Try another user name.")."</div>");
			if ($err == 'existEmail')
				echo("<div class='error'>"._("Email")." (".$_POST['txtEmail'].") "._("you have choosen aleady exists. Try another email.")."</div>");
			if ($err == 'emptyNick')
				echo("<div class='error'>"._("Empty user name")."</div>");
			if ($err == 'db')
				echo("<div class='error'>"._("A technical error has occured")."</div>");
			if ($err == 'captcha')
				echo("<div class='error'>"._("Security code error. Try again.")."</div>");
		?>
		<!--google_ad_section_start(weight=ignore)-->
		<form name="userdata" method="post" action="index.php?ToDo=NewUser">
		<table>
			<tr>
				<td width="180"><?php echo _("I am");?> : </td>
	            <td>
					<select name="txtSex" id="txtSex">
						<option value="M" selected><?echo _("Male");?></option>
						<option value="F"><?echo _("Female");?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td ><?php echo _("User name");?> :</td>
				<td width="450"><input name="txtNick" type="text" size="20" maxlength="20" value="<? echo(isset($_POST['txtNick'])?$_POST['txtNick']:""); ?>"></td>
			</tr>
			<tr>
				<td><?php echo _("Choose password");?> :</td>
				<td><input name="pwdPassword" type="password" size="16" maxlength="16"></td>
			</tr>
			<tr>
				<td><?php echo _("Confirm password");?> :</td>
				<td><input name="pwdPassword2" type="password" size="16" maxlength="16"></td>
			</tr>
			<tr>
				<td><?php echo _("First name");?> :</td>
				<td><input name="txtFirstName" type="text" size="20" maxlength="20" value="<? echo(isset($_POST['txtFirstName'])?$_POST['txtFirstName']:""); ?>"></td>
			</tr>
			<tr>
				<td><?php echo _("Last name");?> :</td>
				<td><input name="txtLastName" type="text" size="20" maxlength="20" value="<? echo(isset($_POST['txtLastName'])?$_POST['txtLastName']:""); ?>"></td>
			</tr>
			<tr>
	            <td><?php echo _("Email");?> :</td>
	            <td><input name="txtEmail" placeholder="<?php echo _("A valid email is required for confirmation");?>" type="text" size="50" maxlength="50" value="<? echo(isset($_POST['txtEmail'])?$_POST['txtEmail']:""); ?>"></td>
	        </tr>
			<tr>
	            <td><?php echo _("Country");?> :</td>
	            <td><select name="txtCountryCode" id="txtCountryCode">
		            <?
		            echo "\t",'<option value="">', _("Select your country") ,'</option>',"\n";
		            $tmpCountries = listCountriesByLang(getLang());
		            while($tmpCountry = mysql_fetch_array($tmpCountries, MYSQL_ASSOC))
		            {
		            	$selected = "";
		            	$countryCode = isset($_POST['txtCountryCode'])?$_POST['txtCountryCode']:"";
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
	            <td><?php echo _("Birth date");?> :</td>
	            <td><select name="txtAnneeNaissance" id="txtAnneeNaissance">
	            	<?php
	            	echo "\t",'<option value="">', _("Select a year") ,'</option>',"\n";
	            	// Parcours du tableau
					$annee = isset($_POST['txtAnneeNaissance'])?$_POST['txtAnneeNaissance']:"";
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
	        </tr>
	        </table>
			<table>
			<tr>
				<td >
					<?php echo _("Type the security code");?>
					<input type="text" name="captcha_code" size="6" maxlength="6" />
					<a href="#" onclick="document.getElementById('captcha').src = 'securimage/securimage_show.php?' + Math.random(); return false"><img src="images/icone_rafraichir.png" border="0" alt="<?php echo _("Try other security code");?>" title="<?php echo _("Try other security code");?>"/></a> 
					
				</td>
				<td valign="middle">
					<img id="captcha" src="securimage/securimage_show.php" alt="<?php echo _("Captcha Image");?>" title="<?php echo _("Captcha Image");?>"/>
				</td>
			</tr>
			<tr>
				<td align="center" colspan="2">
					<input name="btnCreate" type="button" value="<?php echo _("Sign up for free");?>" onClick="validateForm()" class="button">
				</td>
			</tr>
		</table>
	
			<!-- <input name="ToDo" value="NewUser" type="hidden"> -->
		</form>
		<!--google_ad_section_end-->
		<?}?>
	</div>
</div>
<div id="rightbarlarge">
	<div class="contentbody">
		<h3><?php echo _("Sign in");?></h3>
		<form method="post" action="game_in_progress.php">
			<table>
				<tr>
					<td width="50%"><?php echo _("User name");?> :</td>	        	
		        	<td width="50%"><input name="txtNick" type="text" size="20" maxlength="20"></td>
		        </tr>
		        <tr>
		        	<td><?php echo _("Password");?> :</td>
		        	<td><input name="pwdPassword" type="password" size="20" maxlength="16"></td>
		        </tr>
	        </table>
	        <?if (isset($_GET['err']) && $_GET['err'] == "login") {?>
	        <div class='error'><? echo _("Invalid user name or password !");?></div>
	        <?}?>
	        <input name="chkAutoConn" type="checkbox"/> <? echo _("Remember me");?><br><br>
	        <center><input name="login" value="<? echo _("Sign in");?>" type="submit" class="button"> <img src="images/puce.gif"/> <a href="password.php"><? echo _("Forgot password ?");?></a></center>
	        <input name="ToDo" value="Login" type="hidden">
	     </form>
	     <br>
   		<h3><?php echo _("Play chess on CapaKaspa");?></h3>
   		<span class="newplayer" style="font-size: 12px;"><? echo(getNbActiveGameForAll()); ?></span> <?php echo _("chess games in progress");?><br>
   		<span class="newplayer" style="font-size: 12px;"><? echo(getNbActivePlayers()+getNbPassivePlayers()); ?></span> <?php echo _("players are waiting to play chess games");?><br><br>
   		<img src="images/puce.gif"/> <a href="app_jchess.php"><? echo _("Play chess vs JChess");?></a><br>
   		<img src="images/puce.gif"/> <a href="app_flashchess.php"><? echo _("Play chess vs FlashChess");?></a><br>
   		<img src="images/puce.gif"/> <a href="app_sparkchess.php"><? echo _("Play chess vs SparkChess");?></a><br>
   		<img src="images/puce.gif"/> <a href="app_puzzle.php"><? echo _("Chess puzzle of the day");?></a><br>
   		<img src="images/puce.gif"/> <a href="http://blog.capakaspa.info" target="_blanck"><? echo _("The Chess Blog (french)");?></a><br>
	</div>
</div>
<?
require 'include/page_footer.php';
mysql_close();
?>
