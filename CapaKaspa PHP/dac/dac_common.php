<?php
/* Countries list by language */
function listCountriesByLang($lang)
{
	$tmpQuery = "SELECT countryCode, countryName 
	FROM country
	WHERE countryLang = '".$lang."'
	ORDER BY countryName ASC";

	return mysql_query($tmpQuery);
}
?>