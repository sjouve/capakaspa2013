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
	
$titre_page = "New game";
$desc_page = "";
require 'include/page_header.php';
?>
<script type="text/javascript">
	
</script>
<?
require 'include/page_body.php';
?>
<div id="contentlarge">
	<div class="contentbody">
  
		<form action="index.php" method="post">
			<h3><? echo _("Start new game")?> <a href="manuel-utilisateur-jouer-echecs-capakaspa.pdf#page=10" target="_blank"><img src="images/point-interrogation.gif" border="0"/></a></h3>
			
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
		            		<option value="2"><?echo _("2 days");?></option>
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
						<?echo _("User name")?> :					
					</td>
					<td>
						<input name="txtNick" type="text" size="20" maxlength="20">
					</td>
				</tr>
			</table>
			<input type="submit" value="<?echo _("Start game")?>" class="button">
			<input type="hidden" name="ToDo" value="InvitePlayerByNick">
		</form>
	</div>
</div>
<?
require 'include/page_footer.php';
mysql_close();
?>
