<?php
include("dbConfiguration.php");

// $empId = 'tr014';
// $yesterdayDate = '2023-12-04';
// $sql = "SELECT * FROM `Attendance` where `EmpId` = '$empId' and `AttendanceDate` = '$yesterdayDate'";

$yesterdayDate = date('Y-m-d',strtotime("-1 days"));
$sql = "SELECT * FROM `Attendance` where `AttendanceDate` = '$yesterdayDate' ";
$result=mysqli_query($conn,$sql);
$rowCount=mysqli_num_rows($result);
if($rowCount !=0){
	require 'SendMailClass.php';
	while($row = mysqli_fetch_assoc($result)){
		$attendanceDate = $row["AttendanceDate"];
		$attendanceDate =  date("d-M-Y", strtotime($attendanceDate));
		$empId = $row["EmpId"];
		$name = $row["Name"];
		$emailId = $row["EmailId"];
		$inDateTime = $row["InDateTime"];
		$inDateTime =  date("d-M-Y H:i:s", strtotime($inDateTime));
		$outDateTime = $row["OutDateTime"];
		$outDateTime =  date("d-M-Y H:i:s", strtotime($outDateTime));
		$workingHours = $row["WorkingHours"];

		$subject = "$attendanceDate attendance";
		$msg = "Dear $name,<br><br>";
		$msg .= "Please find your $attendanceDate attendance: <br><br>";
		$msg .= "<b>In time:</b> $inDateTime <br>";
		$msg .= "<b>Out time:</b> $outDateTime <br>";
		$msg .= "<b>Working hours:</b> $workingHours <br><br>";
		$msg .= "Regards,<br>";
		$msg .= "Trinity automation team...<br>";
		$classObj = new SendMailClass();
		$response = $classObj->sendMailAttendance($emailId, $subject, $msg, null);

		// header('Content-Type: text/html');
		// echo $msg;
	}
}
else{

}
?>