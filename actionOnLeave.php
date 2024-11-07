<?php 
include("dbConfiguration.php");

$leaveId = $_REQUEST["leaveId"];
$action = $_REQUEST["action"];
$sql = "SELECT * FROM `LeaveMaster` where `Id`=$leaveId ";
$query = mysqli_query($conn,$sql);
$rowCount = mysqli_num_rows($query);
if($rowCount != 0){
	$row = mysqli_fetch_assoc($query);
	$status = $row["Status"];
	if($status == 1){
		$msg = "<h1 style='color:green'>Leave already Approved<h1>";
	}
	else if($status == 2){
		$msg = "<h1 style='color:red'>Leave already Rejected</h1>";
	}
	else if($status == 0){
		$updateSql = "UPDATE `LeaveMaster` set `Status` = $action where `Id`=$leaveId";
		$msg = "";
		if(mysqli_query($conn,$updateSql)){
			$sql = "SELECT l.EmpId, e.Name, e.EmailId, e.LeaveBalance, l.FromDate, l.ToDate, l.IsLeaveBalanceCalculated, l.RM_EmailId, e.State as TmHrEmailId FROM LeaveMaster l join EmployeeMaster e on l.EmpId = e.EmpId where l.Id = $leaveId";

			$query = mysqli_query($conn,$sql);
			$row = mysqli_fetch_assoc($query);
			$empId = $row["EmpId"];
			$name = $row["Name"];
			$toMailId = $row["EmailId"];
			$leaveBalance = $row["LeaveBalance"];
			$fromDate = $row["FromDate"];
			$toDate = $row["ToDate"];
			$ilbc = $row["IsLeaveBalanceCalculated"];
			$rmEmailId = $row["RM_EmailId"];
			$tmHrEmailId = $row["TmHrEmailId"] == null ? "" : $row["TmHrEmailId"];

			$rmEmailId .= ",".$tmHrEmailId;

			$msg = "Dear $name,"."<br>";
			if($action == 1){
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
		    $mailStatus = $classObj->sendLeaveMail($toMailId, $rmEmailId, $subject, $msg, null);
		}
		else{
			$msg = "<h1 style='color:yellow'>Something went wrong</h1>";
		}
	}
}
textToHtml($msg);
?>
<?php 
header('Content-Type: text/html');
function textToHtml($text){
	echo "$text";
	echo "<script>
	setInterval(function(){
		window.close();
	},2000);
	</script>";
}
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