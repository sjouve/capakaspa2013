<?
/* these are utility functions used by other functions */
function getPieceName($piece)
{
	// TODO A franciser (voir alt sur case de l'échiquier)
	// Save
	switch($piece & COLOR_MASK)
	{
		case PAWN:
			$name = "pawn";
			break;
		case KNIGHT:
			$name = "knight";
			break;
		case BISHOP:
			$name = "bishop";
			break;
		case ROOK:
			$name = "rook";
			break;
		case QUEEN:
			$name = "queen";
			break;
		case KING:
			$name = "king";
			break;
	}

	return $name;
}

function getPieceChar($piece)
{
	
	if ($piece == 0)
		return "0";
		
	if ($piece & BLACK)
	{
		switch($piece & COLOR_MASK)
		{
			case PAWN:
				$char = "P";
				break;
			case KNIGHT:
				$char = "C";
				break;
			case BISHOP:
				$char = "F";
				break;
			case ROOK:
				$char = "T";
				break;
			case QUEEN:
				$char = "D";
				break;
			case KING:
				$char = "R";
				break;
		}
	}
	else
	{
	  	switch($piece & COLOR_MASK)
		{
			case PAWN:
				$char = "p";
				break;
			case KNIGHT:
				$char = "c";
				break;
			case BISHOP:
				$char = "f";
				break;
			case ROOK:
				$char = "t";
				break;
			case QUEEN:
				$char = "d";
				break;
			case KING:
				$char = "r";
				break;
		}
	}
	return $char;
}

/* Pour batch */
function getPieceCharFromName($color, $piece)
{
	
	switch($piece)
	{
		case "pawn":
			$code = "p";
			break;
		case "knight":
			$code = "c";
			break;
		case "bishop":
			$code = "f";
			break;
		case "rook":
			$code = "t";
			break;
		case "queen":
			$code = "d";
			break;
		case "king":
			$code = "r";
			break;
	}

	if ($color == "black")
		$code = strtoupper($code);

	return $code;
}


function getPieceCode($color, $piece)
{
	
	// Load
	switch($piece)
	{
		case "pawn":
			$code = PAWN;
			break;
		case "knight":
			$code = KNIGHT;
			break;
		case "bishop":
			$code = BISHOP;
			break;
		case "rook":
			$code = ROOK;
			break;
		case "queen":
			$code = QUEEN;
			break;
		case "king":
			$code = KING;
			break;
	}

	if ($color == "black")
		$code = BLACK | $code;

	return $code;
}

function getPieceCodeChar($char)
{
	
	// Load
	switch($char)
	{
		case "p":
			$code = PAWN;
			break;
		case "c":
			$code = KNIGHT;
			break;
		case "f":
			$code = BISHOP;
			break;
		case "t":
			$code = ROOK;
			break;
		case "d":
			$code = QUEEN;
			break;
		case "r":
			$code = KING;
			break;
		case "P":
			$code = BLACK | PAWN;
			break;
		case "C":
			$code = BLACK | KNIGHT;
			break;
		case "F":
			$code = BLACK | BISHOP;
			break;
		case "T":
			$code = BLACK | ROOK;
			break;
		case "D":
			$code = BLACK | QUEEN;
			break;
		case "R":
			$code = BLACK | KING;
			break;
		case "0":
			$code = 0;
			break;
	}

	return $code;
}

function getPGNCode($piecename)
{
	switch($piecename)
	{
		case 'pawn':
			$pgnCode = "";
			break;
		case 'knight':
			$pgnCode = "N";
			break;
		case 'bishop':
			$pgnCode = "B";
			break;
		case 'rook':
			$pgnCode = "R";
			break;
		case 'queen':
			$pgnCode = "Q";
			break;
		case 'king':
			$pgnCode = "K";
			break;
	}

	return $pgnCode;
}

function isBoardDisabled()
{
	global $board, $isPromoting, $isUndoRequested, $isDrawRequested, $isGameOver, $playersColor, $nb_game_vacation;

	/* if current player is promoting, a message needs to be replied to (Undo or Draw) or the game is over, then board is Disabled */
	$tmpIsBoardDisabled = (($isPromoting || $isUndoRequested || $isDrawRequested || $isGameOver) == true || $playersColor == "" || $nb_game_vacation > 0);
	
	/* if opponent is in the process of promoting, then board is diabled */
	if (!$tmpIsBoardDisabled)
	{
		if ($playersColor == "white")
			$promotionRow = 7;
		else
			$promotionRow = 0;

		for ($i = 0; $i < 8; $i++)
			if (($board[$promotionRow][$i] & COLOR_MASK) == PAWN)
				$tmpIsBoardDisabled = true;
	}

	return $tmpIsBoardDisabled;
}

