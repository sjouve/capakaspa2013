<?
/* Depend on:
require 'include/constants.php';
require_once('bwc/bwc_chessutils.php');
require 'dac/dac_players.php';
require 'bwc/bwc_board.php';
require 'bwc/bwc_games.php';
*/

/* Batch activation */
function batchActivation()
{
	$listPlayers = listPlayers();
	$precEmail = "";
	while($player = mysqli_fetch_array($listPlayers, MYSQLI_ASSOC))
	{
		$res = updatePlayer($player['playerID'], $player['PASSWORD'], $player['firstName'], $player['lastName'], $player['nick'], $player['email'], $player['profil'], $player['situationGeo'], $player['anneeNaissance'], 0);
		
		if ($precEmail != $player['email'])
		{
			// Envoi du message de confirmation
			$mailSubject = "[CapaKaspa] Confirmation de votre inscription";
			$mailMsg = "Vous recevez ce message car vous disposez d'un compte pour la zone de jeu en différé du site CapaKaspa :\n";
			$mailMsg .= "- Surnom : ".$player['nick']."\n";
			$mailMsg .= "- Passe : ".$player['PASSWORD']."\n\n";
			$mailMsg .= "La procédure d'inscription ayant évoluée, les anciens comptes doivent maintenant confirmer leur inscription.\n\n";
			$mailMsg .= "Pour activer votre compte veuillez cliquer sur le lien suivant (en cas de problème copier le lien dans la barre d'adresse de votre navigateur) :\n";
			$mailMsg .= "http://www.capakaspa.info/index.php?ToDo=activer&playerID=".$player['playerID']."&nick=".$player['nick'];
			$mailMsg .= "\n\nCe message a été envoyé automatiquement à partir du site CapaKaspa (http://www.capakaspa.info).\n";
			
			$res = sendMail($player['email'], $mailSubject, $mailMsg);
			echo($player['nick']." - ".$player['email']);
			$precEmail = $player['email'];
		}
	}
		
}

/* Batch mise à jour des positions */
function batchPosition()
{
	$games = mysqli_query($dbh,"SELECT gameID FROM games");
	while ($thisGame = mysqli_fetch_array($games, MYSQLI_ASSOC))
	{		
		echo($thisGame['gameID']." - ");
		$position = "000000000000000000000000000000000000000000000000000000000000000000000000";
		
		$pieces = mysqli_query($dbh,"SELECT * FROM pieces WHERE gameID = ".$thisGame['gameID']." ORDER BY row, col");
	
		
		while ($thisPiece = mysqli_fetch_array($pieces, MYSQLI_ASSOC))
		{
			$position{8*$thisPiece["row"]+$thisPiece["col"]} = getPieceCharFromName($thisPiece["color"], $thisPiece["piece"]);
			
		}
		
		echo($position." | ");
		
		$res = mysqli_query($dbh,"UPDATE games SET position = '".$position."' WHERE gameID = ".$thisGame['gameID']);
		
	}
}

