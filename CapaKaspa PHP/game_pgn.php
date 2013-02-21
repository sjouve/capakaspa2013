<?
header('Content-disposition: attachment; filename=game.pgn');
header('Content-type: text/plain');
session_start();

/* load settings */
if (!isset($_CONFIG))
	require 'include/config.php';

require 'dac/dac_games.php';
require 'bwc/bwc_common.php';
require 'bwc/bwc_games.php';
require 'bwc/bwc_chessutils.php';
require 'include/localization.php';
require 'include/constants.php';


/* connect to database */
require 'include/connectdb.php';

$gameID = $_GET['id'];

$tmpGame = getGame($gameID);
loadHistory($gameID);
$listeCoups = writeHistoryPGN($history, $numMoves);
$strPGN = getPGN($tmpGame['whiteNick'], $tmpGame['blackNick'], $tmpGame['type'], $tmpGame['flagBishop'], $tmpGame['flagKnight'], $tmpGame['flagRook'], $tmpGame['flagQueen'], $listeCoups);

echo $strPGN;
mysql_close();
?>