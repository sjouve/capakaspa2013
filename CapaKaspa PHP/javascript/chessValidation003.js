﻿// these functions are used to test the validity of moves
var knightMove = [[-1, -2], [+1, -2], [-2, -1], [-2, +1], [-1, +2], [+1, +2], [+2, -1], [+2, +1]];
var diagonalMove = [[-1, -1], [-1, +1], [+1, +1], [+1, -1]];
var horzVertMove = [[-1, 0], [0, +1], [+1, 0], [0, -1]];

// The array 'direction' is a combination of diagonalMove and horzVertMove 
// It could also be created using 'var direction = horzVertMove.concat(diagonalMove)'
// although the order of the elements would be different
var direction = [[-1, -1], [-1, 0], [-1, +1], [0, +1], [+1, +1], [+1, 0], [+1, -1], [0, -1]];
var pawnMove = [[+1, -1], [+1, 0], [+2, 0], [+1, +1]];

// object definition (used by isSafe)
function GamePiece()
{
	this.piece = 0;
	this.dist = 0;
}

	/* isSafe tests whether the square at testRow, testCol is safe */
	/* for a piece of color testColor to travel to */
	function isSafe(testRow, testCol, testColor)
	{
		/* NOTE: if a piece occupates the square itself,
			that piece does not participate in determining the safety of the square */

		/* IMPORTANT: note that if we're checking to see if the square is safe for a pawn
			we're moving, we need to verify the safety for En-passant */

		/* OPTIMIZE: cache results (if client-side game only, invalidate cache after each move) */

		/* AI NOTE: just because a square isn't entirely safe doesn't mean we don't want to
			move there; for instance, we may be protected by another piece */

		/* DESIGN NOTE: this function is mostly designed with CHECK checking in mind and
			may not be suitable for other purposes */

		var ennemyColor = 0;
		
		if (testColor == 'white')
			ennemyColor = 128; /* 1000 0000 */

		/* check for knights first */
		for (var i = 0; i < 8; i++) {	// Check all eight possible knight moves
			var fromRow = testRow + knightMove[i][0];
			var fromCol = testCol + knightMove[i][1];
			if (isInBoard(fromRow, fromCol))
				if (board[fromRow][fromCol] == (KNIGHT | ennemyColor))	// Enemy knight found
						return false;
		}

		/* tactic: start at test pos and check all 8 directions for an attacking piece */
		/* directions:
			0 1 2
			7 * 3
			6 5 4
		*/
		var pieceFound = new Array();
		for (i = 0; i < 8; i++)
			pieceFound[i] = new GamePiece();

		for (i = 1; i < 8; i++)
		{
			if (((testRow - i) >= 0) && ((testCol - i) >= 0))
				if ((pieceFound[0].piece == 0) && (board[testRow - i][testCol - i] != 0))
				{
					pieceFound[0].piece = board[testRow - i][testCol - i];
					pieceFound[0].dist = i;
				}

			if ((testRow - i) >= 0)
				if ((pieceFound[1].piece == 0) && (board[testRow - i][testCol] != 0))
				{
					pieceFound[1].piece = board[testRow - i][testCol];
					pieceFound[1].dist = i;
				}

			if (((testRow - i) >= 0) && ((testCol + i) < 8))
				if ((pieceFound[2].piece == 0) && (board[testRow - i][testCol + i] != 0))
				{
					pieceFound[2].piece = board[testRow - i][testCol + i];
					pieceFound[2].dist = i;
				}

			if ((testCol + i) < 8)
				if ((pieceFound[3].piece == 0) && (board[testRow][testCol + i] != 0))
				{
					pieceFound[3].piece = board[testRow][testCol + i];
					pieceFound[3].dist = i;
				}

			if (((testRow + i) < 8) && ((testCol + i) < 8))
				if ((pieceFound[4].piece == 0) && (board[testRow + i][testCol + i] != 0))
				{
					pieceFound[4].piece = board[testRow + i][testCol + i];
					pieceFound[4].dist = i;
				}

			if ((testRow + i) < 8)
				if ((pieceFound[5].piece == 0) && (board[testRow + i][testCol] != 0))
				{
					pieceFound[5].piece = board[testRow + i][testCol];
					pieceFound[5].dist = i;
				}

			if (((testRow + i) < 8) && ((testCol - i) >= 0))
				if ((pieceFound[6].piece == 0) && (board[testRow + i][testCol - i] != 0))
				{
					pieceFound[6].piece = board[testRow + i][testCol - i];
					pieceFound[6].dist = i;
				}

			if ((testCol - i) >= 0)
				if ((pieceFound[7].piece == 0) && (board[testRow][testCol - i] != 0))
				{
					pieceFound[7].piece = board[testRow][testCol - i];
					pieceFound[7].dist = i;
				}
		}

		/* check pieces found for possible threats */
		for (var i = 0; i < 8; i++)
			if ((pieceFound[i].piece != 0) && ((pieceFound[i].piece & BLACK) == ennemyColor))
				switch(i)
				{
					/* diagonally: queen, bishop, pawn, king */
					case 0:
					case 2:
					case 4:
					case 6:
						if (((pieceFound[i].piece & COLOR_MASK) == QUEEN)
								|| ((pieceFound[i].piece & COLOR_MASK) == BISHOP))
						{
							return false;
						}

						if ((pieceFound[i].dist == 1)
								&& ((pieceFound[i].piece & COLOR_MASK) == PAWN))
						{
							if ((ennemyColor == WHITE) && ((i == 0) || (i == 2)))
								return false;
							else if ((ennemyColor == BLACK) && ((i == 4) || (i == 6)))
								return false;
						}

						if ((pieceFound[i].dist == 1)
								&& ((pieceFound[i].piece & COLOR_MASK) == KING))
						{
							/* Are the kings next to each other? */
							if ((board[testRow][testCol] & COLOR_MASK) == KING)
								return false;

							/* save current board destination */
							var tmpPiece = board[testRow][testCol];

							/* update board with move (client-side) */
							board[testRow][testCol] = pieceFound[i].piece;

							var kingRow = 0;
							var kingCol = 0;
							switch(i)
							{
								case 0: kingRow = testRow - 1; kingCol = testCol - 1;
									break;
								case 1: kingRow = testRow - 1; kingCol = testCol;
									break;
								case 2: kingRow = testRow - 1; kingCol = testCol + 1;
									break;
								case 3: kingRow = testRow;     kingCol = testCol + 1;
									break;
								case 4: kingRow = testRow + 1; kingCol = testCol + 1;
									break;
								case 5: kingRow = testRow + 1; kingCol = testCol;
									break;
								case 6: kingRow = testRow + 1; kingCol = testCol - 1;
									break;
								case 7: kingRow = testRow;     kingCol = testCol - 1;
									break;
							}

							board[kingRow][kingCol] = 0;

							/* if king needs to move into check to capture piece, isSafe() is true */
							var tmpIsSafe = isInCheck(getOtherColor(testColor));

							/* restore board to previous state */
							board[kingRow][kingCol] = pieceFound[i].piece;
							board[testRow][testCol] = tmpPiece;

							/* if king CAN eat target without moving into check, return false */
							/* otherwise, continue checking other piecesFound */
							if (!tmpIsSafe)
								return false;
						}
						break;

					/* horizontally/vertically: queen, rook, king */
					case 1:
					case 3:
					case 5:
					case 7:
						if (((pieceFound[i].piece & COLOR_MASK) == QUEEN)
								|| ((pieceFound[i].piece & COLOR_MASK) == ROOK))
						{
							return false;
						}

						if ((pieceFound[i].dist == 1)
								&& ((pieceFound[i].piece & COLOR_MASK) == KING))
						{
							/* Are the kings next to each other? */
							if ((board[testRow][testCol] & COLOR_MASK) == KING)
								return false;

							/* save current board destination */
							var tmpPiece = board[testRow][testCol];

							/* update board with move (client-side) */
							board[testRow][testCol] = pieceFound[i].piece;

							var kingRow = 0;
							var KingCol = 0;
							switch(i)
							{
								case 0: kingRow = testRow - 1; kingCol = testCol - 1;
									break;
								case 1: kingRow = testRow - 1; kingCol = testCol;
									break;
								case 2: kingRow = testRow - 1; kingCol = testCol + 1;
									break;
								case 3: kingRow = testRow;     kingCol = testCol + 1;
									break;
								case 4: kingRow = testRow + 1; kingCol = testCol + 1;
									break;
								case 5: kingRow = testRow + 1; kingCol = testCol;
									break;
								case 6: kingRow = testRow + 1; kingCol = testCol - 1;
									break;
								case 7: kingRow = testRow;     kingCol = testCol - 1;
									break;
							}

							board[kingRow][kingCol] = 0;

							/* if king needs to move into check to capture piece, isSafe() is true */
							var tmpIsSafe = isInCheck(getOtherColor(testColor));

							/* restore board to previous state */
							board[kingRow][kingCol] = pieceFound[i].piece;
							board[testRow][testCol] = tmpPiece;

							/* if king CAN eat target without moving into check, return false */
							/* otherwise, continue checking other piecesFound */
							if (!tmpIsSafe)
								return false;
						}
						break;
				}
		return true;
	}

	function isValidMoveKing(fromRow, fromCol, toRow, toCol, tmpColor)
	{
		/* The king cannot move to a square occupied by a friendly piece. Not for Chess960 */
		if (boardGameType != 2 || (boardGameType == 2 && (board[toRow][toCol] & COLOR_MASK) != ROOK))
			if ((board[toRow][toCol] != 0) && (getPieceColor(board[toRow][toCol]) == tmpColor))
			{
				return false;
			}
		
		/* Temporarily move king to destination to see if in check */
		var tmpPiece = board[toRow][toCol];
		board[toRow][toCol] = board[fromRow][fromCol];
		board[fromRow][fromCol] = 0;

		/* The king does not move to a square that is attacked by an enemy piece */
		if(tmpColor == 'white')
			var atkColor = BLACK;
		else
			var atkColor = WHITE;
		
		if (isInCheck(tmpColor))
		{
			/* return king to original position */
			board[fromRow][fromCol] = board[toRow][toCol];
			board[toRow][toCol] = tmpPiece;

			//errMsg = "Cannot move into check.";
			errMsg = document.getElementById('#alert_err_move_check_id').innerHTML;
			
			return false;
		}

		/* return king to original position */
		board[fromRow][fromCol] = board[toRow][toCol];
		board[toRow][toCol] = tmpPiece;

		/* NORMAL MOVE: */
		if (((boardGameType != 2) || ((boardGameType == 2) && (tmpColor == 'white') && ((board[toRow][toCol]) != (WHITE | ROOK)))
				|| ((boardGameType == 2) && (tmpColor == 'black') && ((board[toRow][toCol]) != (BLACK | ROOK))))
			&& ((Math.abs(toRow - fromRow) <= 1) && (Math.abs(toCol - fromCol) <= 1)))
		{
			return true;
		}
		/* CASTLING: Chess960*/
		else if (boardGameType == 2 && (board[toRow][toCol] & COLOR_MASK) == ROOK)
		{
			// Chess960 Cas du roque à voir (pour le roque on déplace le Roi sur la Tour du côté du roque voulu)
			/*
			 * La position finale du roque est exactement la même que dans les échecs orthodoxes, peu importe la position initiale.
			 * Après le roque du côté a, qui se note « O-O-O », le roi est en c et la tour en d alors qu'après le roque du côté h, 
			 * qui se note « O-O », le roi se retrouve en g et la tour en f. Les cases qui se retrouvent entre l'emplacement initial 
			 * de la tour et sa case finale de même que celles qui se trouvent entre la case initiale du roi et celle de son arrivée 
			 * doivent être vacantes (sauf si elles sont occupées par la pièce participant au roque) et doivent répondre aux mêmes 
			 * exigences qu'aux échecs orthodoxes. Dans certaines positions, une des deux pièces ne bouge pas.
			 */
			
			/* 
			 * The king that makes the castling move has not yet moved in the game.
			 */
			for (i = 0; i <= numMoves; i++)
			{
				/* if king has already moved */
				if ((chessHistory[i][FROMROW] == fromRow) && (chessHistory[i][CURPIECE] == "king"))
				{
					//errMsg = "Can only castle if king has not moved yet.";
					errMsg = document.getElementById('#alert_err_castle_king_id').innerHTML;
					
					return false;
				}
				/* if rook has already moved */
				else if ((chessHistory[i][FROMROW] == fromRow) && (chessHistory[i][FROMCOL] == rookCol))
				{
					//errMsg = "Can only castle if rook has not moved yet.";
					errMsg = document.getElementById('#alert_err_castle_rook_id').innerHTML;
					return false;
				}
			}
			
			/*
			* Squares for Rook and King are empty
			*/
			if (fromCol > toCol)
			{
				if (board[fromRow][2] != 0 && (board[fromRow][2] & COLOR_MASK) != KING && ((board[fromRow][2] & COLOR_MASK) != ROOK || fromCol < 2)) 
				{
					//errMsg = "Can only castle if there are no pieces between the rook and the king";
					errMsg = document.getElementById('#alert_err_castle_pieces_id').innerHTML;
					
					return false;
				}
				if (board[fromRow][3] != 0 && ((board[fromRow][3] & COLOR_MASK) != ROOK || fromCol < 3) && (board[fromRow][3] & COLOR_MASK) != KING) 
				{
					//errMsg = "Can only castle if there are no pieces between the rook and the king";
					errMsg = document.getElementById('#alert_err_castle_pieces_id').innerHTML;
					
					return false;
				}
			}
			else
			{
				if (board[fromRow][5] != 0 && ((board[fromRow][5] & COLOR_MASK) != ROOK || fromCol > 5) && (board[fromRow][5] & COLOR_MASK) != KING) 
				{
					//errMsg = "Can only castle if there are no pieces between the rook and the king";
					errMsg = document.getElementById('#alert_err_castle_pieces_id').innerHTML;
					
					return false;
				}
				if (board[fromRow][6] != 0 && (board[fromRow][6] & COLOR_MASK) != KING && ((board[fromRow][6] & COLOR_MASK) != ROOK || fromCol > 6)) 
				{
					//errMsg = "Can only castle if there are no pieces between the rook and the king";
					errMsg = document.getElementById('#alert_err_castle_pieces_id').innerHTML;
					
					return false;
				}
			}
				
			/*
			* All squares between the rook and king before the castling move are empty.
			*/
			if (fromCol > toCol) tmpStep = -1;
			else tmpStep = 1;
			
			for (i = fromCol + tmpStep; i != toCol; i += tmpStep)
				if (board[fromRow][i] != 0 && (board[toRow][i] & COLOR_MASK) != ROOK)
				{
					//errMsg = "Can only castle if there are no pieces between the rook and the king";
					errMsg = document.getElementById('#alert_err_castle_pieces_id').innerHTML;
					
					return false;
				}
			
			/*
		    * The king is not in check.
		    * The king does not move over a square that is attacked by an enemy piece during the castling move
			*/
			if (isSafe(fromRow, fromCol, tmpColor)
					&& isSafe(fromRow, fromCol + tmpStep, tmpColor))
			{
				return true;
			}
			else
			{
				//errMsg = "When castling, the king cannot move over a square that is attacked by an ennemy piece";
				errMsg = document.getElementById('#alert_err_castle_attack_id').innerHTML;
				
				return false;
			}
			
			return true;
		}
		/* CASTLING: No Chess960*/
		else if ((fromRow == toRow) && (fromCol == 4) && (Math.abs(toCol - fromCol) == 2) && boardGameType != 2)
		{
			/*
			The following conditions must be met:
			    * The King and rook must occupy the same rank (or row).
			    * The king that makes the castling move has not yet moved in the game.
			*/
			// No castling for beginner game with no rook
			if ((boardGameType == 1) && ((board[toRow][7] & COLOR_MASK) != ROOK ) && ((board[toRow][0] & COLOR_MASK) != ROOK))
			{
				errMsg = document.getElementById('#alert_err_move_king_id').innerHTML;
				return false;
			}
			
			var rookCol = 0;
			if (toCol - fromCol == 2)
				rookCol = 7;

			/* ToDo: chessHistory check can probably be cut in half by only checking every other move (ie: current color's moves) */
			for (i = 0; i <= numMoves; i++)
			{
				/* if king has already moved */
				if ((chessHistory[i][FROMROW] == fromRow) && (chessHistory[i][CURPIECE] == "king"))
				{
					//errMsg = "Can only castle if king has not moved yet.";
					errMsg = document.getElementById('#alert_err_castle_king_id').innerHTML;
					
					return false;
				}
				/* if rook has already moved */
				else if ((chessHistory[i][FROMROW] == fromRow) && (chessHistory[i][FROMCOL] == rookCol))
				{
					//errMsg = "Can only castle if rook has not moved yet.";
					errMsg = document.getElementById('#alert_err_castle_rook_id').innerHTML;
					return false;
				}
			}

			/*
			    * All squares between the rook and king before the castling move are empty.
			*/
			tmpStep = (toCol - fromCol) / 2;
			for (i = 4 + tmpStep; i != rookCol; i += tmpStep)
				if (board[fromRow][i] != 0)
				{
					//errMsg = "Can only castle if there are no pieces between the rook and the king";
					errMsg = document.getElementById('#alert_err_castle_pieces_id').innerHTML;
					
					return false;
				}

			/*
			    * The king is not in check.
			    * The king does not move over a square that is attacked by an enemy piece during the castling move
			*/

			/* NOTE: the king's destination has already been checked, so */
			/* all that's left is it's initial position and it's final one */
			if (isSafe(fromRow, fromCol, tmpColor)
					&& isSafe(fromRow, fromCol + tmpStep, tmpColor))
			{
				return true;
			}
			else
			{
				//errMsg = "When castling, the king cannot move over a square that is attacked by an ennemy piece";
				errMsg = document.getElementById('#alert_err_castle_attack_id').innerHTML;
				
				return false;
			}
		}
		/* INVALID MOVE */
		else
		{
			//errMsg = "Kings cannot move like that.";
			errMsg = document.getElementById('#alert_err_move_king_id').innerHTML;
			
			return false;
		}

	}

	/* checks whether a pawn is making a valid move */
	function isValidMovePawn(fromRow, fromCol, toRow, toCol, tmpDir)
	{
		if (((toRow - fromRow)/Math.abs(toRow - fromRow)) != tmpDir)
		{
			//errMsg = "Pawns cannot move backwards, only forward.";
			errMsg = document.getElementById('#alert_err_move_pawn_id').innerHTML;
			
			return false;
		}

		/* standard move */
		if ((tmpDir * (toRow - fromRow) == 1) && (toCol == fromCol) && (board[toRow][toCol] == 0))
			return true;
		/* first move double jump - white */
		if ((tmpDir == 1) && (fromRow == 1) && (toRow == 3) && (toCol == fromCol) && (board[2][toCol] == 0) && (board[3][toCol] == 0))
			return true;
		/* first move double jump - black */
		if ((tmpDir == -1) && (fromRow == 6) && (toRow == 4) && (toCol == fromCol) && (board[5][toCol] == 0) && (board[4][toCol] == 0))
			return true;
		/* standard eating */
		else if ((tmpDir * (toRow - fromRow) == 1) && (Math.abs(toCol - fromCol) == 1) && (board[toRow][toCol] != 0))
			return true;
		/* en passant - white */
		else if ((tmpDir == 1) && (fromRow == 4) && (toRow == 5) && (board[4][toCol] == (PAWN | BLACK)))
		{
			/* can only move en passant if last move is the one where the white pawn moved up two */
			if ((chessHistory[numMoves][TOROW] == 4) && (chessHistory[numMoves][FROMROW] == 6) && (chessHistory[numMoves][TOCOL] == toCol))
				return true;
			else
			{
				//errMsg = "Pawns can only move en passant immediately after an opponent played his pawn.";
				errMsg = document.getElementById('#alert_err_move_passant_id').innerHTML;
				
				return false;
			}
		}
		/* en passant - black */
		else if ((tmpDir == -1) && (fromRow == 3) && (toRow == 2) && (board[3][toCol] == PAWN))
		{
			/* can only move en passant if last move is the one where the black pawn moved up two */
			if ((chessHistory[numMoves][TOROW] == 3) && (chessHistory[numMoves][FROMROW] == 1) && (chessHistory[numMoves][TOCOL] == toCol))
				return true;
			else
			{
				//errMsg = "Pawns can only move en passant immediately after an opponent played his pawn.";
				errMsg = document.getElementById('#alert_err_move_passant_id').innerHTML;
				
				return false;
			}
		}
		else
		{
			//errMsg = "Pawns cannot move like that.";
			errMsg = document.getElementById('#alert_err_move_pawn_id').innerHTML;
			
			return false;
		}
	}

	/* checks wether a knight is making a valid move */
	function isValidMoveKnight(fromRow, fromCol, toRow, toCol)
	{
		//errMsg = "Knights cannot move like that.";
		errMsg = document.getElementById('#alert_err_move_knight_id').innerHTML;
		
		if (Math.abs(toRow - fromRow) == 2)
		{
			if (Math.abs(toCol - fromCol) == 1)
				return true;
			else
				return false;
		}
		else if (Math.abs(toRow - fromRow) == 1)
		{
			if (Math.abs(toCol - fromCol) == 2)
				return true;
			else
				return false;
		}
		else
		{
			return false;
		}
	}

	/* checks whether a bishop is making a valid move */
	function isValidMoveBishop(fromRow, fromCol, toRow, toCol)
	{
		if (Math.abs(toRow - fromRow) == Math.abs(toCol - fromCol))
		{
			if (toRow > fromRow)
			{
				if (toCol > fromCol)
				{
					for (i = 1; i < (toRow - fromRow); i++)
						if (board[fromRow + i][fromCol + i] != 0)
						{
							//errMsg = "Bishops cannot jump over other pieces.";
							errMsg = document.getElementById('#alert_err_move_bishop_jump_id').innerHTML;
							
							return false;
						}
				}
				else
				{
					for (i = 1; i < (toRow - fromRow); i++)
						if (board[fromRow + i][fromCol - i] != 0)
						{
							//errMsg = "Bishops cannot jump over other pieces.";
							errMsg = document.getElementById('#alert_err_move_bishop_jump_id').innerHTML;
							return false;
						}
				}

				return true;
			}
			else
			{
				if (toCol > fromCol)
				{
					for (i = 1; i < (fromRow - toRow); i++)
						if (board[fromRow - i][fromCol + i] != 0)
						{
							//errMsg = "Bishops cannot jump over other pieces.";
							errMsg = document.getElementById('#alert_err_move_bishop_jump_id').innerHTML;
							
							return false;
						}
				}
				else
				{
					for (i = 1; i < (fromRow - toRow); i++)
						if (board[fromRow - i][fromCol - i] != 0)
						{
							//errMsg = "Bishops cannot jump over other pieces.";
							errMsg = document.getElementById('#alert_err_move_bishop_jump_id').innerHTML;
							
							return false;
						}
				}

				return true;
			}
		}
		else
		{
			//errMsg = "Bishops cannot move like that.";
			errMsg = document.getElementById('#alert_err_move_bishop_id').innerHTML;
			
			return false;
		}
	}

	/* checks wether a rook is making a valid move */
	function isValidMoveRook(fromRow, fromCol, toRow, toCol)
	{
		if (toRow == fromRow)
		{
			if (toCol > fromCol)
			{
				for (i = (fromCol + 1); i < toCol; i++)
					if (board[fromRow][i] != 0)
					{
						//errMsg = "Rooks cannot jump over other pieces.";
						errMsg = document.getElementById('#alert_err_move_rook_jump_id').innerHTML;
						
						return false;
					}
			}
			else
			{
				for (i = (toCol + 1); i < fromCol; i++)
					if (board[fromRow][i] != 0)
					{
						//errMsg = "Rooks cannot jump over other pieces.";
						errMsg = document.getElementById('#alert_err_move_rook_jump_id').innerHTML;
						return false;
					}

			}

			return true;
		}
		else if (toCol == fromCol)
		{
			if (toRow > fromRow)
			{
				for (i = (fromRow + 1); i < toRow; i++)
					if (board[i][fromCol] != 0)
					{
						//errMsg = "Rooks cannot jump over other pieces.";
						errMsg = document.getElementById('#alert_err_move_rook_jump_id').innerHTML;
						return false;
					}
			}
			else
			{
				for (i = (toRow + 1); i < fromRow; i++)
					if (board[i][fromCol] != 0)
					{
						//errMsg = "Rooks cannot jump over other pieces.";
						errMsg = document.getElementById('#alert_err_move_rook_jump_id').innerHTML;
						return false;
					}

			}

			return true;
		}
		else
		{
			//errMsg = "Rooks cannot move like that.";
			errMsg = document.getElementById('#alert_err_move_rook_id').innerHTML;
			return false;
		}
	}

	/* this function checks whether a queen is making a valid move */
	function isValidMoveQueen(fromRow, fromCol, toRow, toCol)
	{
		if (isValidMoveRook(fromRow, fromCol, toRow, toCol) || isValidMoveBishop(fromRow, fromCol, toRow, toCol))
			return true;

		if (errMsg.search("jump") == -1)
			//errMsg = "Queens cannot move like that.";
			errMsg = document.getElementById('#alert_err_move_queen_id').innerHTML;
		else
			//errMsg = "Queens cannot jump over other pieces.";
			errMsg = document.getElementById('#alert_err_move_queen_jump_id').innerHTML;

		return false;
	}

	/* this functions checks to see if curColor is in check */
	function isInCheck(curColor)
	{
		var targetKing = getPieceCode(curColor, "king");

		/* find king */
		for (i = 0; i < 8; i++)
			for (j = 0; j < 8; j++)
				if (board[i][j] == targetKing)
					/* verify it's location is safe */
					return !isSafe(i, j, curColor);

		/* the next lines will hopefully NEVER be reached */
		errMsg = "CRITICAL ERROR: KING MISSING!"
		return false;
	}

	/* Ignoring pins, could the piece on the from-square move to the to-square? */

	function isValidNoPinMove(fromRow, fromCol, toRow, toCol, epCol)
	{
		var tmpDir = 1;
		var curColor = "white";

		if (board[fromRow][fromCol] & BLACK)
		{
			tmpDir = -1;
			curColor = "black";
		}

		var isValid = false;

		switch(board[fromRow][fromCol] & COLOR_MASK)
		{
			case PAWN:
				isValid = isValidMovePawn(fromRow, fromCol, toRow, toCol, tmpDir, epCol);
				break;

			case KNIGHT:
				isValid = isValidMoveKnight(fromRow, fromCol, toRow, toCol);
				break;

			case BISHOP:
				isValid = isValidMoveBishop(fromRow, fromCol, toRow, toCol);
				break;

			case ROOK:
				isValid = isValidMoveRook(fromRow, fromCol, toRow, toCol);
				break;

			case QUEEN:
				isValid = isValidMoveQueen(fromRow, fromCol, toRow, toCol);
				break;

			case KING:
				isValid = isValidMoveKing(fromRow, fromCol, toRow, toCol, curColor);
				break;

			default:	/* ie: not implemented yet */
		}

		return isValid;

	}
	
	function isValidMove(fromRow, fromCol, toRow, toCol, epCol)
	{
		if(!isValidNoPinMove(fromRow, fromCol, toRow, toCol, epCol))
			return false;	// The piece on the from-square doesn't even move in this way

		/* now that we know the move itself is valid, let's make sure we're not moving into check */
		/* NOTE: we don't need to check for the king since it's covered by isValidMoveKing() */
		var curColor = "white";

		if (board[fromRow][fromCol] & BLACK)
			curColor = "black";
		var isValid = true;

		/* now that we know the move itself is valid, let's make sure we're not moving into check */
		/* NOTE: we don't need to check for the king since it's covered by isValidMoveKing() */

		if ((board[fromRow][fromCol] & COLOR_MASK) != KING)
		{
			/* save current board destination */
			var tmpPiece = board[toRow][toCol];

			/* is it an en passant capture? Then remove the captured pawn */
			var tmpEnPassant = 0;
			if (((board[fromRow][fromCol] & COLOR_MASK) == PAWN) && (Math.abs(toCol - fromCol) == 1) && (tmpPiece == 0))
			{
				tmpEnPassant = board[fromRow][toCol];
				board[fromRow][toCol] = 0;
			}

			/* update board with move (client-side) */
			board[toRow][toCol] = board[fromRow][fromCol];
			board[fromRow][fromCol] = 0;

			/* are we in check now? */
			if (isInCheck(curColor))
			{
				/* if so, invalid move */
				errMsg = document.getElementById('#alert_err_move_check_id').innerHTML;
				//errMsg = "Cannot move into check.";
				isValid = false;
			}

			/* restore board to previous state */
			board[fromRow][fromCol] = board[toRow][toCol];
			board[toRow][toCol] = tmpPiece;
			if (tmpEnPassant != 0)
			{
				board[fromRow][toCol] = tmpEnPassant;
			}
		}

		return isValid;
	}

