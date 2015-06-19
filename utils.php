<?php

// Billomat API calls
function callAPI($call,$payload=""){
	$curl = curl_init("https://".USER_ID.".billomat.net$call");
	$header = array("X-BillomatApiKey: ".API_KEY);
	if (APP_ID != "" && APP_SECRET != ""){
		$header[] = "X-AppId: ".APP_ID;
		$header[] = "X-AppSecret: ".APP_SECRET;
	}
	if ($payload != ""){ 
		//curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");          
		curl_setopt($curl, CURLOPT_POST, count($payload));                                                              
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
		$header[] = "Content-Type: application/json";
	}
	else{
		$header[] = "Accept: application/json";
	}    
	curl_setopt($curl, CURLOPT_HTTPHEADER, $header);		                                                                  
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($curl);
	if (DEBUG_LEVEL == 2){
		echo "<pre>".print_r(curl_getinfo($curl),TRUE)."</pre><br><br>";
		echo "<pre>".print_r($response,TRUE)."</pre>";
	}
	$http_code = curl_getinfo($curl,CURLINFO_HTTP_CODE);
	if ($http_code == "200"){
		return json_decode($response);
	}
	else {
		return array("error" => "HTTP code $http_code");	
	}
	
}

?>