<?
// Get language code 2 characters
function getLang()
{
	$lang = getenv("LC_ALL");
	return substr($lang, 0, 2);
} 
?>