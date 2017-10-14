<?
/* Depend on:
require_once('dac/dac_players.php');
require 'dac/dac_games.php';
*/

/* Accès aux donnéees concernant la table Games, History, Messages */

/* Return le PGN de la partie */
function getPGN($whiteNick, $blackNick, $type, $flagBishop, $flagKnight, $flagRook, $flagQueen, $chess960, $listeCoups, $gameResult)
{
	$startFEN = "";
	if ($type == 1)
	{
		$startFEN = "1111k111/pppppppp/8/8/8/8/PPPPPPPP/1111K111";
		if ($flagBishop == 1)
		{
			$startFEN[2]="b";
			$startFEN[5]="b";
			$startFEN[37]="B";
			$startFEN[40]="B";			
		}
		if ($flagKnight == 1)
		{
			$startFEN[1]="n";
			$startFEN[6]="n";
			$startFEN[36]="N";
			$startFEN[41]="N";			
		}
		if ($flagRook == 1)
		{
			$startFEN[0]="r";
			$startFEN[7]="r";
			$startFEN[35]="R";
			$startFEN[42]="R";			
		}
		if ($flagQueen == 1)
		{
			$startFEN[3]="q";
			$startFEN[38]="Q";
		}
	}
	if ($type == 2)
	{
		$startFEN = "11111111/pppppppp/8/8/8/8/PPPPPPPP/11111111 w KQkq - 0 1";
		// Init Chess960
		for ($i = 0; $i < 8; $i++)
		{
			$char = $chess960[$i];
			$startFEN[$i]=mb_strtolower($char);
			$startFEN[$i+35]=$char;
		}
	}
	
	$pattern = "[\n\r]";
	$pgnstring = "";
	if ($startFEN != "")
		$pgnstring .= "[FEN \"".$startFEN."\"]\n";
	$pgnstring .= "[Site \"CapaKaspa\"]\n[White \"".$whiteNick."\"]\n[Black \"".$blackNick."\"]\n";
	if ($type == 2) $pgnstring .= "[Variant \"Chess960\"]\n";
	$pgnstring .= "[Result \"".$gameResult."\"]\n";
	$pgnstring .= "\n";
	
	$pgnstring .= mb_eregi_replace($pattern," ",$listeCoups);
		return $pgnstring;
	
}

/* Code ECO d'une position */
function getEco($position)
{
	global $dbh;
	$all_fen_eco = mysqli_query($dbh,"SELECT F.eco eco, F.trait trait, E.name name FROM fen_eco F, eco E WHERE fen = '".$position."' AND F.eco = E.eco");
	$fen_eco = mysqli_fetch_array($all_fen_eco, MYSQLI_ASSOC);
	return $fen_eco;
}

/* Compte le nombre de parties actives pour un tournoi */
function getNbActiveTournamentGames($tournamentID)
{
	$res = countActiveTournamentGames($tournamentID);
	return $res['nbGames'];
}

/* Compte le nombre de parties actives pour un joueur */
function getNbActiveGame($playerID)
{
	$res = countActiveGame($playerID);
	return $res['nbGames'];
}

/* Compte le nombre de parties actives sur le site */
function getNbActiveGameForAll()
{
	$res = countActiveGameForAll();
	return $res['nbGames'];
}

/* Met Ã  jour la date de dernier coup d'une partie */
function updateTimestamp()
{
	
	global $dbh;
	mysqli_query($dbh,"UPDATE games SET lastMove = NOW() WHERE gameID = ".$_POST['gameID']);
}

/* Met Ã  jour la position et le code ECO d'une partie */
function updateGame($gameID, $position, $ecoCode)
{
	
	global $dbh;
	$res = mysqli_query($dbh,"UPDATE games SET lastMove = NOW(), position = '".$position."', eco = '".$ecoCode."' WHERE gameID = ".$gameID);
	return $res;
}


/* 	
 Calcul de la date cible de la partie : date qui ne doit pas dÃ©passer la date du dernier coup
 Prend en compte la cadence
*/
function calculateTargetDate($lastMove, $whitePlayerID, $blackPlayerID, $cadence)
{
	
	$targetDate = date("Y-m-d", mktime(0,0,0, date('m'), date('d') - $cadence, date('Y')));
	return $targetDate;
}

/* I: $gameID
 * O: $history, $numMoves
 */
function loadHistory($gameID)
{
	global $history, $numMoves;
	global $dbh;
	
	$allMoves = mysqli_query($dbh,"SELECT * FROM history WHERE gameID = ".$gameID." ORDER BY timeOfMove");

	$numMoves = -1;
	while ($thisMove = mysqli_fetch_array($allMoves, MYSQLI_ASSOC))
	{
		$numMoves++;
		$history[$numMoves] = $thisMove;
	}
}

