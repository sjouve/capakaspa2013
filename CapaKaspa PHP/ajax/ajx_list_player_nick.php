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

// Connect DB
require '../include/connectdb.php';

require '../include/localization.php';

// List players
$str=$_GET["str"];

$tmpPlayers = listPlayersByNickName($str);

if (mysql_num_rows($tmpPlayers) > 0)
{
	while($tmpPlayer = mysql_fetch_array($tmpPlayers, MYSQL_ASSOC))
	{
		echo("<option value='".$tmpPlayer['playerID']."'>".$tmpPlayer['nick']." (".$tmpPlayer['firstName']." ".$tmpPlayer['lastName'].")</option>");
	}
}
else
{
	echo("<option value='' selected>"._("No player found")."</option>");
}


mysql_close();
?>