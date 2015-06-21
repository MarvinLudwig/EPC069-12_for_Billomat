<?php
// Repository: https://github.com/MarvinLudwig/EPC069-12_for_Billomat
// Licence: Apache 2.0
// Version 1.0

function msg($id, $params = null){
	switch ($id){
		case 'inv_no' : return 'RE-Nr.'; 
		case 'date' : return 'Datum';
		case 'client_no' : return 'KD-Nr.';
		case 'name_city' : return 'Name u. Ort';
		case 'email_invalid' : return 'Die Emailadresse ist ungültig.';
		case 'send_all_invoices' : return 'Alle selektierten Rechnungen senden';
		case 'show' : return 'anzeigen';
		case 'send' : return 'senden';
		case 'show_qr' : return 'QR-Code für Rechnung '.$params['invoice_number'].' anzeigen';
		case 'send_mail' : return 'Rechnung '.$params['invoice_number'].' senden';
		case 'cut_text' : return 'Gekürzt würde der Text so aussehen:';
		case 'cut_text_shall_we' : return 'Soll er gekürzt werden?';
	}
}

?>
