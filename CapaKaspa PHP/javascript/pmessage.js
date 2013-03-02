// Manage private message
function displayPrivateMessage(playerID, withPlayerID, withEmail)
{
	document.getElementById("messages").innerHTML = "<img src='images/ajaxloader.gif'/>";
	document.getElementById("messageForm").style.display = "block";
	if (document.getElementById("toPlayerID").value != "")
		document.getElementById("contact"+document.getElementById("toPlayerID").value).style.backgroundColor = "#FFFFFF";
	document.getElementById("toPlayerID").value = withPlayerID;
	document.getElementById("toEmail").value = withEmail;
	if (document.getElementById("contact"+withPlayerID))
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
			displayPrivateMessage(fromPlayerID, toPlayerID, toEmail);
		}
	};
	xmlhttp.open("GET","ajax/ajx_insert_pmessage.php?fromID="+fromPlayerID+"&toID="+toPlayerID+"&toEmail="+toEmail+"&mes="+message,true);
	xmlhttp.send();
}

function insertPrivateMessagePopup(fromPlayerID, toPlayerID, toEmail)
{
	message = encodeURI(document.getElementById("privateMessage").value);
	document.getElementById("popupMessageForm").style.display = "none";
	document.getElementById("popupMessageProgress").style.display = "block";
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
			document.getElementById("popupMessageProgress").style.display = "none";
			document.getElementById("popupMessageSuccess").style.display = "block";
			setTimeout(closePopupMessage, 1800);		
		}
	};
	xmlhttp.open("GET","ajax/ajx_insert_pmessage.php?fromID="+fromPlayerID+"&toID="+toPlayerID+"&toEmail="+toEmail+"&mes="+message,true);
	xmlhttp.send();
}

function closePopupMessage()
{
	popup('popUpDiv');
	document.getElementById("popupMessageForm").style.display = "block";
	document.getElementById("popupMessageSuccess").style.display = "none";
	document.getElementById("privateMessage").value = "";
}