function canSquareBeBlocked(testRow, testCol, testColor)
{
	/*
	NOTE: This function is similar to isSafe(); however, the pawn detection
	is different. While the original function checks pawns moving diagonally
	or en-passant, this function doesn't.
	Since this function is intended for checkmate detection, specifically the
	canBlockAttacker() function, it must validate pawns moving forward.
	Also, king is not /allowed/ to block a square.
	NOTE: testColor is the attacker color!
	*/

	var ennemyColor = WHITE;	// Attacking
	var myColor = BLACK;		// Blocking

	if (testColor == 'black')
	{
		ennemyColor = BLACK; /* 1000 0000 */
		myColor = WHITE;
	}

	/* check for knights first */
	for (var i = 0; i < 8; i++) {	// Check all eight possible knight moves
		var fromRow = testRow + knightMove[i][0];
		var fromCol = testCol + knightMove[i][1];

		if (isInBoard(fromRow, fromCol))
			if (board[fromRow][fromCol] == (KNIGHT | myColor))	// Knight found
				if(isValidMove(fromRow, fromCol, testRow, testCol))
					return true;	// It can move and block the attack
	}

	/* tactic: start at test pos and check all 8 directions for an attacking piece */

	/* directions:    BLACK:    WHITE:

		0 1 2         2 1 0     6 5 4

		7 * 3         3 * 7     7 * 3

		6 5 4         4 5 6     0 1 2

	*/

	for (var j = 0; j < 8; j++)		// Look for pieces in all directions

	{

		var fromRow = testRow;

		var fromCol = testCol;

		for (var i = 1; i < 8; i++)	// Distance from the test square

		{

			fromRow += direction[j][0];

			fromCol += direction[j][1];

			if (isInBoard(fromRow, fromCol))

			{ // if square is in board..

				if (board[fromRow][fromCol] != 0)

				{ // We found the first piece in this direction

					if((board[fromRow][fromCol] & BLACK) == myColor)

					{ // It is my piece

						if(isValidMove(fromRow, fromCol, testRow, testCol))

							return true;	// It can move and block the attack

					}

					break;		// No need to look further in this direction

				}

			}

			else

				break;	// We fell off the edge of the board

		}

	}

	return false;	// The attack cannot be blocked

}



