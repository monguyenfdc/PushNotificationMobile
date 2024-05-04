<?php 
// Function push notification server to mobile app with diver token
// Android: FCM Token
// ISO: APNS Token
// You can get Token with lib firebase, expo-notification...
// Now....start
class pushNotification {
	
	function createNoti($token, $title, $content)
    {
        (strpos($token, ":")!==false)?$this-> FCM_send($token,$title,$content):$this-> APNS_send($token,$title,$content);
    }
	
// Function send data to server push FCM/APNS with curl
	function httpPostJsonHeader($url,$headers, $postdata)
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($ch);
        curl_close($ch);
        return ($result);
    }

	function b64($raw, $json=false){
		if($json) $raw = json_encode($raw);
		return str_replace('=', '', strtr(base64_encode($raw), '+/', '-_')); 
	}

// Function push notification on IOS with APNS server
	function APNS_send($token,$title,$mess)
    {	   
	$authKey = "AuthKey_<ADASDASD>.p8"; 	// => Name .p8 file
	$teamId = 'HAHASDHAD';					// => team ID from account developer
	$tokenId = 'ADASDASD';					// => key .p8 ID
	$bundleId = 'com.abc.abc';				// => Bundle ID from App
	$devToken = $token;
	$notification_payload['aps'] = [
		'alert' => [
			'title' => $title,			
			'body' => $mess
		],
		"badge" => 1
	];

	$production = true;
	$token_key = file_get_contents('./<DIR>/'.$authKey); //=> file .p8 location on hosting/server web
	$jwt_header = [
		'alg' => 'ES256', 
		'kid' => $tokenId
	];

	$jwt_payload = [
		'iss' => $teamId, 
		'iat' => time()
	];
	$raw_token_data = $this->b64($jwt_header, true).".".$this->b64($jwt_payload, true);

	$signature = '';
	openssl_sign($raw_token_data, $signature, $token_key, 'SHA256');
	$jwt = $raw_token_data.".".$this->b64($signature);
	$request_body = json_encode($notification_payload);
	$endpoint = $production ? 'https://api.push.apple.com/3/device': 'https://api.development.push.apple.com/3/device';
	$url = "$endpoint/$devToken";
	$headers=[
			"content-type: application/json",
			"authorization: bearer $jwt",
			"apns-topic: $bundleId"
		];
	$this-> httpPostJsonHeader($url,$headers, $request_body);
	
    }
	
// Function push notification on Android with FCM server
	function FCM_send($token,$title,$mess)
    {        
        $key="AAAAX3e0lxQ:AFHAJFSHADJFSDFJSDFDFigawpeIEWcm0mMEBwBeR..."; // => FCM KEY from firebase
        $url = "https://fcm.googleapis.com/fcm/send";
		$data=[
			"to"=>$token,
			"priority"=>"normal",
			"data"=> [
			  "title"=> $title,
			  "message"=>$mess			 
			],
		];
		$headers = [
		"Content-Type: application/json",
		"Authorization: key=$key"
		];
     $this-> httpPostJsonHeader($url,$headers, json_encode($data));
    }
	
    function getAccessTokenHTTPv1(){
    // Read service account details
        $adr='./<DIR>/';
        $privatekey="your-privite-key.json";
        $authConfigString = file_get_contents($adr.$privatekey);

        // Parse service account details
        $authConfig = json_decode($authConfigString);

        // Read private key from service account details
        $secret = openssl_get_privatekey($authConfig->private_key);

        // Create the token header
        $header = json_encode([
            'typ' => 'JWT',
            'alg' => 'RS256'
        ]);

        // Get seconds since 1 January 1970
        $time = time();

        $payload = json_encode([
            "iss" => $authConfig->client_email,
            "scope" => "https://www.googleapis.com/auth/firebase.messaging",
            "aud" => "https://oauth2.googleapis.com/token",
            "exp" => $time + 3600,
            "iat" => $time
        ]);

        // Encode Header
        $base64UrlHeader = $this->b64(($header);

        // Encode Payload
        $base64UrlPayload = $this->b64(($payload);

        // Create Signature Hash
        $signature='';
        openssl_sign($base64UrlHeader . "." . $base64UrlPayload, $signature, $secret, OPENSSL_ALGO_SHA256);

        // Encode Signature to Base64Url String
        $base64UrlSignature = $this->b64(($signature);

        // Create JWT
        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

        //-----Request token------
        $options = array('http' => array(
            'method'  => 'POST',
            'content' => 'grant_type=urn:ietf:params:oauth:grant-type:jwt-bearer&assertion='.$jwt,
            'header'  =>
                "Content-Type: application/x-www-form-urlencoded"
        ));
        $context  = stream_context_create($options);
        $responseText = file_get_contents("https://oauth2.googleapis.com/token", false, $context);
        file_put_contents($adr.'access_token.json',$responseText);
        return($responseText);
    }


    function FCM_send_Via_HTTPv1($token,$title,$mess){
        $adr='./<DIR>/';
        $access_token=file_exists($adr.'access_token.json')?json_decode(file_get_contents($adr.'access_token.json'),true):json_decode($this->getAccessTokenHTTPv1(),true);
        $url = "https://fcm.googleapis.com/v1/projects/succons-a48f0/messages:send";
		
		// Tạo yêu cầu
	$request = [
	"message"=>[
		"token" => $token,
		"notification" => [
		    "title" => $title,
		    "body" => $mess,
		]
	    ]
	];		
	// Gửi yêu cầu
	$headers = array(
	    "Authorization: Bearer " . $access_token['access_token'],
	    "Content-Type: application/json; UTF-8",
	);
		
        $result=json_decode(httpPostJsonHeader($url,$headers, json_encode($request)), true);
        if(isset($result['error'])&&$result['error']['code']=='401'){
		$access_token=json_decode($this-> getAccessTokenHTTPv1(),true);
		$headers = array(
			    "Authorization: Bearer " . $access_token['access_token'],
			    "Content-Type: application/json; UTF-8",
			    );
		httpPostJsonHeader($url,$headers, json_encode($request));
        	}
	}
}

