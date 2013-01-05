<?php
$locale = "en_EN";
$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
if ($lang="fr") $locale = "fr_FR";
if (isSet($_GET["locale"])) $locale = $_GET["locale"];
putenv("LC_ALL=$locale");
setlocale(LC_ALL, $locale);
bindtextdomain("messages", "./locale");
textdomain("messages");
?>