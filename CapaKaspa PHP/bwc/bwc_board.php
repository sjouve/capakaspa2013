<?
/* these functions are used to start a new game */
function initBoard($flagRook, $flagQueen, $flagKnight, $flagBishop, $chess960)
{
	global $board;

	/* clear board */
	for ($i = 0; $i < 8; $i++)
	{
		for ($j = 0; $j < 8; $j++)
		{
			$board[$i][$j] = 0;
		}
	}

	if ($chess960 == "")
	{
		if ($flagRook==1)
		{
			$board[0][0] = WHITE | ROOK;
			$board[0][7] = WHITE | ROOK;
			$board[7][0] = BLACK | ROOK;
			$board[7][7] = BLACK | ROOK;
		}
		if ($flagKnight==1)
		{
			$board[0][1] = WHITE | KNIGHT;
			$board[0][6] = WHITE | KNIGHT;
			$board[7][1] = BLACK | KNIGHT;
			$board[7][6] = BLACK | KNIGHT;
		}
		if ($flagBishop==1)
		{
			$board[0][2] = WHITE | BISHOP;
			$board[0][5] = WHITE | BISHOP;
			$board[7][2] = BLACK | BISHOP;
			$board[7][5] = BLACK | BISHOP;
		}
		if ($flagQueen==1)
		{
			$board[0][3] = WHITE | QUEEN;
			$board[7][3] = BLACK | QUEEN;
		}
	
		$board[0][4] = WHITE | KING;
		$board[7][4] = BLACK | KING;
	
		
	}
	else
	{
		// Init Chess960
		for ($i = 0; $i < 8; $i++)
		{
			$char = $chess960[$i];
			$board[0][$i] = WHITE | getPieceCodeChar960($char);
			$board[7][$i] = BLACK | getPieceCodeChar960($char);
		}
	}
	
	/* setup pawns */
	for ($i = 0; $i < 8; $i++)
	{
		$board[1][$i] = WHITE | PAWN;
		$board[6][$i] = BLACK | PAWN;
	}
}

function getPositionFromBoard($board)
{
	$position = "";
	
	// Construire la chaîne de la position courante à partir de l'échiquier
	// Pour chaque ligne
	for ($i = 0; $i < 8; $i++)
	{
		// Pour chaque colonne
		for ($j = 0; $j < 8; $j++)
		{
			$position .= getPieceChar($board[$i][$j]);			
		}
	}
	return $position;	
}

function createNewGame($gameID)
{
	/* clear history */
	global $numMoves;
	
	$numMoves = -1;
	mysql_query("DELETE FROM history WHERE gameID = ".$gameID);
	$res_game = mysql_query("SELECT type, flagBishop, flagKnight, flagRook, flagQueen, chess960 FROM games WHERE gameID = ".$gameID);
	$game = mysql_fetch_array($res_game, MYSQL_ASSOC);
	
	if ($game['type'] == 0)
		initBoard(1, 1, 1, 1, "");
	else if ($game['type'] == 1)
		initBoard($game['flagRook'], $game['flagQueen'], $game['flagKnight'], $game['flagBishop'], "");
	else 
		initBoard($game['flagRook'], $game['flagQueen'], $game['flagKnight'], $game['flagBishop'], $game['chess960']);
}

