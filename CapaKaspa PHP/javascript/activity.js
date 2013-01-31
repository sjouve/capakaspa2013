// Manage activity
function displayActivity(start, type, playerID)
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
	xmlhttp.open("GET","ajax/ajx_display_activity.php?start="+start+"&type="+type+"&player="+playerID,true);
	xmlhttp.send();
}