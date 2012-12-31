<? /* functions for outputting to html and javascript */

    /* Utilisé pour la mosaique */
    function drawboardGame($gameID, $whitePlayer, $blackPlayer, $position)
	{

		global $isPlayersTurn;

        // Nombre de 1/2 coups
        $allMoves = mysql_query("SELECT count(gameID) nbMove FROM history WHERE gameID = ".$gameID." ORDER BY timeOfMove");
		$thisMove = mysql_fetch_array($allMoves, MYSQL_ASSOC);
		$numMoves = $thisMove['nbMove'] - 1;

        // Remplir l'échiquier
		$strPos = 0;
		for ($i = 0; $i < 8; $i++)
			for ($j = 0; $j < 8; $j++)
			{
				$board[$i][$j] = getPieceCodeChar($position{$strPos});
				$strPos++;
			}

		/* Couleur du joueur qui charge la partie */
		if ($whitePlayer == $_SESSION['playerID'])
			$playersColor = "white";
		else if ($blackPlayer == $_SESSION['playerID'])
			$playersColor = "black";
		else
            /* Le joueur ne joue pas la partie */
            $playerColor = "";


		/* find out if it's the current player's turn */
		if (( (($numMoves == -1) || (($numMoves % 2) == 1)) && ($playersColor == "white"))
				|| ((($numMoves % 2) == 0) && ($playersColor == "black")) )
			$isPlayersTurn = true;
		else
			$isPlayersTurn = false;

		/* determine who's perspective of the board to show */
        $perspective = $playersColor;


		echo ("<table border='0' bgcolor='#000000' cellpadding='0' cellspacing'0'><tr><td><table bgcolor='#ffffff' border='0' cellpadding='0' cellspacing'0'>\n");


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
					echo ("#AA7777'>");
				else
					echo ("#CCBBBB'>");

				echo ("<img name='pos$i-$j' src='images/mosaique/");

				/* if position is empty... */
				if ($board[$i][$j] == 0)
				{
					/* draw empty square */
					$tmpALT="blank";
				}
				else
				{
					/* draw correct piece */
					if ($board[$i][$j] & BLACK)
						$tmpALT = "black_";
					else
						$tmpALT = "white_";

					$tmpALT .= getPieceName($board[$i][$j]);
				}

				echo($tmpALT.".gif' height='25' width='25' border='0' alt='".$tmpALT."'>");

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
		if (( (($numMoves == -1) || (($numMoves % 2) == 1)) && ($playersColor == "white"))
				|| ((($numMoves % 2) == 0) && ($playersColor == "black")) )
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
				echo ("   <td bgcolor='");

				/* if board is disabled, show board in grayscale */
				if ($isDisabled)
				{
					if (($j + ($i % 2)) % 2 == 0)
						echo ("#444444'>");
					else
						echo ("#BBBBBB'>");
				}
				else
				{
					if (($j + ($i % 2)) % 2 == 0)
						echo ("#AA7777'>");
					else
						echo ("#CCBBBB'>");
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

				echo ("<img name='pos$i-$j' src='images/".$_SESSION['pref_theme']."/");

				/* if position is empty... */
				if ($board[$i][$j] == 0)
				{
					/* draw empty square */
					$tmpALT="blank";
				}
				else
				{
					/* draw correct piece */
					if ($board[$i][$j] & BLACK)
						$tmpALT = "black_";
					else
						$tmpALT = "white_";

					$tmpALT .= getPieceName($board[$i][$j]);
				}

				echo($tmpALT.".gif' height='35' width='35' border='0' alt='".$tmpALT."'>");

				if (!$isDisabled && $isPlayersTurn)
					echo ("</a>");

				echo ("</td>\n");
			}

			echo ("</tr>\n");
		}

		echo ("</table>\n\n");
	}

	function writeJSboard()
	{
		global $board, $numMoves;

		/* old PHP versions don't have _POST, _GET and _SESSION as auto_globals */
		if (!minimum_version("4.1.0"))
			global $_POST, $_GET, $_SESSION;

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
	function writeJSHistory()
	{
		global $history, $numMoves;

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

	function writeVerbousHistory()
	{
		global $history, $numMoves;

		echo ("<table width='300' border='0'>\n");
		echo ("<tr><th bgcolor='beige' colspan='2'>Feuille de partie</th></tr>\n");

		for ($i = $numMoves; $i >= 0; $i--)
		{
			if ($i % 2 == 1)
			{
				echo ("<tr bgcolor='black'>");
				echo ("<td width='20'><font color='white'>".($i + 1)."</font></td><td><font color='white'>");
			}
			else
			{
				echo ("<tr bgcolor='white'>");
				echo ("<td width='20'>".($i + 1)."</td><td><font color='black'>");
			}

			$tmpReplaced = "";
			if (!is_null($history[$i]['replaced']))
				$tmpReplaced = $history[$i]['replaced'];

			$tmpPromotedTo = "";
			if (!is_null($history[$i]['promotedTo']))
				$tmpPromotedTo = $history[$i]['promotedTo'];

			$tmpCheck = ($history[$i]['isInCheck'] == 1);

			echo(moveToVerbousString($history[$i]['curColor'], $history[$i]['curPiece'], $history[$i]['fromRow'], $history[$i]['fromCol'], $history[$i]['toRow'], $history[$i]['toCol'], $tmpReplaced, $tmpPromotedTo, $tmpCheck));

			echo ("</font></td></tr>\n");
		}

		echo ("<tr bgcolor='#BBBBBB'><td>0</td><td>Nouvelle partie</td></tr>\n");
		echo ("</table>\n");
	}

	function writeHistoryPGN()
	{
		global $history, $numMoves;

		
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

	function writeHistory()
	{
		
		$listeCoups = writeHistoryPGN();
		return $listeCoups;
	}

	function writeStatus()
	{
		global $numMoves, $history, $isCheckMate, $statusMessage, $isPlayersTurn, $whiteNick, $blackNick, $whitePlayerID, $blackPlayerID, $ecoCode, $ecoName, $dateCreated, $whiteElo, $blackElo, $whiteSocialID, $whiteSocialNet, $blackSocialID, $blackSocialNet;
			
		?>
		<table border="0" width="300" align="center" cellspacing="0" cellpadding="0">
		<tr bgcolor="beige" valign="top">
			<th width="15%">
				<img src="<?echo(getPicturePath($whiteSocialNet, $whiteSocialID));?>" width="40" height="40" style="margin:3px;"/>
			</th>
			<th width="35%" align="left">
			<?
				if ($isPlayersTurn)
				{	
					echo("<div class='playername'><a href='profil_consultation.php?playerID=".$whitePlayerID."'>".$whiteNick."</a><br/>".$whiteElo);
					if (getOnlinePlayer($whitePlayerID)) echo (" <img src='images/user_online.gif'/>");
					if ($whiteNick == $_SESSION['nick']) echo (" <img src='images/hand.gif'/>");
					echo("</div>");
				}
				else
				{
					if ($whiteNick == $_SESSION['nick'] || $blackNick == $_SESSION['nick'])
					{
						echo("<div class='playername'><a href='profil_consultation.php?playerID=".$whitePlayerID."'>".$whiteNick."</a><br/>".$whiteElo);
						if (getOnlinePlayer($whitePlayerID)) echo (" <img src='images/user_online.gif'/>");
						if ($whiteNick != $_SESSION['nick']) echo (" <img src='images/hand.gif'/>"); 
						echo("</div>");
					}
					else
					{
					  	echo("<div class='playername'><a href='profil_consultation.php?playerID=".$whitePlayerID."'>".$whiteNick."</a><br/>".$whiteElo);
					  	if (getOnlinePlayer($whitePlayerID)) echo (" <img src='images/user_online.gif'/>");
						echo("</div>");
					}
				}
			?>
			</th>
			<th width="35%" align="right">
			<?
				if ($isPlayersTurn)
				{
					
					echo("<div class='playername'><a href='profil_consultation.php?playerID=".$blackPlayerID."'>".$blackNick."</a><br/>");
					if ($blackNick == $_SESSION['nick']) echo ("<img src='images/hand.gif'/> ");
					if (getOnlinePlayer($blackPlayerID)) echo (" <img src='images/user_online.gif'/>");
					echo($blackElo."</div>");	
				}
				else
				{
					if ($whiteNick == $_SESSION['nick'] || $blackNick == $_SESSION['nick'])
					{
						
						echo("<div class='playername'><a href='profil_consultation.php?playerID=".$blackPlayerID."'>".$blackNick."</a><br/>");
						if ($blackNick != $_SESSION['nick']) echo ("<img src='images/hand.gif'/> ");
						if (getOnlinePlayer($blackPlayerID)) echo (" <img src='images/user_online.gif'/>");
						echo($blackElo."</div>");	
					}
					else
					{
					  	echo("<div class='playername'><a href='profil_consultation.php?playerID=".$blackPlayerID."'>".$blackNick."</a><br/>");
					  	if (getOnlinePlayer($blackPlayerID)) echo (" <img src='images/user_online.gif'/>");
					  	echo($blackElo."</div>");
					}
				}
			?>
			</th>
			<th width="15%">
				<img src="<?echo(getPicturePath($blackSocialNet, $blackSocialID));?>" width="40" height="40" style="margin:3px;"/><br/>
			</th>
		</tr>
		<tr bgcolor="beige">
			<th colspan="4">
				<div class="econame"><?echo("[".$ecoCode."] ".$ecoName);?></div>
				<div class="econame"><a href="manuel-utilisateur-jouer-echecs-capakaspa.pdf#page=6" target="_blank" title="<?php echo _("Open help")?>"><img src="images/point-interrogation.gif" border="0"/></a> <a href="javascript:document.gamedata.submit();"><img src="images/icone_rafraichir.png" border="0" title="<?echo _("Refresh game")?>" alt="<?echo _("Refresh game")?>"/></a>
               <?echo _("Game started at")?> : <? echo($dateCreated);?></div>
			</th>
			
		</tr>
		
		<tr>
		<?
		if (($numMoves == -1) || ($numMoves % 2 == 1))
			$curColor = _("Whites");
		else
			$curColor = _("Blacks");

		if (!$isCheckMate && ($history[$numMoves]['isInCheck'] == 1))
			echo("<td align='center' bgcolor='red' colspan='4'>\n<b>".$curColor." "._("are in check")." !</b><br/>\n".$statusMessage."</td>\n");
		else
			echo("<td align='center' colspan='4'><b>".$statusMessage."&nbsp;</b></td>\n");
			
		?>
		</tr>
		</table>
		<?
	}

	function writePromotion($isMobile)
	{
	?>
		
		<table <?if (!$isMobile) {?>width="350"<?};?> border="0">
		<tr><td>
			<?echo _("Promote the pawn in")?> :
			<br>
			<input type="radio" name="promotion" value="<? echo (QUEEN); ?>" checked="checked"> <?echo _("Queen")?>
			<input type="radio" name="promotion" value="<? echo (ROOK); ?>"> <?echo _("Rook")?>
			<input type="radio" name="promotion" value="<? echo (KNIGHT); ?>"> <?echo _("Knight")?>
			<input type="radio" name="promotion" value="<? echo (BISHOP); ?>"> <?echo _("Bishop")?>
			<input type="button" name="btnPromote" value="<? echo _("OK")?>" class="button" onClick="promotepawn()" />
		</td></tr>
		</table>
		
	<?
	}

	function writeUndoRequest($isMobile)
	{
	?>
		
		<table <?if (!$isMobile) {?>width="350"<?};?> border="0">
		<tr><td>
			<?echo _("Your opponent wants to cancel last move. Are you agree ?")?>
			<br>
			<input type="radio" name="undoResponse" value="yes"> <?echo _("Yes")?>
			<input type="radio" name="undoResponse" value="no" checked="checked"> <?echo _("No")?>
			<input type="hidden" name="isUndoResponseDone" value="no">
			<input type="button" value="<? echo _("OK")?>" class="button" onClick="this.form.isUndoResponseDone.value = 'yes'; this.form.submit()">
		</td></tr>
		</table>
		
	<?
	}

	function writeDrawRequest($isMobile)
	{
	?>
		
		<table <?if (!$isMobile) {?>width="350"<?};?> border="0">
		<tr><td>
			<?echo _("Your opponent do a draw proposal. Are you agree ?")?>
			<br>
			<input type="radio" name="drawResponse" value="yes"> <?echo _("Yes")?>
			<input type="radio" name="drawResponse" value="no" checked="checked"> <?echo _("No")?>
			<input type="hidden" name="isDrawResponseDone" value="no">
			<input type="button" value="<? echo _("OK")?>" class="button" onClick="this.form.isDrawResponseDone.value = 'yes'; this.form.submit()">
		</td></tr>
		</table>
		
	<?
	}
?>
