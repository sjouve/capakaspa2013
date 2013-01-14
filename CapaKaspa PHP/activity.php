<?
	session_start();

	/* load settings */
	if (!isset($_CONFIG))
		require 'include/config.php';
	require 'include/constants.php';
	require 'bwc/bwc_common.php';
	require 'bwc/bwc_games.php';
	
	/* connect to database */
	require 'include/connectdb.php';

	$errMsg = "";

	/* check session status */
	require 'include/sessioncheck.php';
	
	$titre_page = _("Activity");
	$desc_page = _("Activity");
    require 'include/page_header.php';
?>
    <script type="text/javascript">
    function displayComment(divId)
	{
    	document.getElementById(divId).style.display = "block";
	}
		
	</script>
<?
    require 'include/page_body.php';
?>
	<div id="contentlarge">
    	<div class="contentbody">
	  	<?
		if ($errMsg != "")
			echo("<div class='error'>".$errMsg."</div>");
		?>
		
		<div class="activity">
			<div class="leftbar">
				<img src="<?echo(getPicturePath("FB", "sebastien.jouve.fr"));?>" width="40" height="40" border="0"/>
			</div>
			<div class="details">
				<div class="title">
					Sébastien Jouve a joué le coup 1.e4 contre Eric Jouve
				</div>
				<div class="content">
					<? drawboardGame(2798, 1, 408, "tcfdrfctppp0pppp00000000000p00000000000000000000PPPPPPPPTCFDRFCT");?>
				</div>
				<div class="footer">
					! Bon - <a href="javascript:displayComment('comment1');">Commenter</a> - Il y a 30 minutes
				</div>
				<div class="comment" id="comment1">
					Test commentaires
				</div>
			</div>
		</div>
		
		<div class="activity">
			<div class="leftbar">
				<img src="<?echo(getPicturePath("", ""));?>" width="40" height="40" border="0"/>
			</div>
			<div class="details">
				<div class="title">
					Sébastien Jouve a joué le coup 1.e4 contre Eric Jouve
				</div>
				<div class="content">
					<? drawboardGame(2798, 1, 408, "tcfdrfctppp0pppp00000000000p00000000000000000000PPPPPPPPTCFDRFCT");?>
				</div>
				<div class="footer">
					! Bon - <a href="javascript:displayComment('comment2');">Commenter</a> - Il y a 30 minutes
				</div>
				<div class="comment" id="comment2">
					Test commentaires
				</div>
			</div>
			
		</div>
		
    	</div>
    </div>
<?
    require 'include/page_footer.php';
    mysql_close();
?>
