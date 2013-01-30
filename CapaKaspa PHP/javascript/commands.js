// these functions interact with the server
	
function play()
{		
	document.gamedata.submit();
}

function undo()
{
	
	document.gamedata.fromRow.value = "";
	document.gamedata.fromCol.value = "";
	document.gamedata.toRow.value = "";
	document.gamedata.toCol.value = "";
	
	document.gamedata.submit();
	
}

function draw()
{
	var vok=false;
	vok = confirm(document.getElementById('#confirm_draw_proposal_id').innerHTML);
	if (vok)
	{
		document.gamedata.requestDraw.value = "yes";
		if (DEBUG)
			alert("gamedata.requestDraw = " + document.gamedata.requestDraw.value);

		document.gamedata.submit();
	}
}

function resigngame()
{
	var vok=false;
	vok = confirm(document.getElementById('#confirm_resign_game_id').innerHTML);
	if (vok)
	{
		document.gamedata.resign.value = "yes";
		if (DEBUG)
			alert("gamedata.resign = " + document.gamedata.resign.value);

		document.gamedata.submit();
	}
}

function logout()
{
	document.gamedata.action = "tableaubord.php";
	document.gamedata.submit();
}

function promotepawn()
{
	var blackPawnFound = false;
	var whitePawnFound = false;
	var i = -1;
	while (!blackPawnFound && !whitePawnFound && i < 8)
	{
		i++;
		
		/* check for black pawn being promoted */
		if (board[0][i] == (BLACK | PAWN))
			blackPawnFound = true;
		
		/* check for white pawn being promoted */
		if (board[7][i] == (WHITE | PAWN))
			whitePawnFound = true;
	}

	/* to which piece is the pawn being promoted to? */
	var promotedTo = 0;
	for (var j = 0; j <= 3; j++)
	{
		if (document.gamedata.promotion[j].checked)
			promotedTo = parseInt(document.gamedata.promotion[j].value);
	}
	
	if (promotedTo == QUEEN) pieceLetter = 'q';
	if (promotedTo == ROOK) pieceLetter = 'r';
	if (promotedTo == KNIGHT) pieceLetter = 'k';
	if (promotedTo == BISHOP) pieceLetter = 'b';
	
	/* change pawn to promoted piece */
	var ennemyColor = "black";
	if (blackPawnFound)
	{
		ennemyColor = "white";
		board[0][i] = (BLACK | promotedTo);
		eval("document.images['pos" + 0 + "-" + i +"'].src = 'pgn4web/" + CURRENTTHEME + "/35/b" + pieceLetter + ".png'");
		
		if (DEBUG)
			alert("Promoting to: (black) " + board[0][i]);

	}
	else if (whitePawnFound)
	{
		board[7][i] = (WHITE | promotedTo);
		eval("document.images['pos" + 7 + "-" + i +"'].src = 'pgn4web/" + CURRENTTHEME + "/35/w" + pieceLetter + ".png'");
		
		if (DEBUG)
			alert("Promoting to: (white) " + board[7][i]);
	}
	else
		alert("WARNING!: cannot find pawn being promoted!");
		
	/* verify check and checkmate status */
	if (isInCheck(ennemyColor))
	{
		if (DEBUG)
			alert("Promotion results in check!");

		document.gamedata.isInCheck.value = "true";
		document.gamedata.isCheckMate.value = isCheckMate(ennemyColor);
	}
	else
		document.gamedata.isInCheck.value = "false";

	eval("document.images['pos" + row + "-" + rookCol+"'].src = 'pgn4web/" + CURRENTTHEME + "/35/clear.png'");
	
	document.getElementById('btnPlay').style.visibility = 'visible';
	document.getElementById('btnUndo').style.visibility = 'visible';
	document.getElementById('shareMove').style.display = 'inline';
	
	/* update board and database */
	//document.gamedata.submit();
}
