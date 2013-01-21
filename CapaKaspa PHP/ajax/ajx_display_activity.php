<?
/*
 * Display activity for followings
 * 
 */
session_start();
// Parameters
if (!isset($_CONFIG))
	require '../include/config.php';
require '../include/localization.php';

// Connect DB
require '../include/connectdb.php';
require '../dac/dac_activity.php';
require 'bwc_common.php';
require 'bwc_games.php';

// Load activity from 
$start=$_GET["start"];

$fmt = new IntlDateFormatter(getenv("LC_ALL"), IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);
$limit = 19;
$tmpActivities = listActivityFollowing($start, $limit, $_SESSION['playerID']);

while($tmpActivity = mysql_fetch_array($tmpActivities, MYSQL_ASSOC))
{
	$postDate = new DateTime($tmpActivity['postDate']);
	$strPostDate = $fmt->format($postDate);
	echo("
		<div class='activity'>
				<div class='leftbar'>
					<img src='".getPicturePath("FB", "sebastien.jouve.fr")." width='40' height='40' border='0'/>
				</div>
				<div class='details'>
					<div class='title'>
						<a href=''><span class='name'>Sébastien Jouve</span></a> a joué le coup 1.e4 contre <span class='name'>Eric Jouve</span>
					</div>
					<div class='content'>
						".drawboardGame(2798, 1, 408, "tcfdrfctppp0pppp00000000000p00000000000000000000PPPPPPPPTCFDRFCT")."
					</div>
					<div class='footer'>
						! Bon - ");?> <a href="javascript:displayComment('<?echo(ACTIVITY);?>', 1);">Commenter</a> <? echo("- <span class='date'>Il y a 30 minutes</span>
					</div>
					<div class='comment' id='comment1'>
						"._("Load comments...")."
					</div>
				</div>
			</div>
	");
}
mysql_close();
?>
<!--
			Id joueur activité
			Photo joueur activité
			Non joueur activité
			Elo joueur activité
			Message activité
			Date activité
			Position partie
			PARTIE
			Id joueur adversaire
			Nom joueur adversaire
			Elo joueur adversaire
			Id partie
			ECO code partie
			Position partie
			ECO libellé
			
			Messages : coup, res:victoire, res:défaire, res:nulle, propose nulle, 
		 -->