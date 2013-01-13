<?
	session_start();

	/* load settings */
	if (!isset($_CONFIG))
		require 'include/config.php';

	require 'bwc/bwc_common.php';
	
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
		
		
		
    	</div>
    </div>
<?
    require 'include/page_footer.php';
    mysql_close();
?>
