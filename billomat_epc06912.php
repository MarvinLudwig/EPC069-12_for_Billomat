<?php
// Repository: https://github.com/MarvinLudwig/EPC069-12_for_Billomat
// Licence: Apache 2.0
// Version 1.0
 
require("config.php");
require("lib/EPC06912/EPC06912.php");
require("lib/qrcode/qrlib.php");


if(ob_get_length() > 0) ob_end_flush();

$input = file_get_contents('php://input'); 	
$data = json_decode($input);
// Show QR code
if ($data->action == "SHOW_QR"){
	$invoice = $data->invoices->invoice;
	$epc = EPC06912::create($data->name,$data->bic,$data->iban,$invoice->amount,$invoice->payment_reference,LANG);
	if ($epc != false) {
		$qr = createQRCode($epc);
		$result[] = array("id" => $invoice->id, "result" => "OK", "details" => $qr);
	}
	else {
		$result[] = array("id" => $invoice->id, "error" => EPC06912::$error);
	}	
	echo json_encode($result);
}
// Send one or more emails with attached invoice and QR-code
else if ($data->action == "SEND_MAIL"){
	$invoices = $data->invoices;
	$file = fopen("invoices", "a"); 
	foreach ($invoices as $invoice){
		$result = array();
		$epc = EPC06912::create($data->name,$data->bic,$data->iban,$invoice->amount,$invoice->payment_reference,LANG);
		if ($epc != false) { 
			if (filter_var($invoice->client_email, FILTER_VALIDATE_EMAIL)){
				$qrCode = createQRCode($epc);
				if (TEST_EMAIL != "") $payload["email"]["recipients"]["to"] = TEST_EMAIL;
				else $payload["email"]["recipients"]["to"] = $invoice->client_email;
				$attachment = new stdClass();
				$attachment->filename = QR_FILENAME;
				$attachment->mimetype = "image/png";
				$attachment->base64file = $qrCode;
				$payload["email"]["attachments"]["attachment"] = $attachment;
				$callResult = BillomatAPI::call("/api/invoices/".$invoice->id."/email",$payload);
				if (empty($callResult["error"])){
					$result[] = array("id" => $invoice->id, "result" => "OK");
					if (TEST_EMAIL == "") fputs($file, $invoice->id.";");
				}
				else {
					$result[] = array("id" => $invoice->id, "error" => $callResult["error"]);
				}
			}
			else {
					$result[] = array("id" => $invoice->id, "error" => array("code" => 1, "message" => msg('email_invalid')));
			}
		}
		else {
			$result[] = array("id" => $invoice->id, "error" => EPC06912::$error);
		}
		echo json_encode($result);
		if(ob_get_length() > 0) ob_flush();
		flush();
	}
}

function createQRCode ($qr_payload){
	ob_start();
	QRCode::png($qr_payload, null,"M",5,5);
	$imageString = base64_encode( ob_get_contents() );
	ob_end_clean();
	return $imageString;
}


?>
