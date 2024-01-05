<?php
class LeaveBalanceClass{
	public function getLeaveBalance($leaveId){
		require_once 'dbConfiguration.php';

		// echo $leaveId;
		$holiDate = "SELECT `Date` FROM `Holidays`";
		$holiQuery = mysqli_query($conn,$holiDate);
		$holiList = array();
		while($holiRow = mysqli_fetch_assoc($holiQuery)){
			$d = $holiRow["Date"];
			array_push($holiList, $d);
		}

		$sql = "SELECT * FROM `LeaveMaster` where `Status`=1 and `Id`=$leaveId";
		$query = mysqli_query($conn,$sql);
		$actualLeave = 0;
		$row = mysqli_fetch_assoc($query);
		$empId = $row["EmpId"];
		$fromDate = $row["FromDate"];
		$toDate = $row["ToDate"];
		$dateList = $this->getDatesFromRange($fromDate, $toDate);
		for($i=0;$i<count($dateList);$i++){
			$thisDate = $dateList[$i];
			$dayName = date('l', strtotime($thisDate));
			if(!($dayName == "Saturday" || $dayName == "Sunday" || in_array($thisDate,$holiList))){
				$actualLeave++;
			}
		}
		

		$leaveBalance = "SELECT `LeaveBalance` FROM `EmployeeMaster` where `EmpId` = '$empId'";
		$leaveBalanceQuery = mysqli_query($conn,$leaveBalance);
		$leaveBalanceRow = mysqli_fetch_assoc($leaveBalanceQuery);
		$empLeaveBalance = $leaveBalanceRow["LeaveBalance"];	

		$remainEmpLeave = 0;
		if($empLeaveBalance > $actualLeave){
			$remainEmpLeave = $empLeaveBalance - $actualLeave;
		}
		else{
			// $remainEmpLeave = $actualLeave - $empLeaveBalance;
			$remainEmpLeave = 0;
		}

		// $updateRemainLeave = "UPDATE `EmployeeMaster` set `LeaveBalance` = $remainEmpLeave where `EmpId` = '$empId'";
		// mysqli_query($conn,$updateRemainLeave);

		return $remainEmpLeave;
	}

	public function getDatesFromRange($start, $end, $format = 'Y-m-d') {     
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
}
?>