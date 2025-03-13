<?php
include("dbConfiguration.php");
$methodType = $_SERVER['REQUEST_METHOD'];
if($methodType != "POST"){
	return;
}
$json = file_get_contents('php://input');
$jsonData=json_decode($json);

$leaveId = $jsonData->leaveId;
$status = $jsonData->status;
$updateLeave = "UPDATE `LeaveMaster` set `Status` = $status where `Id` = $leaveId ";
$output = "";
if(mysqli_query($conn,$updateLeave)){
	$output -> responseCode = "100000";
	$output -> responseDesc = "Successfully update";

	$sql = "SELECT l.EmpId, e.Name, e.EmailId, e.LeaveBalance, l.FromDate, l.ToDate, l.HalfDay, l.IsLeaveBalanceCalculated FROM LeaveMaster l join EmployeeMaster e on l.EmpId = e.EmpId where l.Id = $leaveId";

	$query = mysqli_query($conn,$sql);
	$row = mysqli_fetch_assoc($query);
	$empId = $row["EmpId"];
	$name = $row["Name"];
	$toMailId = $row["EmailId"];
	$leaveBalance = $row["LeaveBalance"];
	$fromDate = $row["FromDate"];
	$toDate = $row["ToDate"];
	$halfDay = $row["HalfDay"];
	$ilbc = $row["IsLeaveBalanceCalculated"];

	$msg = "Dear $name,"."<br>";
	if($status == 1){
		$msg .= "Your leave is Approved.";
		if($ilbc == 0){
			$actualLeave = 0;
			$dateList = getDatesFromRange($fromDate, $toDate);
			for($i=0;$i<count($dateList);$i++){
				$thisDate = $dateList[$i];
				$dayName = date('l', strtotime($thisDate));
				if(!($dayName == "Saturday" || $dayName == "Sunday" || in_array($thisDate,$holiList))){
					$actualLeave++;
				}
			}

			if($halfDay == "Yes"){
				$actualLeave = $actualLeave - 0.5;
			}
			
			$empLeaveBalance = $leaveBalance;	

			$remainEmpLeave = 0;
			if($empLeaveBalance > $actualLeave){
				$remainEmpLeave = $empLeaveBalance - $actualLeave;
			}
			else{
				// $remainEmpLeave = $actualLeave - $empLeaveBalance;
				$remainEmpLeave = 0;
			}


			$updateRemainLeave = "UPDATE `EmployeeMaster` set `LeaveBalance`=$remainEmpLeave where `EmpId`='$empId'";
			if(mysqli_query($conn,$updateRemainLeave)){
				$isLeave = "UPDATE `LeaveMaster` set `IsLeaveBalanceCalculated`=1 where `Id`=$leaveId";
				mysqli_query($conn,$isLeave);
			}
		}
			
	}
	else{
		$msg .= "Your leave is Rejected.";
	}


    $fromDate = date("d-M-Y", strtotime($fromDate));
    $toDate = date("d-M-Y", strtotime($toDate));

    $subject = "Leave - from ".$fromDate.' to '.$toDate;
	
	require 'SendMailClass.php';
    $classObj = new SendMailClass();
    $mailStatus = $classObj->sendMail($toMailId, $subject, $msg, null);
    // $mailStatus = $classObj->sendMailTest($toMailId, $subject, $msg, null);

}
else{
	$output -> responseCode = "0";
	$output -> responseDesc = "Something wrong";
}
echo json_encode($output);

?>

<?php
function getDatesFromRange($start, $end, $format = 'Y-m-d') {     
    // Declare an empty array
    $array = array();
      
    // Variable that store the date interval
    // of period 1 day
    $interval = new DateInterval('P1D');
  
    $realEnd = new DateTime($end);
    $realEnd->add($interval);
  
    $period = new DatePeriod(new DateTime($start), $interval, $realEnd);
  
    // Use loop to store date into array
    foreach($period as $date) {                 
        $array[] = $date->format($format); 
    }
  
    // Return the array elements
    return $array;
}

?>