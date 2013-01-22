// Manage like on activity
function displayLike(entityType, entityId)
{
	document.getElementById("like"+entityId).style.display = "block";

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
			document.getElementById("like"+entityId).innerHTML=xmlhttp.responseText;
		}
	};
	xmlhttp.open("GET","ajax/ajx_display_like.php?type="+entityType+"&id="+entityId,true);
	xmlhttp.send();
}

function deleteLike(entityType, entityId, likeId)
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
			document.getElementById("like"+entityId).innerHTML=xmlhttp.responseText;
			displayComment(entityType, entityId);
		}
	};
	xmlhttp.open("GET","ajax/ajx_delete_like.php?id="+likeId+"&type="+entityType+"&entityid="+entityId,true);
	xmlhttp.send();
}

function insertLike(entityType, entityId)
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
			document.getElementById("like"+entityId).innerHTML=xmlhttp.responseText;
			displayComment(entityType, entityId);
		}
	};
	xmlhttp.open("GET","ajax/ajx_insert_like.php?type="+entityType+"&id="+entityId,true);
	xmlhttp.send();
}