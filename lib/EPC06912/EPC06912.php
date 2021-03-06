<?php 
// Creates payload for QR-Code according to EPC guideline EPC069-12
// Repository: https://github.com/MarvinLudwig/EPC069-12
// Licence: Apache 2.0
// Version: 1.1

class EPC06912 {

	public static $error;

	public static function create ($name, $bic, $iban, $amount, $text, $lang = "en", $encoding_str = null){
	
		require_once("lang_".$lang.".php");
	
		self::$error = "";
		$error = "";
		$cutEpc = "";
		
		// detect encoding
		if ($encoding_str == null){
			$encoding_str = mb_detect_encoding($bic.$name.$iban.$amount.$text,mb_detect_order(),true);
			if ($encoding_str != "UTF-8") $encoding_str = "ISO-8859-1";
		}
		$encodings = array("UTF-8" => 1,"ISO-8859-1" => 2,"ISO-8859-2" => 3,"ISO-8859-4" => 4,
							"ISO-8859-5" => 5,"ISO-8859-7" => 6,"ISO-8859-10" => 7,"ISO-8859-15" => 8);
		$encoding_epc = $encodings[$encoding_str];
		
		// remove whitespace
		$bic = str_replace(" ", "", $bic);
		$iban = str_replace(" ", "", $iban);
		// check values (make sure that BIC and IBAN are valid, here we check only for length)
		if ($encoding_epc == "") $error .= $msg["encoding_1"]." $encoding_str ".$msg["encoding_2"]."\r\n"; 
		if (!is_numeric($amount)) $error .= $msg['amount_invalid']."\r\n";
		else {
			$amount = (float)$amount;
			$amount_arr = explode(".",$amount);
			if (count($amount_arr) == 2) {
				$decimals = $amount_arr[1];
				if (strlen($decimals) > 2) $error .= $msg['amount_decimals']."\r\n";
			}
		}
		if (mb_strlen($name,$encoding_str) > 70) $error .= $msg['name_length']."\r\n";
		if (mb_strlen($bic,$encoding_str) > 11) $error .= $msg['bic_length']."\r\n";
		if (mb_strlen($iban,$encoding_str) > 34) $error .= $msg['iban_length']."\r\n";
		if (mb_strlen($amount,$encoding_str) > 12) $error .= $msg['amount_length']."\r\n";
		if (mb_strlen($text,$encoding_str) > 140) $error .= $msg['text_length']."\r\n";
		if ($error != "") {
			self::$error = array ("code" => 1, "message" => $error);
			return false;
		}
		
		// create string
		$epc = "";
		$epc .=  "BCD"."\n"; // Service Tag - BCD (currently the only possible value)
		$epc .=  "001"."\n"; // Version
		$epc .=  $encoding_epc."\n"; 
		$epc .=  "SCT"."\n"; // Identification - SCT: SEPA Credit Transfer (currently the only possible value)
		$epc .=  $bic."\n"; 
		$epc .=  $name."\n"; 
		$epc .=  $iban."\n";
		$epc .=  "EUR".$amount."\n"; 
		$epc .=  "OTHR"."\n"; // Default purpose - OTHR (TODO: implement others)
		$epc .=  $text."\n";
		
		// check max bytes (331)
		$strBytes = strlen($epc);
		if ($strBytes > 331){
			$textBytes = strlen($text);
			$excessBytes = $strBytes-331;
			
			// Check if we could cut the text to comply with the bytes limit
			if ($textBytes >= $excessBytes){ 
				$cutText = mb_strcut($text,0,$textBytes-$excessBytes-1,$encoding_str);
				$error .= $msg['payload_bytes_1']." ($strBytes). ".$msg['payload_bytes_1']." $textBytes ".$msg['bytes'];
			}
			
			if ($error != "") {
				self::$error = array ("code" => 2, "message" => $error, "details" => $cutText);
				return false;
			}
		}
		return $epc;
	}
}

?>