/* Mise à jour du code ECO des anciennes parties */
function batchEco()
{
	global $board;
	$games = mysqli_query($dbh,"SELECT gameID FROM games WHERE eco is null OR eco = ''");
	
	while ($thisGame = mysqli_fetch_array($games, MYSQLI_ASSOC))
	{
	  	echo("Partie : ".$thisGame['gameID']."\n");
		//
		//
		// initBoard
	  	initBoard();
	  	$ecoCode = "";
		$allMoves = mysqli_query($dbh,"SELECT * FROM history WHERE gameID = ".$thisGame['gameID']." ORDER BY timeOfMove");

		$numMoves = -1;
		while ($thisMove = mysqli_fetch_array($allMoves, MYSQLI_ASSOC))
		{
			$numMoves++;
			if ($numMoves>20)
				break;
				
			//
			//
			// Adapter doMove avec le coup
			/* if moving en-passant */
			/* (ie: if pawn moves diagonally without replacing anything) */
			if ((($board[$thisMove['fromRow']][$thisMove['fromCol']] & COLOR_MASK) == PAWN) && ($thisMove['toCol'] != $thisMove['fromCol']) && ($board[$thisMove['toRow']][$thisMove['toCol']] == 0))
				/* delete eaten pawn */
				$board[$thisMove['fromRow']][$thisMove['toCol']] = 0;
			
			/* move piece to destination, replacing whatever's there */
			$board[$thisMove['toRow']][$thisMove['toCol']] = $board[$thisMove['fromRow']][$thisMove['fromCol']];
	
			/* delete piece from old position */
			$board[$thisMove['fromRow']][$thisMove['fromCol']] = 0;
	
			/* if not Undoing, but castling */
			if (($doUndo != "yes") && (($board[$thisMove['toRow']][$thisMove['toCol']] & COLOR_MASK) == KING) && (($thisMove['toCol'] - $thisMove['fromCol']) == 2))
			{
				/* castling to the right, move the right rook to the left side of the king */
				$board[$thisMove['toRow']][5] = $board[$thisMove['toRow']][7];
	
				/* delete rook from original position */
				$board[$thisMove['toRow']][7] = 0;
			}
			elseif (($doUndo != "yes") && (($board[$_POST['toRow']][$_POST['toCol']] & COLOR_MASK) == KING) && (($_POST['fromCol'] - $_POST['toCol']) == 2))
			{
				/* castling to the left, move the left rook to the right side of the king */
				$board[$_POST['toRow']][3] = $board[$_POST['toRow']][0];
	
				/* delete rook from original position */
				$board[$_POST['toRow']][0] = 0;
			}
			
			
			$position = "";
			
			//
			//
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
			
			echo("Position : ".$position."\n");
			
			// A qui le tour
			if (($numMoves == -1) || ($numMoves % 2 == 1))
				$turnColor = "w";
			else
				$turnColor = "b";
				
			// Contrôle code ECO de la position
			$fen_eco = getEco($position);
			if ($fen_eco)
			{
				$newEco = $fen_eco['eco'];
				$turnColorEco = $fen_eco['trait'];
			};
			
			if ($newEco != $ecoCode && $turnColorEco == $turnColor)
			{
				echo("ECO : ".$ecoCode."\n");
				$ecoCode = $newEco;
				// Mettre à jour la date du dernier coup et la position
				$res = mysqli_query($dbh,"UPDATE games SET eco = '".$ecoCode."' WHERE gameID = ".$thisGame['gameID']);
			}
			
		}		
	}
}

