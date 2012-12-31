<?
require_once('dac_players.php');
require 'dac_games.php';

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

function loadHistory()
{
	global $history, $numMoves;
	
	$allMoves = mysql_query("SELECT * FROM history WHERE gameID = ".$_POST['gameID']." ORDER BY timeOfMove");

	$numMoves = -1;
	while ($thisMove = mysql_fetch_array($allMoves, MYSQL_ASSOC))
	{
		$numMoves++;
		$history[$numMoves] = $thisMove;
	}
}

function savePromotion()
{
	global $history, $numMoves, $isInCheck;

	
	if ($isInCheck)
	{
		$tmpIsInCheck = 1;
		$history[$numMoves]['isInCheck'] = 1;
	}
	else
		$tmpIsInCheck = 0;

	$history[$numMoves]['promotedTo'] = getPieceName($_POST['promotion']);

	$tmpQuery = "UPDATE history SET promotedTo = '".getPieceName($_POST['promotion'])."', isInCheck = ".$tmpIsInCheck." WHERE gameID = ".$_POST['gameID']." AND timeOfMove = '".$history[$numMoves]['timeOfMove']."'";
	mysql_query($tmpQuery);

	updateTimestamp();

	/* if email notification is activated and move does not result in a pawn's promotion... */
	if ($CFG_USEEMAILNOTIFICATION)
	{
		if ($history[$numMoves]['replaced'] == null)
			$tmpReplaced = '';
		else
			$tmpReplaced = $history[$numMoves]['replaced'];

		// Couleur de l'adversaire
		if (($numMoves == -1) || ($numMoves % 2 == 1))
			$oppColor = "black";
		else
			$oppColor = "white";
		
		// Récupérer les informations sur l'adversaire
		if ($oppColor == 'white')
		{	
			$tmpOpponent = mysql_query("SELECT P.email email, PR.value value FROM games G, players P, preferences PR WHERE G.gameID = ".$_POST['gameID']." AND G.whitePlayer = P.playerID AND PR.playerID = P.playerID AND PR.preference='emailnotification'");
		}
		else
		{
			$tmpOpponent = mysql_query("SELECT P.email email, PR.value value FROM games G, players P, preferences PR WHERE G.gameID = ".$_POST['gameID']." AND G.blackPlayer = P.playerID AND PR.playerID = P.playerID AND PR.preference='emailnotification'");
		}
		
		$opponent = mysql_fetch_array($tmpOpponent, MYSQL_ASSOC);
	
		if ($opponent['value'] == 'oui')
		{	
				
			// Avertir l'adversaire par email
			webchessMail('move', $opponent['email'], moveToPGNString($history[$numMoves]['curColor'], $history[$numMoves]['curPiece'], $history[$numMoves]['fromRow'], $history[$numMoves]['fromCol'], $history[$numMoves]['toRow'], $history[$numMoves]['toCol'], $tmpReplaced, $history[$numMoves]['promotedTo'], $isInCheck), $_SESSION['nick']);
		
		}
	}
}

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
	/* if email notification is activated and move does not result in a pawn's promotion... */
	/* NOTE: moves resulting in pawn promotion are handled by savePromotion() above */
	
	// get opponent's player ID, email, nick et pref email notification
	if (($numMoves == -1) || ($numMoves % 2 == 1))
	{	
		$tmpOpponent = mysql_query("SELECT P.email email, PR.value value FROM games G, players P, preferences PR WHERE G.gameID = ".$_POST['gameID']." AND G.whitePlayer = P.playerID AND PR.playerID = P.playerID AND PR.preference='emailnotification'");
	}
	else
	{
		$tmpOpponent = mysql_query("SELECT P.email email, PR.value value FROM games G, players P, preferences PR WHERE G.gameID = ".$_POST['gameID']." AND G.blackPlayer = P.playerID AND PR.playerID = P.playerID AND PR.preference='emailnotification'");
	}
	
	$opponent = mysql_fetch_array($tmpOpponent, MYSQL_ASSOC);
	
	if ($opponent['value'] == 'oui')
	{	
		
		// notify opponent of move via email
		webchessMail('move', $opponent['email'], moveToPGNString($history[$numMoves]['curColor'], $history[$numMoves]['curPiece'], $history[$numMoves]['fromRow'], $history[$numMoves]['fromCol'], $history[$numMoves]['toRow'], $history[$numMoves]['toCol'], $history[$numMoves]['replaced'], '', $isInCheck), $_SESSION['nick']);
		
	}
	
}
		
