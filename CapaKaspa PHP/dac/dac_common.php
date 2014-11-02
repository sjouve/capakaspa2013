<?php
/* Countries with players list by language */
function listCountriesByLang($lang)
{
	global $dbh;
	$tmpQuery = "SELECT C.countryCode, C.countryName 
	FROM country C, players P
	WHERE countryLang = '".$lang."'
	AND C.countryCode = P.countryCode
	AND P.activate = 1
	GROUP BY C.countryCode, C.countryName
	ORDER BY countryName ASC";

	return mysqli_query($dbh,$tmpQuery);
}

/* All Countries list by language */
function listAllCountriesByLang($lang)
{
	global $dbh;
	$tmpQuery = "SELECT countryCode, countryName 
	FROM country
	WHERE countryLang = '".$lang."'
	ORDER BY countryName ASC";

	return mysqli_query($dbh,$tmpQuery);
}
?>