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
	global $dbh;
	/* clear history */
	global $numMoves;
	
	$numMoves = -1;
	mysqli_query($dbh,"DELETE FROM history WHERE gameID = ".$gameID);
	$res_game = mysqli_query($dbh,"SELECT type, flagBishop, flagKnight, flagRook, flagQueen, chess960 FROM games WHERE gameID = ".$gameID);
	$game = mysqli_fetch_array($res_game, MYSQLI_ASSOC);
	
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
	global $board, $isPromoting, $isChess960Castling, $history, $numMoves;

	/* if moving en-passant */
	/* (ie: if pawn moves diagonally without replacing anything) */
	if ((($board[$_POST['fromRow']][$_POST['fromCol']] & COLOR_MASK) == PAWN) && ($_POST['toCol'] != $_POST['fromCol']) && ($board[$_POST['toRow']][$_POST['toCol']] == 0))
		/* delete eaten pawn */
		$board[$_POST['fromRow']][$_POST['toCol']] = 0;
	
	if (!$isChess960Castling)
	{
		/* move piece to destination, replacing whatever's there */
		$board[$_POST['toRow']][$_POST['toCol']] = $board[$_POST['fromRow']][$_POST['fromCol']];
	
		/* delete piece from old position */
		$board[$_POST['fromRow']][$_POST['fromCol']] = 0;
	}
	
	/* promoting */
	if ($isPromoting)
		$board[$_POST['toRow']][$_POST['toCol']] = $_POST['promotion'] | ($board[$_POST['toRow']][$_POST['toCol']] & BLACK);
	
	/* castling */
	if ($isChess960Castling)
	{
		// castling to the left
		$tmpPosKing = $_POST['fromCol'];
		$tmpPosRook = $_POST['toCol'];
		
		if ($_POST['fromCol'] > $_POST['toCol']) {
			// King 
			$board[$_POST['toRow']][2] = $board[$_POST['toRow']][$_POST['fromCol']];
			// Rook
			$board[$_POST['toRow']][3] = $board[$_POST['toRow']][$_POST['toCol']];
			// Removes piece from original position if not new position
			if ($tmpPosKing != 3 && $tmpPosKing != 2) $board[$_POST['toRow']][$_POST['fromCol']] = 0;
			if ($tmpPosRook != 3 && $tmpPosRook != 2) $board[$_POST['toRow']][$_POST['toCol']] = 0;
		}
		else {
			// King 
			$board[$_POST['toRow']][6] = $board[$_POST['toRow']][$_POST['fromCol']];
			// Rook
			$board[$_POST['toRow']][5] = $board[$_POST['toRow']][$_POST['toCol']];
			// Removes piece from original position if not new position
			if ($tmpPosKing != 6 && $tmpPosKing != 5) $board[$_POST['toRow']][$_POST['fromCol']] = 0;
			if ($tmpPosRook != 6 && $tmpPosRook != 5) $board[$_POST['toRow']][$_POST['toCol']] = 0;
		}
		
	}
	else
	{
		if ((($board[$_POST['toRow']][$_POST['toCol']] & COLOR_MASK) == KING) && (($_POST['toCol'] - $_POST['fromCol']) == 2))
		{
			/* castling to the right, move the right rook to the left side of the king */
			$board[$_POST['toRow']][5] = $board[$_POST['toRow']][7];
	
			/* delete rook from original position */
			$board[$_POST['toRow']][7] = 0;
		}
		elseif ((($board[$_POST['toRow']][$_POST['toCol']] & COLOR_MASK) == KING) && (($_POST['fromCol'] - $_POST['toCol']) == 2))
		{
			/* castling to the left, move the left rook to the right side of the king */
			$board[$_POST['toRow']][3] = $board[$_POST['toRow']][0];
	
			/* delete rook from original position */
			$board[$_POST['toRow']][0] = 0;
		}
	}

	return true;
}

?>