/* canBeCaptured returns true if the piece at testRow, testCol can be captured */

function canBeCaptured(testRow, testCol, epCol)

{

	/* DESIGN NOTE: this function is designed only with CAPTURE checking in mind and should 

		not be used for other purposes, e.g. if there is no piece (or a king) on the give square */

	/* Both normal captures and en passant captures are checked. The epCol parameter

	   should contain the column number of the en passant square or -1 if there is none.

	   If epCol >= 0 it indicates that we are replying to a pawn double advance move */



	var	tmpDir = -1;

	var	ennemyColor = BLACK;

	if (board[testRow][testCol] & BLACK)

	{

		tmpDir = 1;

		ennemyColor = WHITE;

	}

	var thePiece = getPieceName(board[testRow][testCol]);



	var atkSquare = new Array();

	atkSquare = getAttackers(testRow, testCol, ennemyColor);	// Find all attackers

	

	for (var i = 0; i < atkSquare.length; i++)	// Are the attackers pinned or can they capture?

		if(isValidMove(atkSquare[i][0], atkSquare[i][1], testRow, testCol))

			return true;	// The piece can be captured



	// If thePiece is a pawn can it by captured en passant?

	if(thePiece == 'pawn' && ((testRow == 3 && ennemyColor == BLACK) || (testRow == 4 && ennemyColor == WHITE)))

	{	// The pawn is on the correct row for a possible e.p. capture

		if(testCol > 0 && board[testRow][testCol-1] == (PAWN | ennemyColor))

			if(board[testRow + tmpDir][testCol] == 0)	// It's not a regular capture

					if(isValidMove(testRow, testCol-1, testRow + tmpDir, testCol, epCol))

						return true;	// En passant capture

		if(testCol < 7 && board[testRow][testCol+1] == (PAWN | ennemyColor))

			if(board[testRow + tmpDir][testCol] == 0)	// It's not a regular capture

					if(isValidMove(testRow, testCol+1, testRow + tmpDir, testCol, epCol))

						return true;	// En passant capture

	}

	return false;	// The piece cannot be captured

}



