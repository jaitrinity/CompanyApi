<?php
include("dbConfiguration.php");
require 'GenerateSalarySlipClass.php';
require 'SendMailClass.php';

$methodType = $_SERVER['REQUEST_METHOD'];
if($methodType != "POST"){
    return;
}

$json = file_get_contents('php://input');
$jsonData = json_decode($json);

// $todayDate = date('d-M-Y', strtotime('0 day'));

$empId = $jsonData->empId;
$name = $jsonData->name;
$emailId = $jsonData->emailId;
$monthYear=$jsonData->monthYear;
$salarySlipName = $jsonData->salarySlipName;

$classObj = new GenerateSalarySlipClass();
$response = $classObj->generateSS($empId, $monthYear);


// $msg = "Dear $name, "."<br>";
// $msg .= "Please find Salary Slip for $monthYear."."<br><br>";
// $msg .= "PFA"."<br><br>";
// $msg .= "Regards"."<br>";
// $msg .= "Trinity Automation Team.";

// $subject = "Salary Slip - ".$monthYear;
// $dir = "SalarySlip_".$monthYear;
// $pdfFileName = $salarySlipName.".pdf";
// $classObj = new SendMailClass();
// $response = $classObj->sendLeaveMailJustMe($emailId, $subject, $msg, "/var/www/trinityapplab.in/html/Company/files/".$dir."/".$pdfFileName);

// $response = true;
$output = "";
if($response){
    $output -> responseCode = "100000";
    // $output -> responseDesc = "Salary slip send to your employee e-mail id";
    $output -> responseDesc = "Salary slip created, employee can download from `Salary Slip` tab";
}
else{
    $output -> responseCode = "0";
    $output -> responseDesc = "Something wend wrong while create salary slip";
}
echo json_encode($output);
?>

