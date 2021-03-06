﻿// this is the main function that interacts with the user everytime they click on a square

	/* called whenever a square is clicked on */
	var is1stClick = true;
	
	function squareClickedFirst(row, col, isEmpty, curColor)
	{
		if (document.gamedata.toRow.value != "" && document.gamedata.toRow.value != "")
			return null;
		
		if (getPieceColor(board[row][col]) == curColor)
		{
			document.gamedata.fromRow.value = row;
			document.gamedata.fromCol.value = col;

			highlight(row, col);

			is1stClick = false;
		}
		else
		{
			if (curColor == "white")
				colorAlert = document.getElementById('#alert_color_white_id').innerHTML;
			else
				colorAlert = document.getElementById('#alert_color_black_id').innerHTML;
			alert(document.getElementById('#alert_color_play_id').innerHTML + " " + colorAlert + ".");
		}
	}
	
	function squareClickedSecond(row, col, isEmpty, curColor)
	{
		if (document.gamedata.toRow.value != "" && document.gamedata.toRow.value != "")
			return null;
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
			
			var fromRow = parseInt(document.gamedata.fromRow.value);
			var fromCol = parseInt(document.gamedata.fromCol.value);
			
			/* if, on a player's second click, they click on one of their own piece */
			/* act as if he was clicking for the first time (ie: select it) */
			if (board[row][col] != 0 )
				if (getPieceColor(board[row][col]) == curColor )
				{
					if (boardGameType != 2 || (boardGameType == 2 && (getPieceName(board[row][col]) != 'rook' || getPieceName(board[fromRow][fromCol]) != 'king')))
					{
						squareClickedFirst(row, col, isEmpty, curColor);
						return null;
					}
				}

			document.gamedata.toRow.value = row;
			document.gamedata.toCol.value = col;

			if (isValidMove(fromRow, fromCol, row, col))
			{	
				// Note the move in the history of the game
				// Note that this entry won't be used unless numMoves is incremented
				// Important: Some checks below need this entry, while it would cause others to fail
				// Therefore numMoves must be locally incremented and then reset again when it's needed
				var idx = numMoves + 1;
				chessHistory[idx] = new Array();
				chessHistory[idx][CURPIECE] = thePiece;
				chessHistory[idx][CURCOLOR] = curColor;
				chessHistory[idx][FROMROW] = fromRow;
				chessHistory[idx][FROMCOL] = fromCol;
				chessHistory[idx][TOROW] = row;
				chessHistory[idx][TOCOL] = col;
				
				var ennemyColor = "white";
				if (curColor == "white")
					ennemyColor = "black";
				
				var isCapture = (board[row][col] != 0);
				var thePiece = getPieceName(board[fromRow][fromCol]);
				var fromPiece = getPieceName(board[row][col]);
				
				// Update board with move (client-side)
				if (boardGameType == 2 && thePiece == 'king' && fromPiece == 'rook' && getPieceColor(board[row][col]) == curColor)
				{	// Castling Chess960
					var rookToCol = 3;
					var kingToCol = 2;
					// Pour identifier le grand roque en Chess960
					chessHistory[idx][TOCOL] = -1;
					if (col > fromCol)
					{
						rookToCol = 5;
						kingToCol = 6;
						// Pour identifier petit roque en Chess960
						chessHistory[idx][TOCOL] = 8;
					}
					tmpKing = board[row][fromCol];
					tmpRook = board[row][col];
					board[row][fromCol] = 0;
					board[row][col] = 0;
					board[row][kingToCol] = tmpKing; 
					board[row][rookToCol] = tmpRook;
					tmpImageKing = eval("document.images['pos" + fromRow + "-" + fromCol+"'].src");
					tmpImageRook = eval("document.images['pos" + row + "-" + col+"'].src");
					eval("document.images['pos" + fromRow + "-" + fromCol+"'].src = 'pgn4web/" + CURRENTTHEME + "/35/clear.png'");
					eval("document.images['pos" + row + "-" + col+"'].src = 'pgn4web/" + CURRENTTHEME + "/35/clear.png'");
					eval("document.images['pos" + row + "-" + kingToCol+"'].src = tmpImageKing");
					eval("document.images['pos" + row + "-" + rookToCol+"'].src = tmpImageRook");
					
				}
				else
				{
					board[row][col] = board[fromRow][fromCol];
					board[fromRow][fromCol] = 0;
					eval("document.images['pos" + row + "-" + col+"'].src = document.images['pos" + fromRow + "-" + fromCol+"'].src");
					eval("document.images['pos" + fromRow + "-" + fromCol+"'].src = 'pgn4web/" + CURRENTTHEME + "/35/clear.png'");
				}
				
                /* if this is a castling move the rook must also be moved */
				if (boardGameType != 2 && (thePiece == 'king') && (Math.abs(col - fromCol) == 2))
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
				else if((thePiece == 'pawn') && (col != fromCol) && (!isCapture))
				{	/* if this is an en passant capture, the captured pawn must be removed */
					board[fromRow][col] = 0;
					
					eval("document.images['pos" + fromRow + "-" + col+"'].src = 'pgn4web/" + CURRENTTHEME + "/35/clear.png'");
				}
				
				/* En passant
				// TODO A voir avec bloc du dessus
				if ((getPieceName(board[row][col]) == 'pawn') && (col != fromCol))
				{
					//vider col fromRaw
					eval("document.images['pos" + fromRow + "-" + col+"'].src = 'pgn4web/" + CURRENTTHEME + "/35/clear.png'");
					
				}*/
				
				///////
				if (isInCheck(ennemyColor))
				{
					numMoves++;
					document.gamedata.isInCheck.value = "true";
					if(thePiece == 'pawn' && Math.abs(row - fromRow) == 2)	// Pawn double advance
						var epCol = col;	// The column of the en passant square
					else
						var epCol = -1;
					document.gamedata.isCheckMate.value = isCheckMate(ennemyColor, epCol);
					numMoves--;	
				}
				else
				{	// Not in check
					document.gamedata.isInCheck.value = "false";
					numMoves++;		// Use the additional entry in chessHistory (the current move)
					if(ennemyColor == 'white')
						var myColor = WHITE;
					else
						var myColor = BLACK;

					if(countMoves(myColor) == 0)
					{
						alert(document.getElementById('#alert_draw_stalemate_id').innerHTML);
						document.getElementById('drawResult').value= 'true';
						document.getElementById('drawCase').value= 'stalemate';
					}

					numMoves--;		// Reset chessHistory to it's initial size
				}

				// Is the game drawn due to insufficient material to checkmate?
				var count = 0;
				var canCheckmate = false;

				for (var i = 0; i < 8; i++)
				{
					for (var j = 0; j < 8; j++)
						if(board[i][j] != 0 && (board[i][j] & COLOR_MASK) != KING)
						{
							if((board[i][j] & COLOR_MASK) != KNIGHT && (board[i][j] & COLOR_MASK) != BISHOP)
								canCheckmate = true;
							else
								count++;
						}
				}

				if(count < 2 && !canCheckmate)
				{
					alert(document.getElementById('#alert_draw_material_id').innerHTML);
					document.getElementById('drawResult').value= 'true';
					document.getElementById('drawCase').value= 'material';
				}

				// Is the game drawn because this is the third time that the exact same position arises?
				numMoves++;		// Use the additional entry in chessHistory (the current move)
				// TODO Faire fonctionner historyToFEN pour Chess960
				if (boardGameType != 2) {
					var FEN = historyToFEN();	// The chessHistory in FEN format
					if(isThirdTimePosDraw(FEN))
					{
						alert(document.getElementById('#alert_draw_3_times_id').innerHTML);
						document.getElementById('drawResult').value= 'true';
						document.getElementById('drawCase').value= '3times';
					}
	
					// Draw because of no capture of pawn move for the last 50 moves?
					if(!isCapture && (thePiece != 'pawn') && isFiftyMoveDraw(FEN[FEN.length-1]))
					{
						alert(document.getElementById('#alert_draw_50_moves_id').innerHTML);
						document.getElementById('drawResult').value= 'true';
						document.getElementById('drawCase').value= '50moves';
					}
				}
				numMoves--;		// Reset chessHistory to it's initial size
				
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
                	document.getElementById('requestDraw').style.display = 'inline';
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
		var curColor = "black";
		if ((numMoves == -1) || (numMoves % 2 == 1))
			curColor = "white";
		
		if (is1stClick && !isEmpty)
			squareClickedFirst(row, col, isEmpty, curColor);
		else
			squareClickedSecond(row, col, isEmpty, curColor);
	}

