<?
	$_CONFIG = true;

	/* database settings */
	/* local server */
	$CFG_SERVER = "127.0.0.1";
	$CFG_USER = "root";
	$CFG_PASSWORD = "";
	$CFG_DATABASE = "capakaspa_prod";

    /* remote server 1and1 */
	/*$CFG_SERVER = "db317.1and1.fr";
	$CFG_USER = "dbo148414304";
	$CFG_PASSWORD = "4tgtrwae";
	$CFG_DATABASE = "db148414304";*/
	
	/* remote server 1and1 test */
	/*$CFG_SERVER = "db496.1and1.fr";
	$CFG_USER = "dbo151183899";
	$CFG_PASSWORD = "NMFTAtrX";
	$CFG_DATABASE = "db151183899";*/
	
	/* remote server 1and1 capakaspa2013 */
	/*$CFG_SERVER = "db455196105.db.1and1.com";
	$CFG_USER = "dbo455196105";
	$CFG_PASSWORD = "sj230570";
	$CFG_DATABASE = "db455196105";*/
	
	/* server settings */
	// session times out if user doesn't interact after 600 secs (10 mins)
	$CFG_SESSIONTIMEOUT = 6000;
	
	/* email notification requires PHP to be properly configured for */
	/* SMTP operations.  This flag allows you to easily activate
						   or deactivate this feature.  It is highly recommended you test
						   it before putting it into production */
	$CFG_USEEMAILNOTIFICATION = true;	
	
	// email address people see when receiving CapaKaspa generated mail
	$CFG_MAILADDRESS = "capakaspa@capakaspa.info";		
?>