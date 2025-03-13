<?php
class SendOtpClass{
	function sendOtp($mobile, $otp, $appName){
		$authorization = "[authorization]";
		$route="dlt";
		$senderId="TRIAPP";
		$messageId="180712";

		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://www.fast2sms.com/dev/bulkV2?authorization=$authorization&sender_id=$senderId&message=".urlencode($messageId)."&variables_values=".urlencode($otp.'|'.$appName)."&route=$route&numbers=".urlencode($mobile),
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_SSL_VERIFYHOST => 0,
		  CURLOPT_SSL_VERIFYPEER => 0,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_HTTPHEADER => array(
		    "cache-control: no-cache"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  // echo "cURL Error #:" . $err;
		} else {
		  // echo $response;
		}

		return $response;

	}
}
?>