/* Find all pieces of color atkColor that attack the given square */

/* Note: Even if a piece attacks a square it may not be able to move there */

/* Note: En passant captures are not considered by this function */

function getAttackers(toRow, toCol, atkColor)

{

	var atkSquare = new Array();



	/* check for knights first */

	for (var i = 0; i < 8; i++) {	// Check all eight possible knight moves

		var fromRow = toRow + knightMove[i][0];

		var fromCol = toCol + knightMove[i][1];

		if (isInBoard(fromRow, fromCol))

			if (board[fromRow][fromCol] == (KNIGHT | atkColor))	// Enemy knight found

					atkSquare[atkSquare.length] = [fromRow, fromCol];

	}

	/* tactic: start at test square and check all 8 directions for an attacking piece */

	/* directions:

		0 1 2

		7 * 3

		6 5 4

	*/



	for (var j = 0; j < 8; j++)		// Look in all directions

	{

		var fromRow = toRow;

		var fromCol = toCol;

		for (var i = 1; i < 8; i++)	// Distance from thePiece

		{

			fromRow += direction[j][0];

			fromCol += direction[j][1];

			if (isInBoard(fromRow, fromCol))

			{

				if (board[fromRow][fromCol] != 0)

				{	// We found the first piece in this direction

					if((board[fromRow][fromCol] & BLACK) == atkColor)	// It is an enemy piece

					{

						if(isAttacking(board[fromRow][fromCol], fromRow, fromCol, getPieceColor(board[fromRow][fromCol]), toRow, toCol))

							atkSquare[atkSquare.length] = [fromRow, fromCol];	// An attacker found

					}

					break;		// No need to look further in this direction

				}

			}

			else

				break;

		}

	}

	return atkSquare;

}



