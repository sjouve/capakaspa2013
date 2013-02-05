<?
/* Depend on:
require_once('dac/dac_players.php');
require 'dac/dac_games.php';
*/

/* Accès aux données concernant la table Games, History, Messages */

/* Return le PGN de la partie */
function getPGN($whiteNick, $blackNick, $type, $flagBishop, $flagKnight, $flagRook, $flagQueen, $listeCoups)
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
			$startFEN[34]="Q";
		}
	}
	
	$pattern = "[\n\r]";
	$pgnstring = "[FEN \"".$startFEN."\"][White \"".$whiteNick."\"][Black \"".$blackNick."\"] ".mb_eregi_replace($pattern," ",$listeCoups);
	
	return $pgnstring;
	
}

/* Code ECO d'une position */
function getEco($position)
{
	$all_fen_eco = mysql_query("SELECT F.eco eco, F.trait trait, E.name name FROM fen_eco F, eco E WHERE fen = '".$position."' AND F.eco = E.eco");
	$fen_eco = mysql_fetch_array($all_fen_eco, MYSQL_ASSOC);
	return $fen_eco;
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

/* Met à jour la date de dernier coup d'une partie */
function updateTimestamp()
{
	
	mysql_query("UPDATE games SET lastMove = NOW() WHERE gameID = ".$_POST['gameID']);
}

/* Met à jour la position et le code ECO d'une partie */
function updateGame($gameID, $position, $ecoCode)
{
	
	$res = mysql_query("UPDATE games SET lastMove = NOW(), position = '".$position."', eco = '".$ecoCode."' WHERE gameID = ".$gameID);
	return $res;
}


/* 	
 Calcul de la date cible de la partie : date qui ne doit pas dépasser la date du dernier coup
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
	
	$allMoves = mysql_query("SELECT * FROM history WHERE gameID = ".$gameID." ORDER BY timeOfMove");

	$numMoves = -1;
	while ($thisMove = mysql_fetch_array($allMoves, MYSQL_ASSOC))
	{
		$numMoves++;
		$history[$numMoves] = $thisMove;
	}
}

/* I: $history, $board, $isPromoting, $numMoves, $isInCheck
 * O: $history, $isPromoting, $numMoves
*/
function saveHistory()
{
	global $board, $isPromoting, $history, $numMoves, $isInCheck;

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
			$tmpQuery = "INSERT INTO history (timeOfMove, gameID, curPiece, curColor, fromRow, fromCol, toRow, toCol, replaced, promotedTo, isInCheck) VALUES (Now(), ".$_POST['gameID'].", '".getPieceName($board[$_POST['fromRow']][$_POST['fromCol']])."', '$curColor', ".$_POST['fromRow'].", ".$_POST['fromCol'].", ".$_POST['toRow'].", ".$_POST['toCol'].", null, null, ".$history[$numMoves]['isInCheck'].")"; 
			$history[$numMoves]['replaced'] = null;
			$tmpReplaced = "";
		}
		
	}
	else
	{
		if ($isPromoting)
			$tmpQuery = "INSERT INTO history (timeOfMove, gameID, curPiece, curColor, fromRow, fromCol, toRow, toCol, replaced, promotedTo, isInCheck) VALUES (Now(), ".$_POST['gameID'].", '".getPieceName($board[$_POST['fromRow']][$_POST['fromCol']])."', '$curColor', ".$_POST['fromRow'].", ".$_POST['fromCol'].", ".$_POST['toRow'].", ".$_POST['toCol'].", '".getPieceName($board[$_POST['toRow']][$_POST['toCol']])."', '".getPieceName($_POST['promotion'])."', ".$history[$numMoves]['isInCheck'].")"; 
		else
			$tmpQuery = "INSERT INTO history (timeOfMove, gameID, curPiece, curColor, fromRow, fromCol, toRow, toCol, replaced, promotedTo, isInCheck) VALUES (Now(), ".$_POST['gameID'].", '".getPieceName($board[$_POST['fromRow']][$_POST['fromCol']])."', '$curColor', ".$_POST['fromRow'].", ".$_POST['fromCol'].", ".$_POST['toRow'].", ".$_POST['toCol'].", '".getPieceName($board[$_POST['toRow']][$_POST['toCol']])."', null, ".$history[$numMoves]['isInCheck'].")"; 
		
		$history[$numMoves]['replaced'] = getPieceName($board[$_POST['toRow']][$_POST['toCol']]);
		$tmpReplaced = $history[$numMoves]['replaced'];
	}

	$res = mysql_query($tmpQuery);
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
	
	// Activity
	if (isset($_POST['chkShareMove']))
		insertActivity($_SESSION['playerID'], GAME, $_POST['gameID'], $strMove, 'move');
}

