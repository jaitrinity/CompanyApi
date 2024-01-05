<?php 
// Crontab at 5th date on month at 04:00 AM
include("dbConfiguration.php");
$period = date("M-Y",strtotime("-1 month"));
// $period = 'Dec-2022';
$date = strtotime('01-'.$period);
$paidDays = date("t", $date);

$holiDate = "SELECT `Date` FROM `Holidays`";
$holiQuery = mysqli_query($conn,$holiDate);
$holiList = array();
while($holiRow = mysqli_fetch_assoc($holiQuery)){
	$d = $holiRow["Date"];
	array_push($holiList, $d);
}

$sql = "SELECT distinct `EmpId` FROM `LeaveMaster` where `Status` = 1 and find_in_set('$period',`MonthInclude`) <> 0 ";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
	$empId = $row["EmpId"];

	$sql1 = "SELECT * FROM `LeaveMaster` where `EmpId` = '$empId' and `Status` = 1 and find_in_set('$period',`MonthInclude`) <> 0 ";
	$query1 = mysqli_query($conn,$sql1);
	$actualLeave = 0;
	while($row1 = mysqli_fetch_assoc($query1)){
		$fromDate = $row1["FromDate"];
		$toDate = $row1["ToDate"];
		$dateList = getDatesFromRange($fromDate, $toDate);
		for($i=0;$i<count($dateList);$i++){
			$thisDate = $dateList[$i];
			$thisDate_monthYear = date('M-Y', strtotime($thisDate));
			if($thisDate_monthYear == $period){
				$dayName = date('l', strtotime($thisDate));
				if(!($dayName == "Saturday" || $dayName == "Sunday" || in_array($thisDate,$holiList))){
					$actualLeave++;
				}
			}	
		}
	}
	$insertLossOfPay = "INSERT INTO `EmployeeLossOfPay`(`EmpId`, `ActualLeave`, `MonthYear`) VALUES ('$empId', $actualLeave, '$period')";
	mysqli_query($conn,$insertLossOfPay);

	$leaveBalance = "SELECT `LeaveBalance` FROM `EmployeeMaster` where `EmpId` = '$empId'";
	$leaveBalanceQuery = mysqli_query($conn,$leaveBalance);
	$leaveBalanceRow = mysqli_fetch_assoc($leaveBalanceQuery);
	$empLeaveBalance = $leaveBalanceRow["LeaveBalance"];

	$monthLossOfPay = 0;
	$lossOfPaySql = "SELECT * FROM `EmployeeLossOfPay` where `EmpId` = '$empId' and `MonthYear` = '$period' ";
	$lossOfPayQuery = mysqli_query($conn,$lossOfPaySql);
	while($lossOfPayRow = mysqli_fetch_assoc($lossOfPayQuery)){
		$ac = $lossOfPayRow["ActualLeave"];
		$monthLossOfPay += $ac;
	}	

	$remainEmpLeave = 0;
	$lossOfPay = 0;
	if($empLeaveBalance > $monthLossOfPay){
		$remainEmpLeave = $empLeaveBalance - $monthLossOfPay;
	}
	else{
		$lossOfPay = $monthLossOfPay - $empLeaveBalance;
	}

	$updateLossOfPay = "UPDATE `EmployeeLossOfPay` set `LossOfPay` = $lossOfPay where `EmpId` = '$empId' and `MonthYear` = '$period'";
	mysqli_query($conn,$updateLossOfPay);

	$insertDeduction = "INSERT INTO `EmployeeDeductions` (`EmpId`, `Basic`, `GrossSalary`, `MonthYear`, `PaidDays`, `LossOfPay`)";
	$insertDeduction .= "SELECT ee.EmpId, ee.Basic, ee.GrossSalary, '$period' as MonthYear, $paidDays as PaidDays, $lossOfPay as LossOfPay FROM EmployeeMaster e join EmployeeEarnings ee on e.EmpId = ee.EmpId where ee.EmpId = '$empId'";
	mysqli_query($conn,$insertDeduction);

	$updateRemainLeave = "UPDATE `EmployeeMaster` set `LeaveBalance` = $remainEmpLeave where `EmpId` = '$empId'";
	mysqli_query($conn,$updateRemainLeave);
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