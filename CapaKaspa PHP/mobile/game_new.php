<?
session_start();
	
/* load settings */
if (!isset($_CONFIG))
	require '../include/config.php';

require '../dac/dac_players.php';
require '../dac/dac_activity.php';
require '../dac/dac_games.php';
require '../bwc/bwc_common.php';
require '../bwc/bwc_chessutils.php';
require '../bwc/bwc_players.php';
require '../bwc/bwc_games.php';


/* connect to the database */
require '../include/connectdb.php';
	
/* check session status */
require '../include/sessioncheck.php';

require '../include/localization.php';
	
$titre_page = _("New game");
$desc_page = _("Invite someone to play a new chess game");
require 'include/page_header.php';

/* invite from players search */
$username = isset($_POST['opponent'])? $_POST['opponent'] :"";

?>
<script src="http://jouerauxechecs.capakaspa.info/javascript/formValidation.js" type="text/javascript"></script>
<script type="text/javascript">
function chess960()
{
  var aKnight = "N";
  var aBishop = "B";
  var aRook   = "R";
  var aQueen  = "Q";
  var aKing   = "K";

  var sLetter = "01234567";
  var nPlace, sPlace;

  var sPiece   = new Array("X","X","X","X","X","X","X","X");
  var sPlace = "";

  var aPlace = new Array("0246", "1357");
  for (var i=0; i<2; i++)
  {
    nPlace = Math.floor(Math.random() * 4);
    sPlace = aPlace[i].substring(nPlace, nPlace + 1);
    sPiece[sPlace] = aBishop;
    nPlace = sLetter.indexOf(sPlace);
    sLetter = sLetter.substring(0,nPlace) + sLetter.substring(nPlace+1, sLetter.length);
  } // i

  var aPiece = new Array(aQueen, aKnight, aKnight);
  for (var i=0; i<3; i++)
  {
    nPlace = Math.floor(Math.random() * (6 - i));
    sPlace = sLetter.substring(nPlace, nPlace + 1);
    sPiece[sPlace] = aPiece[i];
    sLetter = sLetter.substring(0,nPlace) + sLetter.substring(nPlace+1, sLetter.length);
  } // i

  var aPiece = new Array(aRook, aKing, aRook);
  for (var i=0; i<3; i++)
  {
    sPiece[sLetter.substring(i, i+1)] = aPiece[i];
  } // i

  var sp="";
  for (var i=0; i<8; i++)
  {
    sp=sp.concat(sPiece[i]);
  } // i

  return sp;

} //Chess960

function getChess960()
{
	document.startGameForm.chess960.value = chess960();	
}

function showHint(str, type)
{
	var xmlhttp;
	if (str.length < 2)
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
function startGame()
{
	document.startGameForm.submit();
}
</script>
<?
$attribut_body = "onload=\"showHint('".$username."', 1)\"";
$activeMenu = 0;
require 'include/page_body.php';
?>
		
		<div class="blockform">
		<h3><? echo _("Start new game")?></h3>
		<form name="startGameForm" action="game_in_progress.php" method="post">
			<table width="100%">
				<tr>
					<td width="40%">
						<?echo _("Game type")?> : 
					</td>
					<td width="60%">
						<input type="radio" name="type" value="0" checked> <?echo _("Classic game")?> <img title="<?echo _("Ranked game")?>" src="images/classements.gif"/>
					</td>
				</tr>
				<tr>
					<td>
						&nbsp;
					</td>
					<td>
						<input type="radio" name="type" value="2" onclick="javascript:getChess960();"> <?echo _("Fischer Chess Random (Chess960)")?> <img title="<?echo _("Ranked game")?>" src="images/classements.gif"/>
						<input type="hidden" name="chess960" value="">
					</td>
				</tr>
				<tr>
					<td>
						&nbsp;
					</td>
					<td>
						<input type="radio" name="type" value="1"> <?echo _("Beginner game with King, Pawns and")?><br>
						<input type="checkbox" name="flagBishop" value="1"> <?echo _("Bishops")?>
						<input type="checkbox" name="flagKnight" value="1"> <?echo _("Knigths")?><br>
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
		            		<option value="2"><?echo _("2 days");?></option>
		            		<option value="3"><?echo _("3 days");?></option>
		            		<option value="4"><?echo _("4 days");?></option>
		            		<option value="5"><?echo _("5 days");?></option>
		            		<option value="7" selected><?echo _("7 days");?></option>
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
						<input type="radio" name="color" value="white" checked> <?echo _("White")?><br>
						<input type="radio" name="color" value="black"> <?echo _("Black")?><br>
						<input type="radio" name="color" value="random"> <?echo _("Random")?>
					</td>
				</tr>
				<tr>
					<td>
						<?echo _("Select a player")?> :					
					</td>
					<td>
						<select id="opponent" name="opponent" style="width:200px;">
							<option value="0" selected><?echo _("Type a part of user name, first name or last name in the box")?></option>
						</select><br>
						<div id="ajaxprogressname" style="display: none;"><img src="images/ajaxprogress.gif"></div>			
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<?echo _("Don't select a player to invite all players")?>				
					</td>
				</tr>
			</table>
			<div id="button_right"><input type="button" value="<?echo _("Start game")?>" class="button" onclick="javascript:startGame();"></div>
			<input type="hidden" name="ToDo" value="InvitePlayer">
		</form>
		</div>
<?
require 'include/page_footer.php';
mysqli_close($dbh);
?>
