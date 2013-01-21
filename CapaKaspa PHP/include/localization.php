<?
// 1- By default display in english
$locale = "en_EN";
// 2- If client language is french display french
$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
if ($lang="fr") $locale = "fr_FR";
// 3- If user connected get preference language
if (isSet($_SESSION["pref_language"]) && $_SESSION["playerID"]!=-1) $locale = $_SESSION["pref_language"];
// 4- Finally language in URL
if (isSet($_GET["locale"])) $locale = $_GET["locale"];
putenv("LC_ALL=$locale");
setlocale(LC_ALL, $locale);
bindtextdomain("messages", "./locale");
textdomain("messages");
?>