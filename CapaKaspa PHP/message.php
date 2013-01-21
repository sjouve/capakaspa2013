<?
	session_start();

	/* load settings */
	if (!isset($_CONFIG))
		require 'include/config.php';
	
	require 'dac/dac_players.php';
	require 'bwc/bwc_common.php';
	require 'bwc/bwc_chessutils.php';
	require 'bwc/bwc_players.php';
	
	/* connect to database */
	require 'include/connectdb.php';

	$errMsg = "";

	/* check session status */
	require 'include/sessioncheck.php';
	
	$titre_page = _("Message");
	$desc_page = _("Message");
    require 'include/page_header.php';
?>
    <script type="text/javascript">

		
		
	</script>
<?
    require 'include/page_body_no_menu.php';
?>
	<div id="contentlarge">
    	<div class="contentbody">
	  	<?
		if ($errMsg != "")
			echo("<div class='error'>".$errMsg."</div>");
		?>
		
		
		
    	</div>
    </div>
<?
    require 'include/page_footer.php';
    mysql_close();
?>
