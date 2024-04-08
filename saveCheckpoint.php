<?php
include("dbConfiguration.php");
require 'EmployeeTenentId.php';
require 'SendMailClass.php';

$json = file_get_contents('php://input');
file_put_contents('/var/www/trinityapplab.in/html/Company/log/log_'.date("Y-m-d").'.log', date("Y-m-d H:i:s").' '.$json."\n", FILE_APPEND);
$jsonData=json_decode($json,true);
$req = $jsonData[0];

 $mapId=$req['mappingId'];
 $empId=$req['Emp_id'];
 $mId=$req['M_Id'];
 $lId=$req['locationId'];
 $event=$req['event'];
 $geolocation=$req['geolocation'];
 if($geolocation == null) $geolocation = $req["latLong"];
 $geolocation = str_replace(",", "/", $geolocation);
 $distance=$req['distance'];
 $mobiledatetime=$req['mobiledatetime'];
 $fakeGpsMessage=$req['fakeGpsMessage'];

 $caption = $req['caption'];
 $transactionId = $req['timeStamp'];
 $checklist = $req['checklist'];
 $dId = $req['did'];
 $assignId = $req['assignId'];
 $actId = $req['activityId'];
 $lastTransHdrId = "";
 $activityId = 0;

if ((strpos($mobiledatetime, 'AM') !== false) || (strpos($mobiledatetime, 'PM')) || (strpos($mobiledatetime, 'am') !== false) || (strpos($mobiledatetime, 'pm')))   {
	$date = date_create_from_format("Y-m-d h:i:s A","$mobiledatetime");
	$date1 = date_format($date,"Y-m-d H:i:s");
}
else{
	$date1 = $mobiledatetime;
}

 if($lId == ""){
 	$lId = '1';
 }

 if($mId == ''){
 	$mId = '0';
 }

 if($mapId == ''){
 	$mapId = '0';
 }
 
 if($actId == ''){
	 $actId = null;
 }
 
 if($event == 'Submit'){

 	$existSql = "SELECT `ActivityId` FROM `Activity` where `MobileTimestamp` = '$transactionId' and Event = 'Submit'";
	$existResult = mysqli_query($conn,$existSql);
	$existRowCount=mysqli_num_rows($existResult);
	if($existRowCount !=0){
		$existrow = mysqli_fetch_assoc($existResult);
		$existActId = $existrow["ActivityId"];
		$existOutput = "";
		$existOutput -> error = "200";
		$existOutput -> message = "success";
		$existOutput -> TransID = "$existActId";
		echo json_encode($existOutput);

		return;
	}
 
 	$classObj = new EmployeeTenentId();
	$empDetObj = $classObj->getEmployeeDetails($conn,$empId);
	$empDetObj = json_decode($empDetObj,true);
	$empName = $empDetObj["empName"];
	$tenentId = $empDetObj["tenentId"];

	// for Incident and First TODO task user.
	if($actId == null  && $actId == ''){
		// $sql = "SELECT r1.Verifier_Role, r1.Approver_Role FROM `Menu` r1 where r1.MenuId = '$mId' ";
		// $result = mysqli_query($conn,$sql);
		// $row = mysqli_fetch_assoc($result);
		// $verifier_Role = $row["Verifier_Role"];
		// $approver_Role = $row["Approver_Role"];
		if($mId == 1){
			$rmSql = "SELECT `RMId` FROM `EmployeeMaster` where `EmpId` = '$empId'";
			$rmResult = mysqli_query($conn,$rmSql);
			$rmRow = mysqli_fetch_assoc($rmResult);
			$rmId = $rmRow["RMId"];
		}
		

		$activitySql = "Insert into Activity(DId,MappingId,EmpId,MenuId,LocationId,Event,GeoLocation,Distance,MobileDateTime,MobileTimestamp,Tenent_Id)"
						." values ('$dId','$mapId','$empId','$mId','$lId','$event','$geolocation','$distance','$date1','$transactionId',$tenentId)";
		
		if(mysqli_query($conn,$activitySql)){
			$activityId = mysqli_insert_id($conn);
		}
		
		$isFinallySubmit = "";
		if($checklist != null && count($checklist) != 0){
			$insertMapping = "INSERT INTO `Mapping`(`EmpId`, `Verifier`, `MenuId`, `LocationId`, `Start`, `End`, `ActivityId`, `Tenent_Id`, `CreateDateTime`) values ('$empId', '$rmId', '$mId', '$lId', curdate(), curdate(), '$activityId', '$tenentId', current_timestamp) ";
			mysqli_query($conn,$insertMapping);

			if($actId == null  && $actId == ''){
				$mappingId = $conn->insert_id;
				$insertInTransHdr="INSERT INTO `TransactionHDR` (`ActivityId`,`Status`,`Lat_Long`, `FakeGPS_App`) VALUES 
				('$activityId','Created','$geolocation','$fakeGpsMessage')";
				
				if(mysqli_query($conn,$insertInTransHdr)){
					$lastTransHdrId = $conn->insert_id;
					$fromDate = "";
					$toDate = "";
					$reason = "";
					foreach($checklist as $k=>$v)
					{
						$answer=$v['value'];
						$chkp_idArray=explode("_",$v['Chkp_Id']);
						$dependentChpId=0;
						if(count($chkp_idArray) > 1){
							// For Dependent Checkpoint
							$chkp_id = $chkp_idArray[1];
							$dependentChpId = $chkp_idArray[0];
							if($chkp_id == 1){
								$fromDate = $answer;	
							}
							if($chkp_id == 2){
								$toDate = $answer;
							}
							if($chkp_id == 3){
								$reason = $answer;
							}
						}
						else{
							// For Non-dependent Checkpoint
							$chkp_id = $chkp_idArray[0];
							if($chkp_id == 1){
								$fromDate = $answer;
								$fromDate = str_replace('/', '-', $fromDate);
								$fromDate = date("Y-m-d", strtotime($fromDate));
							}
							if($chkp_id == 2){
								$toDate = $answer;
								$toDate = str_replace('/', '-', $toDate);
								$toDate = date("Y-m-d", strtotime($toDate));
							}
							if($chkp_id == 3){
								$reason = $answer;
							}
						}	
						
						// $dependent=$v['Dependent'];
						// if($dependent == ""){
						// 	$dependent = 0;
						// }
						

						// $insertInTransDtl="INSERT INTO `TransactionDTL` (`ActivityId`,`ChkId`,`Value`,`DependChkId`) VALUES (?,?,?,?)";
						$insertInTransDtl="INSERT INTO `TransactionDTL` (`ActivityId`,`ChkId`,`Value`,`DependChkId`) VALUES (?,?,?,?)";
						$stmt = $conn->prepare($insertInTransDtl);
						$stmt->bind_param("iisi", $activityId, $chkp_id, $answer, $dependentChpId);
						$stmt->execute();

						if($fromDate != "" && $toDate != "" && $reason != ""){
							$monthList = array();
							$dayList = getDatesFromRange($fromDate, $toDate);
							for($i=0;$i<count($dayList);$i++){
								$thisDate = $dayList[$i];
								$thisDate_monthYear = date('M-Y', strtotime($thisDate));
								if(count($monthList) == 0){
									array_push($monthList, $thisDate_monthYear);
								}
								else{
									if(!in_array($thisDate_monthYear,$monthList)){
										array_push($monthList, $thisDate_monthYear);
									}
								}
							}

							$monthImplode = implode(",", $monthList);

							$insertLeave = "INSERT INTO `LeaveMaster`(`EmpId`, `FromDate`, `ToDate`, `Reason`, `MonthInclude`, `ActivityId`) VALUES (?,?,?,?,?,?)";
							$stmt = $conn->prepare($insertLeave);
							$stmt->bind_param("sssssi", $empId, $fromDate, $toDate, $reason, $monthImplode, $activityId);
							if($stmt->execute()){
								$insertId = $conn->insert_id;

								$rmEmpSql = "SELECT e1.EmailId FROM EmployeeMaster e join EmployeeMaster e1 on e.RMId = e1.EmpId where e.EmpId = '$empId'";
								$rmEmpQuery = mysqli_query($conn,$rmEmpSql);
								$rmEmpRow = mysqli_fetch_assoc($rmEmpQuery);
								$rmEmailId = $rmEmpRow["EmailId"];
							
								$fromDate = date("d-M-Y", strtotime($fromDate));
								$toDate = date("d-M-Y", strtotime($toDate));
								// $subject = "Leave - ".$insertId;
								$subject = "Leave - from ".$fromDate.' to '.$toDate;
								$msg = "Dear Mam/Sir,"."<br>";
								$msg .= "Leave apply by <b>$empName</b> from <b>$fromDate</b> to <b>$toDate</b>."."<br>";
								$msg .= "<b>Reason</b> : $reason"."<br>";
								$msg .= "Please take action(Approve or Reject) on this leave.";

								$classObj = new SendMailClass();
								$mailStatus = $classObj->sendMail($rmEmailId, $subject, $msg, null);
							}

						}
						
					}
				}
			}
			else{
				$isAllSave = false;
				foreach($checklist as $k=>$v)
				{
					$answer=$v['value'];
					$chkp_idArray=explode("_",$v['Chkp_Id']);
					$dependentChpId=0;
					if(count($chkp_idArray) > 1){
						$chkp_id = $chkp_idArray[1];
						$dependentChpId = $chkp_idArray[0];
					}
					else{
						$chkp_id = $chkp_idArray[0];
						if($chkp_id == 5197){
							$isFinallySubmit = $answer;
						}
					}	
					
					// $dependent=$v['Dependent'];
					// if($dependent == ""){
					// 	$dependent = 0;
					// }
					

					// $insertInTransDtl="INSERT INTO `TransactionDTL` (`ActivityId`,`ChkId`,`Value`,`DependChkId`) VALUES (?,?,?,?)";
					$insertInTransDtl="INSERT INTO `TransactionDTL` (`ActivityId`,`ChkId`,`Value`,`DependChkId`) VALUES (?,?,?,?)";
					$stmt = $conn->prepare($insertInTransDtl);
					$stmt->bind_param("iisi", $activityId, $chkp_id, $answer, $dependentChpId);
					if($stmt->execute()){
						$isAllSave = true;
					}
					
				}

				if($isAllSave){
					$lastTransHdrId = $activityId;

				}
			}

			// Update `ActivityId` in `Mapping` table after first of TODO task user.
			if($assignId != ""){
				$updateAssignTaskSql = "Update Mapping set ActivityId = '$activityId' where MappingId = $assignId";
				mysqli_query($conn,$updateAssignTaskSql);

			}
				
		}
		
	}
	// For second or more TODO task user.
	else{
		// $sql = "SELECT r1.Verifier_Role, r1.Approver_Role FROM `Menu` r1 where r1.MenuId = '$mId' ";
		// $result = mysqli_query($conn,$sql);
		// $row = mysqli_fetch_assoc($result);
		// $verifier_Role = $row["Verifier_Role"];
		// $approver_Role = $row["Approver_Role"];

		$activitySql = "Insert into Activity(DId,MappingId,EmpId,MenuId,LocationId,Event,GeoLocation,Distance,MobileDateTime,MobileTimestamp,Tenent_Id)"
						." values ('$dId','$mapId','$empId','$mId','$lId','$event','$geolocation','$distance','$date1','$transactionId',$tenentId)";
		
		if(mysqli_query($conn,$activitySql)){
			$activityId = mysqli_insert_id($conn);
		}
		
		$isFinallySubmit = "";
		if($checklist != null && count($checklist) != 0){
			$insertMapping = "INSERT INTO `Mapping`(`EmpId`, `MenuId`, `LocationId`, `Start`, `End`, `ActivityId`, `Tenent_Id`, `CreateDateTime`) 
			values ('$empId', '$mId', '$lId', curdate(), curdate(), '$activityId', '$tenentId', current_timestamp) ";
			mysqli_query($conn,$insertMapping);

			// not in use below if block.
			if($actId == null  && $actId == ''){
				$mappingId = $conn->insert_id;
				$insertInTransHdr="INSERT INTO `TransactionHDR` (`ActivityId`,`Status`,`Lat_Long`, `FakeGPS_App`) VALUES 
				('$activityId','Created','$geolocation','$fakeGpsMessage')";
				
				if(mysqli_query($conn,$insertInTransHdr)){
					$lastTransHdrId = $conn->insert_id;
					foreach($checklist as $k=>$v)
					{
						$answer=$v['value'];
						$chkp_idArray=explode("_",$v['Chkp_Id']);
						$dependentChpId=0;
						if(count($chkp_idArray) > 1){
							$chkp_id = $chkp_idArray[1];
							$dependentChpId = $chkp_idArray[0];
						}
						else{
							$chkp_id = $chkp_idArray[0];
						}	
						
						// $dependent=$v['Dependent'];
						// if($dependent == ""){
						// 	$dependent = 0;
						// }
						

						// $insertInTransDtl="INSERT INTO `TransactionDTL` (`ActivityId`,`ChkId`,`Value`,`DependChkId`) VALUES (?,?,?,?)";
						$insertInTransDtl="INSERT INTO `TransactionDTL` (`ActivityId`,`ChkId`,`Value`,`DependChkId`) VALUES (?,?,?,?)";
						$stmt = $conn->prepare($insertInTransDtl);
						$stmt->bind_param("iisi", $activityId, $chkp_id, $answer, $dependentChpId);
						$stmt->execute();
					}
					
				}
			}
			// for insert details of second or more TODO task checkpoint in `TransactionDTL` in table.
			else{
				$isAllSave = false;
				foreach($checklist as $k=>$v)
				{
					$answer=$v['value'];
					$chkp_idArray=explode("_",$v['Chkp_Id']);
					$dependentChpId=0;
					if(count($chkp_idArray) > 1){
						$chkp_id = $chkp_idArray[1];
						$dependentChpId = $chkp_idArray[0];
					}
					else{
						$chkp_id = $chkp_idArray[0];
					}	
					
					// $dependent=$v['Dependent'];
					// if($dependent == ""){
					// 	$dependent = 0;
					// }
				

					// $insertInTransDtl="INSERT INTO `TransactionDTL` (`ActivityId`,`ChkId`,`Value`,`DependChkId`) VALUES (?,?,?,?)";
					$insertInTransDtl="INSERT INTO `TransactionDTL` (`ActivityId`,`ChkId`,`Value`,`DependChkId`) VALUES (?,?,?,?)";
					$stmt = $conn->prepare($insertInTransDtl);
					$stmt->bind_param("iisi", $activityId, $chkp_id, $answer, $dependentChpId);
					if($stmt->execute()){
						$isAllSave = true;
					}
					
				}

				if($isAllSave){
					$lastTransHdrId = $activityId;

				}
			}

			
				
		}
		// not in use below if block.
		if($assignId != ""){
			$updateAssignTaskSql = "Update Mapping set ActivityId = '$activityId' where MappingId = $assignId";
			mysqli_query($conn,$updateAssignTaskSql);

		}
		// for update second or more user `ActivityId` in `TransactionHDR` table
		if($actId != null && $actId != ''){
			$selectTransHdrSql = "Select * from TransactionHDR  where ActivityId = $actId";
			$selectTransHdrResult = mysqli_query($conn,$selectTransHdrSql);
			$thRow=mysqli_fetch_array($selectTransHdrResult);
			if($thRow['Status'] == 'Created'){
				if($isFinallySubmit != 'No'){
					$updateTransHdrSql = "Update TransactionHDR set Status = 'Verified', VerifierActivityId = '$activityId', Verify_Final_Submit = '$isFinallySubmit' where ActivityId = $actId";
				}
				else{
					$updateTransHdrSql = "Update TransactionHDR set VerifierActivityId = '$activityId', Verify_Final_Submit = '$isFinallySubmit' 
					where ActivityId = $actId";
				}
			}
			else if($thRow['Status'] == 'Verified'){
				if($isFinallySubmit != 'No'){
					$updateTransHdrSql = "Update TransactionHDR set Status = 'Approved',ApproverActivityId = '$activityId' where ActivityId = $actId";
				}
			}
			
			mysqli_query($conn,$updateTransHdrSql);
		}
	}


	
	$output = new StdClass;
	if($lastTransHdrId != ""){
		$output -> error = "200";
		$output -> message = "success";
		$output -> TransID = "$activityId";
	}
	else{
		$output -> error = "0";
		$output -> message = "something wrong";
		$output -> TransID = "$activityId";
	}
	echo json_encode($output);

	// file_put_contents('/var/www/trinityapplab.in/html/Company/log/log_'.date("Y-m-d").'.log', date("Y-m-d H:i:s").' '.json_encode($output)."\n", FILE_APPEND);
		
	
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