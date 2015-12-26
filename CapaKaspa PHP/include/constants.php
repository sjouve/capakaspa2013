<?
// chess constants
define ("EMPTY", 0);	/* 0000 0000 */
define ("PAWN", 1);	/* 0000 0001 */
define ("KNIGHT", 2);	/* 0000 0010 */
define ("BISHOP", 4);	/* 0000 0100 */
define ("ROOK", 8);	/* 0000 1000 */
define ("QUEEN", 16);	/* 0001 0000 */
define ("KING", 32);	/* 0010 0000 */
define ("BLACK", 128);	/* 1000 0000 */
define ("WHITE", 0);
define ("COLOR_MASK", 127);	/* 0111 1111 */

// Entity type
define ("HISTORY", "history");
define ("MESSAGE", "message");
define ("GAME", "game");
define ("ACTIVITY", "activity");
define ("COMMENT", "comment");

// Game type
define ("CLASSIC", 0);
define ("BEGINNER", 1);
define ("CHESS960", 2);

// Tournament status
define ("WAITING", "WT");
define ("INPROGRESS", "IP");
define ("ENDED", "ED");
?>