/* I: $gameID, $numMoves
 * O: $board, $playersColor, $tmpGame
*/
function loadGame($gameID, $numMoves)
{
	global $board, $playersColor, $ecoCode, $ecoName;
	
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
	// Ajouter ici le nombre de jours d'absence à prendre en compte
	$targetDate = calculateTargetDate($tmpGame['lastMove'], $tmpGame['whitePlayer'], $tmpGame['blackPlayer'], $tmpGame['timeMove']);
	
	// Terminer la partie si dépassement de temps
	$res = mysql_query("UPDATE games 
						SET gameMessage = 'playerResigned', messageFrom = '".$turnColor."' 
						WHERE lastMove < '".$targetDate."' 
						AND (gameMessage <> 'draw' 
						AND gameMessage <> 'checkMate' 
						AND gameMessage <> 'playerResigned') 
						AND gameID = ".$_POST['gameID']);
	
	return $tmpGame;
	
}

function saveGame()
{
	// TODO Vérifier impact $ecoCode, $ecoName
	global $board, $playersColor, $ecoCode, $ecoName, $numMoves;
	
	// Sauvegarde de l'échiquier sous la forme d'une chaîne de 64 caractères
	// tcfdrfct pppppppp 00000000 00000000 00000000 00000000 PPPPPPPP TCFDRFCT
	
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
	
			
	// Mettre à jour la date du dernier coup et la position
	$res = updateGame($_POST['gameID'], $position, $ecoCode);
	return $res;
}

function processMessages()
{
	global $isUndoRequested, $isDrawRequested, $isUndoing, $isGameOver, $isCheckMate, $playersColor, $statusMessage;
	
	if (DEBUG)
		echo("Entering processMessages()<br>\n");
	
	$isUndoRequested = false;
	$isGameOver = false;
	
	if ($playersColor == "white")
		$opponentColor = "black";
	else
		$opponentColor = "white";

	/* *********************************************** */
	/* queue user generated (ie: using forms) messages */
	/* *********************************************** */
	if (DEBUG)
		echo("Processing user generated (ie: form) messages...<br>\n");

	/* queue a request for an undo */
	$Test = isset($_POST['requestUndo']) ? $_POST['requestUndo']:Null;
	if ($Test == "yes")
	{
		/* if the two players are on the same system, execute undo immediately */
		/* NOTE: assumes the two players discussed it live before undoing */
		if ($_SESSION['isSharedPC'])
			$isUndoing = true;
		else
		{
			$tmpQuery = "INSERT INTO messages (gameID, msgType, msgStatus, destination) VALUES (".$_POST['gameID'].", 'undo', 'request', '".$opponentColor."')";
			mysql_query($tmpQuery);
		}
		
		updateTimestamp();
	}
	
	/* queue a request for a draw */
	$Test = isset($_POST['requestDraw']) ? $_POST['requestDraw']:Null;
	if ($Test == "yes")
	{
		/* if the two players are on the same system, execute Draw immediately */
		/* NOTE: assumes the two players discussed it live before declaring the game a draw */
		if ($_SESSION['isSharedPC'])
		{
			$tmpQuery = "UPDATE games SET gameMessage = 'draw', messageFrom = '".$playersColor."' WHERE gameID = ".$_POST['gameID'];
			mysql_query($tmpQuery);
		}
		else
		{
			$tmpQuery = "INSERT INTO messages (gameID, msgType, msgStatus, destination) VALUES (".$_POST['gameID'].", 'draw', 'request', '".$opponentColor."')";
			mysql_query($tmpQuery);
		}

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
		
			$tmpQuery = "UPDATE messages SET msgStatus = '".$tmpStatus."', destination = '".$opponentColor."' WHERE gameID = ".$_POST['gameID']." AND msgType = 'undo' AND msgStatus = 'request' AND destination = '".$playersColor."'";
			mysql_query($tmpQuery);
		
			updateTimestamp();
		}
	}
	
	/* response to a request for a draw */
	if (isset($_POST['drawResponse']))
	{
		// TODO Ajouter cas isDrawResponseDone No
		if ($_POST['isDrawResponseDone'] == 'yes')
		{
			if ($_POST['drawResponse'] == "yes")
			{
				$tmpStatus = "approved";
				$tmpQuery = "UPDATE games SET gameMessage = 'draw', messageFrom = '".$playersColor."' WHERE gameID = ".$_POST['gameID'];
				mysql_query($tmpQuery);				
			}
			else
				$tmpStatus = "denied";
		
			$tmpQuery = "UPDATE messages SET msgStatus = '".$tmpStatus."', destination = '".$opponentColor."' WHERE gameID = ".$_POST['gameID']." AND msgType = 'draw' AND msgStatus = 'request' AND destination = '".$playersColor."'";
			mysql_query($tmpQuery);

			updateTimestamp();
			
			/* if email notification is activated... */
			if ($tmpStatus == "approved")
			{
				/* Notification */
				chessNotification('draw', $opponentColor, '', $_SESSION['nick'], $_POST['gameID']);
				insertActivity($_SESSION['playerID'], GAME, $_POST['gameID'], "", 'draw');
			}
		}
	}
	
	/* draw by rules */
	$Test = isset($_POST['drawResult']) ? $_POST['drawResult']:Null;
	if ($Test == 'true')
	{
		$tmpQuery = "UPDATE games SET gameMessage = 'draw', messageFrom = '".$playersColor."' WHERE gameID = ".$_POST['gameID'];
		mysql_query($tmpQuery);
		
	}
		
	/* resign the game */
	$Test = isset($_POST['resign']) ? $_POST['resign']:Null;
	if ($Test == "yes")
	{
		$tmpQuery = "UPDATE games SET gameMessage = 'playerResigned', messageFrom = '".$playersColor."' WHERE gameID = ".$_POST['gameID'];
		mysql_query($tmpQuery);

		updateTimestamp();

		/* Notification */
		chessNotification('resignation', $opponentColor, '', $_SESSION['nick'], $_POST['gameID']);
		insertActivity($_SESSION['playerID'], GAME, $_POST['gameID'], "", 'resignation');
			
	}
	
	
	/* ******************************************* */
	/* process queued messages (ie: from database) */
	/* ******************************************* */
	$tmpQuery = "SELECT * FROM messages WHERE gameID = ".$_POST['gameID']." AND destination = '".$playersColor."'";
	$tmpMessages = mysql_query($tmpQuery);

	while($tmpMessage = mysql_fetch_array($tmpMessages, MYSQL_ASSOC))
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
						$tmpQuery = "DELETE FROM messages WHERE gameID = ".$_POST['gameID']." AND msgType = 'undo' AND msgStatus = 'approved' AND destination = '".$playersColor."'";
						mysql_query($tmpQuery);
						$statusMessage .= _("Move cancellation accepted")."\n";
						break;
					case 'denied':
						$isUndoing = false;
						$tmpQuery = "DELETE FROM messages WHERE gameID = ".$_POST['gameID']." AND msgType = 'undo' AND msgStatus = 'denied' AND destination = '".$playersColor."'";
						mysql_query($tmpQuery);
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
						$tmpQuery = "DELETE FROM messages WHERE gameID = ".$_POST['gameID']." AND msgType = 'draw' AND msgStatus = 'approved' AND destination = '".$playersColor."'";
						mysql_query($tmpQuery);
						$statusMessage .= _("Draw proposal accepted")."\n";
						break;
					case 'denied':
						$tmpQuery = "DELETE FROM messages WHERE gameID = ".$_POST['gameID']." AND msgType = 'draw' AND msgStatus = 'denied' AND destination = '".$playersColor."'";
						mysql_query($tmpQuery);
						$statusMessage .= _("Draw proposal refused")."\n";
						break;
				}
				break;
		}
	}

	/* requests pending */
	$tmpQuery = "SELECT * FROM messages WHERE gameID = ".$_POST['gameID']." AND msgStatus = 'request' AND destination = '".$opponentColor."'";
	$tmpMessages = mysql_query($tmpQuery);

	while($tmpMessage = mysql_fetch_array($tmpMessages, MYSQL_ASSOC))
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
		mysql_query("UPDATE games SET gameMessage = 'checkMate', messageFrom = '".$playersColor."' WHERE gameID = ".$_POST['gameID']);
	// TODO Insert activity pour checkmate
	
	$tmpQuery = "SELECT gameMessage, messageFrom FROM games WHERE gameID = ".$_POST['gameID'];
	$tmpMessages = mysql_query($tmpQuery);
	$tmpMessage = mysql_fetch_array($tmpMessages, MYSQL_ASSOC);
	
	if ($tmpMessage['gameMessage'] == "draw")
	{
		$statusMessage .= _("Draw game")."\n";
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
		$statusMessage .= $tmpColor." "._("resign game")."\n";
		$isGameOver = true;
	}

	if ($tmpMessage['gameMessage'] == "checkMate")
	{
		$statusMessage .= _("Check and Mat!")." ".$tmpColor." "._("win the game")."\n";
		$isGameOver = true;
		$isCheckMate = true;
	}
}

