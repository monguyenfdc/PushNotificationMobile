<?php 
// Function push notification server to mobile app with diver token
// Android: FCM Token
// ISO: APNS Token
// You can get Token with lib firebase, expo-notification...
// Now....start
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
