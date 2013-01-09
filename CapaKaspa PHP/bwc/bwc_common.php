<?
// Get language code 2 characters
function getLang()
{
	$lang = getenv("LC_ALL");
	return substr($lang, 0, 2);
}

// Number of days between 2 dates
function nbDays($debut, $fin) {
	$tDeb = explode("-", $debut);
	$tFin = explode("-", $fin);

	$diff = mktime(0, 0, 0, $tFin[1], $tFin[2], $tFin[0]) -
	mktime(0, 0, 0, $tDeb[1], $tDeb[2], $tDeb[0]);

	return(($diff / 86400)+1);

}

/* Affichage navigation pagination liste
 * $pge = numéro de la page courante
* $limit = nombre de résultats par page
* $nb_tot = nombre de résultats
* $nbpages = nombre de pages
*/
function displayPageNav($pge, $limit, $nb_tot, $nbpages)
{
	echo("<div id='navliste'>");
	// Affichage de la première page si nécessaire (si nb total de pages supérieur à 5)
	if($nbpages > 1 and $pge > 0)
		echo("<div class='bouton'><a href='javascript:loadPage(0)'><img src='images/bt_paginateur_premier.png'/></a></div> ");

	// AFFICHAGE DU LIEN PRECEDENT SI BESOIN EST (LA PREMIERE PAGES EST 0)
	if ($pge > 0)
	{
		$precedent = $pge - 1;
		echo("<div class='bouton'><a href='javascript:loadPage(".$precedent.")'><img src='images/bt_paginateur_precedent.png'/></a></div> ");
	}

	echo("<div class='pages'>");
	// AFFICHAGE DES NUMEROS DE PAGE
	$i=0;
	$j=1;
	if($nb_tot > $limit)
	{
		while($i < $nbpages)
		{ //  Pour limiter l'affichage du nombre de pages restantes
			if ($i > $pge-5 and $i < $pge+5)
			{
				if($i != $pge)
					echo("<a href='javascript:loadPage(".$i.")'>".$j."</a> ");
				else
					echo($j." "); // Page courante
			}
			$i++;
			$j++;
		}
	}
	echo("</div>");
		
	// AFFICHAGE DU LIEN SUIVANT SI BESOIN EST
	if($pge < $nbpages-1)
	{
		$suivant = $pge+1;
		echo("<div class='bouton'><a href='javascript:loadPage(".$suivant.")'><img src='images/bt_paginateur_suivant.png'/></a></div> ");
	}
	// Affichage de la dernière page si nécessaire
	if($nbpages > 1 and $pge < $nbpages-1)
	{
		$fin = $nbpages-1;
		echo("<div class='bouton'><a href='javascript:loadPage(".$fin.")'><img src='images/bt_paginateur_dernier.png'/></a></div> ");
	}
	echo("<div class='pages'> (".$nbpages." pages - ".$nb_tot." résultats)</div>");
	echo("</div>");
}
?>