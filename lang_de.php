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
		case 'rate_limit' : return 'Es werden nicht alle Rechnungen angezeigt, da das Abruflimit erreicht wurde. Das Limit ist wieder verfügbar um ';
		case 'rate_limit_raise' : return 'Sie können die Anzahl der Abrufe erhöhen, indem Sie die App hier registrieren: https://'.USER_ID.'.billomat.net/portal/apps und in der config.php die APP_ID und APP_SECRET eintragen.';
	}
}

?>