function loadGame()
{
	global $board, $playersColor, $whiteNick, $blackNick, $whitePlayerID, $blackPlayerID,$numMoves, $CFG_EXPIREGAME, $dialogue, $ecoCode, $ecoName, $dateCreated, $whiteElo, $blackElo, $whiteSocialID, $whiteSocialNet, $blackSocialID, $blackSocialNet;
	
	// Informations sur la partie : voir le type de partie (position normale ou pas) et le problème du code ECO
	$tmpQuery = "SELECT G.whitePlayer whitePlayer, G.blackPlayer blackPlayer, G.dialogue dialogue, G.position position, 
	G.eco eco, DATE_FORMAT(G.lastMove, '%Y-%m-%d') lastMove, DATE_FORMAT(G.dateCreated, '%d/%m/%Y %T') dateCreatedF, 
	G.type type, G.flagBishop flagBishop, G.flagKnight flagKnight, G.flagRook flagRook, G.flagQueen flagQueen, 
	E.name ecoName, W.nick whiteNick, B.nick blackNick, W.elo whiteElo, B.elo blackElo, W.socialNetwork whiteSocialNet,
	B.socialNetwork blackSocialNet,  W.socialID whiteSocialID, B.socialID blackSocialID 
	FROM games G left join eco E on E.eco = G.eco, players W, players B 
	WHERE gameID = ".$_POST['gameID']." 
	AND G.whitePlayer = W.playerID 
	AND G.blackPlayer = B.playerID";
	
	$tmpGames = mysql_query($tmpQuery);
	$tmpGame = mysql_fetch_array($tmpGames, MYSQL_ASSOC);
	
	// Remplir l'échiquier
	$strPos = 0;
	for ($i = 0; $i < 8; $i++)
		for ($j = 0; $j < 8; $j++)
		{
			$board[$i][$j] = getPieceCodeChar($tmpGame['position']{$strPos});
			$strPos++;
		}
	
	$dateCreated = $tmpGame['dateCreatedF'];
			
	// Dialogue
	$dialogue = $tmpGame['dialogue'];
	
	// Code ECO de la partie
	$ecoCode = $tmpGame['eco'];
	$ecoName = $tmpGame['ecoName'];
		
	// Couleur du joueur qui charge la partie
	if ($tmpGame['whitePlayer'] == $_SESSION['playerID'])
		$playersColor = "white";
	else if ($tmpGame['blackPlayer'] == $_SESSION['playerID'])
		$playersColor = "black";
	else
		// Le joueur ne joue pas la partie
		$playerColor = "";
	
	// Récupérer les surnom et ID
	$blackNick = $tmpGame['blackNick'];
	$blackPlayerID = $tmpGame['blackPlayer'];
	$whiteNick = $tmpGame['whiteNick'];
	$whitePlayerID = $tmpGame['whitePlayer'];
	$blackElo = $tmpGame['blackElo'];
	$blackSocialID = $tmpGame['blackSocialID'];
	$blackSocialNet = $tmpGame['blackSocialNet'];
	$whiteElo = $tmpGame['whiteElo'];
	$whiteSocialID = $tmpGame['whiteSocialID'];
	$whiteSocialNet = $tmpGame['whiteSocialNet'];
	
	// A qui le tour
	if (($numMoves == -1) || ($numMoves % 2 == 1))
		$turnColor = "white";
	else
		$turnColor = "black";
		
	// Dépassement délai entre 2 coups
	// Ajouter ici le nombre de jours d'absence à prendre en compte
	$targetDate = calculateTargetDate($tmpGame['lastMove'], $whitePlayerID, $blackPlayerID, $CFG_EXPIREGAME);
	
	// Terminer la partie si dépassement de temps
	$res = mysql_query("UPDATE games SET gameMessage = 'playerResigned', messageFrom = '".$turnColor."' WHERE lastMove < '".$targetDate."' AND (gameMessage <> 'draw' AND gameMessage <> 'checkMate' AND gameMessage <> 'playerResigned') AND gameID = ".$_POST['gameID']);
	
	return $tmpGame;
	
}

