<?
	session_start();

	/* load settings */
	if (!isset($_CONFIG))
		require 'include/config.php';
	require 'include/constants.php';
	
	/* connect to database */
	require 'include/connectdb.php';

	$errMsg = "";

	/* check session status */
	require 'include/sessioncheck.php';
	
	require 'include/localization.php';
	
	$titre_page = _("Activity");
	$desc_page = _("Activity");
    require 'include/page_header.php';
?>
    <script src="javascript/comment.js" type="text/javascript"></script>
    <script type="text/javascript">
    function displayActivity(start)
	{
		//document.getElementById("Activities"+entityId).style.display = "block";
	
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
				document.getElementById("activities"+start).innerHTML=xmlhttp.responseText;
			}
		};
		xmlhttp.open("GET","ajax/ajx_display_activity.php?start="+start,true);
		xmlhttp.send();
	}
    </script>
<?
    $attribut_body = "onload='displayActivity(0)'";
	require 'include/page_body.php';
?>
	<div id="content">
    	<div class="contentbody">
	  	<?
		if ($errMsg != "")
			echo("<div class='error'>".$errMsg."</div>");
		?>
			<div id="activities0"><? echo_("Load activities")?></div>
		
    	</div>
    </div>
<?
    require 'include/page_footer.php';
    mysql_close();
?>
