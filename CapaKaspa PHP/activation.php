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

require 'include/localization.php';

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

$titre_page = _("Activate your account");
$desc_page = _("Activate your account and play chess on CapaKaspa");
require 'include/page_header.php';
require 'include/page_body.php';
?>
<div id="contentlarge">
	<div class="contentbody">
    <?/* Traiter les erreurs */
		if ($err == 0)
			echo("<div class='error'>"._("This account doesn't exist")."</div>");
		if ($err == -1)
			echo("<div class='error'>"._("A technical problem prevented the sending of the message")."</div>");
		if ($err == -2)
			echo("<div class='error'>"._("The email address is already in use")."</div>");
		if ($err == -3)
			echo("<div class='error'>"._("The email address is not valid")."</div>");			
	?>
	<? if ($err == 1 && $ToDo == 'Valider') {?>
		<b><? echo _("An activation message has been sent to the email address.");?></b>
	<? } else {?>
	<b><? echo _("Your account is not activated.");?></b>
	<p><? echo _("You are surely one of the following case");?> :
		<ul>
		<li><? echo _("You received the message containing the link to activate your account but you have not yet used");?>,</li>
		<li><? echo _("you do not receive a message containing the activation link");?>,</li>
		<li><? echo _("you have disabled your account");?>.</li>
		</ul>
     </p>
	 <p><? echo _("If you prefer, you can use the form below to update your email address associated with your account and start the activation process");?>.</p>
	 <br>
	<form name="userdata" method="post" action="activation.php">
			<table align="center">
				<tr>
		            <td><? echo _("User name");?> : </td>
		            <td><input name="txtNick" type="text" size="20" maxlength="20" value="<?echo(isset($_POST['txtNick'])?$_POST['txtNick']:"");?>">
		            </td>
		        </tr>
		        <tr>
					<td><? echo _("Password");?> : </td>
		            <td><input name="txtPassword" type="password" size="16" maxlength="16" value="<?echo(isset($_POST['txtPassword'])?$_POST['txtPassword']:"");?>">
		            </td>
		        </tr>
		        <tr>
					<td><? echo _("Email");?> : </td>
		            <td><input name="txtEmail" type="text" size="50" maxlength="50" value="<?echo(isset($_POST['txtEmail'])?$_POST['txtEmail']:"");?>">
		            </td>
		        </tr>
			</table>
	
			<center><input name="buttonSend" value="<? echo _("Send");?>" type="submit" class="button"></center>
			<input type="hidden" name="ToDo" value="Valider">
		</form> 
		<br>
		<? } ?>
		<br>
		<br>
      	<br>
	</div>
</div>
<?
require 'include/page_footer.php';
mysqli_close($dbh);
?>