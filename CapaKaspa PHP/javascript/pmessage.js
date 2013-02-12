// Manage private message
function displayPrivateMessage(playerID, withPlayerID, withEmail)
{
	document.getElementById("messages").innerHTML = "<img src='images/ajaxloader.gif'/>";
	document.getElementById("messageForm").style.display = "block";
	if (document.getElementById("toPlayerID").value != "")
		document.getElementById("contact"+document.getElementById("toPlayerID").value).style.backgroundColor = "#FFFFFF";
	document.getElementById("toPlayerID").value = withPlayerID;
	document.getElementById("toEmail").value = withEmail;
	document.getElementById("contact"+withPlayerID).style.backgroundColor = "#EEEEEE";
	
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
			document.getElementById("messages").innerHTML=xmlhttp.responseText;
		}
	};
	xmlhttp.open("GET","ajax/ajx_display_pmessage.php?pID="+playerID+"&wID="+withPlayerID, true);
	xmlhttp.send();
}
/*
function deleteComment(entityType, entityId, commentId)
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
			displayComment(entityType, entityId);
		}
	};
	xmlhttp.open("GET","ajax/ajx_delete_comment.php?id="+commentId,true);
	xmlhttp.send();
}*/

function insertPrivateMessage(fromPlayerID, toPlayerID, toEmail)
{
	message = encodeURI(document.getElementById("privateMessage").value);
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
			popup('popUpDiv');
		}
	};
	xmlhttp.open("GET","ajax/ajx_insert_pmessage.php?fromID="+fromPlayerID+"&toID="+toPlayerID+"&mes="+message,true);
	xmlhttp.send();
}