function saveGame()
{
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
	global $isUndoRequested, $isDrawRequested, $isUndoing, $isGameOver, $isCheckMate, $playersColor, $statusMessage, $CFG_USEEMAILNOTIFICATION;
	
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
				/* get opponent's player ID */
				if ($playersColor == 'white')
					$tmpOpponentID = mysql_query("SELECT blackPlayer FROM games WHERE gameID = ".$_POST['gameID']);
				else
					$tmpOpponentID = mysql_query("SELECT whitePlayer FROM games WHERE gameID = ".$_POST['gameID']);
				
				$opponentID = mysql_result($tmpOpponentID, 0);
			
				$tmpOpponentEmail = mysql_query("SELECT email FROM players WHERE playerID = ".$opponentID);
				
				/* if opponent is using email notification... */
				if (mysql_num_rows($tmpOpponentEmail) > 0)
				{
					$opponentEmail = mysql_result($tmpOpponentEmail, 0);
					if ($opponentEmail != '')
					{
						/* notify opponent of resignation via email */
						webchessMail('draw', $opponentEmail, '', $_SESSION['nick']);
					}
				}
			}
		}
	}
	
	/* resign the game */
	$Test = isset($_POST['resign']) ? $_POST['resign']:Null;
	if ($Test == "yes")
	{
		$tmpQuery = "UPDATE games SET gameMessage = 'playerResigned', messageFrom = '".$playersColor."' WHERE gameID = ".$_POST['gameID'];
		mysql_query($tmpQuery);

		updateTimestamp();

		/* if email notification is activated... */
		if ($CFG_USEEMAILNOTIFICATION)
		{
			/* get opponent's player ID */
			if ($playersColor == 'white')
				$tmpOpponentID = mysql_query("SELECT blackPlayer FROM games WHERE gameID = ".$_POST['gameID']);
			else
				$tmpOpponentID = mysql_query("SELECT whitePlayer FROM games WHERE gameID = ".$_POST['gameID']);
			
			$opponentID = mysql_result($tmpOpponentID, 0);
		
			$tmpOpponentEmail = mysql_query("SELECT email FROM players WHERE playerID = ".$opponentID);
			
			/* if opponent is using email notification... */
			if (mysql_num_rows($tmpOpponentEmail) > 0)
			{
				$opponentEmail = mysql_result($tmpOpponentEmail, 0);
				if ($opponentEmail != '')
				{
					/* notify opponent of resignation via email */
					webchessMail('resignation', $opponentEmail, '', $_SESSION['nick']);
				}
			}
		}
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
						$statusMessage .= _("Move cancellation accepted")."<br>\n";
						break;
					case 'denied':
						$isUndoing = false;
						$tmpQuery = "DELETE FROM messages WHERE gameID = ".$_POST['gameID']." AND msgType = 'undo' AND msgStatus = 'denied' AND destination = '".$playersColor."'";
						mysql_query($tmpQuery);
						$statusMessage .= _("Move cancellation refused")."<br>\n";
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
						$statusMessage .= _("Draw proposal accepted")."<br>\n";
						break;
					case 'denied':
						$tmpQuery = "DELETE FROM messages WHERE gameID = ".$_POST['gameID']." AND msgType = 'draw' AND msgStatus = 'denied' AND destination = '".$playersColor."'";
						mysql_query($tmpQuery);
						$statusMessage .= _("Draw proposal refused")."<br>\n";
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
				$statusMessage .= _("Move cancellation pending")."<br>\n";
				break;
			case 'draw':
				$statusMessage .= _("Draw proposal pending")."<br>\n";
				break;
		}
	}	
	
	/* game level status: draws, resignations and checkmate */
	/* if checkmate, update games table */
	$Test = isset($_POST['isCheckMate']) ? $_POST['isCheckMate']:Null;
	if ($Test == 'true')
		mysql_query("UPDATE games SET gameMessage = 'checkMate', messageFrom = '".$playersColor."' WHERE gameID = ".$_POST['gameID']);

	$tmpQuery = "SELECT gameMessage, messageFrom FROM games WHERE gameID = ".$_POST['gameID'];
	$tmpMessages = mysql_query($tmpQuery);
	$tmpMessage = mysql_fetch_array($tmpMessages, MYSQL_ASSOC);
	
	if ($tmpMessage['gameMessage'] == "draw")
	{
		$statusMessage .= _("Draw game")."<br>\n";
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
		$statusMessage .= $tmpColor." "._("resign game")."<br>\n";
		$isGameOver = true;
	}

	if ($tmpMessage['gameMessage'] == "checkMate")
	{
		$statusMessage .= _("Check and Mat!")." ".$tmpColor." "._("win the game")."<br>\n";
		$isGameOver = true;
		$isCheckMate = true;
	}
}
	
?>