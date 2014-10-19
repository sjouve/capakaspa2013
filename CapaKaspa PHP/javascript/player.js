// Manage player
function displayPlayers(start, player, critFav, critStat, critEloS, critEloE, critCtry, critName)
{
	if (start == 0)
		document.getElementById("players"+start).innerHTML="<img src='images/ajaxloader.gif'/>";
	document.getElementById("players"+start).style.display = "block";

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
			document.getElementById("players"+start).innerHTML=xmlhttp.responseText;
		}
	};
	xmlhttp.open("GET","ajax/ajx_display_player.php?start="+start+"&player="+player+"&cf="+critFav+"&cs="+critStat+"&ces="+critEloS+"&cee="+critEloE+"&cc="+critCtry+"&cn="+critName,true);
	xmlhttp.send();
}

function displayPlayersRanking(start, player, critCtry, critType, critOrder, rank, prevElo)
{
	if (start == 0)
		document.getElementById("players"+start).innerHTML="<img src='images/ajaxloader.gif'/>";
	document.getElementById("players"+start).style.display = "block";

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
			document.getElementById("players"+start).innerHTML=xmlhttp.responseText;
		}
	};
	xmlhttp.open("GET","ajax/ajx_display_ranking.php?start="+start+"&player="+player+"&cc="+critCtry+"&tp="+critType+"&od="+critOrder+"&rk="+rank+"&ce="+prevElo,true);
	xmlhttp.send();
}