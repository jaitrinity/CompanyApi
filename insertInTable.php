<?php
include("dbConfiguration.php");
require 'base64ToAny.php';
require 'SendMailClass.php';

$insertType = $_REQUEST["insertType"];
$methodType = $_SERVER['REQUEST_METHOD'];
$json = file_get_contents('php://input');
$jsonData = json_decode($json);
if($insertType == "employee" && $methodType === 'POST'){
	$base64 = new Base64ToAny();

	$offerId = $jsonData->offerId;
	$name = $jsonData->name;
	$fatherHusbandName = $jsonData->fatherHusbandName;
	$mobile = $jsonData->mobile;
	$password = $jsonData->password;
	$emailId = $jsonData->emailId;
	$dob = $jsonData->dob;
	$dob = str_replace('/', '-', $dob);
	$dob = date("Y-m-d", strtotime($dob));
	$doj = $jsonData->doj;
	$doj = str_replace('/', '-', $doj);
	$doj = date("Y-m-d", strtotime($doj));
	$address = $jsonData->address;
	$pan = $jsonData->pan;
	$panStr = $jsonData->panStr;
	$panStr = $base64->base64_to_jpeg($panStr,$mobile.'_PAN');
	$aadhar = $jsonData->aadhar;
	$aadharStr = $jsonData->aadharStr;
	$aadharStr = $base64->base64_to_jpeg($aadharStr,$mobile.'_Aadhar');
	$basic = $jsonData->basic;
	$hsr = $jsonData->hsr;
	$conveyanceAllowance = $jsonData->conveyanceAllowance;
	$medicalAllowance = $jsonData->medicalAllowance;
	$telephoneAllowance = $jsonData->telephoneAllowance;
	$specialAllowance = $jsonData->specialAllowance;
	$otherAllowance = $jsonData->otherAllowance;
	$specialAllowance = $jsonData->specialAllowance;
	$retentionBonus = $jsonData->retentionBonus;

	$sql1 = "select * from `EmployeeMaster` where `Mobile` = '$mobile' and `IsActive` = 1";
	$query1 = mysqli_query($conn,$sql1);

	$isExist1 = false;
	if(mysqli_num_rows($query1) != 0){
		$isExist1 = true;
	}

	$output = "";
	if($isExist1){
		$output -> responseCode = "422";
		$output -> responseDesc = "already exist employee on ".$mobile." mobile number";
	}
	
	else{
		$configSql = "SELECT `EmpCount` FROM `Configration`";
		$configQuery = mysqli_query($conn,$configSql);
		$configRow = mysqli_fetch_assoc($configQuery);
		$empCount = $configRow["EmpCount"];
		$employeeId = 'tr'.str_pad($empCount, 3, '0', STR_PAD_LEFT);
		// $employeeId = $mobile;

		$insertEmployee = "INSERT INTO `EmployeeMaster`(`EmpId`, `Name`, `FatherHusbandName`, `Mobile`, `EmailId`, `Password`, `DOB`, `DOJ`, `Address`, `RoleId`, `AadharNumber`, `AadharAttachment`, `PAN`, `PANAttachment`) VALUES ('$employeeId', '$name', '$fatherHusbandName', '$mobile', '$emailId', '$password', '$dob', '$doj', '$address', 2, '$aadhar', '$aadharStr', '$pan', '$panStr')";

		// echo $insertEmployee;

		if(mysqli_query($conn,$insertEmployee)){
			$output -> employeeId = $employeeId;
			$output -> responseCode = "100000";
			$output -> responseDesc = "Successfully inserted";	

			$updateOffer = "UPDATE `OfferLetter` set `IsRegisteredEmp` = 1 where `Id` = $offerId";
			mysqli_query($conn,$updateOffer);

			$updateConfig = "UPDATE `Configration` set `EmpCount` = `EmpCount` + 1";
			mysqli_query($conn,$updateConfig);

			$insertEar = "INSERT INTO `EmployeeEarnings`(`EmpId`, `Basic`, `HRA`, `ConveyanceAllowance`, `MedicalAllowance`, `TelephoneAllowance`, `SpecialAllowance`, `OtherAllowance`, `RetentionBonus`) VALUES ('$employeeId', $basic, $hsr, $conveyanceAllowance, $medicalAllowance, $telephoneAllowance, $specialAllowance, $otherAllowance, $retentionBonus) ";
			mysqli_query($conn,$insertEar);

			
			$portalUrl = "https://www.trinityapplab.in/Company/Portal/#/login";
			$subject = "Congratulation";
			$msg = "Hi $name,"."<br>";
			$msg .= "Thanks for joining <b>Trinity Mobile App Lab</b> family."."<br>";
			$msg .= "Please use link <a href='$portalUrl'>Company</a> for portal. Password is same as mobile number. You can change at anytime"."<br>";
			$classObj = new SendMailClass();
			$mailStatus = $classObj->sendMail($emailId, $subject, $msg, null);
		}
		else{
			$output -> responseCode = "0";
			$output -> responseDesc = "Something wrong";
		}
	}
	echo json_encode($output);
}
else if($insertType == "deduction" && $methodType === 'POST'){
	$empId = $jsonData->empId;
	$basic = $jsonData->basic;
	$month = $jsonData->month;
	$paidDays = $jsonData->paidDays;
	$grossSalary = $jsonData->grossSalary;
	$retentionBonus = $jsonData->retentionBonus;
	$professionTax = $jsonData->professionTax;
	// $lossOfPay = $jsonData->lossOfPay;
	$lossOfPay = 0;
	$otherDeductions = $jsonData->otherDeductions;
	$incomeTax = $jsonData->incomeTax;
	$otherTax = $jsonData->otherTax;

	$empLossOfPay = "SELECT `LossOfPay` FROM `EmployeeLossOfPay` where `EmpId` = '$empId' and `MonthYear` = '$month'";
	$empLossOfPayQuery = mysqli_query($conn,$empLossOfPay);
	$rowCount = mysqli_num_rows($empLossOfPayQuery);
	if($rowCount != 0){
		$empLossOfPayRow = mysqli_fetch_assoc($empLossOfPayQuery);
		$lossOfPay = $empLossOfPayRow["LossOfPay"];
	}

	$insertDeduction = "INSERT INTO `EmployeeDeductions`(`EmpId`, `Basic`, `GrossSalary`, `MonthYear`, `PaidDays`, `RetentionBonus`, `ProfessionTax`, `LossOfPay`, `OtherDeductions`, `IncomeTax`, `OtherTax`) VALUES ('$empId', $basic, $grossSalary, '$month', $paidDays, $retentionBonus, $professionTax, $lossOfPay, $otherDeductions, $incomeTax, $otherTax)";

	$output = "";
	if(mysqli_query($conn,$insertDeduction)){
		$output -> responseCode = "100000";
		$output -> responseDesc = "Successfully inserted";
	}
	else{
		$output -> responseCode = "0";
		$output -> responseDesc = "Something wrong";
	}
	echo json_encode($output);
}
else if($insertType == "uploadDeduction" && $methodType === 'POST'){
	$failExcelArr = [];
	foreach($jsonData as $importData) {
		$loginEmpId = $importData->loginEmpId;
		$basic = $importData->basic;
		$grossSalary = $importData->grossSalary;
		$month = $importData->month;
		$paidDays = $importData->paidDays;
		$empId = $importData->empId;
		// $name = $importData->name;
		$retentionBonus = $importData->retentionBonus;
		$professionTax = $importData->professionTax;
		// $lossOfPay = $importData->lossOfPay;
		$lossOfPay = 0;
		$otherDeductions = $importData->otherDeductions;
		$incomeTax = $importData->incomeTax;
		$otherTax = $importData->otherTax;

		$empLossOfPay = "SELECT `LossOfPay` FROM `EmployeeLossOfPay` where `EmpId` = '$empId' and `MonthYear` = '$month'";
		$empLossOfPayQuery = mysqli_query($conn,$empLossOfPay);
		$rowCount = mysqli_num_rows($empLossOfPayQuery);
		if($rowCount != 0){
			$empLossOfPayRow = mysqli_fetch_assoc($empLossOfPayQuery);
			$lossOfPay = $empLossOfPayRow["LossOfPay"];
		}

		$insertDeduction = "INSERT INTO `EmployeeDeductions`(`EmpId`, `Basic`, `GrossSalary`, `MonthYear`, `PaidDays`, `RetentionBonus`, `ProfessionTax`, `LossOfPay`, `OtherDeductions`, `IncomeTax`, `OtherTax`) VALUES ('$empId', $basic, $grossSalary, '$month', $paidDays, $retentionBonus, $professionTax, $lossOfPay, $otherDeductions, $incomeTax, $otherTax)";
		// echo $insertDeduction.'---';
		if(mysqli_query($conn,$insertDeduction)){
			// Succfully insert
		}
		else{
			array_push($failExcelArr, $mobile);
		}
	}

	$output = "";
	if(count($failExcelArr) == 0){
		$output -> responseCode = "100000";
		$output -> responseDesc = "Successfully inserted";
	}
	else{
		$output -> responseCode = "-102003";
		$output -> responseDesc = "Except ".implode(',',$failExcelArr)." Mobile of excel, Data Successfully inserted";
	}
	echo json_encode($output);
}
else if($insertType == "offerLetter" && $methodType === 'POST'){
	$offerDate = date('Y-m-d', strtotime('0 day'));
	$offerExpireDate = date('Y-m-d', strtotime('3 day'));

	$intervieweeId = $jsonData->intervieweeId;
	$name = $jsonData->name;
	$mobile = $jsonData->mobile;
	$emailId = $jsonData->emailId;
	$addLine1 = $jsonData->addLine1;
	$addLine2 = $jsonData->addLine2;
	$designation = $jsonData->designation;
	$doj = $jsonData->doj;
	$doj = str_replace('/', '-', $doj);
	$doj = date("Y-m-d", strtotime($doj));
	$lpa = $jsonData->lpa;

	$insertOffer = "INSERT INTO `OfferLetter`(`Name`, `Mobile`, `EmailId`, `AddressLine1`, `AddressLine2`, `Designation`, `DOJ`, `LPA`, `OfferDate`, `OfferExpierDate`) VALUES ('$name', '$mobile', '$emailId', '$addLine1', '$addLine2', '$designation', '$doj', '$lpa', '$offerDate', '$offerExpireDate')";

	$output = "";
	if(mysqli_query($conn,$insertOffer)){
		$output -> responseCode = "100000";
		$output -> responseDesc = "Successfully inserted";

		$updateInterview = "UPDATE `Interviewee` set `IsOfferGenerate` = 1 where `Id` = $intervieweeId";
		mysqli_query($conn,$updateInterview);
	}
	else{
		$output -> responseCode = "0";
		$output -> responseDesc = "Something wrong";
	}
	echo json_encode($output);
}
else if($insertType == "saveLeave" && $methodType === 'POST'){
	$empId = $jsonData->empId;
	$empName = $jsonData->empName;
	$fromDate = $jsonData->fromDate;
	$fromDate = str_replace('/', '-', $fromDate);
	$fromDate = date("Y-m-d", strtotime($fromDate));
	$toDate = $jsonData->toDate;
	$toDate = str_replace('/', '-', $toDate);
	$toDate = date("Y-m-d", strtotime($toDate));
	$reason = $jsonData->reason;

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

	$insertLeave = "INSERT INTO `LeaveMaster`(`EmpId`, `FromDate`, `ToDate`, `Reason`, `MonthInclude`) VALUES (?,?,?,?,?)";
	$stmt = $conn->prepare($insertLeave);
	$stmt->bind_param("sssss", $empId, $fromDate, $toDate, $reason, $monthImplode);
	$output = "";
	if($stmt->execute()){
		$leaveId = $conn->insert_id;
		$output -> responseCode = "100000";
		$output -> responseDesc = "Successfully inserted";

		$menuId = 1; 
		$locationId = 1; 
		$event = 'Submit';
		$tenentId = 1;
		$activitySql = "INSERT INTO Activity(`EmpId`, `MenuId`, `LocationId`, `Event`, `MobileDateTime`, `Tenent_Id`) 
		VALUES ('$empId', $menuId, '$locationId', '$event', CURRENT_TIMESTAMP, $tenentId)";
		if(mysqli_query($conn,$activitySql)){
			$activityId = $conn->insert_id;
			$insertMapping = "INSERT INTO `Mapping`(`EmpId`, `MenuId`, `LocationId`, `Start`, `End`, `ActivityId`, `Tenent_Id`) VALUES ('$empId', $menuId, '$locationId', curdate(), curdate(), $activityId, $tenentId) ";
			if(mysqli_query($conn,$insertMapping)){
				$mappingId = $conn->insert_id;
				$insertInTransHdr = "INSERT INTO `TransactionHDR` (`ActivityId`,`Status`) VALUES ($activityId, 'Created')";
				if(mysqli_query($conn,$insertInTransHdr)){
					$exFromDate = explode("-", $fromDate);
					$exToDate = explode("-", $toDate);

					$fromDate1 = $exFromDate[2]."/".$exFromDate[1]."/".$exFromDate[0];
					$toDate1 = $exToDate[2]."/".$exToDate[1]."/".$exToDate[0];

					$insertInTransDtl="INSERT INTO `TransactionDTL`(`ActivityId`, `ChkId`, `Value`, `DependChkId`) 
					VALUES (?,1,?,0),(?,2,?,0),(?,3,?,0)";
					$stmt = $conn->prepare($insertInTransDtl);
					$stmt->bind_param("isisis", $activityId, $fromDate1, $activityId, $toDate1, $activityId, $reason);
					$stmt->execute();

					$updateActId = "UPDATE `LeaveMaster` set `ActivityId` = $activityId where `Id` = $leaveId";
					mysqli_query($conn,$updateActId);
				}
			}	
		}

		$rmEmpSql = "SELECT e1.EmailId FROM EmployeeMaster e join EmployeeMaster e1 on e.RMId = e1.EmpId where e.EmpId = '$empId'";
		$rmEmpQuery = mysqli_query($conn,$rmEmpSql);
		$rmEmpRow = mysqli_fetch_assoc($rmEmpQuery);
		$rmEmailId = $rmEmpRow["EmailId"];

		$fromDate = date("d-M-Y", strtotime($fromDate));
		$toDate = date("d-M-Y", strtotime($toDate));
		// $subject = "Leave - ".$leaveId;
		$subject = "Leave - from ".$fromDate.' to '.$toDate;
		$msg = "Dear Mam/Sir,"."<br>";
		$msg .= "Leave apply by <b>$empName</b> from <b>$fromDate</b> to <b>$toDate</b>."."<br>";
		$msg .= "<b>Reason</b> : $reason"."<br>";
		$msg .= "Please take action(Approve or Reject) on this leave.";
		$classObj = new SendMailClass();
		$mailStatus = $classObj->sendMail($rmEmailId, $subject, $msg, null);
	}
	else{
		$output -> responseCode = "0";
		$output -> responseDesc = "Something wrong";
	}
	echo json_encode($output);
}
// else if($insertType == "saveLeave" && $methodType === 'POST'){
// 	$empId = $jsonData->empId;
// 	$empName = $jsonData->empName;
// 	$fromDate = $jsonData->fromDate;
// 	$fromDate = str_replace('/', '-', $fromDate);
// 	$fromDate = date("Y-m-d", strtotime($fromDate));
// 	$toDate = $jsonData->toDate;
// 	$toDate = str_replace('/', '-', $toDate);
// 	$toDate = date("Y-m-d", strtotime($toDate));
// 	$reason = $jsonData->reason;

