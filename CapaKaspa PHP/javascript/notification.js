// Manage notification
function displayNotification(start, playerID)
{
	if (start == 0)
		document.getElementById("notifications"+start).innerHTML="<img src='images/ajaxloader.gif'/>";
	document.getElementById("notifications"+start).style.display = "block";

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
			document.getElementById("notifications"+start).innerHTML=xmlhttp.responseText;
		}
	};
	xmlhttp.open("GET","ajax/ajx_display_notification.php?start="+start+"&player="+playerID, true);
	xmlhttp.send();
}
