<?php
/* Countries list by language */
function listCountriesByLang($lang)
{
	global $dbh;
	$tmpQuery = "SELECT countryCode, countryName 
	FROM country
	WHERE countryLang = '".$lang."'
	ORDER BY countryName ASC";

	return mysqli_query($dbh,$tmpQuery);
}
?>