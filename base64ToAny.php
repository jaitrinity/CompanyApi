<?php
class Base64ToAny{

	function base64_to_jpeg($base64_string, $fileName) {
		$dir = date("M-Y-d");
		if (!file_exists('/var/www/trinityapplab.in/html/Company/files/'.$dir)) {
		    mkdir('/var/www/trinityapplab.in/html/Company/files/'.$dir, 0777, true);
		}

		$folderPath = "/var/www/trinityapplab.in/html/Company/files/".$dir."/";
		$imageURL = "https://www.trinityapplab.in/Company/files/".$dir."/";
		// split the string on commas
	    // $data[ 0 ] == "data:image/png;base64"
	    // $data[ 1 ] == <actual base64 string>

		// $t=time();
	 	// $fileName = $t;

	    $data = explode( ',', $base64_string );
		if($data[0] == "data:image/png;base64"){
			$fileName .= '.png';
		}
		else if($data[0] == "data:image/jpeg;base64" || $data[0] == "data:image/jpeg;base64"){
			$fileName .= '.jpg';
		}
		else if($data[0] == "data:video/mp4;base64"){
			$fileName .= '.mp4';
		}
		else if($data[0] == "data:application/pdf;base64"){
			$fileName .= '.pdf';
		}
		else if($data[0] == "data:application/vnd.openxmlformats-officedocument.wordprocessingml.document;base64"){
			$fileName .= '.docx';
		}
		else if($data[0] == "data:application/msword;base64"){
			$fileName .= '.doc';
		}

	    // open the output file for writing
	    $ifp = fopen( $folderPath.$fileName, 'wb' ); 
	    // we could add validation here with ensuring count( $data ) > 1
	    fwrite( $ifp, base64_decode( $data[ 1 ] ) );
	    // clean up the file resource
	    fclose( $ifp ); 

	    return $imageURL.$fileName; 
	}

	function generateSignInvoice($base64_string, $fileName) {
		$dir = "Invoice";
		if (!file_exists('/var/www/trinityapplab.in/html/Company/files/'.$dir)) {
		    mkdir('/var/www/trinityapplab.in/html/Company/files/'.$dir, 0777, true);
		}

		$folderPath = "/var/www/trinityapplab.in/html/Company/files/".$dir."/";
		$imageURL = "https://www.trinityapplab.in/Company/files/".$dir."/";
		// split the string on commas
	    // $data[ 0 ] == "data:image/png;base64"
	    // $data[ 1 ] == <actual base64 string>

		// $t=time();
	 	// $fileName = $t;

	    $data = explode( ',', $base64_string );
		if($data[0] == "data:image/png;base64"){
			$fileName .= '.png';
		}
		else if($data[0] == "data:image/jpeg;base64" || $data[0] == "data:image/jpeg;base64"){
			$fileName .= '.jpg';
		}
		else if($data[0] == "data:video/mp4;base64"){
			$fileName .= '.mp4';
		}
		else if($data[0] == "data:application/pdf;base64"){
			$fileName .= '.pdf';
		}

	    // open the output file for writing
	    $ifp = fopen( $folderPath.$fileName, 'wb' ); 
	    // we could add validation here with ensuring count( $data ) > 1
	    fwrite( $ifp, base64_decode( $data[ 1 ] ) );
	    // clean up the file resource
	    fclose( $ifp ); 

	    return $imageURL.$fileName; 

	}

	function generatePO($base64_string, $fileName) {
		$dir = 'PO_'.date("M-Y-d");
		if (!file_exists('/var/www/trinityapplab.in/html/Company/files/'.$dir)) {
		    mkdir('/var/www/trinityapplab.in/html/Company/files/'.$dir, 0777, true);
		}

		$folderPath = "/var/www/trinityapplab.in/html/Company/files/".$dir."/";
		$imageURL = "https://www.trinityapplab.in/Company/files/".$dir."/";
		// split the string on commas
	    // $data[ 0 ] == "data:image/png;base64"
	    // $data[ 1 ] == <actual base64 string>

		// $t=time();
	 	// $fileName = $t;

	    $data = explode( ',', $base64_string );
		if($data[0] == "data:image/png;base64"){
			$fileName .= '.png';
		}
		else if($data[0] == "data:image/jpeg;base64" || $data[0] == "data:image/jpeg;base64"){
			$fileName .= '.jpg';
		}
		else if($data[0] == "data:video/mp4;base64"){
			$fileName .= '.mp4';
		}
		else if($data[0] == "data:application/pdf;base64"){
			$fileName .= '.pdf';
		}

	    // open the output file for writing
	    $ifp = fopen( $folderPath.$fileName, 'wb' ); 
	    // we could add validation here with ensuring count( $data ) > 1
	    fwrite( $ifp, base64_decode( $data[ 1 ] ) );
	    // clean up the file resource
	    fclose( $ifp ); 

	    return $imageURL.$fileName; 
	}
}
?>