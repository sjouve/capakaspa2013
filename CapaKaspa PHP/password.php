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

require 'include/connectdb.php';

require 'include/localization.php';

/* Traitement des actions */
$err=1;
$ToDo = isset($_POST['ToDo']) ? $_POST['ToDo']:"";

switch($ToDo)
{
	case 'Send':
		$err = sendPassword($_POST['txtEmail']);
		break;
}	

$titre_page = _("Forgotten password - CapaKaspa");
$desc_page = _("Play chess and share your games. Retrieve your password");
require 'include/page_header.php';
require 'include/page_body.php';
?>
<div id="contentlarge">
    <div class="contentbody">
    <?/* Traiter les erreurs */
		if ($err == 0)
			echo("<div class='error'>"._("No account available with this email")."</div>");
		if ($err == -1)
			echo("<div class='error'>"._("A technical problem prevented the sending of the message")."</div>");	
	?>
	<? if ($err == 1 && $ToDo == 'Valider') {?>
		<div class='success'><?php echo _("A message has been sent to the specified email address.");?></div>
	<? } else {?>
	<h3><?php echo _("Forgotten password");?></h3>
    	<p><?php echo _("Already have an account to access the play area but <b>you forgot your password</b>.");?></p>
    	<p><?php echo _("Enter the email address that you assigned to this account. A message will be sent to this address. It will contain the information for sign in.");?></p>
		<form name="userdata" method="post" action="jouer-echecs-differe-passe-oublie.php">
			<table align="center">
				<tr>
		            <td> Email : </td>
		            <td><input name="txtEmail" type="text" size="50" maxlength="50" value="<?echo(isset($_POST['txtEmail'])?$_POST['txtEmail']:"");?>">
		            </td>
		        </tr>
			</table>
			<input type="hidden" name="ToDo" value="Send">
			<center><input name="Send" value="<?php echo _("Send");?>" type="submit" class="button"></center>
		</form>
      <?}?>
		   
    </div>
</div>
<?
require 'include/page_footer.php';
mysql_close();
?>