function moveToPGNString($curColor, $piece, $fromRow, $fromCol, $toRow, $toCol, $pieceCaptured, $promotedTo, $isChecking)
{
	$pgnString = "";
	
	/* check for castling */
	if (($piece == "king") && (abs($toCol - $fromCol) == 2))
	{
		/* if king-side castling */
		if (($toCol - $fromCol) == 2)
			$pgnString .= ("O-O");
		else
			$pgnString .= ("O-O-O");
	}
	else
	{
		/* PNG code for moving piece */
		$pgnString .= getPGNCode($piece);

		/* source square */
		$pgnString .= chr($fromCol + 97).($fromRow + 1);

		/* check for captured pieces */
		if ($pieceCaptured != "")
			$pgnString .= "x";
		else
			$pgnString .= "-";

		/* destination square */
		$pgnString .= chr($toCol + 97).($toRow + 1);

		/* check for pawn promotion */
		if ($promotedTo != "")
			$pgnString .= "=".getPGNCode($promotedTo);
	}
	
	/* check for CHECK */
	if ($isChecking)
		$pgnString .= "+";

	/* if checkmate, $pgnString .= "#"; */

	return $pgnString;
}

function chessNotification($msgType, $receiverColor, $move, $senderName, $gameID)
{
	// TODO Création des activités
	/* default message and subject */
	$mailmsg = "";
	$mailsubject = "[CapaKaspa] ";
	
	$receiver = getPrefNotification($gameID, $receiverColor);
	$locale = isset($receiver['language'])?$receiver['language']:"en_EN";
	
	// Email with receiver language
	putenv("LC_ALL=$locale");
	setlocale(LC_ALL, $locale);
	bindtextdomain("messages", "./locale");
	textdomain("messages");
	
	$strPlayer = _("The player");
	$strOpponent = _("Your opponent");
	
	if ($receiver['value'] == 'oui')
	{
		
		/* load specific message and subject */
		switch($msgType)
		{
				
			case 'invitation':
				$mailsubject .= _("You are invited to play a new game");
				$mailmsg = $strPlayer." ".$senderName._(" invites you to play a new game.");
				break;
				
			case 'withdrawal':
				$mailsubject .= _("Invitation canceled");
				$mailmsg = $strPlayer." ".$senderName._(" canceled its invitation to play a new game.");
				break;
				
			case 'resignation':
				$mailsubject .= _("Resignation");
				$mailmsg = $strOpponent." ".$senderName._(" resigned.");
				break;
				
			case 'move':
				$mailsubject .= _("New move");
				$mailmsg = $strOpponent." ".$senderName._(" play the move :");
				$mailmsg .= "\n".$move;
				break;
				
			case 'accepted':
				$mailsubject .= _("Invitation accepted");
				$mailmsg = $strPlayer." ".$senderName._(" has accepted your invitation. A new game began.");
				if ($move) {
					$mailmsg .= "\n\n".$senderName._(" joined a message :");
					$mailmsg .= "\n".stripslashes(strip_tags($move));
				}
				break;
				
			case 'declined':
				$mailsubject .= _("Invitation refused");
				$mailmsg = $strPlayer." ".$senderName._(" refused your invitation.");
				if ($move) {
					$mailmsg .= "\n\n".$senderName._(" joined a message :");
					$mailmsg .= "\n".stripslashes(strip_tags($move));
				}
				break;
				
			case 'draw':
				$mailsubject .= _("Draw proposal accepted");
				$mailmsg = $strPlayer." ".$senderName._(" accepted your drax proposal.\nThe game ended : 1/2-1/2.");
				break;
		}
	
		sendMail($receiver['email'], $mailsubject, $mailmsg);
	}
}
	
function getTurnColor($numMoves)
{
	if (($numMoves == -1) || ($numMoves % 2 == 1))
		return "white";
	else
		return "black";
}
?>