/* Is the given square attacked by a piece of color atkColor? */

function isAttacked(toRow, toCol, atkColor)

{

	return getAttackers(toRow, toCol, atkColor).length > 0;

}



/* Count how many different moves are possible in the current position for myColor */

function countMoves(myColor)

{

	var moves = genAllMoves(myColor);

	var count = 0;

	for (var i in moves)			// For all board rows

	{

		for (var j in moves[i])		// Check all columns

		{

			count += moves[i][j].length;

		}

	}

	return count;

}



/* Generate all possible moves for the side indicated by the myColor parameter */

function genAllMoves(myColor)

{

	var moves = new Array();

	for (var i = 0; i < 8; i++)			// For all board rows

	{

		for (var j = 0; j < 8; j++)		// Check all columns

		{

			if(board[i][j] != 0 && ((board[i][j] & BLACK) == myColor))

			{

				if(!(i in moves))

					moves[i] = new Array();

				moves[i][j] = genPieceMoves(i, j);

			}

		}

	}

	return moves;

}



/* Generate moves for a rook, bishop or queen placed at the from-square */

function genSlideMoves(fromRow, fromCol, moveDir)

{

	var toSquare = new Array();	// Store the generated moves

	var	ennemyColor = BLACK;

	if (board[fromRow][fromCol] & BLACK)

	{

		ennemyColor = WHITE;

	}

	for (var j = 0; j < moveDir.length; j++)	// Check all (valid) directions

	{

		var toRow = fromRow;

		var toCol = fromCol;

		for (var i = 1; i < 8; i++)	// Distance from the piece

		{

			toRow += moveDir[j][0];

			toCol += moveDir[j][1];

			if (isInBoard(toRow, toCol))

			{

				if (board[toRow][toCol] != 0)

				{	// We found the first piece in this direction

					if((board[toRow][toCol] & BLACK) == ennemyColor)	// It's an enemy piece

					{

						if(isValidMove(fromRow, fromCol, toRow, toCol))

							toSquare[toSquare.length] = [toRow, toCol];	// A capture

					}

					break;		// No need to look further in this direction

				}

				else	// an empty square

				{

					if(isValidMove(fromRow, fromCol, toRow, toCol))

						toSquare[toSquare.length] = [toRow, toCol];	// Move to an empty square

				}

			}

			else

				break;

		}

	}

	return toSquare;

}



