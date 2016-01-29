// Manage activity
function displayActivity(start, type, playerID, activityID)
{
	if (start == 0)
		document.getElementById("activities"+start).innerHTML="<img src='images/ajaxloader.gif'/>";
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
	xmlhttp.open("GET","ajax/ajx_display_activity.php?start="+start+"&type="+type+"&player="+playerID+"&actvt="+activityID,true);
	xmlhttp.send();
}

function deleteActivity(activityID)
{
	document.getElementById("activity"+activityID).style.backgroundColor = "#EEEEEE";
	if (confirm(document.getElementById("#confirm_delete_activity").innerHTML))
	{
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
				document.getElementById("activity"+activityID).style.display = "none";
			}
			else
			{
				document.getElementById("activity"+activityID).style.backgroundColor = "#FFFFFF";
			}
		};
		xmlhttp.open("GET","ajax/ajx_delete_activity.php?id="+activityID,true);
		xmlhttp.send();
	}
	else
	{
		document.getElementById("activity"+activityID).style.backgroundColor = "#FFFFFF";
	}
}