/** 
Pour chaque joueur
- Enregistrer le ELO initial
- Compter le nombre de parties jouées
- Compter le nombre de victoires
- Compter le nombre de nulles
- Compter le nombre de défaites
- Calculer la moyenne ELO des adversaires
- Appliquer la formule
- Enregistrer le nouvel ELO dans l'historique
Fin Pour Chaque
**/
function calculerElo($type)
{
	global $dbh;
	// Dates
	$dateDeb = date('Y-m-d', mktime(0,0,0,date('m')-1,1,date('Y')));
	$dateFin = date('Y-m-d', mktime(0,0,0,date('m'),0,date('Y')));
	echo("Période du ".$dateDeb." au ".$dateFin."<br/>");
	$listPlayers = listPlayersForElo($dateDeb, $dateFin, $type);
	$bonusMalusTable = array(100	=>	470	,
			99	=>	470	,
			98	=>	470	,
			97	=>	470	,
			96	=>	470	,
			95	=>	470	,
			94	=>	444	,
			93	=>	422	,
			92	=>	401	,
			91	=>	383	,
			90	=>	366	,
			89	=>	351	,
			88	=>	336	,
			87	=>	322	,
			86	=>	309	,
			85	=>	296	,
			84	=>	284	,
			83	=>	273	,
			82	=>	262	,
			81	=>	251	,
			80	=>	240	,
			79	=>	230	,
			78	=>	220	,
			77	=>	211	,
			76	=>	202	,
			75	=>	193	,
			74	=>	184	,
			73	=>	175	,
			72	=>	166	,
			71	=>	158	,
			70	=>	149	,
			69	=>	141	,
			68	=>	133	,
			67	=>	125	,
			66	=>	117	,
			65	=>	110	,
			64	=>	102	,
			63	=>	95	,
			62	=>	87	,
			61	=>	80	,
			60	=>	72	,
			59	=>	65	,
			58	=>	57	,
			57	=>	50	,
			56	=>	43	,
			55	=>	36	,
			54	=>	29	,
			53	=>	21	,
			52	=>	14	,
			51	=>	7	,
			50	=>	0	,
			49	=>	-7	,
			48	=>	-14	,
			47	=>	-21	,
			46	=>	-29	,
			45	=>	-36	,
			44	=>	-43	,
			43	=>	-50	,
			42	=>	-57	,
			41	=>	-65	,
			40	=>	-72	,
			39	=>	-80	,
			38	=>	-87	,
			37	=>	-95	,
			36	=>	-102	,
			35	=>	-110	,
			34	=>	-117	,
			33	=>	-125	,
			32	=>	-133	,
			31	=>	-141	,
			30	=>	-149	,
			29	=>	-158	,
			28	=>	-166	,
			27	=>	-175	,
			26	=>	-184	,
			25	=>	-193	,
			24	=>	-202	,
			23	=>	-211	,
			22	=>	-220	,
			21	=>	-230	,
			20	=>	-240	,
			19	=>	-251	,
			18	=>	-262	,
			17	=>	-273	,
			16	=>	-284	,
			15	=>	-296	,
			14	=>	-309	,
			13	=>	-322	,
			12	=>	-336	,
			11	=>	-351	,
			10	=>	-366	,
			9	=>	-383	,
			8	=>	-401	,
			7	=>	-422	,
			6	=>	-444	,
			5	=>	-470	,
			4	=>	-470	,
			3	=>	-470	,
			2	=>	-470	,
			1	=>	-470	,
			0   =>	-470);

	while($player = mysqli_fetch_array($listPlayers, MYSQLI_ASSOC))
	{
		if ($type==0)
			$eloInitial = $player['elo'];
		else
			$eloInitial = $player['elo960'];
		$eloFinal = $eloInitial;
		$nbParties = 0;
		
		$countLost = countLostElo($player['playerID'], $dateDeb, $dateFin, $type);
		$nbDefaites = $countLost['nbGames'];
		$countDraw = countDrawElo($player['playerID'], $dateDeb, $dateFin, $type);
		$nbNulles = $countDraw['nbGames'];
		$countWin = countWinElo($player['playerID'], $dateDeb, $dateFin, $type);
		$nbVictoires = $countWin['nbGames'];
		$nbParties = $nbDefaites + $nbNulles + $nbVictoires;
		
		echo($player['nick']." : ".$nbDefaites."/".$nbNulles."/".$nbVictoires."/".$eloInitial."<br/>");
		
		if ($nbParties>0)
		{
			
			// Moyenne elo des adversaires
			$listEndedGames = listEndedGames($player['playerID'], $dateDeb, $dateFin, $type);
			$sommeElo = 0;
			$moyenneElo = 0;
			echo("<table border='1'><tr><th>B</th><th>ELO</th><th>N</th><th>ELO</th></tr>");
			while($game = mysqli_fetch_array($listEndedGames, MYSQLI_ASSOC))
			{
				$whiteID = $game['whitePlayer'];
				$whiteElo = $game['whiteElo'];
				$blackID = $game['blackPlayer'];
				$blackElo = $game['blackElo'];
				
				if ($whiteID == $player['playerID'])
				{
					$sommeElo = $sommeElo + $blackElo;
				}
				else
				{
					$sommeElo = $sommeElo + $whiteElo;
				}
				
				echo("<tr><td>".$whiteID."</td><td>".$whiteElo."</td><td>".$blackID."</td><td>".$blackElo."</td></tr>");
				
			}
			echo("</table><br/>");
			
			$moyenneElo = ceil($sommeElo/$nbParties);
			
			// Bonus/Malus
			$pourcPoint = ceil((($nbVictoires*1)+($nbNulles*0.5)+($nbDefaites*0))/$nbParties*100);
			$bonusMalus = $bonusMalusTable[$pourcPoint];
			
			// Calcul performance
			$performance = $moyenneElo + $bonusMalus;
			
			echo("MOY:".$moyenneElo." %:".$pourcPoint." BM:".$bonusMalus." P:".$performance."<br/>");
			
			// Calcul nouvel ELO
			if ($performance < $eloInitial)
			{
				
				//Elo=(((16 x Elo initial)+(Performance x nombre de parties) / (16 + nombre de parties)
				$eloFinal = ceil(((16*$eloInitial)+($performance*$nbParties))/(16+$nbParties));
			}
			else
			{
				if ($nbParties>=16)
				{
					
					$eloFinal = ceil($performance);  
				}
				else
				{
				 	
					 //Elo=(((16 - nombres de parties) x Elo initial)+(nombre de parties x performance))/16
					 $eloFinal = ceil((((16-$nbParties)*$eloInitial)+($nbParties*$performance))/16); 
				}
			}
			
			
			
			// Mise à jour historique
		}
			$eloProgress = 0;
			if ($eloInitial>$eloFinal) $eloProgress = 1;
			if ($eloInitial<$eloFinal) $eloProgress = -1;	
			// Mise à jour ELO player
			if ($type==0)
				$res_player = mysqli_query($dbh,"UPDATE players SET elo=".$eloFinal.", eloProgress =".$eloProgress." WHERE playerID = ".$player['playerID']);
			else
				$res_player = mysqli_query($dbh,"UPDATE players SET elo960=".$eloFinal." WHERE playerID = ".$player['playerID']);
			
		echo("=> ELO = ".$eloFinal."<br/><hr/><br/>");
	}
	return 1;
}

?>