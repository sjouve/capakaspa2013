// this is the main function that interacts with the user everytime they click on a square

	/* called whenever a square is clicked on */
	var is1stClick = true;
	
	function squareClickedFirst(row, col, isEmpty, curColor)
	{
		if (getPieceColor(board[row][col]) == curColor)
		{
			document.gamedata.fromRow.value = row;
			document.gamedata.fromCol.value = col;

			highlight(row, col);

			is1stClick = false;
		}
		else
			alert(document.getElementById('#alert_color_play_id').innerHTML + curColorFR + ".");

	}
	
	function squareClickedSecond(row, col, isEmpty, curColor)
	{
		unhighlight(document.gamedata.fromRow.value, document.gamedata.fromCol.value);
		is1stClick = true;

		if ((document.gamedata.fromRow.value == row)
			&& (document.gamedata.fromCol.value == col))
		{
			document.gamedata.fromRow.value = "";
			document.gamedata.fromCol.value = "";
		}
		else
		{
			/* if, on a player's second click, they click on one of their own piece */
			/* act as if he was clicking for the first time (ie: select it) */
			if (board[row][col] != 0 )
				if (getPieceColor(board[row][col]) == curColor)
				{
					squareClickedFirst(row, col, isEmpty, curColor);
					return null;
				}

			var fromRow = document.gamedata.fromRow.value;
			var fromCol = document.gamedata.fromCol.value;
			document.gamedata.toRow.value = row;
			document.gamedata.toCol.value = col;

			if (isValidMove())
			{
				if (DEBUG)
					alert("Move is valid, updating game...");

				var ennemyColor = "white";
				if (curColor == "white")
					ennemyColor = "black";

				/* update board with move (client-side) */
				board[row][col] = board[fromRow][fromCol];
				board[fromRow][fromCol] = 0;
				eval("document.images['pos" + row + "-" + col+"'].src = document.images['pos" + fromRow + "-" + fromCol+"'].src");
				eval("document.images['pos" + fromRow + "-" + fromCol+"'].src = 'pgn4web/" + CURRENTTHEME + "/35/clear.png'");
				
                /* if this is a castling move the rook must also be moved */
				if ((getPieceName(board[row][col]) == 'king') && (Math.abs(col - fromCol) == 2))
				{	// The king only moves two squares when castling
					var rookCol = 0;
					var rookToCol = 3;
					if (col - fromCol == 2)
					{	// Kingside castling (would be == -2 if queenside)
						rookCol = 7;
						rookToCol = 5;
					}
					board[row][rookToCol] = board[row][rookCol];
					board[row][rookCol] = 0;
					eval("document.images['pos" + row + "-" + rookToCol+"'].src = document.images['pos" + row + "-" + rookCol +"'].src");
					eval("document.images['pos" + row + "-" + rookCol+"'].src = 'pgn4web/" + CURRENTTHEME + "/35/clear.png'");
					
				}
				
				// En passant
				if ((getPieceName(board[row][col]) == 'pawn') && (col != fromCol))
				{
					//vider col fromRaw
					eval("document.images['pos" + fromRow + "-" + col+"'].src = 'pgn4web/" + CURRENTTHEME + "/35/clear.png'");
					
				}
				
                if (isInCheck(ennemyColor))
				{
					document.gamedata.isInCheck.value = "true";
					document.gamedata.isCheckMate.value = isCheckMate(ennemyColor);
				}
				else
					document.gamedata.isInCheck.value = "false";
                
                // Display promoting selection
                if ((getPieceName(board[row][col]) == 'pawn') && (row == 0 || row == 7))
				{
                	document.getElementById('promoting').style.display = 'block';
				}
                else
                {
                	document.getElementById('btnPlay').style.visibility = 'visible';
                	document.getElementById('btnUndo').style.visibility = 'visible';
                	document.getElementById('shareMove').style.display = 'inline';              	
                }
				//document.gamedata.submit();
			}
			else
			{
				document.gamedata.toRow.value = "";
				document.gamedata.toCol.value = "";
				
				alert(document.getElementById('#alert_invalid_move_id').innerHTML + " :\n" + errMsg);
			}
		}
	}
	
	function squareClicked(row, col, isEmpty)
	{
		if (DEBUG)
			alert('squareClicked -> row = ' + row + ', col = ' + col + ', isEmpty = ' + isEmpty);

		var curColor = document.getElementById('#alert_color_black_id').innerHTML;
		if ((numMoves == -1) || (numMoves % 2 == 1))
			curColor = document.getElementById('#alert_color_white_id').innerHTML;;
		
		if (is1stClick && !isEmpty)
			squareClickedFirst(row, col, isEmpty, curColor);
		else
			squareClickedSecond(row, col, isEmpty, curColor);
	}

