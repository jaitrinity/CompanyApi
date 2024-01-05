<?php
include("dbConfiguration.php");
require 'SendMailClass.php';
require 'base64ToAny.php';
$methodType = $_SERVER['REQUEST_METHOD'];
$updateType = $_REQUEST["updateType"];
$json = file_get_contents('php://input');
$jsonData=json_decode($json);
if($updateType == "leaveStatus" && $methodType === 'POST'){
	$leaveId = $jsonData->leaveId;
	$status = $jsonData->status;
	
	$updateDevice = "update `LeaveMaster` set `Status` = $status where `Id` = $leaveId ";
	$output = "";
	if(mysqli_query($conn,$updateDevice)){
		$output -> responseCode = "100000";
		$output -> responseDesc = "Successfully update";

        $sql = "SELECT e.Name, e.EmailId, l.FromDate, l.ToDate FROM LeaveMaster l join EmployeeMaster e on l.EmpId = e.EmpId where l.Id = $leaveId";
        $query = mysqli_query($conn,$sql);
        $row = mysqli_fetch_assoc($query);

        $name = $row["Name"];
		$toMailId = $row["EmailId"];
        $fromDate = $row["FromDate"];
        $fromDate = date("d-M-Y", strtotime($fromDate));
        $toDate = $row["ToDate"];
        $toDate = date("d-M-Y", strtotime($toDate));
		// $subject = "Leave - ".$leaveId;
        $subject = "Leave - from ".$fromDate.' to '.$toDate;
		$msg = "Dear $name,"."<br>";
		if($status == 1)
			$msg .= "Your leave is Approved.";
		else
			$msg .= "Your leave is Rejected.";

        $classObj = new SendMailClass();
        $mailStatus = $classObj->sendMail($toMailId, $subject, $msg, null);

	}
	else{
		$output -> responseCode = "0";
		$output -> responseDesc = "Something wrong";
	}
	echo json_encode($output);
}
else if($updateType == "interviewee" && $methodType === 'POST'){
    $intervieweeId = $jsonData->intervieweeId;
    $remark = $jsonData->remark;
    $status = $jsonData->status;

    $updateSql = "update `Interviewee` set `Status` = $status, `Remark` = '$remark' where `Id` = $intervieweeId ";
    $output = "";
    if(mysqli_query($conn,$updateSql)){
        $output -> responseCode = "100000";
        $output -> responseDesc = "Successfully update";

        $sql = "SELECT * from `Interviewee` where `Id` = $intervieweeId";
        $query = mysqli_query($conn,$sql);
        $row = mysqli_fetch_assoc($query);
        $name = $row["Name"];
        $toMailId = "";
        $msg = "";
        // Selected
        if($status == 1){
            $toMailId = $row["EmailId"];
            $msg .= "Dear $name,"."<br>";
            $msg .= "Congratulation you are Selected.";
        }
        // Rejected
        else if($status == 2){
            $msg .= "Dear Mam,"."<br>";
            $msg .= "$name is Rejected.";
        }
        $subject = "Interview result";
        $classObj = new SendMailClass();
        $mailStatus = $classObj->sendMail($toMailId, $subject, $msg, null);
    }
    else{
        $output -> responseCode = "0";
        $output -> responseDesc = "Something wrong";
    }
    echo json_encode($output);
}
else if($updateType == "updateInvoice" && $methodType === 'POST'){
    $invoiceId = $jsonData->invoiceId;
    $invoiceStr = $jsonData->invoiceStr;
    $pdfFileName = "Inv-Sign-".$invoiceId;

    $base64 = new Base64ToAny();
    $invoiceStr = $base64->generateSignInvoice($invoiceStr,$pdfFileName);


    $updateSql = "update `InvoiceMaster` set `Status` = 1 where `InvoiceId` = $invoiceId ";
    $output = "";
    if(mysqli_query($conn,$updateSql)){
        $output -> responseCode = "100000";
        $output -> responseDesc = "Successfully update";
    }
    else{
        $output -> responseCode = "0";
        $output -> responseDesc = "Something wrong";
    }
    echo json_encode($output);
}
else if($updateType == "updateEmployee" && $methodType === 'POST'){
    $viewEmpId = $jsonData->viewEmpId;
    $name = $jsonData->name;
    $fatherHusbandName = $jsonData->fatherHusbandName;
    $mobile = $jsonData->mobile;
    $emailId = $jsonData->emailId;
    $dob = $jsonData->dob;
    $doj = $jsonData->doj;
    $leaveBalance = $jsonData->leaveBalance;
    $basic = $jsonData->basic;
    $hra = $jsonData->hra;
    $conveyanceAllowance = $jsonData->conveyanceAllowance;
    $medicalAllowance = $jsonData->medicalAllowance;
    $telephoneAllowance = $jsonData->telephoneAllowance;
    $specialAllowance = $jsonData->specialAllowance;
    $otherAllowance = $jsonData->otherAllowance;

    $sql = "UPDATE `EmployeeMaster` SET `Name`='$name', `FatherHusbandName`='$fatherHusbandName', `Mobile`='$mobile', `EmailId`='$emailId', `DOB`='$dob', `DOJ`='$doj', `LeaveBalance`=$leaveBalance WHERE `EmpId` = '$viewEmpId'";
    if(mysqli_query($conn,$sql)){
        
        $sql1 = "UPDATE `EmployeeEarnings` SET `Basic`=$basic, `HRA`=$hra, `ConveyanceAllowance`=$conveyanceAllowance, `MedicalAllowance`=$telephoneAllowance, `TelephoneAllowance`=$telephoneAllowance,`SpecialAllowance`=$specialAllowance, `OtherAllowance`=$otherAllowance WHERE `EmpId` = '$viewEmpId'";
        if(mysqli_query($conn,$sql1)){
            $output -> responseCode = "100000";
            $output -> responseDesc = "Successfully update";
        }
        else{
            $output -> responseCode = "0";
            $output -> responseDesc = "Something wrong";
        }
    }
    else{
        $output -> responseCode = "0";
        $output -> responseDesc = "Something wrong";
    }
    echo json_encode($output);

}
else if($updateType == "actionOnEmp" && $methodType === 'POST'){
    $id = $jsonData->id;
    $actionType = $jsonData->actionType;
    $action = $actionType == 1 ? 'Active' : "Deactive";

    $sql = "UPDATE `EmployeeMaster` SET `IsActive`=$actionType WHERE `Id` = $id";
    if(mysqli_query($conn,$sql)){
        $output -> responseCode = "100000";
        $output -> responseDesc = "Successfully $action";
    }
    else{
        $output -> responseCode = "0";
        $output -> responseDesc = "Something wrong";
    }
    echo json_encode($output);
}

?>
