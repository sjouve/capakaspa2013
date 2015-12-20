// Manage game
function displayGames(start, player, critStt, critCol, critRes, critTyp, critRnk, critElo)
{
	if (start == 0)
		document.getElementById("games"+start).innerHTML="<img src='images/ajaxloader.gif'/>";
	document.getElementById("games"+start).style.display = "block";

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
			document.getElementById("games"+start).innerHTML=xmlhttp.responseText;
		}
	};
	xmlhttp.open("GET","ajax/ajx_display_game.php?start="+start+"&player="+player+"&cs="+critStt+"&cc="+critCol+"&cr="+critRes+"&ct="+critTyp+"&ck="+critRnk+"&ce="+critElo,true);
	xmlhttp.send();
}
