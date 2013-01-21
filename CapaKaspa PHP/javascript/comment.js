// Manage comment on activity and game
function displayComment(entityType, entityId)
{
	document.getElementById("comment"+entityId).style.display = "block";

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
			document.getElementById("comment"+entityId).innerHTML=xmlhttp.responseText;
		}
	};
	xmlhttp.open("GET","ajax/ajx_display_comment.php?type="+entityType+"&id="+entityId,true);
	xmlhttp.send();
}

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
}

function insertComment(entityType, entityId)
{
	message = document.getElementById("commenttext"+entityId).value;
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
	xmlhttp.open("GET","ajax/ajx_insert_comment.php?type="+entityType+"&id="+entityId+"&mes="+message,true);
	xmlhttp.send();
}