// Manage follow
function deleteFav(favID, playerID)
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
			document.getElementById("follow"+playerID).innerHTML=xmlhttp.responseText;
		}
	};
	xmlhttp.open("GET","ajax/ajx_delete_follow.php?id="+favID+"&player="+playerID,true);
	xmlhttp.send();
}

function insertFav(playerID)
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
			document.getElementById("follow"+playerID).innerHTML=xmlhttp.responseText;
		}
	};
	xmlhttp.open("GET","ajax/ajx_insert_follow.php?player="+playerID,true);
	xmlhttp.send();
}