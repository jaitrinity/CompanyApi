<?php 
// require 'SendMailClass.php';

// $classObj = new SendMailClass();
// $toMailId = "jai.prakash@trinityapplab.co.in";
// $subject = "Test Subject";
// $msg = "Testing";
// $mailStatus = $classObj->sendMail($toMailId, $subject, $msg, null);
// echo $mailStatus;

// $latlong = "28.692003/77.1518537";
// $latlong = "/";
// $latlong = str_replace(",", "/", $latlong);
// $latlongList = explode("/", $latlong);
// $lat = $latlongList[0];
// $long = $latlongList[1];
// $msg = "";
// if($lat == "" || $long == ""){
// 	$msg = "Lat-long not found plz try again";
// }
// else{
// 	$msg = "success";
// }
// echo $msg;

// require 'AddressByLatLongClass.php';
// $classObj = new AddressByLatLongClass();
// $latlong = "28.692003/77.1518537";
// $latlong = str_replace(",", "/", $latlong);
// $latlongList = explode("/", $latlong);
// $lat = $latlongList[0];
// $long = $latlongList[1];
// $address = $classObj->getAddressByLatLong($lat, $long);
// echo $address;

require 'LeaveBalanceClass.php';
$classObj = new LeaveBalanceClass();
$leaveId = 106;
$leaveBalance = $classObj->getLeaveBalance($leaveId);
echo $leaveBalance;

?>