<?php
class AddressByLatLongClass{
	public function getAddressByLatLong($latitude,$longitude){
		$url = "https://apis.mapmyindia.com/advancedmaps/v1/38wywkjm1wji9pobr5cczivktpwvysme/rev_geocode?lat=".$latitude."&lng=".$longitude;
		$headers = array(
		      "Content-type: application/json"
		 );

		$ch = curl_init($url);
		curl_setopt_array($ch, array(
		  CURLOPT_POST => FALSE,
		  CURLOPT_RETURNTRANSFER => TRUE,
		  CURLOPT_HTTPHEADER => $headers
		));

		$response = curl_exec($ch);
		curl_close($ch);
		// echo $response;

		$response = json_decode($response);

		// echo $response->responsecode;

		$result = $response->results;
		$address = $result[0]->formatted_address;
		return $address;
	}
}

?>