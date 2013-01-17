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
    <script src="javascript/comment.js" type="text/javascript"></script>
<?
    require 'include/page_body.php';
?>
	<div id="content">
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
					<a href=""><span class='name'>Sébastien Jouve</span></a> a joué le coup 1.e4 contre <span class='name'>Eric Jouve</span>
				</div>
				<div class="content">
					<? drawboardGame(2798, 1, 408, "tcfdrfctppp0pppp00000000000p00000000000000000000PPPPPPPPTCFDRFCT");?>
				</div>
				<div class="footer">
					! Bon - <a href="javascript:displayComment('<?echo(ACTIVITY)?>', 1);">Commenter</a> - Il y a 30 minutes
				</div>
				<div class="comment" id="comment1">
					<? echo _("Load comments...");?>
				</div>
			</div>
		</div>
		
		<div class="activity">
			<div class="leftbar">
				<img src="<?echo(getPicturePath("", ""));?>" width="40" height="40" border="0"/>
			</div>
			<div class="details">
				<div class="title">
					<span class='name'>Sébastien Jouve</span> a joué le coup 1.e4 contre <span class='name'>Eric Jouve</span>
				</div>
				<div class="content">
					<? drawboardGame(2798, 1, 408, "tcfdrfctppp0pppp00000000000p00000000000000000000PPPPPPPPTCFDRFCT");?>
				</div>
				<div class="footer">
					! Bon - <a href="javascript:displayComment('<?echo(ACTIVITY)?>', 2);">Commenter</a> - Il y a 30 minutes
				</div>
				<div class="comment" id="comment2">
					Comments
				</div>
			</div>
			
		</div>
		
    	</div>
    </div>
<?
    require 'include/page_footer.php';
    mysql_close();
?>
