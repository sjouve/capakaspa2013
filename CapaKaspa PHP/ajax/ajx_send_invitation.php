<?
/*
 * Display comments for an entity
 * 
 */
session_start();
// Parameters
if (!isset($_CONFIG))
	require '../include/config.php';

require '../dac/dac_players.php';
require '../bwc/bwc_common.php';

// Connect DB
require '../include/connectdb.php';

require '../include/localization.php';

// Load comments for an entity
$email=$_GET["email"];

$tmpPlayer = getPlayerByEmail($email);

if ($tmpPlayer)
{
	echo($tmpPlayer['nick']);
}
else
{
	$mailSubject = "[CapaKaspa] "._("Invitation to play chess game");
	$mailMsg = _("A player want to play with you a chess game on CapaKaspa !")."\n";
	$mailMsg .= _("Its user name is")." : ".getPlayerName(0, $_SESSION['nick'], $_SESSION['firstName'], $_SESSION['lastName']).")\n\n";
	$mailMsg .= _("Click here to sign-up and join him"." : http://jouerauxechecs.capakaspa.info \n\n");
	$res = sendMail($email, $mailSubject, $mailMsg);
}

mysqli_close($dbh);
?>