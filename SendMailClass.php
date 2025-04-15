<?php 
include(dirname(__DIR__).'/PHPMailerAutoload.php');

class SendMailClass{

	public function sendLeaveMail($toMailId, $ccMailId, $subject, $msg, $attachment){
		$status = false;
	    $message = $msg;
	    $mail = new PHPMailer;
	    $mail->isSMTP();                                      
	    $mail->Host = 'smtp.gmail.com';
	    $mail->SMTPAuth = true;
	    $mail->Username = 'communication@trinityapplab.co.in';
	    $mail->Password = 'ouxjcbqxcurarzhq';   
	    $mail->Port = 587;
	    $mail->SMTPSecure = 'tls';
	    
	    $mail->setFrom("communication@trinityapplab.co.in","Trinity");
	    $mail->addAttachment($attachment);
	    $mail->isHTML(true);   

	    // To mail's
	    $mail->addAddress($toMailId);

	    // CC mail's
	    $ccMailList = explode(",", $ccMailId);
	    for($i=0;$i<count($ccMailList);$i++){
	    	$loopCC = $ccMailList[$i];
	    	$mail->addCC($loopCC);
	    }
	    $mail->addCC('shruti@trinityapplab.co.in');
	    $mail->addCC('ayush.agarwal@trinityapplab.co.in');

	    // BCC mail's
	    // $mail->addBCC("jai.prakash@trinityapplab.co.in");

	    $mail->Subject = $subject;
	    $mail->Body = "$message<br>";
	    
	        
	    if(!$mail->send())
	    {
	        // echo 'Mailer Error: ' . $mail->ErrorInfo;
	        // echo"<br>Could not send";
	        $status = false;
	    }
	    else{
	        // echo "mail sent";
	        $status = true;
	    }
	    return $status;
	}

	public function sendLeaveMailJustMe($toMailId, $subject, $msg, $attachment){
		$status = false;
	    $message = $msg;
	    $mail = new PHPMailer;
	    $mail->isSMTP();                                      
	    $mail->Host = 'smtp.gmail.com';
	    $mail->SMTPAuth = true;
	    $mail->Username = 'communication@trinityapplab.co.in';
	    $mail->Password = 'ouxjcbqxcurarzhq';   
	    $mail->Port = 587;
	    $mail->SMTPSecure = 'tls';
	    
	    $mail->setFrom("communication@trinityapplab.co.in","Trinity");
	    $mail->addAttachment($attachment);
	    $mail->isHTML(true);   

	    // To mail's
	    $mail->addAddress($toMailId);

	    // CC mail's
	    // $mail->addCC('shruti@trinityapplab.co.in');
	    // $mail->addCC('ayush.agarwal@trinityapplab.co.in');

	    // BCC mail's
	    // $mail->addBCC("jai.prakash@trinityapplab.co.in");

	    $mail->Subject = $subject;
	    $mail->Body = "$message<br>";
	    
	        
	    if(!$mail->send())
	    {
	        // echo 'Mailer Error: ' . $mail->ErrorInfo;
	        // echo"<br>Could not send";
	        $status = false;
	    }
	    else{
	        // echo "mail sent";
	        $status = true;
	    }
	    return $status;
	}

	public function sendMail($toMailId, $subject, $msg, $attachment){
		$status = false;
	    $message = $msg;
	    $mail = new PHPMailer;
	    $mail->isSMTP();                                      
	    $mail->Host = 'smtp.gmail.com';
	    $mail->SMTPAuth = true;
	    $mail->Username = 'communication@trinityapplab.co.in';
	    $mail->Password = 'ouxjcbqxcurarzhq';   
	    $mail->Port = 587;
	    $mail->SMTPSecure = 'tls';
	    
	    // To mail's
	    if($toMailId == ""){
	    	//$mail->addAddress("ankita.verma@trinityapplab.co.in");
	    }else{
	    	$mail->addAddress($toMailId);
	    }
	    // $mail->addAddress("pushkar.tyagi@trinityapplab.co.in");
	    $mail->setFrom("communication@trinityapplab.co.in","Trinity");
	    $mail->addAttachment($attachment);
	    $mail->isHTML(true);   

	    // CC mail's
	    if($toMailId != ""){
	    	//$mail->addCC('ankita.verma@trinityapplab.co.in');
	    }
	    $mail->addCC('shruti@trinityapplab.co.in');
	    if(!($toMailId == "ayush.agarwal@trinityapplab.co.in" || $toMailId == "pushkar.tyagi@trinityapplab.co.in")){
	    	$mail->addCC('ayush.agarwal@trinityapplab.co.in');
    		// $mail->addCC('pushkar.tyagi@trinityapplab.co.in');
	    }
	    // BCC mail's
	    // $mail->addBCC("jai.prakash@trinityapplab.co.in");

	    $mail->Subject = $subject;
	    $mail->Body = "$message<br>";
	    
	        
	    if(!$mail->send())
	    {
	        // echo 'Mailer Error: ' . $mail->ErrorInfo;
	        // echo"<br>Could not send";
	        $status = false;
	    }
	    else{
	        // echo "mail sent";
	        $status = true;
	    }
	    return $status;
	}

