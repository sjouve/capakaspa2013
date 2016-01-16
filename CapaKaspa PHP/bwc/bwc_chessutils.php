<?
/* these are utility functions used by other functions */
function getPieceCharForImage($piece)
{
	switch($piece & COLOR_MASK)
	{
		case PAWN:
			$name = "p";
			break;
		case KNIGHT:
			$name = "n";
			break;
		case BISHOP:
			$name = "b";
			break;
		case ROOK:
			$name = "r";
			break;
		case QUEEN:
			$name = "q";
			break;
		case KING:
			$name = "k";
			break;
	}

	return $name;
}

function getPieceName($piece)
{
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

function getPieceCodeChar960($char)
{

	// Load
	switch($char)
	{
		case "P":
			$code = PAWN;
			break;
		case "N":
			$code = KNIGHT;
			break;
		case "B":
			$code = BISHOP;
			break;
		case "R":
			$code = ROOK;
			break;
		case "Q":
			$code = QUEEN;
			break;
		case "K":
			$code = KING;
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
	global $board, $isDrawRequested, $isGameOver, $playersColor, $nb_game_vacation, $isPlayersTurn;

	/* if current player is promoting, a message needs to be replied to (Undo or Draw) or the game is over, then board is Disabled */
	$tmpIsBoardDisabled = (($isDrawRequested || $isGameOver) == true || $playersColor == "" || $nb_game_vacation > 0 || !$isPlayersTurn);

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
	else if ($piece == "king" && $pieceCaptured == "chess960" && $fromCol > $toCol)
	{		
		/* Chess960 castling */
		$pgnString .= ("O-O-O");
	}
	else if (($piece == "king") && $pieceCaptured == "chess960" && $fromCol < $toCol)
	{
		/* Chess960 castling */
		$pgnString .= ("O-O");
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

function chessNotification($msgType, $receiverColor, $more, $senderName, $gameID)
{
	/* default message and subject */
	$mailmsg = "";
	$mailsubject = "[CapaKaspa] ";
	
	$receiver = getPrefNotification($gameID, $receiverColor);
	$locale = isset($receiver['language'])?$receiver['language']:"en_EN";
	
	// Email with receiver language
	putenv("LC_ALL=$locale");
	setlocale(LC_ALL, $locale);
	bindtextdomain("messages", "./locale");
	bind_textdomain_codeset("messages", "UTF-8");
	textdomain("messages");
	
	$strPlayer = _("The player");
	$strOpponent = _("Your opponent");
		
	// Mail subject and message + Activity
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
			
		case 'accepted':
			$mailsubject .= _("Invitation accepted");
			$mailmsg = $strPlayer." ".$senderName._(" has accepted your invitation. A new game began.");
			if ($more) {
				$mailmsg .= "<br>".$senderName._(" joined a message :");
				$mailmsg .= "<br>[".stripslashes(strip_tags($more))."]";
			}
			break;
				
		case 'declined':
			$mailsubject .= _("Invitation refused");
			$mailmsg = $strPlayer." ".$senderName._(" refused your invitation.");
			if ($more) {
				$mailmsg .= "<br>".$senderName._(" joined a message :");
				$mailmsg .= "<br>[".stripslashes(strip_tags($more))."]";
			}
			break;
			
		case 'move':
			$mailsubject .= _("New move");
			$mailmsg = $strOpponent." ".$senderName._(" played a move in your game")." #".$gameID."...";
			//$mailmsg .= "\n".$more;
			break;
			
		case 'resignation':
			$mailsubject .= _("Resignation");
			$mailmsg = $strOpponent." ".$senderName._(" resigned.");
			break;
			
		case 'draw':
			$mailsubject .= _("Draw proposal accepted");
			$mailmsg = $strPlayer." ".$senderName._(" accepted your draw proposal. The game ended.");
			break;
			
		case 'drawrule':
			$mailsubject .= _("Draw game");
			$mailmsg = _("Your game against")." ".$senderName._(" ended by a draw.");
			break;
		
		case 'checkmate':
			$mailsubject .= _("Checkmate");
			$mailmsg = _("Your game against")." ".$senderName._(" ended by a checkmate.");
			break;
			
		case 'time':
			$mailsubject .= _("Time expiration");
			$mailmsg = _("Your game against")." ".$senderName._(" ended by time expiration.");
			break;
	}
		
	if ($receiver['value'] == 'oui')
	{
		sendMail($receiver['email'], $mailsubject, $mailmsg);
	}
	
	$locale = $_SESSION["pref_language"];
	// Repositionne la langue de l'utilisateur
	putenv("LC_ALL=$locale");
	setlocale(LC_ALL, $locale);
	bindtextdomain("messages", "./locale");
	bind_textdomain_codeset("messages", "UTF-8");
	textdomain("messages");
}
	
function getTurnColor($numMoves)
{
	if (($numMoves == -1) || ($numMoves % 2 == 1))
		return "white";
	else
		return "black";
}
?>