/* I: $history, $board, $isPromoting, $numMoves, $isInCheck
 * O: $history, $isPromoting, $numMoves
*/
function saveHistory($gameType)
{
	global $board, $isPromoting, $history, $numMoves, $isInCheck, $isChess960Castling;
	global $dbh;
	
	/* set destination row for pawn promotion */
	if ($board[$_POST['fromRow']][$_POST['fromCol']] & BLACK)
		$targetRow = 0;
	else
		$targetRow = 7;
	
	/* determine if move results in pawn promotion */
	if ((($board[$_POST['fromRow']][$_POST['fromCol']] & COLOR_MASK) == PAWN) && ($_POST['toRow'] == $targetRow))
		$isPromoting = true;
	else
		$isPromoting = false;
		
		
	/* determine chess960 castling : from piece and to piece have same colour */
	$isChess960Castling = false;
	if ($gameType == 2) {
		if (
				(
					($board[$_POST['fromRow']][$_POST['fromCol']] == (KING | WHITE)) 
					&& 
					($board[$_POST['toRow']][$_POST['toCol']] == (ROOK | WHITE))
				)
				||
				(
					($board[$_POST['fromRow']][$_POST['fromCol']] == (KING | BLACK)) 
					&& 
					($board[$_POST['toRow']][$_POST['toCol']] == (ROOK | BLACK))
				)
			)
		{
			$isChess960Castling = true;
		}
	}
	
	
		
	/* determine who's playing based on number of moves so far */
	if (($numMoves == -1) || ($numMoves % 2 == 1))
	{
		$curColor = "white";
		$oppColor = "black";
		$targetRow = 7;
	}
	else
	{
		$curColor = "black";
		$oppColor = "white";
		$targetRow = 0;
	}

	/* add move to history */
	$numMoves++;
	$history[$numMoves]['gamedID'] = $_POST['gameID'];
	$history[$numMoves]['curPiece'] = getPieceName($board[$_POST['fromRow']][$_POST['fromCol']]);
	$history[$numMoves]['curColor'] = $curColor;
	$history[$numMoves]['fromRow'] = $_POST['fromRow'];
	$history[$numMoves]['fromCol'] = $_POST['fromCol'];
	$history[$numMoves]['toRow'] = $_POST['toRow'];
	$history[$numMoves]['toCol'] = $_POST['toCol'];
	if ($isPromoting)
		$history[$numMoves]['promotedTo'] = getPieceName($_POST['promotion']);
	else 
		$history[$numMoves]['promotedTo'] = null;

	if ($isInCheck)
		$history[$numMoves]['isInCheck'] = 1;
	else
		$history[$numMoves]['isInCheck'] = 0;

	if (DEBUG)
	{
		if ($history[$numMoves]['curPiece'] == '')
			echo ("WARNING!!!  missing piece at ".$_POST['fromRow'].", ".$_POST['fromCol'].": ".$board[$_POST['fromRow']][$_POST['fromCol']]."<p>\n");
	}

	if ($board[$_POST['toRow']][$_POST['toCol']] == 0)
	{
		// Prise en passant
		if ($history[$numMoves]['curPiece'] == "pawn" and $history[$numMoves]['fromCol'] != $history[$numMoves]['toCol'])
		{
			$tmpQuery = "INSERT INTO history (timeOfMove, gameID, curPiece, curColor, fromRow, fromCol, toRow, toCol, replaced, promotedTo, isInCheck) VALUES (Now(), ".$_POST['gameID'].", '".getPieceName($board[$_POST['fromRow']][$_POST['fromCol']])."', '$curColor', ".$_POST['fromRow'].", ".$_POST['fromCol'].", ".$_POST['toRow'].", ".$_POST['toCol'].", 'pawn', null, ".$history[$numMoves]['isInCheck'].")"; 

			$history[$numMoves]['replaced'] = "pawn";
			$tmpReplaced = $history[$numMoves]['replaced'];	
		}
		else
		{
			if ($isPromoting)
				$tmpQuery = "INSERT INTO history (timeOfMove, gameID, curPiece, curColor, fromRow, fromCol, toRow, toCol, replaced, promotedTo, isInCheck) VALUES (Now(), ".$_POST['gameID'].", '".getPieceName($board[$_POST['fromRow']][$_POST['fromCol']])."', '$curColor', ".$_POST['fromRow'].", ".$_POST['fromCol'].", ".$_POST['toRow'].", ".$_POST['toCol'].", null, '".getPieceName($_POST['promotion'])."', ".$history[$numMoves]['isInCheck'].")";
			else
				$tmpQuery = "INSERT INTO history (timeOfMove, gameID, curPiece, curColor, fromRow, fromCol, toRow, toCol, replaced, promotedTo, isInCheck) VALUES (Now(), ".$_POST['gameID'].", '".getPieceName($board[$_POST['fromRow']][$_POST['fromCol']])."', '$curColor', ".$_POST['fromRow'].", ".$_POST['fromCol'].", ".$_POST['toRow'].", ".$_POST['toCol'].", null, null, ".$history[$numMoves]['isInCheck'].")";
			$history[$numMoves]['replaced'] = null;
			$tmpReplaced = "";
		}
		
	}
	else
	{
		if ($isChess960Castling)
			$history[$numMoves]['replaced'] = "chess960";
		else 
			$history[$numMoves]['replaced'] = getPieceName($board[$_POST['toRow']][$_POST['toCol']]);
			
		$tmpReplaced = $history[$numMoves]['replaced'];
		
		if ($isPromoting)
			$tmpQuery = "INSERT INTO history (timeOfMove, gameID, curPiece, curColor, fromRow, fromCol, toRow, toCol, replaced, promotedTo, isInCheck) VALUES (Now(), ".$_POST['gameID'].", '".getPieceName($board[$_POST['fromRow']][$_POST['fromCol']])."', '$curColor', ".$_POST['fromRow'].", ".$_POST['fromCol'].", ".$_POST['toRow'].", ".$_POST['toCol'].", '".$history[$numMoves]['replaced']."', '".getPieceName($_POST['promotion'])."', ".$history[$numMoves]['isInCheck'].")"; 
		else
			$tmpQuery = "INSERT INTO history (timeOfMove, gameID, curPiece, curColor, fromRow, fromCol, toRow, toCol, replaced, promotedTo, isInCheck) VALUES (Now(), ".$_POST['gameID'].", '".getPieceName($board[$_POST['fromRow']][$_POST['fromCol']])."', '$curColor', ".$_POST['fromRow'].", ".$_POST['fromCol'].", ".$_POST['toRow'].", ".$_POST['toCol'].", '".$history[$numMoves]['replaced']."', null, ".$history[$numMoves]['isInCheck'].")"; 
		
	}

	$res = mysqli_query($dbh,$tmpQuery);
	if ($res)
		return TRUE;
	else
		return FALSE;
	
}

function sendEmailNotification($history, $isPromoting, $numMoves, $isInCheck)
{
	/* if move does not result in a pawn's promotion... */
	/* NOTE: moves resulting in pawn promotion are handled by savePromotion() above */
	
	$oppColor = getTurnColor($numMoves);
	$strMove = moveToPGNString($history[$numMoves]['curColor'], $history[$numMoves]['curPiece'], $history[$numMoves]['fromRow'], $history[$numMoves]['fromCol'], $history[$numMoves]['toRow'], $history[$numMoves]['toCol'], $history[$numMoves]['replaced'], $history[$numMoves]['promotedTo'], $isInCheck);
	
	// Notification
	chessNotification('move', $oppColor, $strMove, $_SESSION['nick'], $_POST['gameID']);
	if (($numMoves == -1) || ($numMoves % 2 == 1))
	{
		$numMovePGN = $numMoves / 2 + 0.5;
		$numMovePGN .= "..";
	}
	else
		$numMovePGN = ($numMoves / 2 + 1);
		
	// Activity
	if (isset($_POST['chkShareMove']))
		insertActivity($_SESSION['playerID'], GAME, $_POST['gameID'], $numMovePGN.".".$strMove, 'move');
}