/* these functions deal specifically with moving a piece */
function doMove()
{
	global $board, $isPromoting, $doUndo, $history, $numMoves;

	/* if moving en-passant */
	/* (ie: if pawn moves diagonally without replacing anything) */
	if ((($board[$_POST['fromRow']][$_POST['fromCol']] & COLOR_MASK) == PAWN) && ($_POST['toCol'] != $_POST['fromCol']) && ($board[$_POST['toRow']][$_POST['toCol']] == 0))
		/* delete eaten pawn */
		$board[$_POST['fromRow']][$_POST['toCol']] = 0;
	
	/* move piece to destination, replacing whatever's there */
	$board[$_POST['toRow']][$_POST['toCol']] = $board[$_POST['fromRow']][$_POST['fromCol']];

	/* delete piece from old position */
	$board[$_POST['fromRow']][$_POST['fromCol']] = 0;
	
	/* promoting */
	if ($isPromoting)
		$board[$_POST['toRow']][$_POST['toCol']] = $_POST['promotion'] | ($board[$_POST['toRow']][$_POST['toCol']] & BLACK);
	
	/* if not Undoing, but castling */
	if (($doUndo != "yes") && (($board[$_POST['toRow']][$_POST['toCol']] & COLOR_MASK) == KING) && (($_POST['toCol'] - $_POST['fromCol']) == 2))
	{
		/* castling to the right, move the right rook to the left side of the king */
		$board[$_POST['toRow']][5] = $board[$_POST['toRow']][7];

		/* delete rook from original position */
		$board[$_POST['toRow']][7] = 0;
	}
	elseif (($doUndo != "yes") && (($board[$_POST['toRow']][$_POST['toCol']] & COLOR_MASK) == KING) && (($_POST['fromCol'] - $_POST['toCol']) == 2))
	{
		/* castling to the left, move the left rook to the right side of the king */
		$board[$_POST['toRow']][3] = $board[$_POST['toRow']][0];

		/* delete rook from original position */
		$board[$_POST['toRow']][0] = 0;
	}

	return true;
}

/* these functions deal specifically with undoing a move */
function doUndo()
{
	global $board, $numMoves;
	
	/* get the last move from the history */
	/* NOTE: MySQL currently has no support for subqueries */
	$tmpMaxTime = mysql_query("SELECT Max(timeOfMove) FROM history WHERE gameID = ".$_POST['gameID']);
	$maxTime = mysql_result($tmpMaxTime,0);
	$moves = mysql_query("SELECT * FROM history WHERE gameID = ".$_POST['gameID']." AND timeOfMove = '$maxTime'");

	/* if there actually is a move... */
	if ($lastMove = mysql_fetch_array($moves, MYSQL_ASSOC))
	{
		/* if the last move was played by this player */
		
			/* undo move */
			$fromRow = $lastMove['fromRow'];
			$fromCol = $lastMove['fromCol'];
			$toRow = $lastMove['toRow'];
			$toCol = $lastMove['toCol'];

			$board[$fromRow][$fromCol] = getPieceCode($lastMove['curColor'], $lastMove['curPiece']);
			$board[$toRow][$toCol] = 0;

			/* check for en-passant */
			/* if pawn moves diagonally without replacing a piece, it's en passant */
			if (($lastMove['curPiece'] == "pawn") && ($toCol != $fromCol) && is_null($lastMove['replaced']))
			{
				if ($lastMove['curColor'] == "black")
					$board[$fromRow][$toCol] = getPieceCode("white", "pawn");
				else
					$board[$fromRow][$toCol] = getPieceCode("black", "pawn");
			}
			
			/* check for castling */
			if ((($board[$fromRow][$fromCol] & COLOR_MASK) == KING) && (abs($toCol - $fromCol) == 2))
			{
				/* move rook back as well */
				if (($toCol - $fromCol) == 2)
				{
					$board[$fromRow][7] = $board[$fromRow][5];
					$board[$fromRow][5] = 0;
				}
				else
				{
					$board[$fromRow][0] = $board[$fromRow][3];
					$board[$fromRow][3] = 0;
				}
			}

			/* restore lost piece */
			if (!is_null($lastMove['replaced']))
			{
				if ($lastMove['curColor'] == "black")
					$board[$toRow][$toCol] = getPieceCode("white", $lastMove['replaced']);
				else
					$board[$toRow][$toCol] = getPieceCode("black", $lastMove['replaced']);
			}

			/* remove last move from history */
			$numMoves--;
			mysql_query("DELETE FROM history WHERE gameID = ".$_POST['gameID']." AND timeOfMove = '$maxTime'");

		/* else */
			/* output error message */
	}
}
?>