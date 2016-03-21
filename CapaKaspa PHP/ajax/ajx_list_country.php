<?
/*
 * Display countries
 * 
 */
session_start();
// Parameters
if (!isset($_CONFIG))
	require '../include/config.php';

require '../dac/dac_common.php';
require '../bwc/bwc_common.php';

// Connect DB
require '../include/connectdb.php';

require '../include/localization.php';

echo "\t",'<option value="0">', _("Select your country") ,'</option>',"\n";
$tmpCountries = listAllCountriesByLang(getLang());
while($tmpCountry = mysqli_fetch_array($tmpCountries, MYSQLI_ASSOC))
{
	$selected = "";
	$countryCode = isset($_POST['txtCountryCode'])?$_POST['txtCountryCode']:"";
	if($tmpCountry['countryCode'] == $countryCode)
	{
		$selected = " selected";
	}
	echo "\t",'<option value="', $tmpCountry['countryCode'] ,'"', $selected ,'>', $tmpCountry['countryName'] ,'</option>',"\n";
}	
		           
mysqli_close($dbh);
?>