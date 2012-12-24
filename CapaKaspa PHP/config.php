<?
	$_CONFIG = true;

	/* database settings */
	/* local server */
	$CFG_SERVER = "localhost";
	$CFG_USER = "root";
	$CFG_PASSWORD = "";
	$CFG_DATABASE = "capakaspa";
	
	/* remote server free */
	/*$CFG_SERVER = "sql.free.fr";
	$CFG_USER = "capakaspa";
	$CFG_PASSWORD = "b4rm7i3n";
	$CFG_DATABASE = "capakaspa";*/

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
	
	/* server settings */
	// session times out if user doesn't interact after 600 secs (10 mins)
	$CFG_SESSIONTIMEOUT = 6000;
	
	// number of days before untouched games expire		
	$CFG_EXPIREGAME = 14;			
	
	/* email notification requires PHP to be properly configured for */
	/* SMTP operations.  This flag allows you to easily activate
						   or deactivate this feature.  It is highly recommended you test
						   it before putting it into production */
	$CFG_USEEMAILNOTIFICATION = true;	
	
	// email address people see when receiving CapaKaspa generated mail
	$CFG_MAILADDRESS = "capakaspa@capakaspa.info";
	
	/* NON UTILISE : A SUPPRIMER */
	// min number of secs between automatic reloads reloads
	$CFG_MINAUTORELOAD = 5;			
	$CFG_MAXUSERS = 50;
	$CFG_MAXACTIVEGAMES = 10;
	// whether a user can change their nick from the main menu
	$CFG_NICKCHANGEALLOWED = false;		
?>
