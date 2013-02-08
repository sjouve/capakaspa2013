<?
session_start();
	
/* load settings */
if (!isset($_CONFIG))
	require 'include/config.php';

require 'dac/dac_players.php';
require 'bwc/bwc_common.php';
require 'bwc/bwc_chessutils.php';
require 'bwc/bwc_players.php';

/* connect to the database */
require 'include/connectdb.php';
	
/* check session status */
require 'include/sessioncheck.php';
	
$titre_page = _("New game");
$desc_page = _("Invite someone to play a new chess game");
require 'include/page_header.php';

/* invite from players search */
$username = isset($_POST['opponent'])? $_POST['opponent'] :"";

?>
<script src="javascript/menu.js" type="text/javascript"></script>
<script src="javascript/formValidation.js" type="text/javascript"></script>
<script type="text/javascript">
function showHint(str, type)
{
	var xmlhttp;
	if (str.length < 3)
	{
		document.getElementById("txtHint").innerHTML="";
		return;
	}
	if (window.XMLHttpRequest)
	{// code for IE7+, Firefox, Chrome, Opera, Safari
	  	xmlhttp=new XMLHttpRequest();
	}
	else
	{// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
		xmlhttp.onreadystatechange=function()
	{
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
	    	document.getElementById("opponent").innerHTML=xmlhttp.responseText;
	    	document.getElementById("ajaxprogressname").style.display = 'none';
	    }
	}
	document.getElementById("ajaxprogressname").style.display = 'inline';
	xmlhttp.open("GET","ajax/ajx_list_player_nick.php?str="+str+"&type="+type, true);
	xmlhttp.send();
}

function sendInvitation(email)
{
	var xmlhttp;
	document.getElementById("email_format_error").style.display = 'none';
	document.getElementById("account_exist_error").style.display = 'none';
	document.getElementById("send_invitation_success").style.display = 'none';
	
	if (window.XMLHttpRequest)
	{// code for IE7+, Firefox, Chrome, Opera, Safari
	  	xmlhttp=new XMLHttpRequest();
	}
	else
	{// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
		xmlhttp.onreadystatechange=function()
	{
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
			if (xmlhttp.responseText.length > 0)
			{
				document.getElementById("ajaxprogressemail").style.display = 'none';
	    		document.getElementById("account_exist_error").style.display = 'inline';
	    		document.getElementById("txtHint").value = xmlhttp.responseText;
	    		showHint(xmlhttp.responseText, 1);    		
			}
			else
			{
				document.getElementById("ajaxprogressemail").style.display = 'none';
				document.getElementById("send_invitation_success").style.display = 'inline';
			}
	    }
	}
	if (isEmailAddress(email))
	{
		document.getElementById("ajaxprogressemail").style.display = 'inline';
		xmlhttp.open("GET","ajax/ajx_send_invitation.php?email="+email, true);
		xmlhttp.send();
	}
	else
	{
		document.getElementById("email_format_error").style.display = 'inline';
	}
}

function startGame()
{
	if (document.getElementById('opponent').value.length > 0)
		document.startGame.submit();
}
</script>
<?
$attribut_body = "onload=\"highlightMenu(1); showHint('".$username."', 1)\"";
require 'include/page_body.php';
?>
<div id="contentlarge">
	<div class="contentbody">
  
		<h3><? echo _("Start new game")?> <a href="manuel-utilisateur-jouer-echecs-capakaspa.pdf#page=10" target="_blank"><img src="images/point-interrogation.gif" border="0"/></a></h3>
		<form name="startGame" action="index.php" method="post">
			<table width="100%">
				<tr>
					<td width="20%">
						<?echo _("Game type")?> : 
					</td>
					<td width="80%">
						<input type="radio" name="type" value="0" checked> <?echo _("Classic game")?>
					</td>
				</tr>
				<tr>
					<td>
						&nbsp;
					</td>
					<td>
						<input type="radio" name="type" value="1"> <?echo _("Beginner game with King, Pawns and")?>
						<input type="checkbox" name="flagBishop" value="1"> <?echo _("Bishops")?>
						<input type="checkbox" name="flagKnight" value="1"> <?echo _("Knigths")?>
						<input type="checkbox" name="flagRook" value="1"> <?echo _("Rooks")?>
						<input type="checkbox" name="flagQueen" value="1"> <?echo _("Queen")?>
					</td>
				</tr>
				<tr>
					<td>
						<?echo _("Time per move")?> : 
					</td>
					<td>
						<select name="timeMove" id="timeMove">
		            		<option value="2" selected><?echo _("2 days");?></option>
		            		<option value="3"><?echo _("3 days");?></option>
		            		<option value="4"><?echo _("4 days");?></option>
		            		<option value="5"><?echo _("5 days");?></option>
		            		<option value="7"><?echo _("7 days");?></option>
		            		<option value="10"><?echo _("10 days");?></option>
		            		<option value="14"><?echo _("14 days");?></option>
		            	</select>
					</td>
				</tr>
				<tr>
					<td >
						<?echo _("Play as (color)")?> : 
					</td>
					<td>
						<input type="radio" name="color" value="white" checked> <?echo _("White")?>
						<input type="radio" name="color" value="black"> <?echo _("Black")?>
						<input type="radio" name="color" value="random"> <?echo _("Random")?>
					</td>
				</tr>
				<tr>
					<td>
						<?echo _("Select a player")?> :					
					</td>
					<td>
						<select id="opponent" name="opponent" style="width:380px;">
							<option value="" selected><?echo _("Type a part of user name, first name or last name in the box")?></option>
						</select>
						<input id="txtHint" name="txtHint" type="text" size="15" maxlength="20" value="<? echo($username);?>" onkeyup="javascript:showHint(this.value, 0);">
						<div id="ajaxprogressname" style="display: none;"><img src="images/ajaxprogress.gif"></div>				
					</td>
				</tr>
			</table>
			<input type="button" value="<?echo _("Start game")?>" class="button" onClick="javascript:startGame()">
			<input type="hidden" name="ToDo" value="InvitePlayer">
		</form>
		<br>
		
		<h3><? echo _("Advanced search")?></h3>
		<p><? echo _("Find a player with advanced search and invite him to play a new game.");?></p>
		<input type="button" class="link" value="Search players" onclick="location.href='player_search.php'">
		<br><br>
		
		<h3><? echo _("Invite friends to join you")?></h3>
		<p><? echo _("Invite by email a friend not on CapaKaspa to join you. If a player exist with the email you will invite to start a new game directly.");?></p>
		
			<table width="100%">
				<tr>
					<td width="20%">
						<?echo _("Email address")?> : 
					</td>
					<td width="80%">
						<input type="text" id="txtEmail" name="txtEmail" value="" size="30" maxlength="50">
						<div id="ajaxprogressemail" style="display: none;"><img src="images/ajaxprogress.gif"></div>
						<div class="error" id="email_format_error" style="display: none"><?echo _("It's not a valid email format")?></div>
						<div class="error" id="account_exist_error" style="display: none"><?echo _("An account exist with this email. Start a new game.")?></div>
						<div class="success" id="send_invitation_success" style="display: none"><?echo _("Invitation was sent successfully")?></div>
					</td>
				</tr>
			</table>
			<input type="button" value="<?echo _("Send invitation")?>" class="button" onclick="javascript:sendInvitation(document.getElementById('txtEmail').value);">
			<input type="hidden" name="ToDo" value="InviteFriend">
		
	</div>
</div>
<?
require 'include/page_footer.php';
mysql_close();
?>