/* functions for outputting to html and javascript */

/* Miniature */
function drawboardGame($gameID, $whitePlayer, $blackPlayer, $position)
{

	global $isPlayersTurn;

	// Nombre de 1/2 coups
	$allMoves = mysql_query("SELECT count(gameID) nbMove FROM history WHERE gameID = ".$gameID." ORDER BY timeOfMove");
	$thisMove = mysql_fetch_array($allMoves, MYSQL_ASSOC);
	$numMoves = $thisMove['nbMove'] - 1;

	// Remplir l'échiquier
	// TODO Prévoir le cas du type de partie dans la position initiale
	if (!isset($position)) $position = "tcfdrfctpppppppp00000000000000000000000000000000PPPPPPPPTCFDRFCT";
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
	
			echo ("<img name='pos$i-$j' src='pgn4web/".$_SESSION['pref_theme']."/20/");
	
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
function drawboard($withCoord)
{
	global $board, $playersColor, $numMoves, $nb_game_vacation;
	
	/* find out if it's the current player's turn */
	if (( (($numMoves == -1) || (($numMoves % 2) == 1)) && ($playersColor == "white")) || ((($numMoves % 2) == 0) && ($playersColor == "black")) )
		$isPlayersTurn = true;
	else
		$isPlayersTurn = false;
	
	/* determine who's perspective of the board to show */
	if ($_SESSION['isSharedPC'] && !$isPlayersTurn)
	{
		if ($playersColor == "white")
			$perspective = "black";
		else
			$perspective = "white";
	}
	else
	{
		$perspective = $playersColor;
	}
	
	/* NOTE: if both players are using the same PC, in a sense it's always the players turn */
	if ($_SESSION['isSharedPC'])
		$isPlayersTurn = true;

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
			echo ("   <td id='".$i.$j."' bgcolor='");

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

			echo ("<img name='pos$i-$j' src='pgn4web/".$_SESSION['pref_theme']."/35/");

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
	
	?>
	<table border="0" align="center" cellspacing="0" cellpadding="0">
	<tr bgcolor="#EEEEEE" valign="top">
		<th width="10%" align="left">
			<img src="<?echo(getPicturePath($tmpGame['whiteSocialNet'], $tmpGame['whiteSocialID']));?>" width="40" height="40" style="margin:3px;"/>
		</th>
		<th width="40%" align="left">
	    <?
          	if ($isPlayersTurn)
          	{
          		echo("<div class='playername'><a href='player_view.php?playerID=".$tmpGame['whitePlayer']."'>".$tmpGame['whiteFirstName']." ".$tmpGame['whiteLastName']."</a><br/>".$tmpGame['whiteElo']);
          		if (getOnlinePlayer($tmpGame['whitePlayer'])) echo (" <img src='images/user_online.gif'/>");
          		if ($tmpGame['whiteNick'] == $_SESSION['nick']) echo (" <img src='images/hand.gif'/>");
          		echo("</div>");
          	}
          	else
          	{
          		if ($tmpGame['whiteNick'] == $_SESSION['nick'] || $tmpGame['blackNick'] == $_SESSION['nick'])
          		{
          			echo("<div class='playername'><a href='player_view.php?playerID=".$tmpGame['whitePlayer']."'>".$tmpGame['whiteFirstName']." ".$tmpGame['whiteLastName']."</a><br/>".$tmpGame['whiteElo']);
          			if (getOnlinePlayer($tmpGame['whitePlayer'])) echo (" <img src='images/user_online.gif'/>");
          			if ($tmpGame['whiteNick'] != $_SESSION['nick']) echo (" <img src='images/hand.gif'/>");
          			echo("</div>");
          		}
          		else
          		{
          			echo("<div class='playername'><a href='player_view.php?playerID=".$tmpGame['whitePlayer']."'>".$tmpGame['whiteFirstName']." ".$tmpGame['whiteLastName']."</a><br/>".$tmpGame['whiteElo']);
          			if (getOnlinePlayer($tmpGame['whitePlayer'])) echo (" <img src='images/user_online.gif'/>");
          			echo("</div>");
          		}
          	}
          	?>
          	</th>
          	<th width="40%" align="right">
          	<?
          	if ($isPlayersTurn)
          	{
          		echo("<div class='playername'><a href='player_view.php?playerID=".$tmpGame['blackPlayer']."'>".$tmpGame['blackFirstName']." ".$tmpGame['blackLastName']."</a><br/>");
          		if ($tmpGame['blackNick'] == $_SESSION['nick']) echo ("<img src='images/hand.gif'/> ");
          		if (getOnlinePlayer($tmpGame['blackPlayer'])) echo (" <img src='images/user_online.gif'/>");
          		echo($tmpGame['blackElo']."</div>");	
          	}
          	else
          	{
          		if ($tmpGame['whiteNick'] == $_SESSION['nick'] || $tmpGame['blackNick'] == $_SESSION['nick'])
          		{
          			echo("<div class='playername'><a href='player_view.php?playerID=".$tmpGame['blackPlayer']."'>".$tmpGame['blackFirstName']." ".$tmpGame['blackLastName']."</a><br/>");
          			if ($tmpGame['blackNick'] != $_SESSION['nick']) echo ("<img src='images/hand.gif'/> ");
          			if (getOnlinePlayer($tmpGame['blackPlayer'])) echo (" <img src='images/user_online.gif'/>");
          			echo($tmpGame['blackElo']."</div>");	
          		}
          		else
          		{
          			echo("<div class='playername'><a href='player_view.php?playerID=".$tmpGame['blackPlayer']."'>".$tmpGame['blackFirstName']." ".$tmpGame['blackLastName']."</a><br/>");
          			if (getOnlinePlayer($tmpGame['blackPlayer'])) echo (" <img src='images/user_online.gif'/>");
          			echo($tmpGame['blackElo']."</div>");
          		}
          	}
			?>
          	</th>
          	<th width="10%" align="right">
          		<img src="<?echo(getPicturePath($tmpGame['blackSocialNet'], $tmpGame['blackSocialID']));?>" width="40" height="40" style="margin:3px;"/><br/>
          	</th>
		</tr>
		<tr bgcolor="#EEEEEE">
			<th colspan="4">
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
          		
          		echo("<td align='center' bgcolor='".$bgcolor."' colspan='4'>");
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
	<table <?if (!$isMobile) {?>width="100%"<?};?> border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td align="center" bgcolor="#F2A521">
		<?echo _("Your opponent do a draw proposal. Are you agree ?")?>
		<input type="radio" name="drawResponse" value="yes"> <?echo _("Yes")?>
		<input type="radio" name="drawResponse" value="no" checked="checked"> <?echo _("No")?>
		<input type="hidden" name="isDrawResponseDone" value="no">
		<input type="button" value="<? echo _("OK")?>" class="button" onClick="this.form.isDrawResponseDone.value = 'yes'; this.form.submit()">
		</td>
	</tr>
	</table>	
<?
}

function createInvitation($playerID, $opponentID, $color, $type, $flagBishop, $flagKnight, $flagRook, $flagQueen, $oppColor, $timeMove)
{
	/* prevent multiple pending requests between two players with the same originator */
	$tmpQuery = "SELECT gameID FROM games WHERE gameMessage = 'playerInvited'";
	$tmpQuery .= " AND ((messageFrom = 'white' AND whitePlayer = ".$playerID." AND blackPlayer = ".$opponentID.")";
	$tmpQuery .= " OR (messageFrom = 'black' AND whitePlayer = ".$opponentID." AND blackPlayer = ".$playerID."))";
	
	$tmpExistingRequests = mysql_query($tmpQuery);
	
	if (mysql_num_rows($tmpExistingRequests) == 0)
	{
	
		if ($color == 'random')
			$tmpColor = (mt_rand(0,1) == 1) ? "white" : "black";
		else
			$tmpColor = $color;
	
		if ( $flagBishop == "1") {$flagBishop = 1;} else {$flagBishop = 0;};
		if ( $flagKnight == "1") {$flagKnight = 1;} else {$flagKnight = 0;};
		if ( $flagRook == "1") {$flagRook = 1;} else {$flagRook = 0;};
		if ( $flagQueen == "1") {$flagQueen = 1;} else {$flagQueen = 0;};
		
		$tmpQuery = "INSERT INTO games (whitePlayer, blackPlayer, gameMessage, messageFrom, dateCreated, lastMove, type, flagBishop, flagKnight, flagRook, flagQueen, timeMove) VALUES (";
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
	
		$tmpQuery .= ", 'playerInvited', '".$tmpColor."', NOW(), NOW(), ".$type.", ".$flagBishop.", ".$flagKnight.", ".$flagRook.", ".$flagQueen.", ".$timeMove.")";
	
		mysql_query($tmpQuery);
		$newGameID = mysql_insert_id();
		return $newGameID;
	}
	else return false;
	
}

function getStrGameType($type, $flagBishop, $flagKnight, $flagRook, $flagQueen)
{
	if ($type == 0)
		return _("Classic game");
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
			
		return _("Beginner game<br>King and Pawns").$pieces;
	}
}
?>