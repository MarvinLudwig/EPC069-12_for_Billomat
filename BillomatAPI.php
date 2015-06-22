<?php
// Repository: https://github.com/MarvinLudwig/EPC069-12_for_Billomat
// Licence: Apache 2.0
// Version 1.0

class BillomatAPI{

	private static $rateLimit;
	private static $rateLimitReset;

	// Billomat API call
	public static function call($call,$payload="",$getDetails = false){
		$curl = curl_init("https://".USER_ID.".billomat.net$call");
		$header = array("X-BillomatApiKey: ".API_KEY);
		if (APP_ID != "" && APP_SECRET != ""){
			$header[] = "X-AppId: ".APP_ID;
			$header[] = "X-AppSecret: ".APP_SECRET;
		}
		if ($payload != ""){          
			curl_setopt($curl, CURLOPT_POST, count($payload));                                                              
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
			$header[] = "Content-Type: application/json";
		}
		else{
			$header[] = "Accept: application/json";
		}    
		if ($getDetails) curl_setopt($curl, CURLOPT_HEADERFUNCTION, array("BillomatAPI","findRateDetails"));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);		                                                                  
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curl);
		if (DEBUG_LEVEL == 2){
			echo "<pre>".print_r(curl_getinfo($curl),TRUE)."</pre><br><br>";
			echo "<pre>".print_r($response,TRUE)."</pre>";
		}
		$http_code = curl_getinfo($curl,CURLINFO_HTTP_CODE);
		if ($http_code == "200" || $http_code == "429"){
			if ($getDetails) {
				$details = array("rateLimit" => self::$rateLimit, "rateLimitReset" => self::$rateLimitReset);
				return array("response" => json_decode($response), "details" => $details);
			}
			else return json_decode($response);
		}
		else {
			return array("error" => "HTTP code $http_code");	
		}
		
	}

	private static function findRateDetails( $curl, $header_line ) {
		$colonPos = strpos($header_line,":");
		$fieldName = substr($header_line,0,$colonPos);
		if ($fieldName == "X-Rate-Limit-Remaining") self::$rateLimit = intval(substr($header_line,$colonPos+1,strlen($header_line)-$colonPos));
		else if ($fieldName == "X-Rate-Limit-Reset") self::$rateLimitReset = intval(substr($header_line,$colonPos+1,strlen($header_line)-$colonPos));
		return strlen($header_line);
	}
	
}

?>