	public function sendMailOfferLetter($toMailId, $subject, $msg, $attachment){
		$status = false;
	    $message = $msg;
	    $mail = new PHPMailer;
	    $mail->isSMTP();                                      
	    $mail->Host = 'smtp.gmail.com';
	    $mail->SMTPAuth = true;
	    $mail->Username = 'communication@trinityapplab.co.in';
	    $mail->Password = 'ouxjcbqxcurarzhq';   
	    $mail->Port = 587;
	    $mail->SMTPSecure = 'tls';
	    
	    // To mail's
	    $mail->addAddress($toMailId);
	    
	    $mail->setFrom("communication@trinityapplab.co.in","Trinity");
	    $mail->addAttachment($attachment);
	    $mail->isHTML(true);   

	    // CC mail's
	    $mail->addCC('shruti@trinityapplab.co.in');
	    $mail->addCC('ayush.agarwal@trinityapplab.co.in');
	 
	    // BCC mail's
	    // $mail->addBCC("jai.prakash@trinityapplab.co.in");

	    $mail->Subject = $subject;
	    $mail->Body = "$message<br>";
	    
	        
	    if(!$mail->send())
	    {
	        // echo 'Mailer Error: ' . $mail->ErrorInfo;
	        // echo"<br>Could not send";
	        $status = false;
	    }
	    else{
	        // echo "mail sent";
	        $status = true;
	    }
	    return $status;
	}

	public function sendMailAttendance($toMailId, $subject, $msg, $attachment){
		$status = false;
	    $message = $msg;
	    $mail = new PHPMailer;
	    $mail->isSMTP();                                      
	    $mail->Host = 'smtp.gmail.com';
	    $mail->SMTPAuth = true;
	    $mail->Username = 'communication@trinityapplab.co.in';
	    $mail->Password = 'ouxjcbqxcurarzhq';   
	    $mail->Port = 587;
	    $mail->SMTPSecure = 'tls';
	    
	    // To mail's
	    $mail->addAddress($toMailId);
	    // $mail->addAddress("pushkar.tyagi@trinityapplab.co.in");
	    
	    $mail->setFrom("communication@trinityapplab.co.in","Trinity");
	    $mail->addAttachment($attachment);
	    $mail->isHTML(true);   

	    // CC mail's
	    // $mail->addCC('ankita.verma@trinityapplab.co.in');
	    
	    // BCC mail's
	    // $mail->addBCC("jai.prakash@trinityapplab.co.in");

	    $mail->Subject = $subject;
	    $mail->Body = "$message<br>";
	    
	        
	    if(!$mail->send())
	    {
	        // echo 'Mailer Error: ' . $mail->ErrorInfo;
	        // echo"<br>Could not send";
	        $status = false;
	    }
	    else{
	        // echo "mail sent";
	        $status = true;
	    }
	    return $status;
	}

	public function sendMailTest($toMailId, $subject, $msg, $attachment){
		$status = false;
	    $message = $msg;
	    $mail = new PHPMailer;
	    $mail->isSMTP();                                      
	    $mail->Host = 'smtp.gmail.com';
	    $mail->SMTPAuth = true;
	    $mail->Username = 'communication@trinityapplab.co.in';
	    $mail->Password = 'ouxjcbqxcurarzhq';   
	    $mail->Port = 587;
	    $mail->SMTPSecure = 'tls';
	    
	    // To mail's
	    $mail->addAddress($toMailId);
	    
	    $mail->setFrom("communication@trinityapplab.co.in","Trinity");
	    $mail->addAttachment($attachment);
	    $mail->isHTML(true);   

	    // CC mail's
	    // $mail->addCC('ankita.verma@trinityapplab.co.in');
    	// $mail->addCC('ayush.agarwal@trinityapplab.co.in');
    	// $mail->addCC('pushkar.tyagi@trinityapplab.co.in');
	    
	    // BCC mail's
	    // $mail->addBCC("jai.prakash@trinityapplab.co.in");

	    $mail->Subject = $subject;
	    $mail->Body = "$message<br>";
	    
	        
	    if(!$mail->send())
	    {
	        // echo 'Mailer Error: ' . $mail->ErrorInfo;
	        // echo"<br>Could not send";
	        $status = false;
	    }
	    else{
	        // echo "mail sent";
	        $status = true;
	    }
	    return $status;
	}
}
?>