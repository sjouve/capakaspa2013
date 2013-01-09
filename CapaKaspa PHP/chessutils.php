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
	
	/* Fonction d'envoi de mail */
	function sendMail($msgTo, $mailSubject, $mailMsg)
	{
		global $CFG_MAILADDRESS;

		/* default message and subject */
		$mailmsg = "";
		$mailsubject = "CapaKaspa";

		$headers = "From: CapaKaspa <".$CFG_MAILADDRESS.">\r\n";
		$headers .= "To: ".$msgTo."\r\n";
		$headers .= "Reply-To: CapaKaspa <".$CFG_MAILADDRESS.">\r\n";

		$res = mail($msgTo, $mailSubject, $mailMsg, $headers);
		
		return $res;
	}
	
	function webchessMail($msgType, $msgTo, $move, $opponent)
	{
		global $CFG_MAILADDRESS;

		/* default message and subject */
		$mailmsg = "";
		$mailsubject = "[CapaKaspa] ";
		
		/* load specific message and subject */
		switch($msgType)
		{
				
			case 'invitation':
				$mailsubject .= _("Vous etes invité à jouer une nouvelle partie");
				$mailmsg = "Le joueur ".$opponent." vous invite à jouer une nouvelle partie.";
				break;
				
			case 'withdrawal':
				$mailsubject .= _("Invitation annulée");
				$mailmsg = "Le joueur ".$opponent." a annulé son invitation pour jouer une nouvelle partie.";
				break;
				
			case 'resignation':
				$mailsubject .= _("Abandon");
				$mailmsg = "Votre adversaire ".$opponent." a abandonné la partie.";
				break;
				
			case 'move':
				$mailsubject = _("Nouveau coup");
				$mailmsg = "Votre adversaire ".$opponent." a joué le coup suivant :";
				$mailmsg .= "\n".$move;
				break;
				
			case 'accepted':
				$mailsubject .= _("Invitation acceptée");
				$mailmsg = "Le joueur ".$opponent." a accepté votre invitation. Une nouvelle partie a commencé.";
				if ($move) {
					$mailmsg .= "\n\n".$opponent." a joint un message :";
					$mailmsg .= "\n".stripslashes(strip_tags($move));
				}
				break;
				
			case 'declined':
				$mailsubject .= _("Invitation refusée");
				$mailmsg = "Le joueur ".$opponent." a refusé votre invitation.";
				if ($move) {
					$mailmsg .= "\n\n".$opponent." a joint un message à son refus :";
					$mailmsg .= "\n".stripslashes(strip_tags($move));
				}
				break;
				
			case 'draw':
				$mailsubject .= _("Proposition de nulle acceptée");
				$mailmsg = "Le joueur ".$opponent." a accepté votre proposition de nulle.\nLa partie s'est terminée sur le score : 1/2-1/2.";
				break;
		}
		
		$mailmsg .= "\n\nCe message a été envoyé automatiquement à partir du site CapaKaspa (http://www.capakaspa.info).\n";
		$mailmsg .= "\nCapaKaspa c'est aussi :\n";
		$mailmsg .= "Le blog (http://blog.capakaspa.info) pour découvrir\n";
		$mailmsg .= "Le forum (http://forum.capakaspa.info) pour partager\n";
		$mailmsg .= "Nous suivre sur Facebook (http://www.facebook.com/capakaspa)\n";
		$mailmsg .= "Nous suivre sur Google+ (http://plus.google.com/114694270583726807082)\n";
		
		$headers = "From: CapaKaspa <".$CFG_MAILADDRESS.">\r\n";
		$headers .= "Reply-To: CapaKaspa <".$CFG_MAILADDRESS.">\r\n";
		
		//mail($msgTo, $mailsubject, $mailmsg, $headers);
	}	
?>