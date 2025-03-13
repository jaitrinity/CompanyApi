<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers:content-type");
include("dbConfiguration.php");
$json = file_get_contents('php://input');
$jsonData=json_decode($json);

$mobileNumber = $jsonData->mobileNumber;


$randomotp = rand(100000,999999);
$wrappedList = [];
array_push($wrappedList,$randomotp);
require 'SendOtpClass.php';
$classObj = new SendOtpClass();
$otpResponse = $classObj->sendOtp($mobileNumber, $randomotp, 'Company');
// $responseData = json_decode($otpResponse);
// $msgStatus = $responseData->return;

$output = array('responseDesc' => "SUCCESSFUL", 'wrappedList' => $wrappedList, 'responseCode' => "100000");

echo json_encode($output);
?>
