<?php 
require 'SendSecureMailClassTest.php';
$toMailId = "jai.prakash@trinityapplab.co.in";
$subject = "Secure mail";
$msg = "Hi, i m sending you a secure mail..";
$classObj = new SendSecureMailClassTest();
$response = $classObj->sendMailTest($toMailId, $subject, $msg);
echo $response;

?>