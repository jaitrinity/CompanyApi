<?php
include("dbConfiguration.php");
$json = file_get_contents('php://input');
$jsonData=json_decode($json);

$mobileNumber = $jsonData->mobileNumber;
$newPassword = $jsonData->newPassword;


$sql = "UPDATE `EmployeeMaster` set `Password` = '$newPassword' WHERE  `Mobile` = '$mobileNumber' ";
// echo $sql;
$query=mysqli_query($conn,$sql);
if(mysqli_affected_rows($conn) == 0){
	$output = array('responseDesc' => "No Record Found", 'wrappedList' => [], 'responseCode' => "102001");
}
else{
	$output = array('responseDesc' => "SUCCESSFUL", 'wrappedList' => [], 'responseCode' => "100000");
}


echo json_encode($output);
?>