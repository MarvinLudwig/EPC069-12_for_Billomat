<?php
// Repository: https://github.com/MarvinLudwig/EPC069-12_for_Billomat
// Licence: Apache 2.0
// Version 1.0

function msg($id, $params = null){
	switch ($id){
		case 'inv_no' : return 'Inv #'; 
		case 'date' : return 'Date';
		case 'client_no' : return 'Client #';
		case 'name_city' : return 'Name & City';
		case 'email_invalid' : return 'Email address is invalid.';
		case 'send_all_invoices' : return 'Send all selected invoices';
		case 'show' : return 'show';
		case 'send' : return 'send';
		case 'show_qr' : return 'Show QR-Code for invoice '.$params['invoice_number'];
		case 'send_mail' : return 'Send invoice '.$params['invoice_number'];
		case 'cut_text' : return 'We can cut the text, it would look like that:';
		case 'cut_text_shall_we' : return 'Shall we?';
		case 'rate_limit' : return 'Not all invoices are shown, as we hit the rate limit. The rate limit will be reset at ';
		case 'rate_limit_raise' : return 'You can raise the rate limit by registering the app here: https://'.USER_ID.'.billomat.net/portal/apps and filling in APP_ID and APP_SECRET into config.php';
	}
}

?>