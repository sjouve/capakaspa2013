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
	
	function getPieceNameFR($piecename)
	{
		switch($piecename)
		{
			case 'pawn':
				$pieceNameFR = "pion";
				break;
			case 'knight':
				$pieceNameFR = "cavalier";
				break;
			case 'bishop':
				$pieceNameFR = "fou";
				break;
			case 'rook':
				$pieceNameFR = "tour";
				break;
			case 'queen':
				$pieceNameFR = "dame";
				break;
			case 'king':
				$pieceNameFR = "roi";
				break;
		}
		return $pieceNameFR;
	}
	
	function getColorFR($color, $piece)
	{
		switch($color)
		{
			case 'white':
				if ($piece == "queen" || $piece == "rook")
				{
					$colorFR = "blanche";
				}
				else
				{
					$colorFR = "blanc";
				}
				break;
			case 'black':
				if ($piece == "queen" || $piece == "rook")
				{
					$colorFR = "noire";
				}
				else
				{
					$colorFR = "noir";
				}
				break;
		}
		
		return $colorFR;
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

	function moveToVerbousString($curColor, $piece, $fromRow, $fromCol, $toRow, $toCol, $pieceCaptured, $promotedTo, $isChecked)
	{
		$verbousString = "";
		
		/* ex: white queen from a4 to c6 */
		$verbousString .= getPieceNameFR($piece)." ".getColorFR($curColor, $piece)." de ".chr($fromCol + 97).($fromRow + 1)." vers ".chr($toCol + 97).($toRow + 1);

		/* check for castling */
		if (($piece == "king") && (abs($toCol - $fromCol) == 2))
			$verbousString .= " (roque)";

		/* check for en passant */
		if (($piece == "pawn") && ($toCol != $fromCol) && ($pieceCaptured == ""))
			$verbousString .= " mange le pion en-passant";
			
		if ($pieceCaptured != "")
			$verbousString .= " mange ".getPieceNameFR($pieceCaptured);

		if ($promotedTo != "")
			$verbousString .= "<br>pion promu en ".getPieceNameFR($promotedTo);
		
		return $verbousString;
	}
	
	/* Fonction d'envoi de mail */
	function sendMail($msgTo, $mailSubject, $mailMsg)
	{
		global $CFG_MAILADDRESS;

		/* default message and subject */
		$mailmsg = "";
		$mailsubject = "CapaKaspa";

		$headers .= "From: CapaKaspa <".$CFG_MAILADDRESS.">\r\n";
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
		$mailsubject = "CapaKaspa";
		
		/* load specific message and subject */
		switch($msgType)
		{
			case 'test':
				$mailsubject = "WebChess: Test Message";
				$mailmsg = "Congratulations!!!\n
				If you can see this message, you have successfully setup your email notification!\n\n
				This message has been automatically been sent by WebChess and should not be replied to.\n";
				break;
				
			case 'invitation':
				$mailsubject = "[CapaKaspa] Vous etes invité à jouer une nouvelle partie";
				$mailmsg = "Le joueur ".$opponent." vous invite à jouer une nouvelle partie.";
				break;
				
			case 'withdrawal':
				$mailsubject = "[CapaKaspa] Invitation annulée";
				$mailmsg = "Le joueur ".$opponent." a annulé son invitation pour jouer une nouvelle partie.";
				break;
				
			case 'resignation':
				$mailsubject = "[CapaKaspa] Abandon";
				$mailmsg = "Votre adversaire ".$opponent." a abandonné la partie.";
				break;
				
			case 'move':
				$mailsubject = "[CapaKaspa] Nouveau coup";
				$mailmsg = "Votre adversaire ".$opponent." a joué le coup suivant :";
				$mailmsg .= "\n".$move;
				break;
				
			case 'accepted':
				$mailsubject = "[CapaKaspa] Invitation acceptée";
				$mailmsg = "Le joueur ".$opponent." a accepté votre invitation. Une nouvelle partie a commencé.";
				if ($move) {
					$mailmsg .= "\n\n".$opponent." a joint un message :";
					$mailmsg .= "\n".stripslashes(strip_tags($move));
				}
				break;
				
			case 'declined':
				$mailsubject = "[CapaKaspa] Invitation refusée";
				$mailmsg = "Le joueur ".$opponent." a refusé votre invitation.";
				if ($move) {
					$mailmsg .= "\n\n".$opponent." a joint un message à son refus :";
					$mailmsg .= "\n".stripslashes(strip_tags($move));
				}
				break;
				
			case 'draw':
				$mailsubject = "[CapaKaspa] Proposition de nulle acceptée";
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

	/* returns true if current version of PHP is greater than vercheck */
	/* donated to PHP page (http://www.php.net/manual/en/function.version-compare.php) */
	/* by savetz@northcoast.com and is PHP < 4.1.0 safe */
	function minimum_version( $vercheck ) {
		$minver = explode(".", $vercheck);
		$curver = explode(".", phpversion());
		
		if (($curver[0] < $minver[0])
			|| (($curver[0] == $minver[0])
				&& ($curver[1] < $minver[1]))
			|| (($curver[0] == $minver[0])
				&& ($curver[1] == $minver[1])
				&& ($curver[2][0] < $minver[2][0])))
			return false;
		else
			return true;
	}

	/* allow WebChess to be run on PHP systems < 4.1.0, using old http vars */
	/* heavily based on php4-1-0_varfix.php by Tom Harrison (thetomharrison@hotmail.com) */
	/* only doing the opposite: creating _SESSION, _GET and _POST based on */
	/* their HTTP_*_VARS equivalent */
	function createNewHttpVars($type)
	{
		global $HTTP_POST_VARS, $HTTP_GET_VARS, $HTTP_SESSION_VARS;

		$temp = array();
		switch(strtoupper($type))
		{
			case 'POST':   $temp2 = &$HTTP_POST_VARS;   break;
			case 'GET':    $temp2 = &$HTTP_GET_VARS;    break;
			case 'SESSION':    $temp2 = &$HTTP_SESSION_VARS;    break;
			default: return 0;
		}

		while (list($varname, $varvalue) = each($temp2)) {
			$temp[$varname] = $varvalue;
		}
		
		return ($temp);
	}
	
	function fixOldPHPVersions()
	{
		global $_fixOldPHPVersions;

		if (isset($_fixOldPHPVersions))
			return;
		
		if (!minimum_version("4.1.0"))
		{
			global $_POST, $_GET, $_SESSION;

			$_POST = createNewHttpVars("POST");
			$_GET = createNewHttpVars("GET");
			//$_SESSION = createNewHttpVars("SESSION");
			
			if (!isset($HTTP_SESSION_VARS["_SESSION"]))
				session_register("_SESSION");
		}

		$_fixOldPHPVersions = true;
	}

	// this function was taken from the PHP documentation
	// http://www.php.net/manual/en/function.mt-srand.php
	// seed with microseconds
	function make_seed() {
		list($usec, $sec) = explode(' ', microtime());
		return (float) $sec + ((float) $usec * 100000);
	}
	
	
	// this function was provided to the PHP documentation
	// by houtex_boy@yahoo.com and slightly modified to use
	// the above make_seed()
	// http://www.php.net/manual/en/function.srand.php
	// ensures srand() is only called once
	function init_srand($seed = '')
	{
		static $wascalled = FALSE;
		if (!$wascalled){
			$seed = $seed === '' ? make_seed() : $seed;
			srand($seed);
			$wascalled = TRUE;
		}
	}

	function nbJours($debut, $fin) {
		$tDeb = explode("-", $debut);
		$tFin = explode("-", $fin);
		
		$diff = mktime(0, 0, 0, $tFin[1], $tFin[2], $tFin[0]) - 
		          mktime(0, 0, 0, $tDeb[1], $tDeb[2], $tDeb[0]);
		  
		return(($diff / 86400)+1);
		
	}
	
?>