/* I: $gameID, $numMoves
 * O: $board, $playersColor, $tmpGame
*/
function loadGame($gameID, $numMoves)
{
	global $board, $playersColor, $ecoCode, $ecoName;
	global $dbh;
	
	$tmpGame = getGame($gameID);
	
	$ecoCode = $tmpGame['eco'];
	$ecoName = $tmpGame['ecoName'];
	
	// Remplir l'échiquier
	$strPos = 0;
	for ($i = 0; $i < 8; $i++)
		for ($j = 0; $j < 8; $j++)
		{
			$board[$i][$j] = getPieceCodeChar($tmpGame['position']{$strPos});
			$strPos++;
		}
		
	// Couleur du joueur qui charge la partie
	if ($tmpGame['whitePlayer'] == $_SESSION['playerID'])
		$playersColor = "white";
	else if ($tmpGame['blackPlayer'] == $_SESSION['playerID'])
		$playersColor = "black";
	else
		// Le joueur ne joue pas la partie
		$playerColor = "";
	
	// A qui le tour
	if (($numMoves == -1) || ($numMoves % 2 == 1))
		$turnColor = "white";
	else
		$turnColor = "black";
		
	// Dépassement délai entre 2 coups
	$dateLastMove = new DateTime($tmpGame['lastMove']);
	$dateNow = new DateTime("now");
	$dateLastMove->add(new DateInterval("P".$tmpGame['timeMove']."D"));
	
	if ($dateLastMove < $dateNow && $tmpGame['gameMessage'] == "")
	{
		// Terminer la partie si dépassement de temps
		// Dans cas lastMove est mis à jour pour prise en compte calcul Elo
		$res = mysqli_query($dbh,"UPDATE games 
							SET gameMessage = 'playerResigned', 
								messageFrom = '".$turnColor."',
								lastMove = NOW()
							WHERE gameMessage IS NULL 
							AND gameID = ".$_POST['gameID']);
		
		// Notification for time expiration 
		$whitePrefResult = getPrefValue($tmpGame["whitePlayer"], "shareresult");
		$blackPrefResult = getPrefValue($tmpGame["blackPlayer"], "shareresult");
		if ($turnColor == "white")
		{
			$whiteResult = "lost";
			$blackResult = "won";
		}
		else
		{
			$whiteResult = "won";
			$blackResult = "lost";	
		}
		// Email
		chessNotification('time', "black", '', $tmpGame['whiteNick'], $tmpGame['gameID']);
		chessNotification('time', "white", '', $tmpGame['blackNick'], $tmpGame['gameID']);
		// Activity
		if ($whitePrefResult == "oui")
			insertActivity($tmpGame["whitePlayer"], GAME, $tmpGame["gameID"], $whiteResult, "time");
		if ($blackPrefResult == "oui")
			insertActivity($tmpGame["blackPlayer"], GAME, $tmpGame["gameID"], $blackResult, "time");

	}
	
	return $tmpGame;
	
}

function saveGame()
{
	// TODO Vérifier impact $ecoCode, $ecoName
	global $board, $playersColor, $ecoCode, $ecoName, $numMoves;
	
	// Sauvegarde de l'échiquier sous la forme d'une chaîne de 64 caractères
	// tcfdrfct pppppppp 00000000 00000000 00000000 00000000 PPPPPPPP TCFDRFCT
	
	$position = "";
	
	// Construire la chaîne de la position courante à partir de l'échiquier
	// Pour chaque ligne
	for ($i = 0; $i < 8; $i++)
	{
		// Pour chaque colonne
		for ($j = 0; $j < 8; $j++)
		{
			$position .= getPieceChar($board[$i][$j]);
			
		}
	}
	
	// A qui le tour
	if (($numMoves == -1) || ($numMoves % 2 == 1))
		$turnColor = "w";
	else
		$turnColor = "b";
		
	// Contrôle code ECO de la position
	$fen_eco = getEco($position);
	$turnColorEco = "";
	$newEco = "";
	if ($fen_eco)
	{
		$newEco = $fen_eco['eco'];
		$turnColorEco = $fen_eco['trait'];
	};
	
	if ($newEco != $ecoCode && $turnColorEco == $turnColor)
	{
		$ecoCode = $newEco;
		$ecoName = $fen_eco['name'];
	}
	
			
	// Mettre à  jour la date du dernier coup et la position
	$res = updateGame($_POST['gameID'], $position, $ecoCode);
	return $res;
}

function processMessages($tmpGame)
{
	global $isUndoRequested, $isDrawRequested, $isUndoing, $isGameOver, $isCheckMate, $playersColor, $statusMessage;
	global $dbh;
	
	if (DEBUG)
		echo("Entering processMessages()<br>\n");
	
	$isUndoRequested = false;
	$isGameOver = false;
	
	if ($playersColor == "white") {
		$opponentColor = "black";
		$opponentID = $tmpGame['blackPlayer']; 
	}
	else {
		$opponentColor = "white";
		$opponentID = $tmpGame['whitePlayer'];
	}
	
	$oppPrefResult = getPrefValue($opponentID, "shareresult");
	
	/* *********************************************** */
	/* queue user generated (ie: using forms) messages */
	/* *********************************************** */
	if (DEBUG)
		echo("Processing user generated (ie: form) messages...<br>\n");

	/* queue a request for an undo */
	$Test = isset($_POST['requestUndo']) ? $_POST['requestUndo']:Null;
	if ($Test == "yes")
	{
		$tmpQuery = "INSERT INTO messages (gameID, msgType, msgStatus, destination) VALUES (".$tmpGame['gameID'].", 'undo', 'request', '".$opponentColor."')";
		mysqli_query($dbh,$tmpQuery);
		
		updateTimestamp();
	}
	
	/* queue a request for a draw */
	$Test = isset($_POST['requestDraw']) ? $_POST['requestDraw']:Null;
	if ($Test == "yes")
	{
		$tmpQuery = "INSERT INTO messages (gameID, msgType, msgStatus, destination) VALUES (".$tmpGame['gameID'].", 'draw', 'request', '".$opponentColor."')";
		mysqli_query($dbh,$tmpQuery);
		
		updateTimestamp();
	}

	/* response to a request for an undo */
	if (isset($_POST['undoResponse']))
	{
		if ($_POST['isUndoResponseDone'] == 'yes')
		{
			if ($_POST['undoResponse'] == "yes")
			{
				$tmpStatus = "approved";
				$isUndoing = true;
			}
			else
				$tmpStatus = "denied";
		
			$tmpQuery = "UPDATE messages SET msgStatus = '".$tmpStatus."', destination = '".$opponentColor."' WHERE gameID = ".$tmpGame['gameID']." AND msgType = 'undo' AND msgStatus = 'request' AND destination = '".$playersColor."'";
			mysqli_query($dbh,$tmpQuery);
		
			updateTimestamp();
		}
	}
	
	/* response to a request for a draw */
	if (isset($_POST['drawResponse']))
	{
		if ($_POST['isDrawResponseDone'] == 'yes')
		{
			if ($_POST['drawResponse'] == "yes")
			{
				$tmpStatus = "approved";
				$tmpQuery = "UPDATE games SET gameMessage = 'draw', messageFrom = '".$playersColor."' WHERE gameID = ".$tmpGame['gameID'];
				mysqli_query($dbh,$tmpQuery);				
			}
			else
				$tmpStatus = "denied";
		
			$tmpQuery = "UPDATE messages SET msgStatus = '".$tmpStatus."', destination = '".$opponentColor."' WHERE gameID = ".$tmpGame['gameID']." AND msgType = 'draw' AND msgStatus = 'request' AND destination = '".$playersColor."'";
			mysqli_query($dbh,$tmpQuery);

			updateTimestamp();
			
			/* if email notification is activated... */
			if ($tmpStatus == "approved")
			{
				/* Notification */
				chessNotification('draw', $opponentColor, '', $_SESSION['nick'], $tmpGame['gameID']);
				if ($_SESSION['pref_shareresult'] == 'oui')	
					insertActivity($_SESSION['playerID'], GAME, $tmpGame['gameID'], "", 'draw');
				if ($oppPrefResult == 'oui')
					insertActivity($opponentID, GAME, $tmpGame['gameID'], "", 'draw');
			}
		}
	}
		
	/* resign the game */
	$Test = isset($_POST['resign']) ? $_POST['resign']:Null;
	if ($Test == "yes")
	{
		$tmpQuery = "UPDATE games SET gameMessage = 'playerResigned', messageFrom = '".$playersColor."' WHERE gameID = ".$tmpGame['gameID'];
		mysqli_query($dbh,$tmpQuery);

		updateTimestamp();

		/* Notification */
		chessNotification('resignation', $opponentColor, '', $_SESSION['nick'], $tmpGame['gameID']);
		if ($_SESSION['pref_shareresult'] == 'oui')
			insertActivity($_SESSION['playerID'], GAME, $tmpGame['gameID'], "lost", 'resignation');
		if ($oppPrefResult == 'oui')
			insertActivity($opponentID, GAME, $tmpGame['gameID'], "won", 'resignation');
			
	}
	
	
	/* ******************************************* */
	/* process queued messages (ie: from database) */
	/* ******************************************* */
	$tmpQuery = "SELECT * FROM messages WHERE gameID = ".$tmpGame['gameID']." AND destination = '".$playersColor."'";
	$tmpMessages = mysqli_query($dbh,$tmpQuery);

	while($tmpMessage = mysqli_fetch_array($tmpMessages, MYSQLI_ASSOC))
	{
		switch($tmpMessage['msgType'])
		{
			case 'undo':
				switch($tmpMessage['msgStatus'])
				{
					case 'request':
						$isUndoRequested = true;
						break;
					case 'approved':
						$tmpQuery = "DELETE FROM messages WHERE gameID = ".$tmpGame['gameID']." AND msgType = 'undo' AND msgStatus = 'approved' AND destination = '".$playersColor."'";
						mysqli_query($dbh,$tmpQuery);
						$statusMessage .= _("Move cancellation accepted")."\n";
						break;
					case 'denied':
						$isUndoing = false;
						$tmpQuery = "DELETE FROM messages WHERE gameID = ".$tmpGame['gameID']." AND msgType = 'undo' AND msgStatus = 'denied' AND destination = '".$playersColor."'";
						mysqli_query($dbh,$tmpQuery);
						$statusMessage .= _("Move cancellation refused")."\n";
						break;
				}
				break;
			
			case 'draw':
				switch($tmpMessage['msgStatus'])
				{
					case 'request':
						$isDrawRequested = true;
						break;
					case 'approved':
						$tmpQuery = "DELETE FROM messages WHERE gameID = ".$tmpGame['gameID']." AND msgType = 'draw' AND msgStatus = 'approved' AND destination = '".$playersColor."'";
						mysqli_query($dbh,$tmpQuery);
						$statusMessage .= _("Draw proposal accepted")."\n";
						break;
					case 'denied':
						$tmpQuery = "DELETE FROM messages WHERE gameID = ".$tmpGame['gameID']." AND msgType = 'draw' AND msgStatus = 'denied' AND destination = '".$playersColor."'";
						mysqli_query($dbh,$tmpQuery);
						$statusMessage .= _("Draw proposal refused")."\n";
						break;
				}
				break;
		}
	}

	/* requests pending */
	$tmpQuery = "SELECT * FROM messages WHERE gameID = ".$tmpGame['gameID']." AND msgStatus = 'request' AND destination = '".$opponentColor."'";
	$tmpMessages = mysqli_query($dbh,$tmpQuery);

	while($tmpMessage = mysqli_fetch_array($tmpMessages, MYSQLI_ASSOC))
	{
		switch($tmpMessage['msgType'])
		{
			case 'undo':
				$statusMessage .= _("Move cancellation pending")."\n";
				break;
			case 'draw':
				$statusMessage .= _("Draw proposal pending...")."\n";
				break;
		}
	}	
	
	/* game level status: draws, resignations and checkmate */
	/* if checkmate, update games table */
	$Test = isset($_POST['isCheckMate']) ? $_POST['isCheckMate']:Null;
	if ($Test == 'true')
	{
		mysqli_query($dbh,"UPDATE games SET gameMessage = 'checkMate', messageFrom = '".$playersColor."' WHERE gameID = ".$tmpGame['gameID']);
		/* Notification */
		chessNotification('checkmate', $opponentColor, '', $_SESSION['nick'], $tmpGame['gameID']);
		if ($_SESSION['pref_shareresult'] == 'oui')
			insertActivity($_SESSION['playerID'], GAME, $tmpGame['gameID'], "won", 'checkmate');
		if ($oppPrefResult == 'oui')
			insertActivity($opponentID, GAME, $tmpGame['gameID'], "lost", 'checkmate');
	}
	
	/* draw by rules */
	$Test = isset($_POST['drawResult']) ? $_POST['drawResult']:Null;
	if ($Test == 'true')
	{
		$tmpQuery = "UPDATE games SET gameMessage = 'draw', messageFrom = '".$playersColor."' WHERE gameID = ".$tmpGame['gameID'];
		mysqli_query($dbh,$tmpQuery);
		/* Notification */
		chessNotification('drawrule', $opponentColor, '', $_SESSION['nick'], $tmpGame['gameID']);
		if ($_SESSION['pref_shareresult'] == 'oui')
			insertActivity($_SESSION['playerID'], GAME, $tmpGame['gameID'], "", 'drawrule');
		if ($oppPrefResult == 'oui')
			insertActivity($opponentID, GAME, $tmpGame['gameID'], "", 'drawrule');
	}
	
	$gameResult = "";
	
	$tmpQuery = "SELECT gameMessage, messageFrom FROM games WHERE gameID = ".$tmpGame['gameID'];
	$tmpMessages = mysqli_query($dbh,$tmpQuery);
	$tmpMessage = mysqli_fetch_array($tmpMessages, MYSQLI_ASSOC);
	
	if ($tmpMessage['gameMessage'] == "draw")
	{
		$statusMessage .= _("Draw game")." (1/2-1/2)\n";
		$gameResult = "1/2-1/2";
		$isGameOver = true;
	}
	
	if ($tmpMessage['messageFrom'] == "white")
	{
		$tmpColor = _("Whites");
	} else
	{
		$tmpColor = _("Blacks");
	}
	
	if ($tmpMessage['gameMessage'] == "playerResigned")
	{
		if ($tmpMessage['messageFrom'] == "white") {
			$strResult = " (0-1)";
			$gameResult = "0-1";
		}
		else {
			$strResult = " (1-0)";
			$gameResult = "1-0";
		}
		
		$statusMessage .= $tmpColor." "._("resigned").$strResult."\n";
		$isGameOver = true;
	}

	if ($tmpMessage['gameMessage'] == "checkMate")
	{
		if ($tmpMessage['messageFrom'] == "white") {
			$strResult = " (1-0)";
			$gameResult = "1-0";
		}
		else {
			$strResult = " (0-1)";
			$gameResult = "0-1";
		}
		
		$statusMessage .= _("Check and Mat!")." ".$tmpColor." "._("win the game").$strResult."\n";
		$isGameOver = true;
		$isCheckMate = true;
	}
	return $gameResult;
}

/* functions for outputting to html and javascript */

/* Miniature */
function drawboardGame($gameID, $whitePlayer, $blackPlayer, $position, $nbMoves)
{

	global $isPlayersTurn;
	
	// Nombre de 1/2 coups
	$numMoves = $nbMoves - 1;

	// Remplir l'échiquier
	if (!isset($position) || $position == "") $position = "tcfdrfctpppppppp00000000000000000000000000000000PPPPPPPPTCFDRFCT";
	$strPos = 0;
	for ($i = 0; $i < 8; $i++)
		for ($j = 0; $j < 8; $j++)
		{
			$board[$i][$j] = getPieceCodeChar($position{$strPos});
			$strPos++;
		}

	// Couleur du joueur qui charge la partie
	if ($whitePlayer == $_SESSION['playerID']) 
		$playersColor = "white";
	else if ($blackPlayer == $_SESSION['playerID'])
		$playersColor = "black";
	else
		//Le joueur ne joue pas la partie
		$playersColor = "";

	/* find out if it's the current player's turn */
	if (( (($numMoves == -1) || (($numMoves % 2) == 1)) && ($playersColor == "white")) || ((($numMoves % 2) == 0) && ($playersColor == "black")) )
		$isPlayersTurn = true;
	else
		$isPlayersTurn = false;

	/* determine who's perspective of the board to show */
	$perspective = $playersColor;

	echo ("<table border='0' bgcolor='#000000' cellpadding='0' cellspacing='1'>
			<tr>
				<td>
					<table bgcolor='#ffffff' border='0' cellpadding='0' cellspacing='0'>\n");

	/* setup vars to show player's perspective of the board */
	if ($perspective == "white")
	{
		$topRow = 7;
		$bottomRow = 0;
		$rowStep = -1;
	
		$leftCol = 0;
		$rightCol = 7;
		$colStep = 1;
	}
	else
	{
		$topRow = 0;
		$bottomRow = 7;
		$rowStep = 1;
	
		$leftCol = 7;
		$rightCol = 0;
		$colStep = -1;
	}

	/* for each row... */
	/* NOTE: end condition is ($bottomRow + $rowStep) since we want to output $bottomRow */
	for ($i = $topRow; $i != ($bottomRow + $rowStep); $i += $rowStep)
	{
		echo ("<tr>\n");

		/* for each col... */
		/* NOTE: end condition is ($rightCol + $colStep) since we want to output $rightCol */
		for ($j = $leftCol; $j != ($rightCol + $colStep); $j += $colStep)
		{
			echo ("   <td bgcolor='");
	
			if (($j + ($i % 2)) % 2 == 0)
				echo ("#9B6A15'>");
			else
				echo ("#F2A521'>");
	
			echo ("<img style='vertical-align: middle' name='pos$i-$j' src='pgn4web/".$_SESSION['pref_theme']."/20/");
	
			/* if position is empty... */
			if ($board[$i][$j] == 0)
			{
				/* draw empty square */
				$tmpALT="clear";
			}
			else
			{
				/* draw correct piece */
				if ($board[$i][$j] & BLACK)
					$tmpALT = "b";
				else
					$tmpALT = "w";
		
				$tmpALT .= getPieceCharForImage($board[$i][$j]);
			}
	
			echo($tmpALT.".png' border='0' alt='".$tmpALT."'>");
			echo ("</td>\n");
		}

		echo ("</tr>\n");
	}

	echo ("</table></td></tr></table>\n\n");
}

/* Utilisé dans l'écran d'une partie */
function drawboard($withCoord, $size)
{
	global $board, $playersColor, $numMoves, $nb_game_vacation;
	
	/* find out if it's the current player's turn */
	if (( (($numMoves == -1) || (($numMoves % 2) == 1)) && ($playersColor == "white")) || ((($numMoves % 2) == 0) && ($playersColor == "black")) )
		$isPlayersTurn = true;
	else
		$isPlayersTurn = false;
	
	/* determine who's perspective of the board to show */
	if ($playersColor != "")
	{
		$perspective = $playersColor;
	}
	else
	{
		$perspective = "white";
	}
			
	/* determine if board is disabled */
	$isDisabled = isBoardDisabled();

	/* setup vars to show player's perspective of the board */
	if ($perspective == "white")
	{
		$topRow = 7;
		$bottomRow = 0;
		$rowStep = -1;
	
		$leftCol = 0;
		$rightCol = 7;
		$colStep = 1;
	}
	else
	{
		$topRow = 0;
		$bottomRow = 7;
		$rowStep = 1;
		
		$leftCol = 7;
		$rightCol = 0;
		$colStep = -1;
	}

	echo ("<table class='boardTable' border='0' cellspacing='0'>\n");

	if ($withCoord)
	{
		if ($isDisabled)
			echo ("<tr bgcolor='#DDDDDD'>");
		else
			echo ("<tr bgcolor='beige'>");
			
		/* column headers */
		echo ("<th>&nbsp;</th>");
	
		/* NOTE: end condition is ($rightCol + $colStep) since we want to output $rightCol */
		for ($i = $leftCol; $i != ($rightCol + $colStep); $i += $colStep)
			echo ("<th>".chr($i + 97)."</th>");
	
		echo ("</tr>\n");
	}

	/* for each row... */
	/* NOTE: end condition is ($bottomRow + $rowStep) since we want to output $bottomRow */
	for ($i = $topRow; $i != ($bottomRow + $rowStep); $i += $rowStep)
	{
		echo ("<tr>\n");
		if ($withCoord)
		{
			if ($isDisabled)
				echo ("<th width='20' bgcolor='#DDDDDD'>".($i+1)."</th>\n");
			else
				echo ("<th width='20' bgcolor='beige'>".($i+1)."</th>\n");
		}
			
		/* for each col... */
		/* NOTE: end condition is ($rightCol + $colStep) since we want to output $rightCol */
		for ($j = $leftCol; $j != ($rightCol + $colStep); $j += $colStep)
		{
			echo ("   <td title='".chr($j + 65).($i+1)."' id='".$i.$j."' style='padding: 0px;' bgcolor='");

			/* if board is disabled, show board in grayscale */
			if ($isDisabled)
			{
				if (($j + ($i % 2)) % 2 == 0)
					echo ("#666666'>");
				else
					echo ("#BBBBBB'>");
			}
			else
			{
				if (($j + ($i % 2)) % 2 == 0)
					echo ("#9B6A15'>");
				else
					echo ("#F2A521'>");
			}

			/* if disabled or not player's turn, can't click pieces */
			if (!$isDisabled && $isPlayersTurn)
			{
				echo ("<a href='JavaScript:squareClicked($i, $j, ");
				if ($board[$i][$j] == 0)
					echo ("true)'>");
				else
					echo ("false)'>");
			}

			echo ("<img style='vertical-align: middle' name='pos$i-$j' src='pgn4web/".$_SESSION['pref_theme']."/".$size."/");

			/* if position is empty... */
			if ($board[$i][$j] == 0)
			{
				/* draw empty square */
				$tmpALT="clear";
			}
			else
			{
				/* draw correct piece */
				if ($board[$i][$j] & BLACK)
					$tmpALT = "b";
				else
					$tmpALT = "w";

				$tmpALT .= getPieceCharForImage($board[$i][$j]);
			}

			echo($tmpALT.".png' border='0' alt='".$tmpALT."'>");

			if (!$isDisabled && $isPlayersTurn)
				echo ("</a>");

			echo ("</td>\n");
		}

		echo ("</tr>\n");
	}

	echo ("</table>\n\n");
}

function writeJSboard($board, $numMoves)
{
	
	/* write out constants */
	echo ("var DEBUG = ".DEBUG.";\n");
	
	echo ("var CURRENTTHEME = '".$_SESSION['pref_theme']."';\n");
	echo ("var PAWN = ".PAWN.";\n");
	echo ("var KNIGHT = ".KNIGHT.";\n");
	echo ("var BISHOP = ".BISHOP.";\n");
	echo ("var ROOK = ".ROOK.";\n");
	echo ("var QUEEN = ".QUEEN.";\n");
	echo ("var KING = ".KING.";\n");
	echo ("var BLACK = ".BLACK.";\n");
	echo ("var WHITE = ".WHITE.";\n");
	echo ("var COLOR_MASK = ".COLOR_MASK.";\n");
	
	/* write code for array */
	echo ("var board = new Array();\n");
	for ($i = 0; $i < 8; $i++)
	{
		echo ("board[$i] = new Array();\n");

		for ($j = 0; $j < 8; $j++)
		{
			echo ("board[$i][$j] = ".$board[$i][$j].";\n");
		}
	}

	echo("var numMoves = $numMoves;\n");
	echo("var errMsg = '';\n");	/* global var used for error messages */
}

/* provide history data to javascript function */
/* NOTE: currently, only pawn validation script uses history */
function writeJSHistory($history, $numMoves)
{
	
	/* write out constants */
	echo ("var CURPIECE = 0;\n");
	echo ("var CURCOLOR = 1;\n");
	echo ("var FROMROW = 2;\n");
	echo ("var FROMCOL = 3;\n");
	echo ("var TOROW = 4;\n");
	echo ("var TOCOL = 5;\n");
	
	/* write code for array */
	echo ("var chessHistory = new Array();\n");
	for ($i = 0; $i <= $numMoves; $i++)
	{
		echo ("chessHistory[$i] = new Array();\n");
		echo ("chessHistory[$i][CURPIECE] = '".$history[$i]['curPiece']."';\n");
		echo ("chessHistory[$i][CURCOLOR] = '".$history[$i]['curColor']."';\n");
		echo ("chessHistory[$i][FROMROW] = ".$history[$i]['fromRow'].";\n");
		echo ("chessHistory[$i][FROMCOL] = ".$history[$i]['fromCol'].";\n");
		echo ("chessHistory[$i][TOROW] = ".$history[$i]['toRow'].";\n");
		echo ("chessHistory[$i][TOCOL] = ".$history[$i]['toCol'].";\n");
	}
}


function writeHistoryPGN($history, $numMoves)
{

	$listeCoups = "";

	for ($i = 0; $i <= $numMoves; $i+=2)
	{
		/* Une ligne */
		$listeCoups = $listeCoups.(($i/2) + 1).". ";
	
		$tmpReplaced = "";
		if (!is_null($history[$i]['replaced']))
			$tmpReplaced = $history[$i]['replaced'];
	
		$tmpPromotedTo = "";
		if (!is_null($history[$i]['promotedTo']))
			$tmpPromotedTo = $history[$i]['promotedTo'];
	
		$tmpCheck = ($history[$i]['isInCheck'] == 1);
		/* Coup des blancs */
		$listeCoups = $listeCoups.moveToPGNString($history[$i]['curColor'], $history[$i]['curPiece'], $history[$i]['fromRow'], $history[$i]['fromCol'], $history[$i]['toRow'], $history[$i]['toCol'], $tmpReplaced, $tmpPromotedTo, $tmpCheck)." ";
	
		if ($i == $numMoves)
			/* Le dernier coup est blanc */
			$listeCoups = $listeCoups."\n";
		else
		{
			$tmpReplaced = "";
			if (!is_null($history[$i+1]['replaced']))
				$tmpReplaced = $history[$i+1]['replaced'];
	
			$tmpPromotedTo = "";
			if (!is_null($history[$i+1]['promotedTo']))
				$tmpPromotedTo = $history[$i+1]['promotedTo'];
	
			$tmpCheck = ($history[$i+1]['isInCheck'] == 1);
			/* Coup des noirs */
			$listeCoups = $listeCoups.moveToPGNString($history[$i+1]['curColor'], $history[$i+1]['curPiece'], $history[$i+1]['fromRow'], $history[$i+1]['fromCol'], $history[$i+1]['toRow'], $history[$i+1]['toCol'], $tmpReplaced, $tmpPromotedTo, $tmpCheck)."\n";
		}
	
	}
 
	return $listeCoups;
}

function writeStatus($tmpGame)
{
	global $numMoves, $history, $isCheckMate, $statusMessage, $isPlayersTurn, $ecoCode, $ecoName;
	
	$fmt = new IntlDateFormatter(getenv("LC_ALL"), IntlDateFormatter::SHORT, IntlDateFormatter::SHORT);
	
	$expirationDate = new DateTime($tmpGame['expirationDate']);
	$strExpirationDate = $fmt->format($expirationDate);
	
	// Elo
	if ($tmpGame['type'] == 2)
	{
		$whiteElo = $tmpGame['whiteElo960'];
		$blackElo = $tmpGame['blackElo960'];
	}
	else
	{
		$whiteElo = $tmpGame['whiteElo'];
		$blackElo = $tmpGame['blackElo'];
	}
	
	?>
	<div id="gamestatus">
	<table border="0" align="center" style="width: 100%;" cellspacing="0" cellpadding="0">
	<tr bgcolor="#FFFFFF" valign="top">
		<th width="10%" align="left">
			<img src="<?echo(getPicturePath($tmpGame['whiteSocialNet'], $tmpGame['whiteSocialID']));?>" width="40" height="40" style="margin:3px;"/>
		</th>
		<th width="40%" align="left">
	    <?
          	if ($isPlayersTurn)
          	{
          		echo("<div class='playername'><a href='player_view.php?playerID=".$tmpGame['whitePlayer']."'>".$tmpGame['whiteNick']."</a><br/>".$whiteElo);
          		if (getOnlinePlayer($tmpGame['whitePlayer'])) echo (" <img src='images/user_online.gif' title='"._("Player online")."' alt='"._("Player online")."'>");
          		if ($tmpGame['whiteNick'] == $_SESSION['nick']) echo (" <img src='images/hand.gif' title='"._("Player turn")."' alt='"._("Player turn")."'>");
          		echo("</div>");
          	}
          	else
          	{
          		if ($tmpGame['whiteNick'] == $_SESSION['nick'] || $tmpGame['blackNick'] == $_SESSION['nick'])
          		{
          			echo("<div class='playername'><a href='player_view.php?playerID=".$tmpGame['whitePlayer']."'>".$tmpGame['whiteNick']."</a><br/>".$whiteElo);
          			if (getOnlinePlayer($tmpGame['whitePlayer'])) echo (" <img src='images/user_online.gif' title='"._("Player online")."' alt='"._("Player online")."'>");
          			if ($tmpGame['whiteNick'] != $_SESSION['nick']) echo (" <img src='images/hand.gif' title='"._("Player turn")."' alt='"._("Player turn")."'>");
          			echo("</div>");
          		}
          		else
          		{
          			echo("<div class='playername'><a href='player_view.php?playerID=".$tmpGame['whitePlayer']."'>".$tmpGame['whiteNick']."</a><br/>".$whiteElo);
          			if (getOnlinePlayer($tmpGame['whitePlayer'])) echo (" <img src='images/user_online.gif' title='"._("Player online")."' alt='"._("Player online")."'>");
          			echo("</div>");
          		}
          	}
          	?>
          	</th>
          	<th width="40%" align="right">
          	<?
          	if ($isPlayersTurn)
          	{
          		echo("<div class='playername'><a href='player_view.php?playerID=".$tmpGame['blackPlayer']."'>".$tmpGame['blackNick']."</a><br/>");
          		if ($tmpGame['blackNick'] == $_SESSION['nick']) echo ("<img src='images/hand.gif' title='"._("Player turn")."' alt='"._("Player turn")."'> ");
          		if (getOnlinePlayer($tmpGame['blackPlayer'])) echo (" <img src='images/user_online.gif' title='"._("Player online")."' alt='"._("Player online")."'>");
          		echo($blackElo."</div>");	
          	}
          	else
          	{
          		if ($tmpGame['whiteNick'] == $_SESSION['nick'] || $tmpGame['blackNick'] == $_SESSION['nick'])
          		{
          			echo("<div class='playername'><a href='player_view.php?playerID=".$tmpGame['blackPlayer']."'>".$tmpGame['blackNick']."</a><br/>");
          			if ($tmpGame['blackNick'] != $_SESSION['nick']) echo ("<img src='images/hand.gif' title='"._("Player turn")."' alt='"._("Player turn")."'> ");
          			if (getOnlinePlayer($tmpGame['blackPlayer'])) echo (" <img src='images/user_online.gif' title='"._("Player online")."' alt='"._("Player online")."'>");
          			echo($blackElo."</div>");	
          		}
          		else
          		{
          			echo("<div class='playername'><a href='player_view.php?playerID=".$tmpGame['blackPlayer']."'>".$tmpGame['blackNick']."</a><br/>");
          			if (getOnlinePlayer($tmpGame['blackPlayer'])) echo (" <img src='images/user_online.gif' title='"._("Player online")."' alt='"._("Player online")."'>");
          			echo($blackElo."</div>");
          		}
          	}
			?>
          	</th>
          	<th width="10%" align="right">
          		<img src="<?echo(getPicturePath($tmpGame['blackSocialNet'], $tmpGame['blackSocialID']));?>" width="40" height="40" style="margin:3px;"/><br/>
          	</th>
		</tr>
		<tr bgcolor="#FFFFFF">
			<th colspan="4">
	          	<div class="econame"><a href="javascript:loadgame(<?echo($_POST['gameID']);?>);"><img src="images/icone_rafraichir.png" border="0" title="<?echo _("Refresh game")?>" alt="<?echo _("Refresh game")?>"/></a>
	          	<?	echo(getStrGameType($tmpGame['type'], $tmpGame['flagBishop'], $tmpGame['flagKnight'], $tmpGame['flagRook'], $tmpGame['flagQueen']));
	          		if ($tmpGame['tournamentID'] != "")
						echo(" - <a href='tournament_view.php?ID=".$tmpGame['tournamentID']."'>"._("Tournament")." #".$tmpGame['tournamentID']."</a>");	
				?>
	          	</div>
          		<div class="econame">
				<?	if ($tmpGame['type'] == 0)
						echo("[".$tmpGame['eco']."] ".$tmpGame['ecoName']. " - ");
					echo _("Expiration")?> : <? if ($tmpGame['gameMessage'] == '') echo($strExpirationDate); else echo _("Ended game");?></div>
			</th>
		</tr>
		</table>
		</div>
		<?if ((!$isCheckMate && ($history[$numMoves]['isInCheck'] == 1)) || isset($statusMessage)) 
		{?>
		<div id="statusMessage" style="width:100%; text-align: center; background-color: #F2A521; padding: 5px;">
          		<?
          		if (($numMoves == -1) || ($numMoves % 2 == 1))
          			$curColor = _("Whites");
          		else
          			$curColor = _("Blacks");
          		
          		if (!$isCheckMate && ($history[$numMoves]['isInCheck'] == 1))
          			echo("<b>".$curColor." "._("are in check")." !</b> ");
          		echo($statusMessage."&nbsp;");
          		?>
		</div>
		<? }?>
	<?
}

function writeStatusMobile($tmpGame)
{
	global $numMoves, $history, $isCheckMate, $statusMessage, $isPlayersTurn, $ecoCode, $ecoName;

	$fmt = new IntlDateFormatter(getenv("LC_ALL"), IntlDateFormatter::SHORT, IntlDateFormatter::SHORT);

	$expirationDate = new DateTime($tmpGame['expirationDate']);
	$strExpirationDate = $fmt->format($expirationDate);
	
	// Elo
	if ($tmpGame['type'] == 2)
	{
		$whiteElo = $tmpGame['whiteElo960'];
		$blackElo = $tmpGame['blackElo960'];
	}
	else
	{
		$whiteElo = $tmpGame['whiteElo'];
		$blackElo = $tmpGame['blackElo'];
	}
	
	?>
	<table border="0" align="center" cellspacing="0" cellpadding="0" width="100%">
	<tr bgcolor="#EEEEEE" valign="top">
		<th width="50%" align="left">
	    <?
          	if ($isPlayersTurn)
          	{
          		echo("<div class='playername'><a href='player_view.php?playerID=".$tmpGame['whitePlayer']."'>".$tmpGame['whiteNick']."</a><br>".$whiteElo);
          		if (getOnlinePlayer($tmpGame['whitePlayer'])) echo (" <img src='images/user_online.gif' title='"._("Player online")."' alt='"._("Player online")."'>");
          		if ($tmpGame['whiteNick'] == $_SESSION['nick']) echo (" <img src='images/hand.gif' title='"._("Player turn")."' alt='"._("Player turn")."'>");
          		echo("</div>");
          	}
          	else
          	{
          		if ($tmpGame['whiteNick'] == $_SESSION['nick'] || $tmpGame['blackNick'] == $_SESSION['nick'])
          		{
          			echo("<div class='playername'><a href='player_view.php?playerID=".$tmpGame['whitePlayer']."'>".$tmpGame['whiteNick']."</a><br>".$whiteElo);
          			if (getOnlinePlayer($tmpGame['whitePlayer'])) echo (" <img src='images/user_online.gif' title='"._("Player online")."' alt='"._("Player online")."'>");
          			if ($tmpGame['whiteNick'] != $_SESSION['nick']) echo (" <img src='images/hand.gif' title='"._("Player turn")."' alt='"._("Player turn")."'>");
          			echo("</div>");
          		}
          		else
          		{
          			echo("<div class='playername'><a href='player_view.php?playerID=".$tmpGame['whitePlayer']."'>".$tmpGame['whiteNick']."</a><br>".$whiteElo);
          			if (getOnlinePlayer($tmpGame['whitePlayer'])) echo (" <img src='images/user_online.gif' title='"._("Player online")."' alt='"._("Player online")."'>");
          			echo("</div>");
          		}
          	}
          	?>
          	</th>
          	<th width="50%" align="right">
          	<?
          	if ($isPlayersTurn)
          	{
          		echo("<div class='playername'><a href='player_view.php?playerID=".$tmpGame['blackPlayer']."'>".$tmpGame['blackNick']."</a><br>");
          		if ($tmpGame['blackNick'] == $_SESSION['nick']) echo ("<img src='images/hand.gif' title='"._("Player turn")."' alt='"._("Player turn")."'> ");
          		if (getOnlinePlayer($tmpGame['blackPlayer'])) echo (" <img src='images/user_online.gif' title='"._("Player online")."' alt='"._("Player online")."'>");
          		echo($blackElo."</div>");	
          	}
          	else
          	{
          		if ($tmpGame['whiteNick'] == $_SESSION['nick'] || $tmpGame['blackNick'] == $_SESSION['nick'])
          		{
          			echo("<div class='playername'><a href='player_view.php?playerID=".$tmpGame['blackPlayer']."'>".$tmpGame['blackNick']."</a><br>");
          			if ($tmpGame['blackNick'] != $_SESSION['nick']) echo ("<img src='images/hand.gif' title='"._("Player turn")."' alt='"._("Player turn")."'> ");
          			if (getOnlinePlayer($tmpGame['blackPlayer'])) echo (" <img src='images/user_online.gif' title='"._("Player online")."' alt='"._("Player online")."'>");
          			echo($blackElo."</div>");	
          		}
          		else
          		{
          			echo("<div class='playername'><a href='player_view.php?playerID=".$tmpGame['blackPlayer']."'>".$tmpGame['blackNick']."</a><br>");
          			if (getOnlinePlayer($tmpGame['blackPlayer'])) echo (" <img src='images/user_online.gif' title='"._("Player online")."' alt='"._("Player online")."'>");
          			echo($blackElo."</div>");
          		}
          	}
			?>
          	</th>
		</tr>
		<tr bgcolor="#EEEEEE">
			<th colspan="2">
				<?if ($tmpGame['tournamentID'] != "")
						echo("<div class='econame'><a href='tournament_view.php?ID=".$tmpGame['tournamentID']."'>"._("Tournament")." #".$tmpGame['tournamentID']."</a></div>");
	          	?>
				<div class="econame">
	          	<?	echo(getStrGameType($tmpGame['type'], $tmpGame['flagBishop'], $tmpGame['flagKnight'], $tmpGame['flagRook'], $tmpGame['flagQueen']));
					if ($tmpGame['type'] == 0)
						echo(" - [".$tmpGame['eco']."] ".$tmpGame['ecoName']);
				?>
	          	</div>
          		<div class="econame"><a href="javascript:loadgame(<?echo($_POST['gameID']);?>);"><img src="images/icone_rafraichir.png" border="0" title="<?echo _("Refresh game")?>" alt="<?echo _("Refresh game")?>"/></a>
					<?echo _("Expiration")?> : <? echo($strExpirationDate);?></div>
			</th>
		</tr>        		
		<tr>
          		<?
          		if (($numMoves == -1) || ($numMoves % 2 == 1))
          			$curColor = _("Whites");
          		else
          			$curColor = _("Blacks");
          		
          		if ((!$isCheckMate && ($history[$numMoves]['isInCheck'] == 1)) || isset($statusMessage))
          			$bgcolor = "F2A521";
          		else
          			$bgcolor = "EEEEEE";
          		
          		echo("<td align='center' bgcolor='".$bgcolor."' colspan='2'>");
          		if (!$isCheckMate && ($history[$numMoves]['isInCheck'] == 1))
          			echo("<b>".$curColor." "._("are in check")." !</b> ");
          		echo($statusMessage."&nbsp;</td>");
          		?>
		</tr>
	</table>
	<?
}

function writeDrawRequest($isMobile)
{
?>	
	<div id="drawRequest" style="width:100%; text-align: center; background-color: #F2A521; padding: 5px;">
		<?echo _("Your opponent do a draw proposal. Are you agree ?")?>
		<input type="radio" name="drawResponse" value="yes"> <?echo _("Yes")?>
		<input type="radio" name="drawResponse" value="no" checked="checked"> <?echo _("No")?>
		<input type="hidden" name="isDrawResponseDone" value="no">
		<input type="button" value="<? echo _("OK")?>" class="button" onClick="this.form.isDrawResponseDone.value = 'yes'; this.form.submit()">
	</div>
<?
}

function createInvitation($playerID, $opponentID, $color, $type, $flagBishop, $flagKnight, $flagRook, $flagQueen, &$oppColor, $timeMove, $chess960)
{
	global $board;
	global $dbh;
	
	if ($chess960 == "" && $type == 2)
		return false;
		
	/* prevent multiple pending requests between two players with the same originator */
	$tmpQuery = "SELECT gameID FROM games WHERE gameMessage = 'playerInvited'";
	$tmpQuery .= " AND ((messageFrom = 'white' AND whitePlayer = ".$playerID." AND blackPlayer = ".$opponentID.")";
	$tmpQuery .= " OR (messageFrom = 'black' AND whitePlayer = ".$opponentID." AND blackPlayer = ".$playerID."))";
	
	$tmpExistingRequests = mysqli_query($dbh,$tmpQuery);
	
	if (mysqli_num_rows($tmpExistingRequests) == 0 || $opponentID == "0")
	{
	
		if ($color == 'random')
			$tmpColor = (mt_rand(0,1) == 1) ? "white" : "black";
		else
			$tmpColor = $color;
	
		if ( $flagBishop == "1") {$flagBishop = 1;} else {$flagBishop = 0;};
		if ( $flagKnight == "1") {$flagKnight = 1;} else {$flagKnight = 0;};
		if ( $flagRook == "1") {$flagRook = 1;} else {$flagRook = 0;};
		if ( $flagQueen == "1") {$flagQueen = 1;} else {$flagQueen = 0;};
		
		$position = "";
		if ($type == 1 || $type == 2)
		{
			initBoard($flagRook, $flagQueen, $flagKnight, $flagBishop, $chess960);
			$position = getPositionFromBoard($board);
		}
		
		$tmpQuery = "INSERT INTO games (whitePlayer, blackPlayer, gameMessage, messageFrom, dateCreated, lastMove, type, flagBishop, flagKnight, flagRook, flagQueen, timeMove, position, chess960) VALUES (";
		if ($tmpColor == 'white')
		{
			$tmpQuery .= $playerID.", ".$opponentID;
			$oppColor = 'black';
		}
		else
		{
			$tmpQuery .= $opponentID.", ".$playerID;
			$oppColor = 'white';
		}
	
		$tmpQuery .= ", 'playerInvited', '".$tmpColor."', NOW(), NOW(), ".$type.", ".$flagBishop.", ".$flagKnight.", ".$flagRook.", ".$flagQueen.", ".$timeMove.", '".$position."','".$chess960."')";
	
		mysqli_query($dbh,$tmpQuery);
		$newGameID = mysqli_insert_id($dbh);
		return $newGameID;
	}
	else return false;
	
}

function createGame($whiteID, $blackID, $type, $flagBishop, $flagKnight, $flagRook, $flagQueen, $timeMove, $chess960)
{
	global $board;
	global $dbh;
	
	if ($chess960 == "" && $type == CHESS960)
		return false;
		
	if ( $flagBishop == "1") {$flagBishop = 1;} else {$flagBishop = 0;};
	if ( $flagKnight == "1") {$flagKnight = 1;} else {$flagKnight = 0;};
	if ( $flagRook == "1") {$flagRook = 1;} else {$flagRook = 0;};
	if ( $flagQueen == "1") {$flagQueen = 1;} else {$flagQueen = 0;};
	
	$position = "";
	if ($type == BEGINNER || $type == CHESS960)
	{
		initBoard($flagRook, $flagQueen, $flagKnight, $flagBishop, $chess960);
		$position = getPositionFromBoard($board);
	}
	else 
	{
		initBoard(1, 1, 1, 1, "");
		$position = getPositionFromBoard($board);
	}
	
	$tmpQuery = "INSERT INTO games (whitePlayer, blackPlayer, dateCreated, lastMove, type, flagBishop, flagKnight, flagRook, flagQueen, timeMove, position, chess960) VALUES (";

	$tmpQuery .= $whiteID.", ".$blackID.", NOW(), NOW(), ".$type.", ".$flagBishop.", ".$flagKnight.", ".$flagRook.", ".$flagQueen.", ".$timeMove.", '".$position."','".$chess960."')";

	mysqli_query($dbh,$tmpQuery);
	$newGameID = mysqli_insert_id($dbh);
	return $newGameID;
	
}

function getStrGameType($type, $flagBishop, $flagKnight, $flagRook, $flagQueen)
{
	if ($type == 0)
		return _("Classic game");
	else if ($type == 2)
		return _("Chess960 game");
	else
	{
		$pieces="";
		if ($flagBishop == 1)
			$pieces .= _(", Bishops");
		if ($flagKnight == 1)
			$pieces .= _(", Knights");
		if ($flagRook == 1)
			$pieces .= _(", Rooks");
		if ($flagQueen == 1)
			$pieces .= _(", Queens");
			
		return _("Beginner game - King and Pawns").$pieces;
	}
}

function getNbGameTurns($playerID)
{
	$nbTurns = 0;
	$tmpGames = listGamesProgressWithMoves($playerID);
	
	if (mysqli_num_rows($tmpGames) > 0)
		while($tmpGame = mysqli_fetch_array($tmpGames, MYSQLI_ASSOC))
		{
			$numMoves = $tmpGame['nbMoves'] - 1;
			if ($tmpGame['whitePlayer'] == $playerID)
				$playersColor = "white";
			else
				$playersColor = "black";
			
			/* find out if it's the current player's turn */
			if (( (($numMoves == -1) || (($numMoves % 2) == 1)) && ($playersColor == "white")) || ((($numMoves % 2) == 0) && ($playersColor == "black")) )
				$nbTurns++;
			
		}
		
	return $nbTurns;
}
?>