/* Generate all moves for the piece at the given square */

/* Currently this function is only used to test for stalemate.

   Therefore castling moves are not checked as they are not relevant 

   for that purpose */

function genPieceMoves(fromRow, fromCol)

{

	var	ennemyColor = BLACK;

	if (board[fromRow][fromCol] & BLACK)

	{

		ennemyColor = WHITE;

	}

	var thePiece = board[fromRow][fromCol];



	var toSquare = new Array();

	

	switch(thePiece & COLOR_MASK)

	{

		case PAWN:

			var forwardDir = 1;

			if (ennemyColor == WHITE)

				forwardDir = -1;



			for (var i = 0; i < 4; i++) {

				var toRow = fromRow + pawnMove[i][0] * forwardDir;

				var toCol = fromCol + pawnMove[i][1];

				if (isInBoard(toRow, toCol))

					if (board[toRow][toCol] == 0 || (board[toRow][toCol] & BLACK) == ennemyColor)

						if(isValidMove(fromRow, fromCol, toRow, toCol))

							toSquare[toSquare.length] = [toRow, toCol];

			}

			break;



		case ROOK:

			toSquare = genSlideMoves(fromRow, fromCol, horzVertMove);

			break;

			

		case KNIGHT:

			for (var i = 0; i < 8; i++) {	// Check all eight possible knight moves

				var toRow = fromRow + knightMove[i][0];

				var toCol = fromCol + knightMove[i][1];

				if (isInBoard(toRow, toCol))

					if (board[toRow][toCol] == 0 || (board[toRow][toCol] & BLACK) == ennemyColor)

						if(isValidMove(fromRow, fromCol, toRow, toCol))

							toSquare[toSquare.length] = [toRow, toCol];

			}

			break;

			

		case BISHOP:

			toSquare = genSlideMoves(fromRow, fromCol, diagonalMove);

			break;

			

		case QUEEN:

			toSquare = genSlideMoves(fromRow, fromCol, direction);

			break;

			

		case KING:

			for (var i = 0; i < 8; i++) {	// Check all eight possible king moves

				var toRow = fromRow + direction[i][0];

				var toCol = fromCol + direction[i][1];

				if (isInBoard(toRow, toCol))

					if (board[toRow][toCol] == 0 || (board[toRow][toCol] & BLACK) == ennemyColor)

						if(isValidMove(fromRow, fromCol, toRow, toCol))

							toSquare[toSquare.length] = [toRow, toCol];

			}

			break;

	}



	return toSquare;

}



