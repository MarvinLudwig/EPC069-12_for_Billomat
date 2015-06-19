<?php

// Must be adjusted:

define("API_KEY", ""); // the API-Key from Billomat
define("USER_ID", ""); // your Billomat user ID

// Can be adjusted:

define("PAYMENT_REFERENCE_INVOICE", "invoice_number, label"); // invoice information that is used as payment reference
define("PAYMENT_REFERENCE_CLIENT", "client_number, name"); // client information that is used as payment reference
define("APP_ID", ""); // Raise the 15 minute limit from 300 to 1000 calls: https://{YOUR_USER_ID}.billomat.net/portal/apps
define("APP_SECRET", "");
define("DEBUG_LEVEL", 1); // 0: no debug, 1: only PHP errors, 2: PHP errors and curl results
define("QR_FILENAME", "GiroCode.png"); // filename of the email attachment
define("CUT_PAYMENT_REFERENCE", "0"); // there is maximum payload of 331 bytes for the qr data
                                       // 1: the payment reference text is cut automatically to meet the limit of 331 bytes
									   // 0: you are asked every time, before the text is cut
									   // note: usually the 331 bytes limit should not be a problem, only if a lot of unicode characters are used 

// Do not touch:

if (DEBUG_LEVEL > 1){
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(E_ALL | E_STRICT);
}

require("utils.php");

?>