// 	$monthList = array();
// 	$dayList = getDatesFromRange($fromDate, $toDate);
// 	for($i=0;$i<count($dayList);$i++){
// 		$thisDate = $dayList[$i];
// 		$thisDate_monthYear = date('M-Y', strtotime($thisDate));
// 		if(count($monthList) == 0){
// 			array_push($monthList, $thisDate_monthYear);
// 		}
// 		else{
// 			if(!in_array($thisDate_monthYear,$monthList)){
// 				array_push($monthList, $thisDate_monthYear);
// 			}
// 		}
// 	}

// 	$monthImplode = implode(",", $monthList);

// 	$insertLeave = "INSERT INTO `LeaveMaster`(`EmpId`, `FromDate`, `ToDate`, `Reason`, `MonthInclude`) VALUES ('$empId', '$fromDate', '$toDate', '$reason', '$monthImplode')";
// 	$output = "";
// 	if(mysqli_query($conn,$insertLeave)){
// 		$insertId = $conn->insert_id;
// 		$output -> responseCode = "100000";
// 		$output -> responseDesc = "Successfully inserted";

// 		$rmEmpSql = "SELECT e1.EmailId FROM EmployeeMaster e join EmployeeMaster e1 on e.RMId = e1.EmpId where e.EmpId = '$empId'";
// 		$rmEmpQuery = mysqli_query($conn,$rmEmpSql);
// 		$rmEmpRow = mysqli_fetch_assoc($rmEmpQuery);
// 		$rmEmailId = $rmEmpRow["EmailId"];