//

// FEN functions

//



Files = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];



function ExpandFEN(FEN) {

  var theFEN = FEN.replace(/[2-8]/g,                    // Expand contiguous empty squares

    function strRepeat(count) {                         //   e.g. change '4' to '1111'

      var strOut = '';

      for(var i=0; i < count; i++) {

       strOut += '1';

      }

     return strOut;

    }

  );

  return theFEN.replace(/\//g, "");                     // Leave only pieces and empty squares

}



function PackFEN(piecePlacement, activeColor, castlingAvail, epSquare, halfmoveClock, fullmoveNumber)

{ // Pack all the FEN fields into one string

	var FEN = '';

	var idx = 0;

	var empty = 0;

	var c = '';

	for(var i=0; i < 64; i++)

	{ // Generate the correct piece placement string

		if(i > 0 && (i % 8 == 0))

		{ // New row

			if(empty > 0)

			{ // Count of empty squares does not continue across rows

				FEN += empty + "";

				empty = 0;

				idx++;

			}

			FEN += '/';	// New row

		}

		c = piecePlacement.charAt(i);

		if(c == '1')

		{ // Count consecutive empty squares

			empty++;

		}

		else

		{ // Non-empty square

			if(empty > 0)

			{ // Add the number of consecutive empty squares to the output string

				FEN += empty + "";

				empty = 0;

				idx++;

			}

			FEN += c + "";

			idx++;

		}

	}

	if(empty > 0)

	{

		FEN += empty + "";

	}

	return FEN + ' ' + activeColor + ' ' + castlingAvail + ' ' + epSquare + ' ' + halfmoveClock + ' ' + fullmoveNumber;

}



function getFENStartPos()

{

	return 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1';

}



// Returns an array of FEN strings for the current game

// Note that this function assumes that the game started from the normal initial position

function historyToFEN()

{

	var FEN = new Array();

	FEN[0] = getFENStartPos();	// The start position

	var activeColor = 'w';

	var wKS = 'K';	// Castling availability

	var wQS = 'Q';

	var bKS = 'k';

	var bQS = 'q';

	var castlingAvail = 'KQkq';

	var epSquare = '-';	// The en passant square

	var halfmoveClock = 0;	// Number of half moves since last capture or pawn move

	var fullmoveNumber = 1;	// The move number

	for (var i = 0; i <= numMoves; i++)

	{

		FEN[i+1] = ExpandFEN(FEN[i]).slice(0, 64);	// Get the piece placement from the FEN string

		if(chessHistory[i][CURCOLOR] == 'white')

			activeColor = 'b';

		else

		{

			activeColor = 'w';

			fullmoveNumber++;

		}

		var fromCol = chessHistory[i][FROMCOL];

		var fromRow = chessHistory[i][FROMROW];

		var row = chessHistory[i][TOROW];

		var col = chessHistory[i][TOCOL];

		if(FEN[i+1].charAt(col + (7 - row) * 8) != '1' || chessHistory[i][CURPIECE] == 'pawn')

			halfmoveClock = 0;	// Restart the count after pawn move or capture

		else

			halfmoveClock++;

		var piece = FEN[i+1].charAt((7 - fromRow) * 8 + fromCol);

		FEN[i+1] = FEN[i+1].slice(0, (7 - fromRow) * 8 + fromCol) + '1' + FEN[i+1].slice((7 - fromRow) * 8 + fromCol + 1);

		FEN[i+1] = FEN[i+1].slice(0, (7 - row) * 8 + col) + piece + FEN[i+1].slice((7 - row) * 8 + col + 1);



		if (chessHistory[i][CURPIECE] == 'king')

		{ // Can't castle after the king has been moved

			if(chessHistory[i][CURCOLOR] == 'white')

			{

				wKS = '';

				wQS = '';

			}

			else

			{

				bKS = '';

				bQS = '';

			}

			/* if this is a castling move the rook must also be moved */

			if (Math.abs(col - fromCol) == 2)

			{	// The king only moves two squares when castling

				var rookCol = 0;

				var rookToCol = 3

				if (col - fromCol == 2)

				{	// Kingside castling (would be == -2 if queenside)

					rookCol = 7;

					rookToCol = 5;

				}

				FEN[i+1] = FEN[i+1].slice(0, (7 - row) * 8 + rookToCol) + FEN[i+1].charAt((7 - row) * 8 + rookCol) + FEN[i+1].slice((7 - row) * 8 + rookToCol + 1);

				FEN[i+1] = FEN[i+1].slice(0, (7 - row) * 8 + rookCol) + '1' + FEN[i+1].slice((7 - row) * 8 + rookCol + 1);

			} 

		}

		else if (chessHistory[i][CURPIECE] == 'rook')

		{

			if(chessHistory[i][CURCOLOR] == 'white')

			{

				if(fromRow == 0)

				{

					if(fromCol == 0)

						wQS = '';

					else

						wKS = '';

				}

			}

			else

			{

				if(fromRow == 7)

				{

					if(fromCol == 0)

						bQS = '';

					else

						bKS = '';

				}

			}

		}

		else if(chessHistory[i][CURPIECE] == 'pawn' && Math.abs(chessHistory[i][TOROW] - chessHistory[i][FROMROW]) == 2)

		{ // Pawn double advance, so en passant capture may be possible on the next move

			if(chessHistory[i][CURCOLOR] == 'white')

			{

				epSquare = Files[fromCol] + '3';

			}

			else

			{

				epSquare = Files[fromCol] + '6';

			}

		}

		castlingAvail = wKS + wQS + bKS + bQS;

		if(castlingAvail == '')

			castlingAvail = '-';

		FEN[i+1] = PackFEN(FEN[i+1], activeColor, castlingAvail, epSquare, halfmoveClock, fullmoveNumber);

		epSquare = '-';

	}

	return FEN;

}



function isFiftyMoveDraw(FEN)

{ // Returns true if the game is drawn due to the fifty move draw rule (no captures or pawn moves)

	return FEN.split(' ')[4] >= 100;

	

}



function isThirdTimePosDraw(FEN)

{ // Returns true if this is the third time that the exact same position arises with the same side to move

	var currentPos = FEN[FEN.length - 1].split(' ', 4).join(' ');

	var count = 0;

	for (var i = 0; i < FEN.length - 1; i++)

	{

		if(currentPos == FEN[i].split(' ', 4).join(' '))

		{

			count++;

		}

	}

	return count >= 2;

}
