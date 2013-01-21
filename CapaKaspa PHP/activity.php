<?
session_start();

/* load settings */
if (!isset($_CONFIG))
	require 'include/config.php';
	
require 'include/constants.php';
require 'dac/dac_players.php';
require 'bwc/bwc_common.php';
require 'bwc/bwc_chessutils.php';
require 'bwc/bwc_players.php';
	
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
	document.getElementById("activities"+start).style.display = "block";

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

function getheight() {
	var myWidth = 0,
		myHeight = 0;
	if (typeof(window.innerWidth) == 'number') {
		//Non-IE
		myWidth = window.innerWidth;
		myHeight = window.innerHeight;
	} else if (document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)) {
		//IE 6+ in 'standards compliant mode'
		myWidth = document.documentElement.clientWidth;
		myHeight = document.documentElement.clientHeight;
	} else if (document.body && (document.body.clientWidth || document.body.clientHeight)) {
		//IE 4 compatible
		myWidth = document.body.clientWidth;
		myHeight = document.body.clientHeight;
	}
		var scrolledtonum = window.pageYOffset + myHeight + 2;
		var heightofbody = document.body.offsetHeight;
		if (scrolledtonum >= heightofbody && document.getElementById("startPage")) {
			displayActivity(document.getElementById("startPage").value);
	}
}

window.onscroll = getheight;
    	
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
			
			<div id="activities0" style="display: none;"><img src='images/ajaxloader.gif'/></div>
		
    	</div>
    </div>
<?
require 'include/page_footer.php';
mysql_close();
?>