// 		$fromDate = date("d-M-Y", strtotime($fromDate));
// 		$toDate = date("d-M-Y", strtotime($toDate));
// 		// $subject = "Leave - ".$insertId;
// 		$subject = "Leave - from ".$fromDate.' to '.$toDate;
// 		$msg = "Dear Mam/Sir,"."<br>";
// 		$msg .= "Leave apply by <b>$empName</b> from <b>$fromDate</b> to <b>$toDate</b>."."<br>";
// 		$msg .= "<b>Reason</b> : $reason"."<br>";
// 		$msg .= "Please take action(Approve or Reject) on this leave.";
// 		$classObj = new SendMailClass();
// 		$mailStatus = $classObj->sendMail($rmEmailId, $subject, $msg, null);
// 	}
// 	else{
// 		$output -> responseCode = "0";
// 		$output -> responseDesc = "Something wrong";
// 	}
// 	echo json_encode($output);
// }
else if($insertType == "interviewee" && $methodType === "POST"){
	$name = $jsonData->name;
	$mobile = $jsonData->mobile;
	$emailId = $jsonData->emailId;
	$companyName = $jsonData->companyName;
	$ctc = $jsonData->ctc;
	$noticePeriod = $jsonData->noticePeriod;
	$cvStr = $jsonData->cvStr;
	$base64 = new Base64ToAny();
	$cvStr = $base64->base64_to_jpeg($cvStr,$mobile.'_CV');

	$insertSql = "INSERT INTO `Interviewee`(`Name`, `Mobile`, `EmailId`, `CompanyName`, `CTC`, `NoticePeriod`, `CV_Attachment`) VALUES ('$name', '$mobile', '$emailId', '$companyName', '$ctc', '$noticePeriod', '$cvStr')";
	$output = "";
	if(mysqli_query($conn,$insertSql)){
		$insertId = $conn->insert_id;
		$output -> responseCode = "100000";
		$output -> responseDesc = "Successfully inserted";
	}
	else{
		$output -> responseCode = "0";
		$output -> responseDesc = "Something wrong";
	}
	echo json_encode($output);

}
else if($insertType == "submitInvoice" && $methodType === "POST"){
	$corporateId = $jsonData->corporateName;
	$clientId = $jsonData->clientName;
	$poNumber = $jsonData->poNumber;
	$workList = $jsonData->workList;
	$subTotal = 0;
	for($i=0;$i<count($workList);$i++){
		$subTotal += $workList[$i]->subTotal;
	}
	$isStateEqual = $jsonData->isStateEqual;
	$CGST = 0;
	$SGST = 0;
	$IGST = 0;
	if($isStateEqual == 1){
		$CGST = 9;
		$SGST = 9;
	}
	else{
		$IGST = 18;
	}

	$insertSql = "INSERT INTO `InvoiceMaster`(`CorporateId`, `ClientId`, `PoNumber`, `SubTotal`, `CGST`, `SGST`, `IGST`) VALUES ($corporateId, $clientId, '$poNumber', $subTotal, $CGST, $SGST, $IGST)";
	$output = "";
	if(mysqli_query($conn,$insertSql)){
		$invoiceId = $conn->insert_id;
		for($j=0;$j<count($workList);$j++){
			$description = $workList[$j]->workDescription;
			$quantity = $workList[$j]->quantity;
			$unitPrice = $workList[$j]->unitPrice;
			$subTotal = $workList[$j]->subTotal;
			$invDescSql = "INSERT INTO `InvoiceDescription`(`InvoiceId`, `Description`, `Quantity`, `UnitPrice`, `SubTotal`) VALUES ($invoiceId, '$description', $quantity, $unitPrice, $subTotal)";
			if(mysqli_query($conn,$invDescSql)){

			}
		}
		

		$output -> responseCode = "100000";
		$output -> responseDesc = "Successfully inserted";
		$output -> invoiceId = $invoiceId;
	}
	else{
		$output -> responseCode = "0";
		$output -> responseDesc = "Something wrong";
	}
	echo json_encode($output);
}
else if($insertType == "client" && $methodType === "POST"){
	$corporateId = $jsonData->corporateId;
	$clientName = $jsonData->clientName;
	$contactPerson = $jsonData->contactPerson;
	$addLine1 = $jsonData->addLine1;
	$addLine2 = $jsonData->addLine2;
	$address = $addLine1.'---'.$addLine2;
	$state = $jsonData->state;
	$gstNo = $jsonData->gstNo;
	$insertSql = "INSERT INTO `ClientMaster`(`CorporateId`, `Name`, `ContactPerson`, `Address`, `State`, `GSTNo`) VALUES ($corporateId, '$clientName', '$contactPerson', '$address', '$state', '$gstNo')";
	$output = "";
	if(mysqli_query($conn,$insertSql)){
		$output -> responseCode = "100000";
		$output -> responseDesc = "Successfully inserted";
	}
	else{
		$output -> responseCode = "0";
		$output -> responseDesc = "Something wrong";
	}
	echo json_encode($output);
}
else if($insertType == "poUpload" && $methodType === 'POST'){
	$base64 = new Base64ToAny();
	$clientId = $jsonData->clientId;
	$poDate = $jsonData->poDate;
	$poDate = str_replace('/', '-', $poDate);
	$poDate = date("Y-m-d", strtotime($poDate));
	$poStr = $jsonData->poStr;
	$t=date("YmdHis");
	$poStr = $base64->generatePO($poStr,$t);
	
	$insertSql = "INSERT INTO `PO_Master`(`ClientId`, `PO_Date`, `PO_Attachment`) VALUES ($clientId, '$poDate', '$poStr')";
	// echo $insertSql;
	$output = "";
	if(mysqli_query($conn,$insertSql)){
		$output -> responseCode = "100000";
		$output -> responseDesc = "Successfully inserted";
	}
	else{
		$output -> responseCode = "0";
		$output -> responseDesc = "Something wrong";
	}
	echo json_encode($output);
}
	
?>

<?php 
// function noOfDaysInDates($fromDate, $toDate){
// 	$fromDate = strtotime($fromDate);
//   	$toDate = strtotime($toDate);
//   	$noOfDays = ($toDate - $fromDate)/60/60/24;
//   	return $noOfDays+1;
// }
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
