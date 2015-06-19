<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta charset="UTF-8">
<link rel="stylesheet" href="style.css" type="text/css">
<script type="text/javascript" src="script.js"></script>
</head>
<body>

<?php
// Repository: https://github.com/MarvinLudwig/EPC069-12_for_Billomat
// Licence: Apache 2.0
// Version 1.0

require("config.php");

//// get invoice and client information
$invoices = array();
$clients = array();
$sent_invoices = explode(";",file_get_contents("invoices")); // invoice IDs that have already been sent
$sent_invoices_new = array();
// get invoices and client ids
$result = callAPI("/api/invoices?status=OPEN&payment_type=BANK_TRANSFER");
$invoices_in = $result->invoices->invoice;
if (!is_array($invoices_in)) $invoices_in[0] = $result->invoices->invoice;
foreach ($invoices_in as $invoice){
	// buffer invoices and client ids, 
	// we might save some calls later
	if (is_object($invoice)){ // ignore pagination attributes
		$invoice_id = $invoice->id;
		if (!in_array($invoice_id,$sent_invoices)){
			$invoices[$invoice_id]["id"] = $invoice_id;
			$invoices[$invoice_id]["date"] = $invoice->date;
			$invoices[$invoice_id]["invoice_number"] = $invoice->invoice_number; 
			$invoices[$invoice_id]["client_id"] = $invoice->client_id;  
			$invoices[$invoice_id]["amount"] = $invoice->open_amount; 
			$clients[$invoice->client_id]["client_id"] = $invoice->client_id;
			// add payment reference information
			$payment_reference = "";
			$payment_reference_invoice = explode(",",PAYMENT_REFERENCE_INVOICE);
			foreach ($payment_reference_invoice as $reference){
				$reference = trim($reference);
				$payment_reference .= $invoice->$reference." ";
			}		  
			$invoices[$invoice->id]["payment_reference"] = $payment_reference;
		}
		else $sent_invoices_new[] = $invoice_id;
	}
}
file_get_contents("invoices",implode(";",$sent_invoices_new));
unset($result);
// get clients' information
foreach ($clients as $client_id => $client){
		$result = callAPI("/api/clients/$client_id");
		$client = $result->client;
		$clients[$client_id]["client_number"] = $client->client_number;
		$clients[$client_id]["name"] = $client->name;
		$clients[$client_id]["city"] = $client->city;
		$clients[$client_id]["email"] = $client->email;
		$clients[$client_id]["salutation"] = $client->salutation; 
		$clients[$client_id]["first_name"] = $client->first_name;  
		$clients[$client_id]["last_name"] = $client->last_name; 
		// add payment reference information
		$payment_reference = "";
		$payment_reference_client = explode(",",PAYMENT_REFERENCE_CLIENT);
		foreach ($payment_reference_client as $reference){
			$reference = trim($reference);
			$payment_reference .= $client->$reference." ";
		}		  
		$clients[$client->id]["payment_reference"] = $payment_reference;
}
unset($result);

// get own bank details
$me = callAPI("/api/clients/myself")->client;
echo "<div id='bank_details'>Name: <span id='name'>".$me->bank_account_owner."</span>"
	." - IBAN: <span id='iban'>".$me->bank_iban."</span>"
	." - BIC: <span id='bic'>".$me->bank_swift."</span></div><br><br>"
	."<div class='sendmail' id='sendall'>Alle selektierten Rechnungen senden <span>&#xf1d9;</span></div>";
	
//// output invoice and client information
echo "<table><thead><td><input id='chkAll' type='checkbox'></td><td>RE-Nr.</td><td>Datum</td><td>KD-Nr.</td><td>Name u. Ort</td><td>Email</td><td>QR</td><td>Mail</td><td class='error'></td></thead><tbody id='invoices'>";
foreach ($invoices as $invoice_id => $invoice){
	$client_id = $invoice["client_id"];
	$client = $clients[$client_id];
	$client_name = $client["name"];
	$client_number = $client["client_number"];
	$client_email = $client["email"];
	
	// enrich invoice with client information 
	$invoices[$invoice_id]["client_name"] = $client_name;
	$invoices[$invoice_id]["client_number"] = $client_number;
	$invoices[$invoice_id]["payment_reference"] = $invoices[$invoice_id]["payment_reference"].$client["payment_reference"];
	
	$invoice_number = $invoice["invoice_number"];
	echo "<tr data-invoice_id='$invoice_id' id='invoice_$invoice_id'>"
		."<td><input class='chkBox' type='checkbox'></td>"
		."<td>".$invoice["invoice_number"]."</td>"
		."<td>".$invoice["date"]."</td>"
		."<td>".$client_number."</td>"
		."<td class='name'>".$client_name." - ".$client["city"]."</td>"
		."<td><input type='email' value='".$client_email."'></td>"
		."<td class='showqr' title='QR-Code für Rechnung $invoice_number anzeigen'>&#xf029;</td>"
		."<td class='sendmail' title='Rechnung $invoice_number senden'>&#xf1d9;</td>"
		."<td class='error'></td>"
		."</tr>";
}
echo "</tbody></table>";
echo '<script type="text/javascript">var all_invoices = '.json_encode($invoices).';'
	 .'CUT_PAYMENT_REFERENCE='.CUT_PAYMENT_REFERENCE.';</script>';
?>
</body>