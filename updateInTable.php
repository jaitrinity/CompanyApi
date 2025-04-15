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
        $mailStatus = $classObj->sendMailOfferLetter($toMailId, $subject, $msg, null);
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
    $dob = str_replace('/', '-', $dob);
    $dob = date("Y-m-d", strtotime($dob));
    $doj = $jsonData->doj;
    $doj = str_replace('/', '-', $doj);
    $doj = date("Y-m-d", strtotime($doj));
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
else if($updateType == "assetStatus" && $methodType === 'POST'){
    $assetId = $jsonData->assetId;
    $status = $jsonData->status;
    
    $updateDevice = "UPDATE `AssetAllocation` set `Status`=$status, `ReturnDate`=date_format(curDate(),'%d-%m-%Y') where `Id`=$assetId";
    $output = "";
    if(mysqli_query($conn,$updateDevice)){
        $output -> responseCode = "100000";
        $output -> responseDesc = "Successfully update";

    }
    else{
        $output -> responseCode = "0";
        $output -> responseDesc = "Something wrong";
    }
    echo json_encode($output);
}
else if($updateType == "complaintStatus" && $methodType === 'POST'){
    $id = $jsonData->id;
    $closeDescription = $jsonData->closeDescription;
    $status = $jsonData->status;
    
    $sql = "UPDATE `Complaint` set `Status`=$status, `CloseDescription`=?, `CloseDate`=curDate() where `Id`=$id";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $closeDescription);

    $output = "";
    if($stmt->execute()){
        $output -> responseCode = "100000";
        $output -> responseDesc = "Successfully update";

    }
    else{
        $output -> responseCode = "0";
        $output -> responseDesc = "Something wrong";
    }
    echo json_encode($output);
}
else if($updateType == "offerLetter" && $methodType === 'POST'){
    $id = $jsonData->id;
    $name = $jsonData->name;
    $mobile = $jsonData->mobile;
    $emailId = $jsonData->emailId;
    $officeLocation = $jsonData->officeLocation;
    $addLine1 = $jsonData->addLine1;
    $addLine2 = $jsonData->addLine2;
    $designation = $jsonData->designation;
    $doj = $jsonData->doj;
    $doj = str_replace('/', '-', $doj);
    $doj = date("Y-m-d", strtotime($doj));
    $lpa = $jsonData->lpa;
    $earningsY = $jsonData->earningsY;
    $basicY = $jsonData->basicY;
    $hraY = $jsonData->hraY;
    $conveyanceY = $jsonData->conveyanceY;
    $laptopY = $jsonData->laptopY;
    $tdsY = $jsonData->tdsY;
    $netSalaryY = $jsonData->netSalaryY;
    $earningsM = $jsonData->earningsM;
    $basicM = $jsonData->basicM;
    $hraM = $jsonData->hraM;
    $conveyanceM = $jsonData->conveyanceM;
    $laptopM = $jsonData->laptopM;
    $tdsM = $jsonData->tdsM;
    $netSalaryM = $jsonData->netSalaryM;

    $updateOffer = "UPDATE `OfferLetter` set `Name`='$name', `Mobile`='$mobile', `EmailId`='$emailId', `OfficeLocation`='$officeLocation', `AddressLine1`='$addLine1', `AddressLine2`='$addLine2', `Designation`='$designation', `DOJ`='$doj', `LPA`='$lpa', `Earnings_Y`=$earningsY, `Earnings_M`=$earningsM, `Basic_Y`=$basicY, `Basic_M`=$basicM, `HRA_Y`=$hraY, `HRA_M`=$hraM, `Conveyance_Y`=$conveyanceY, `Conveyance_M`=$conveyanceM, `Laptop_Y`=$laptopY, `Laptop_M`=$laptopM, `TDS_Y`=$tdsY, `TDS_M`=$tdsM, `NetSalary_Y`=$netSalaryY, `NetSalary_M`=$netSalaryM WHERE `Id`=$id";

    // echo $updateOffer;

    $output = "";
    if(mysqli_query($conn,$updateOffer)){
        $output -> responseCode = "100000";
        $output -> responseDesc = "Successfully update";   
            
    }
    else{
        $output -> responseCode = "0";
        $output -> responseDesc = "Something wrong";
    }
    echo json_encode($output);
}

?>
