<?
$gameID = $_GET['id'];
header('Content-disposition: attachment; filename=game'.$gameID.'.pgn');
header('Content-type: text/plain');
session_start();

/* load settings */
if (!isset($_CONFIG))
	require 'include/config.php';

require 'dac/dac_games.php';
require 'bwc/bwc_common.php';
require 'bwc/bwc_games.php';
require 'bwc/bwc_chessutils.php';
require 'dac/dac_players.php';
require 'include/localization.php';
require 'include/constants.php';

/* debug flag */
define ("DEBUG", 0);

/* connect to database */
require 'include/connectdb.php';

$tmpGame = getGame($gameID);
loadHistory($gameID);
$gameResult = processMessages($tmpGame);
$listeCoups = writeHistoryPGN($history, $numMoves);
$strPGN = getPGN($tmpGame['whiteNick'], $tmpGame['blackNick'], $tmpGame['type'], $tmpGame['flagBishop'], $tmpGame['flagKnight'], $tmpGame['flagRook'], $tmpGame['flagQueen'], $tmpGame['chess960'], $listeCoups, $gameResult);

echo $strPGN;
mysql_close();
?>