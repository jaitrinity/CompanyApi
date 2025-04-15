<?php
include("dbConfiguration.php");
$methodType = $_SERVER['REQUEST_METHOD'];
if($methodType != "POST"){
	$output = array('code' => 405, 'message' => 'Invalid method Type');
	echo json_encode($output);
	return;
}

$selectType = $_REQUEST["selectType"];
$json = file_get_contents('php://input');
$jsonData=json_decode($json);
$loginEmpId = $jsonData->loginEmpId;
$loginEmpRoleId = $jsonData->loginEmpRoleId;
if($selectType == "headerMenu"){
	$sql = "SELECT `MenuName` as menuName, `RouterLink` as routerLink FROM `HeaderMenu` where find_in_set($loginEmpRoleId, `RoleId`) <> 0 and `IsActive`=1";
	$query = mysqli_query($conn,$sql);
	$menuList = array();
	while($row = mysqli_fetch_assoc($query)){
		array_push($menuList, $row);
	}
	echo json_encode($menuList);
}
else if($selectType == "employee"){

	$month_ini = new DateTime("first day of last month");
	//$month_end = new DateTime("last day of last month");

	// $firstDate = $month_ini->format('Y-M-d');
	// $lastDate = $month_end->format('Y-M-d');

	$lastMonthYear = $month_ini->format('M-Y');

	// $sql = "SELECT e.Id, e.EmpId, e.Name, e.FatherHusbandName, e.Mobile, e.EmailId, e.DOB, e.DOJ, e.AadharNumber, e.AadharAttachment, e.PAN, e.PANAttachment, ee.Basic, ee.HRA, ee.ConveyanceAllowance, ee.MedicalAllowance, ee.TelephoneAllowance, ee.SpecialAllowance, ee.OtherAllowance, ee.GrossSalary, ed.PaidDays, ed.MonthYear, ed.RetentionBonus, ed.ProfessionTax, ed.LossOfPay, ed.OtherDeductions, ed.IncomeTax, ed.OtherTax, r.RoleName FROM EmployeeMaster e join EmployeeEarnings ee on e.EmpId = ee.EmpId left join EmployeeDeductions ed on e.EmpId = ed.EmpId left join RoleMaster r on e.RoleId = r.RoleId ";
	// $sql = "SELECT e.Id, e.EmpId, e.Name, e.FatherHusbandName, e.Mobile, e.EmailId, e.DOB, e.DOJ, e.AadharNumber, e.AadharAttachment, e.PAN, e.PANAttachment, ee.Basic, ee.HRA, ee.ConveyanceAllowance, ee.MedicalAllowance, ee.TelephoneAllowance, ee.SpecialAllowance, ee.OtherAllowance, ee.GrossSalary, r.RoleName FROM EmployeeMaster e join EmployeeEarnings ee on e.EmpId = ee.EmpId left join RoleMaster r on e.RoleId = r.RoleId ";

	// $sql = "SELECT e.Id, e.EmpId, e.Name, e.FatherHusbandName, e.Mobile, e.EmailId, e.DOB, e.DOJ, e.LeaveBalance, e.AadharNumber, e.AadharAttachment, e.PAN, e.PANAttachment, ee.Basic, ee.HRA, ee.ConveyanceAllowance, ee.MedicalAllowance, ee.TelephoneAllowance, ee.SpecialAllowance, ee.OtherAllowance, ee.GrossSalary, r.RoleName, (case when ed.MonthYear is null then 'Pending' else 'Done' end) as SalaryStatus, e.IsActive FROM EmployeeMaster e join EmployeeEarnings ee on e.EmpId = ee.EmpId left join RoleMaster r on e.RoleId = r.RoleId left join EmployeeDeductions ed on e.EmpId = ed.EmpId and ed.MonthYear = '$lastMonthYear' ";

	$sql = "SELECT e.Id, e.EmpId, e.Name, e.FatherHusbandName, e.Mobile, e.EmailId, e.DOB, e.DOJ, e.LeaveBalance, e.AadharNumber, e.AadharAttachment, e.PAN, e.PANAttachment, e.RMId, e1.Name as RmName, ee.Basic, ee.HRA, ee.ConveyanceAllowance, ee.MedicalAllowance, ee.TelephoneAllowance, ee.SpecialAllowance, ee.OtherAllowance, ee.GrossSalary, r.RoleName, (case when ed.MonthYear is null then 'Pending'else 'Done'end)as SalaryStatus, e.IsActive FROM EmployeeMaster e join EmployeeEarnings ee on e.EmpId=ee.EmpId left join EmployeeMaster e1 on e.RMId=e1.EmpId left join RoleMaster r on e.RoleId=r.RoleId left join EmployeeDeductions ed on e.EmpId=ed.EmpId and ed.MonthYear='$lastMonthYear' order by e.Id desc";
	$query = mysqli_query($conn,$sql);

	$empArr = array();
	while($row = mysqli_fetch_assoc($query)){
		$id = $row["Id"];
		$empId = $row["EmpId"];
		$empName = $row["Name"];
		$fatherHusbandName = $row["FatherHusbandName"];
		$mobile = $row["Mobile"];
		$emailId = $row["EmailId"];
		$dob = $row["DOB"];
		$doj = $row["DOJ"];
		$leaveBalance = $row["LeaveBalance"];
		$aadharNumber = $row["AadharNumber"];
		$aadharAttachment = $row["AadharAttachment"];
		$pan = $row["PAN"];
		$panAttachment = $row["PANAttachment"];
		$basic = $row["Basic"];
		$hra = $row["HRA"];
		$conveyanceAllowance = $row["ConveyanceAllowance"];
		$medicalAllowance = $row["MedicalAllowance"];
		$telephoneAllowance = $row["TelephoneAllowance"];
		$specialAllowance = $row["SpecialAllowance"];
		$otherAllowance = $row["OtherAllowance"];
		$grossSalary = $row["GrossSalary"];
		$roleName = $row["RoleName"];
		$isActive = $row["IsActive"];

		$json = array(
			'id' => $id,
			'empId' => $empId,
			'empName' => $empName,
			'fatherHusbandName' => $fatherHusbandName,
			'mobile' => $mobile,
			'emailId' => $emailId,
			'dob' => $dob,
			'doj' => $doj,
			'leaveBalance' => $leaveBalance,
			'aadharNumber' => $aadharNumber,
			'aadharAttachment' => $aadharAttachment,
			'pan' => $pan,
			'panAttachment' => $panAttachment,
			'rmId' => $row["RMId"],
			'rmName' => $row["RmName"],
			'basic' => $basic,
			'hra' => $hra,
			'conveyanceAllowance' => $conveyanceAllowance,
			'medicalAllowance' => $medicalAllowance,
			'telephoneAllowance' => $telephoneAllowance,
			'specialAllowance' => $specialAllowance,
			'otherAllowance' => $otherAllowance,
			'grossSalary' => $grossSalary,
			// 'lastMonthYear' => $lastMonthYear,
			'salaryStatus' => $row["SalaryStatus"],
			// 'monthYear' => $row["MonthYear"],
			// 'paidDays' => $row["PaidDays"],
			// 'retentionBonus' => $row["RetentionBonus"],
			// 'professionTax' => $row["ProfessionTax"],
			// 'lossOfPay' => $row["LossOfPay"],
			// 'otherDeductions' => $row["OtherDeductions"],
			// 'incomeTax' => $row["IncomeTax"],
			// 'otherTax' => $row["OtherTax"],
			'roleName' => $roleName,
			'isActive' => $isActive,
			'activeStatus' => $isActive == 1 ? 'Active' : 'Deactive'
		);
		array_push($empArr,$json);
	}
	$output = array();
	$output = array('employeeList' => $empArr, 'lastMonthYear' => $lastMonthYear);
	echo json_encode($output);
}
else if($selectType == "deduction"){
	$empId = $jsonData->empId;
	$month = $jsonData->month;
	$sql = "SELECT * from EmployeeDeductions ed where ed.EmpId = '$empId' and ed.MonthYear = '$month'";
	$query=mysqli_query($conn,$sql);
	$deductionList = array();
	while($row = mysqli_fetch_assoc($query)){
		$json = array(
			'paidDays' => $row["PaidDays"],
			'retentionBonus' => $row["RetentionBonus"],
			'professionTax' => $row["ProfessionTax"],
			'lossOfPay' => $row["LossOfPay"],
			'otherDeductions' => $row["OtherDeductions"],
			'incomeTax' => $row["IncomeTax"],
			'otherTax' => $row["OtherTax"],
			'reimbursements' => $row["Reimbursements"],
			'salarySlipName' => $row["SalarySlipName"]
		);
		array_push($deductionList,$json);
	}
	
	$output = array();
	if(count($deductionList) == 0){
		$output = array('responseCode' => "102001", 'deductionList' => $deductionList);
	}
	else{
		$output = array('responseCode' => "100000", 'deductionList' => $deductionList);
	}
	echo json_encode($output);
}
else if($selectType == "offerLetter"){
	$sql = "SELECT * FROM `OfferLetter` order by `Id` desc ";
	$query=mysqli_query($conn,$sql);

	$listArr = array();
	while($row = mysqli_fetch_assoc($query)){
		$id = $row["Id"];
		$name = $row["Name"];
		$mobile = $row["Mobile"];
		$emailId = $row["EmailId"];
		$addLine1 = $row["AddressLine1"];
		$addLine2 = $row["AddressLine2"];
		$address = $addLine1.', '.$addLine2;
		$designation = $row["Designation"];
		$doj = $row["DOJ"];
		$lpaOrg = $row["LPA"];
		$lpa = moneyFormatIndia($lpaOrg);
		$status = $row["Status"];
		if($status == 0) $status = "Pending";
		else if($status == 1) $status = "Approved";
		else if($status == 2) $status = "Rejected";
		else if($status == 3) $status = "Expired";
		$json = array(
			'id' => $id,
			'name' => $name,
			'mobile' => $mobile,
			'emailId' => $emailId,
			'officeLocation' => $row["OfficeLocation"],
			'addLine1' => $addLine1,
			'addLine2' => $addLine2,
			'address' => $address,
			'designation' => $designation,
			'doj' => $doj,
			'lpaOrg' => $lpaOrg,
			'lpa' => $lpa,
			'status' => $status,
			'offerDate' => $row["OfferDate"],
			'earningsY' => $row["Earnings_Y"],
			'earningsM' => $row["Earnings_M"],
			'basicY' => $row["Basic_Y"],
			'basicM' => $row["Basic_M"],
			'hraY' => $row["HRA_Y"],
			'hraM' => $row["HRA_M"],
			'conveyanceY' => $row["Conveyance_Y"],
			'conveyanceM' => $row["Conveyance_M"],
			'laptopY' => $row["Laptop_Y"],
			'laptopM' => $row["Laptop_M"],
			'tdsY' => $row["TDS_Y"],
			'tdsM' => $row["TDS_M"],
			'netSalaryY' => $row["NetSalary_Y"],
			'netSalaryM' => $row["NetSalary_M"]
		);
		array_push($listArr,$json);
	}
	$output = array();
	$output = array('offerLetterList' => $listArr);
	echo json_encode($output);
}
else if($selectType == "leaves"){
	$sql = "SELECT l.Id, l.EmpId, e.Name, l.FromDate, l.ToDate, l.HalfDay, l.Reason, l.Reason, l.Status, l.ActivityId, (case when l.Status = 0 then 'Pending' when l.Status = 1 then 'Approved' when l.Status = 2 then 'Rejected' end) as LeaveStatus, e.LeaveBalance FROM LeaveMaster l join EmployeeMaster e on l.EmpId = e.EmpId where 1=1 ";
	if($loginEmpRoleId != 1){
		$sql .= " and l.EmpId = '$loginEmpId' ";
	}
	$sql .= "order by l.CreateDate desc";
	$query=mysqli_query($conn,$sql);
	$leaveArr = array();
	while($row = mysqli_fetch_assoc($query)){
		$id = $row["Id"];
		$name = $row["Name"];
		$fromDate = $row["FromDate"];
		$toDate = $row["ToDate"];
		$halfDay = $row["HalfDay"];
		$reason = $row["Reason"];
		$status = $row["Status"];
		$leaveStatus = $row["LeaveStatus"];

		$json = array(
			'id' => $id,
			'empId' => $row["EmpId"],
			'name' => $name,
			'fromDate' => $fromDate,
			'toDate' => $toDate,
			'halfDay' => $halfDay,
			'reason' => $reason,
			'status' => $status,
			'leaveStatus' => $leaveStatus,
			'activityId' => $row["ActivityId"]
		);
		array_push($leaveArr,$json);
	}
	
	$leaveBalance="";
	if($loginEmpRoleId != 1){
		$sql = "SELECT `LeaveBalance` from `EmployeeMaster` where `EmpId`='$loginEmpId'";
		$query=mysqli_query($conn,$sql);
		$row = mysqli_fetch_assoc($query);
		$leaveBalance = $row["LeaveBalance"];
	}

	$output = array('leaveList' => $leaveArr, 'leaveBalance' => $leaveBalance);
	echo json_encode($output);
}
else if($selectType == "leaves_old"){
	$sql = "SELECT l.Id, l.EmpId, e.Name, l.FromDate, l.ToDate, l.Reason, l.Reason, l.Status, l.ActivityId, (case when l.Status = 0 then 'Pending' when l.Status = 1 then 'Approved' when l.Status = 2 then 'Rejected' end) as LeaveStatus, e.LeaveBalance FROM LeaveMaster l join EmployeeMaster e on l.EmpId = e.EmpId where 1=1 ";
	if($loginEmpRoleId != 1){
		$sql .= " and l.EmpId = '$loginEmpId' ";
	}
	$sql .= "order by l.CreateDate desc";
	$query=mysqli_query($conn,$sql);
	$leaveBalance="";
	$leaveArr = array();
	while($row = mysqli_fetch_assoc($query)){
		$id = $row["Id"];
		$name = $row["Name"];
		$fromDate = $row["FromDate"];
		$toDate = $row["ToDate"];
		$reason = $row["Reason"];
		$status = $row["Status"];
		$leaveStatus = $row["LeaveStatus"];
		if($loginEmpRoleId != 1){
			$leaveBalance = $row["LeaveBalance"];
		}

		$json = array(
			'id' => $id,
			'empId' => $row["EmpId"],
			'name' => $name,
			'fromDate' => $fromDate,
			'toDate' => $toDate,
			'reason' => $reason,
			'status' => $status,
			'leaveStatus' => $leaveStatus,
			'activityId' => $row["ActivityId"]
		);
		array_push($leaveArr,$json);
	}
	$output = array();
	$output = array('leaveList' => $leaveArr, 'leaveBalance' => $leaveBalance);
	echo json_encode($output);
}
else if($selectType == "leaveEmp"){
	$sql = "SELECT `EmpId`, `Name` FROM `EmployeeMaster` where `RoleId` != 1 and `IsActive` = 1";
	$query = mysqli_query($conn,$sql);
	$empList = array();
	while($row = mysqli_fetch_assoc($query)){
		$json = array('empId' => $row["EmpId"], 'name' => $row["Name"] );
		array_push($empList,$json);
	}
	$output = array();
	$output = array('empList' => $empList);
	echo json_encode($output);
}
else if($selectType == "interviewee"){
	$sql = "SELECT * FROM `Interviewee` order by `Id` desc";
	$query = mysqli_query($conn,$sql);
	$dataList = array();
	while($row = mysqli_fetch_assoc($query)){
		$status = $row["Status"];
		$statusType = "Pending";
		if($status == 1) $statusType = 'Selected';
		else if($status == 2) $statusType = 'Rejected';
		$json = array(
			'id' => $row["Id"],
			'name' => $row["Name"], 
			'mobile' => $row["Mobile"],
			'emailId' => $row["EmailId"],
			'companyName' => $row["CompanyName"],
			'ctc' => $row["CTC"],
			'noticePeriod' => $row["NoticePeriod"],
			'cvURL' => $row["CV_Attachment"] == null ? "" : $row["CV_Attachment"],
			'status' => $status,
			'statusType' => $statusType
		);
		array_push($dataList,$json);
	}
	$output = array();
	$output = array('intervieweeList' => $dataList);
	echo json_encode($output);
}
else if($selectType == "selectedInterviewee"){
	$sql = "SELECT * FROM `Interviewee` where `Status` = 1 and `IsOfferGenerate` = 0";
	$query = mysqli_query($conn,$sql);
	$dataList = array();
	while($row = mysqli_fetch_assoc($query)){
		$json = array(
			'id' => $row["Id"],
			'name' => $row["Name"], 
			'mobile' => $row["Mobile"],
			'emailId' => $row["EmailId"]
		);
		array_push($dataList,$json);
	}
	$output = array();
	$output = array('selectedIntervieweeList' => $dataList);
	echo json_encode($output);
}
else if($selectType == "offerApproved"){
	$sql = "SELECT * FROM `OfferLetter` where `Status` = 1 and `IsRegisteredEmp` = 0";
	$query = mysqli_query($conn,$sql);
	$dataList = array();
	while($row = mysqli_fetch_assoc($query)){
		$json = array(
			'id' => $row["Id"],
			'name' => $row["Name"], 
			'mobile' => $row["Mobile"],
			'emailId' => $row["EmailId"],
			'designation' => $row["Designation"],
			'doj' => $row["DOJ"]
		);
		array_push($dataList,$json);
	}
	$output = array();
	$output = array('offerApprovedList' => $dataList);
	echo json_encode($output);
}
else if($selectType == "allRmEmp"){
	$rmEmpIdList = "Admin995,tr051,tr052";
	$rmEmpIdList = str_replace(",", "','", $rmEmpIdList);
	$sql = "SELECT `EmpId`, `Name` FROM `EmployeeMaster` where `EmpId` in ('$rmEmpIdList') and `IsActive`=1";
	$query = mysqli_query($conn,$sql);
	$dataList = array();
	while($row = mysqli_fetch_assoc($query)){
		$json = array(
			'empId' => $row["EmpId"],
			'name' => $row["Name"]
		);
		array_push($dataList,$json);
	}
	$output = array('rmEmpList' => $dataList);
	echo json_encode($output);
}
else if($selectType == "corporateAndClient"){
	$sql = "SELECT `CorporateId`, `Name`, `State` FROM `CorporateMaster`";
	$query = mysqli_query($conn,$sql);
	$corporateNameList = array();
	while($row = mysqli_fetch_assoc($query)){
		$json = array(
			'id' => $row["CorporateId"],
			'name' => $row["Name"],
			'state' => $row["State"]
		);
		array_push($corporateNameList,$json);
	}
	// 
	$sql = "SELECT `ClientId`, `CorporateId`, `Name`, `State` FROM `ClientMaster`";
	$query = mysqli_query($conn,$sql);
	$clientNameList = array();
	while($row = mysqli_fetch_assoc($query)){
		$json = array(
			'id' => $row["ClientId"],
			'corporateId' => $row["CorporateId"],
			'name' => $row["Name"],
			'state' => $row["State"]
		);
		array_push($clientNameList,$json);
	}
	$output = array();
	$output = array('corporateNameList' => $corporateNameList, 'clientNameList' => $clientNameList);
	echo json_encode($output);
}
else if($selectType == "invoices"){
	$sql = "SELECT im.InvoiceId, cm.ContactPerson, cm.EmailId, cm.Mobile, im.PoNumber, cm.Name, cm.Address, cm.GSTNo, cm.PANNo, clm.Name as ClientName, clm.ContactPerson as ClientContact, clm.Address as ClientAddress, clm.GSTNo as ClientGST, cm.BankName, cm.AccountNo, cm.IfscNo, im.SubTotal, im.CGST, im.AfterCGST, im.SGST, im.AfterSGST, im.IGST, im.AfterIGST, im.GrantTotal, im.Status, date_format(im.CreateDate,'%d-%b-%Y') as PoDate FROM InvoiceMaster im join CorporateMaster cm on im.CorporateId = cm.CorporateId join ClientMaster clm on im.ClientId = clm.ClientId";
	$query = mysqli_query($conn,$sql);
	$dataList = array();
	while($row = mysqli_fetch_assoc($query)){
		$status = $row["Status"];
		$statusView = $status == 1 ? 'Signed' : 'Unsign'; 
		$json = array('invoiceId' => $row["InvoiceId"], 'corporateName' => $row["Name"], 'clientName' => $row["ClientName"], 'status' => $status, 'statusView' =>$statusView, 'poNumber' => $row["PoNumber"], 'poDate' => $row["PoDate"]);
		array_push($dataList, $json);
	}

	$output = array();
	$output = array('invoiceList' => $dataList);
	echo json_encode($output);
}
else if($selectType == "client"){
	$sql = "SELECT cm.ClientId, com.Name as CorporateName, cm.Name as ClientName, cm.ContactPerson, cm.Address, cm.State, cm.GSTNo FROM ClientMaster cm join CorporateMaster com on cm.CorporateId = com.CorporateId";
	$query = mysqli_query($conn,$sql);
	$dataList = array();
	while($row = mysqli_fetch_assoc($query)){
		$address = str_replace('---', ' ', $row["Address"]);
		$json = array('clientId' => $row["ClientId"], 'corporateName' => $row["CorporateName"], 'clientName' => $row["ClientName"], 'contactPerson' => $row["ContactPerson"], 'address' => $address, 'state' => $row["State"], 'gstNo' => $row["GSTNo"]);
		array_push($dataList, $json);
	}

	$output = array();
	$output = array('clientNameList' => $dataList);
	echo json_encode($output);

}
else if($selectType == "POData"){
	$sql = "SELECT po.Id, cm.Name, date_format(po.PO_Date,'%d-%b-%Y') as PO_Date, po.PO_Attachment FROM PO_Master po join ClientMaster cm on po.ClientId = cm.ClientId";
	$query = mysqli_query($conn,$sql);
	$dataList = array();
	while($row = mysqli_fetch_assoc($query)){
		$json = array('poId' => $row["Id"], 'clientName' => $row["Name"], 'poDate' => $row["PO_Date"], 'poAttachment' => $row["PO_Attachment"]);
		array_push($dataList, $json);
	}

	$output = array();
	$output = array('poList' => $dataList);
	echo json_encode($output);
}
else if($selectType == "validateMobile"){
	$mobile = $jsonData->mobile;
	$sql = "SELECT `EmpId` FROM `EmployeeMaster` where `Mobile` = '$mobile' and `IsActive` = 1";
	$query = mysqli_query($conn,$sql);
	$rowCount = mysqli_num_rows($query);
	$output = "";
	if($rowCount != 0){
		$output -> responseCode = "100000";
		$output -> responseDesc = "Successfully inserted";
	}
	else{
		$output -> responseCode = "102001";
		$output -> responseDesc = "Please enter registered mobile number";
	}
	echo json_encode($output);
}
else if($selectType == "appMenu"){
	$sql = "SELECT * FROM `AppMenu`";
	$query=mysqli_query($conn,$sql);
	$appMenuList = array();
	while($row = mysqli_fetch_assoc($query)){
		$data = array('menuId' => $row["Menu_Id"], 'menuName' => $row["Menu_Name"], 'url' => $row["URL"]);
		array_push($appMenuList, $data);
	}
	$output = array('appMenu' => $appMenuList);
	echo json_encode($output);
}
else if($selectType == "mapView"){
	$filterDate = $jsonData->filterDate;
	$filterDate = str_replace('/', '-', $filterDate);
	$filterDate = date("Y-m-d", strtotime($filterDate));
	$filterEmpId = $jsonData->filterEmpId;

	$sql = "SELECT * FROM `DistanceTravel` where `Emp_Id`='$filterEmpId' and `Visit_Date`='$filterDate' order by `Activity_Id` ";
	$query = mysqli_query($conn,$sql);
	$cnt = 0;
	$mapViewList = array();
	$rowCount = mysqli_num_rows($query);
	$scaledSize = array('width' => 40, 'height' => 50);
	$color = "#0000FF";
	$polylineOptions = array('strokeColor' => $color);
	$renderOptions = array('suppressMarkers' => true, 'polylineOptions' => $polylineOptions);
	$distanceTravel=0;
	$totalVisit=0;
	$idleTime = 0;
	while($row = mysqli_fetch_assoc($query)){
		$distance = $row["Distance_KM"];
		$event = $row["Event"];
		if($event == "periodicData" && $distance == 0){
			continue;
		}
		if($event == "Submit"){
			$totalVisit++;
			// Yellow
			$locIcon = "https://img.icons8.com/ios-glyphs/50/FAB005/marker--v1.png";
		}
		else if($event == 'Start'){
			// Greeen
			$locIcon = "https://img.icons8.com/ios-glyphs/50/40C057/marker--v1.png";
			// $locIcon = "https://img.icons8.com/ios-filled/100/40C057/map-pin.png";
		}
		else if($event == 'Stop'){
			// Red
			$locIcon = "https://img.icons8.com/ios-glyphs/50/FA5252/marker--v1.png";
		}
		else if($event == 'periodicData'){
			// Blue
			$locIcon = "https://img.icons8.com/ios-glyphs/50/228BE6/marker--v1.png";
		}

		$geoLocation = $row["Latitude_Start"].','.$row["Longitude_Start"];
		$mobileDateTime = $row["Visit_Date_Time"];
		$address = $row["Address"];
		$origin_lat = floatval($row["Latitude_Start"]) ;
		$origin_long = floatval($row["Longitude_Start"]);
		$orginJson = array('lat' => $origin_lat, 'lng' => $origin_long);

		$origin_infoWindow = "";
		if($event == "Submit")
			$origin_infoWindow .= "<h3><div>$event - $totalVisit</div>";
		else
			$origin_infoWindow .= "<h3><div>$event</div>";

		$origin_infoWindow .= "<div>Datetime : $mobileDateTime</div>";
		if($address == '')
			$origin_infoWindow .= "<div>Lat-long : $geoLocation</div></h3>";
		else
			$origin_infoWindow .= "<div>Address : $address</div></h3>";
		$origin_icon = array('url' => $locIcon, 'scaledSize' => $scaledSize);
		$origin_labelInfo = array('text' => ' ','fontWeight' => 'bold', 'color'=>'black');
		$originMarker = array( 
			'icon' => $origin_icon,
			'opacity' => 1,
			'infoWindow' => $origin_infoWindow,
			'label'=> $origin_labelInfo
		);

		$dest_lat = floatval($row["Latitude_End"]) ;
		$dest_long = floatval($row["Longitude_End"]);
		$destJson = array('lat' => $dest_lat, 'lng' => $dest_long);
		$dest_InfoWindow = "";
		if($event == "Submit")
			$dest_InfoWindow .= "<h3><div>$event - $totalVisit</div>";
		else
			$dest_InfoWindow .= "<h3><div>$event</div>";
		$dest_InfoWindow .= "<div>Datetime : $mobileDateTime</div>";
		
		if($address == '')
			$dest_InfoWindow .= "<div>Lat-long : $geoLocation</div></h3>";
		else
			$dest_InfoWindow .= "<div>Address : $address</div></h3>";
		$dest_icon = array('url' => $locIcon, 'scaledSize' => $scaledSize);
		$dest_labelInfo = array('text' => ' ','fontWeight' => 'bold', 'color'=>'black');

		$destinationMarker = array(
			'icon' => $dest_icon,
			'opacity' => 1,
			'infoWindow' => $dest_InfoWindow,
			'label'=> $dest_labelInfo
		);
		$markerOptions = array('origin' => $originMarker, 'destination' => $destinationMarker);

		$mapViewJson = array( 
			'origin' => $orginJson, 
			'destination' => $destJson, 
			'renderOptions' => $renderOptions, 
			'markerOptions' => $markerOptions
		);
		array_push($mapViewList, $mapViewJson);

		$distanceTravel += $distance;
	}
	$output = array(
		'totalVisit' => $totalVisit, 
		'distanceTravel' => round($distanceTravel,2), 
		'mapViewList' => $mapViewList
	);
	echo json_encode($output);
}
else if($selectType == "mapView_1"){
	$filterDate = $jsonData->filterDate;
	$filterDate = str_replace('/', '-', $filterDate);
	$filterDate = date("Y-m-d", strtotime($filterDate));
	$filterEmpId = $jsonData->filterEmpId;

	$sql = "SELECT * FROM `DistanceTravel` where `Emp_Id`='$filterEmpId' and `Visit_Date`='$filterDate' ";
	$query = mysqli_query($conn,$sql);
	$cnt = 0;
	$mapViewList = array();
	$rowCount = mysqli_num_rows($query);
	$scaledSize = array('width' => 40, 'height' => 50);
	$color = "#0000FF";
	$polylineOptions = array('strokeColor' => $color);
	$renderOptions = array('suppressMarkers' => true, 'polylineOptions' => $polylineOptions);
	$distanceTravel=0;
	$totalVisit=0;
	$idleTime = 0;
	while($row = mysqli_fetch_assoc($query)){
		$distance = $row["Distance_KM"];
		$event = $row["Event"];
		if($event == "periodicData" && $distance == 0){
			continue;
		}
		if($event == "Submit"){
			$totalVisit++;
			// Yellow
			$locIcon = "https://img.icons8.com/ios-glyphs/50/FAB005/marker--v1.png";
		}
		else if($event == 'Start'){
			// Greeen
			$locIcon = "https://img.icons8.com/ios-glyphs/50/40C057/marker--v1.png";
			// $locIcon = "https://img.icons8.com/ios-filled/100/40C057/map-pin.png";
		}
		else if($event == 'Stop'){
			// Red
			$locIcon = "https://img.icons8.com/ios-glyphs/50/FA5252/marker--v1.png";
		}
		else if($event == 'periodicData'){
			// Blue
			$locIcon = "https://img.icons8.com/ios-glyphs/50/228BE6/marker--v1.png";
		}
		$geoLocation = $row["Latitude_Start"].','.$row["Longitude_Start"];
		$mobileDateTime = $row["Visit_Date_Time"];
		$latitude = $row["Latitude_Start"] ;
		$longitude = $row["Longitude_Start"];
		$address = $row["Address"];
		$latitude = floatval($latitude);
		$longitude = floatval($longitude);
		$cnt = $cnt+1;
		if($cnt == 1){
			$origin_lat = $latitude;
			$origin_long = $longitude;
			$orginJson = array('lat' => $origin_lat, 'lng' => $origin_long);
			$infoWindow = "<h3><div>$event</div>";
			$infoWindow .= "<div>Datetime : $mobileDateTime</div>";
			if($address == '')
				$infoWindow .= "<div>Lat-long : $geoLocation</div></h3>";
			else
				$infoWindow .= "<div>Address : $address</div></h3>";
			// $icon = array('url' => './assets/img/start.png', 'scaledSize' => $scaledSize);
			$icon = array('url' => $locIcon, 'scaledSize' => $scaledSize);
			$labelInfo = array('text' => ' ','fontWeight' => 'bold', 'color'=>'black');
			$originMarker = array( 
				'icon' => $icon,
				'opacity' => 1,
				'infoWindow' => $infoWindow,
				'label'=> $labelInfo
			);
		}
		else{
			if($cnt != 2){
				$orginJson = array('lat' => $origin_lat, 'lng' => $origin_long);
				// $icon = array('url' => './assets/img/all.png', 'scaledSize' => $scaledSize);
				$icon = array('url' => $locIcon, 'scaledSize' => $scaledSize);
				// $labelInfo = array('text' => ''.$totalVisit,'fontWeight' => 'bold', 'color'=>'white');
				$originMarker = array( 
					'icon' => $icon,
					'opacity' => 1,
					'infoWindow' => $infoWindow,
					// 'label'=> $labelInfo
				);
			}

			$dest_lat=$latitude;
			$dest_long=$longitude;
			$destJson = array('lat' => $dest_lat, 'lng' => $dest_long);

			// if($cnt == $rowCount){
			// 	$icon = array('url' => './assets/img/end.png', 'scaledSize' => $scaledSize);
			// }
			// else{
			// 	$icon = array('url' => './assets/img/all.png', 'scaledSize' => $scaledSize);
			// }
			
			// $icon = array('url' => './assets/img/end.png', 'scaledSize' => $scaledSize);
			$icon = array('url' => $locIcon, 'scaledSize' => $scaledSize);
			$infoWindow = "";
			if($event == "Stop"){
				$infoWindow = "<h3><div>$event</div>";
				$labelInfo = array('text' => ' ','fontWeight' => 'bold', 'color'=>'black');
			}
			else if($event == "periodicData"){
				$infoWindow = "<h3><div>$event</div>";
				$labelInfo = array('text' => ' ','fontWeight' => 'bold', 'color'=>'black');
			}
			else{
				$infoWindow = "<h3><div>$event - $totalVisit</div>";
				$labelInfo = array('text' => ''.$totalVisit,'fontWeight' => 'bold', 'color'=>'black');
			}
			$infoWindow .= "<div>Datetime : $mobileDateTime</div>";
			if($address == '')
				$infoWindow .= "<div>Lat-long : $geoLocation</div></h3>";
			else
				$infoWindow .= "<div>Address : $address</div></h3>";
			$destinationMarker = array(
				'icon' => $icon,
				'opacity' => 1,
				'infoWindow' => $infoWindow,
				'label'=> $labelInfo
			);
			$markerOptions = array('origin' => $originMarker, 'destination' => $destinationMarker);

			$mapViewJson = array( 
				'origin' => $orginJson, 
				'destination' => $destJson, 
				'renderOptions' => $renderOptions, 
				'markerOptions' => $markerOptions
			);
			array_push($mapViewList, $mapViewJson);
			
			
			$distanceTravel += $distance;


			$origin_lat = $latitude;
			$origin_long = $longitude;
		}
	}
	$output = array(
		'totalVisit' => $totalVisit, 
		'distanceTravel' => round($distanceTravel,2), 
		'mapViewList' => $mapViewList
	);
	echo json_encode($output);
}
else if($selectType == "mapView_0"){
	$filterDate = $jsonData->filterDate;
	$filterEmpId = $jsonData->filterEmpId;

	$startSql = "(SELECT * FROM `Activity` where `EmpId` = '$filterEmpId' and `Event` in ('Start') and date_format(`MobileDateTime`, '%Y-%m-%d') = '$filterDate' ORDER by `MobileDateTime` ASC LIMIT 0,1)";

	// $periodicDataSql = "(SELECT * FROM `Activity` where `EmpId` = '$filterEmpId' and `Event` in ('periodicData') and date_format(`MobileDateTime`, '%Y-%m-%d') = '$filterDate' ORDER by `MobileDateTime` ASC)";

	$periodicDataSql = "(SELECT * FROM `Activity` where `EmpId` = '$filterEmpId' and `Event` in ('periodicData','Submit') and date_format(`MobileDateTime`, '%Y-%m-%d') = '$filterDate' ORDER by `MobileDateTime` ASC)";

	$stopSql = "(SELECT * FROM `Activity` where `EmpId` = '$filterEmpId' and `Event` in ('Stop') and date_format(`MobileDateTime`, '%Y-%m-%d') = '$filterDate' ORDER by `MobileDateTime` DESC LIMIT 0,1)";

	$sql = $startSql." UNION ALL ".$periodicDataSql." UNION ALL ".$stopSql;

	// echo $sql;
	$query = mysqli_query($conn,$sql);
	$cnt = 0;
	$mapViewList = array();
	$rowCount = mysqli_num_rows($query);
	$scaledSize = array('width' => 40, 'height' => 50);
	$color = "#0000FF";
	$polylineOptions = array('strokeColor' => $color);
	$renderOptions = array('suppressMarkers' => true, 'polylineOptions' => $polylineOptions);
	$distanceTravel=0;
	$totalVisit=0;
	$idleTime = 0;
	while($row = mysqli_fetch_assoc($query)){
		$event = $row["Event"];
		if($event == "Submit"){
			$totalVisit++;
		}
		$geoLocation = $row["GeoLocation"];
		$geoLocation = str_replace("/", ",", $geoLocation);
		$mobileDateTime = $row["MobileDateTime"];
		$distance = $row["Distance"];
		$distance = $distance == '' ? 0 : floatval($distance);
		$timeDiff = $row["TimeDiff"];
		$timeDiff = $timeDiff == '' ? 0 : intval($timeDiff);
		$latitude = explode(",", $geoLocation)[0] ;
		$longitude = explode(",", $geoLocation)[1];
		$latitude = floatval($latitude);
		$longitude = floatval($longitude);
		$cnt = $cnt+1;
		if($cnt == 1){
			$origin_lat = $latitude;
			$origin_long = $longitude;
			$orginJson = array('lat' => $origin_lat, 'lng' => $origin_long);
			$infoWindow = "<h3><div>$event</div>";
			$infoWindow .= "<div>Datetime : $mobileDateTime</div>";
			$infoWindow .= "<div>Lat-long : $geoLocation</div></h3>";
			$icon = array('url' => './assets/img/start.png', 'scaledSize' => $scaledSize);
			$labelInfo = array('text' => ' ','fontWeight' => 'bold', 'color'=>'white');
			$originMarker = array( 
				'icon' => $icon,
				'opacity' => 1,
				'infoWindow' => $infoWindow,
				'label'=> $labelInfo
			);
		}
		else{
			if($distance < 5){
				$idleTime += $timeDiff;
			}
			else{
				$idleTime = 0;
			}
			if($cnt != 2){
				$orginJson = array('lat' => $origin_lat, 'lng' => $origin_long);
				$icon = array('url' => './assets/img/all.png', 'scaledSize' => $scaledSize);
				// $labelInfo = array('text' => ''.$totalVisit,'fontWeight' => 'bold', 'color'=>'white');
				$originMarker = array( 
					// 'icon' => $icon,
					'opacity' => 1,
					'infoWindow' => $infoWindow,
					// 'label'=> $labelInfo
				);
			}

			$dest_lat=$latitude;
			$dest_long=$longitude;
			$destJson = array('lat' => $dest_lat, 'lng' => $dest_long);

			if($cnt == $rowCount){
				$icon = array('url' => './assets/img/end.png', 'scaledSize' => $scaledSize);
			}
			else{
				$icon = array('url' => './assets/img/all.png', 'scaledSize' => $scaledSize);
			}
			

			$infoWindow = "";
			if($event == "Stop"){
				$infoWindow = "<h3><div>$event</div>";
				$labelInfo = array('text' => ' ','fontWeight' => 'bold', 'color'=>'white');
			}
			else if($event == "periodicData"){
				$infoWindow = "<h3><div>$event</div>";
				$labelInfo = array('text' => ' ','fontWeight' => 'bold', 'color'=>'white');
			}
			else{
				$infoWindow = "<h3><div>$event - $totalVisit</div>";
				$labelInfo = array('text' => ''.$totalVisit,'fontWeight' => 'bold', 'color'=>'white');
			}
			$infoWindow .= "<div>Datetime : $mobileDateTime</div>";
			$infoWindow .= "<div>Lat-long : $geoLocation</div></h3>";
			$destinationMarker = array(
				// 'icon' => $icon,
				'opacity' => 1,
				'infoWindow' => $infoWindow,
				'label'=> $labelInfo
			);
			$markerOptions = array('origin' => $originMarker, 'destination' => $destinationMarker);

			$mapViewJson = array( 
				'origin' => $orginJson, 
				'destination' => $destJson, 
				'renderOptions' => $renderOptions, 
				'markerOptions' => $markerOptions
			);
			array_push($mapViewList, $mapViewJson);
			
			$api_key = "AIzaSyDkCjzv4fVu7wlsp31Tu0AnpbyQaxm4Kz8";
			$origin = $origin_lat.",".$origin_long;
			$destinations = $dest_lat.','.$dest_long;
			$url='https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins='.$origin.'&destinations='.$destinations.'&key='.$api_key;

			$json_data=file_get_contents($url);	
			$distance=fnlGetDistance($json_data);
			$distanceTravel += $distance;


			$origin_lat = $latitude;
			$origin_long = $longitude;
		}
	}
	$output = array(
		'totalVisit' => $totalVisit, 
		'distanceTravel' => round($distanceTravel,2), 
		'mapViewList' => $mapViewList
	);
	echo json_encode($output);
}
else if($selectType == "empMapView"){
	// $sql = "SELECT `EmpId` as `empId`, `Name` as `empName` FROM `EmployeeMaster` where `RoleId`=5 and `IsActive`=1";
	$filterSql = "";
	if($loginEmpRoleId == 1){

	}
	else{
		$empIdList = array();
		array_push($empIdList, $loginEmpId);

		$empSql = "SELECT `EmpId` FROM `EmployeeMaster` where `RMId`='$loginEmpId' and `IsActive`=1";
		$empQuery = mysqli_query($conn,$empSql);
		while($empRow = mysqli_fetch_assoc($empQuery)){
			array_push($empIdList, $empRow["EmpId"]);
		}

		$empImp = implode("','", $empIdList);
		$filterSql .= "and `EmpId` in ('$empImp')";
	}
	$sql = "SELECT `EmpId` as `empId`, `Name` as `empName` FROM `EmployeeMaster` where 1=1 $filterSql and `IsActive`=1 order by `Name`";
	$query = mysqli_query($conn,$sql);
	$empArr = array();
	while($row = mysqli_fetch_assoc($query)){
		array_push($empArr, $row);
	}
	$output = array('empList' => $empArr);
	echo json_encode($output);
}
else if($selectType == "attendance"){
	$filterSql = "";
	if($loginEmpRoleId == 1){
		
	}
	else{
		$underEmpList = array();
		array_push($underEmpList, $loginEmpId);

		$empSql = "SELECT `EmpId` FROM `EmployeeMaster` where `RMId`='$loginEmpId' and `IsActive`=1";
		$empQuery = mysqli_query($conn,$empSql);
		while($empRow = mysqli_fetch_assoc($empQuery)){
			array_push($underEmpList, $empRow["EmpId"]);
		}

		$empIds = implode("','", $underEmpList);
		$filterSql .= " and `EmpId` in ('".$empIds."')";
	}

	$attendanceList = array();
	$filterStartDate = $jsonData->filterStartDate;
	$filterEndDate = $jsonData->filterEndDate;

	if($filterStartDate != ""){
		$filterStartDate = str_replace('/', '-', $filterStartDate);
		$filterStartDate = date("Y-m-d", strtotime($filterStartDate));
		$filterSql .= " and `AttendanceDate` >= '$filterStartDate'";
	}
	if($filterEndDate != ""){
		$filterEndDate = str_replace('/', '-', $filterEndDate);
		$filterEndDate = date("Y-m-d", strtotime($filterEndDate));
		$filterSql .= " and `AttendanceDate` <= '$filterEndDate'";
	}

	$attSql = "SELECT * FROM `Attendance` where 1=1 $filterSql ORDER by `AttendanceDate` desc";
	$attQuery=mysqli_query($conn,$attSql);
	while($attRow = mysqli_fetch_assoc($attQuery)){
		$inDateTime = $attRow["InDateTime"] == null ? '' : $attRow["InDateTime"];
		$outDateTime = $attRow["OutDateTime"] == null ? '' : $attRow["OutDateTime"];
		$workingHours = $attRow["WorkingHours"] == null ? '' : $attRow["WorkingHours"];
		$inLatlong = $attRow["InLatlong"] == null ? '' : str_replace("/", ",", $attRow["InLatlong"]);
		$outLatlong = $attRow["OutLatlong"] == null ? '' : str_replace("/", ",", $attRow["OutLatlong"]);

		$attJson = array(
			'empId' => $attRow["EmpId"], 
			'name' => $attRow["Name"], 
			'attendanceDate' => $attRow["AttendanceDate"], 
			'inDateTime' => $inDateTime, 
			'outDateTime' => $outDateTime, 
			'workingHours' => $workingHours, 
			'inLatlong' => $inLatlong, 
			'outLatlong' => $outLatlong
		);
		array_push($attendanceList, $attJson);
	}
	$output = array('attendanceList' => $attendanceList);
	echo json_encode($output);
}
else if($selectType == "asset"){
	$filterSql = "";
	if($loginEmpRoleId == 1){
		
	}
	else{
		$underEmpList = array();
		array_push($underEmpList, $loginEmpId);

		$empSql = "SELECT `EmpId` FROM `EmployeeMaster` where `RMId`='$loginEmpId' and `IsActive`=1";
		$empQuery = mysqli_query($conn,$empSql);
		while($empRow = mysqli_fetch_assoc($empQuery)){
			array_push($underEmpList, $empRow["EmpId"]);
		}

		$empIds = implode("','", $underEmpList);
		$filterSql .= " and aa.EmpId in ('".$empIds."')";
	}

	// $sql = "SELECT * FROM `AssetAllocation` where 1=1 and `Status` != 10 $filterSql ORDER by `Id` desc";
	$sql = "SELECT aa.*, e.Name FROM AssetAllocation aa join EmployeeMaster e on aa.EmpId=e.EmpId  where 1=1 and aa.Status != 10 $filterSql ORDER by aa.Id desc";
	$query=mysqli_query($conn,$sql);
	$dataList = array();
	while($row = mysqli_fetch_assoc($query)){
		$returnDate = $row["ReturnDate"];
		$status = $row["Status"];
		$statusTxt = $status == 1 ? "Issue" : "Return";
		$pic = $row["Pic"];
		$picList = explode(",", $pic);
		$dataJson = array(
			'id' => $row["Id"],
			'activityId' => $row["ActivityId"],
			'empId' => $row["EmpId"], 
			'name' => $row["Name"], 
			'submitDate' => $row["MobileDateTime"], 
			'assetCategory' => $row["AssetCategory"], 
			'deviceName' => $row["DeviceName"], 
			'serialNumber' => $row["SerialNumber"], 
			'issueDate' => $row["IssueDate"], 
			'returnDate' => $returnDate == null ? "" : $returnDate, 
			'remark' => $row["Remark"],
			'status' => $status,
			'statusTxt' => $statusTxt,
			'picList' => $picList
		);
		array_push($dataList, $dataJson);
	}
	$output = array('assetList' => $dataList);
	echo json_encode($output);
}
else if($selectType == "complaint"){
	$filterSql = "";
	if($loginEmpRoleId == 1){
		
	}
	else{
		$underEmpList = array();
		array_push($underEmpList, $loginEmpId);

		$empSql = "SELECT `EmpId` FROM `EmployeeMaster` where `RMId`='$loginEmpId' and `IsActive`=1";
		$empQuery = mysqli_query($conn,$empSql);
		while($empRow = mysqli_fetch_assoc($empQuery)){
			array_push($underEmpList, $empRow["EmpId"]);
		}

		$empIds = implode("','", $underEmpList);
		$filterSql .= " and vc.EmpId in ('".$empIds."')";
	}

	$sql = "SELECT c.Id as id, vc.EmpId as empId, vc.Name as empName, date_format(vc.MobileDateTime,'%d-%m-%Y') as raiseDate, date_format(c.CloseDate,'%d-%m-%Y') as closeDate, c.CloseDescription as closeDescription, vc.ComplaintType as complaintType, vc.Photo as photo, vc.DetailedDescription as description, c.Status as status  FROM Complaint c join V_Complaint vc on c.ActivityId=vc.ActivityId where 1=1 $filterSql and c.Status != 10 order by c.Id desc";
	$query=mysqli_query($conn,$sql);
	$dataList = array();
	while($row = mysqli_fetch_assoc($query)){
		$status = $row["status"];
		$closeDescription = $row["closeDescription"];
		$closeDescription = $closeDescription == null ? "" : $closeDescription;
		$statusTxt = $status == 1 ? "Close" : "In Progress";
		$pic = $row["photo"];
		$picList = explode(",", $pic);

		$row["closeDescription"] = $closeDescription;
		$row["statusTxt"] = $statusTxt;
		$row["picList"] = $picList;
		unset($row["photo"]);
		array_push($dataList, $row);
	}
	$output = array('complaintList' => $dataList);
	echo json_encode($output);
}
else if($selectType == "salarySlip"){
	$filterSql = "";
	if($loginEmpRoleId == 1){
		
	}
	else{
		$underEmpList = array();
		array_push($underEmpList, $loginEmpId);

		$empSql = "SELECT `EmpId` FROM `EmployeeMaster` where `RMId`='$loginEmpId' and `IsActive`=1";
		$empQuery = mysqli_query($conn,$empSql);
		while($empRow = mysqli_fetch_assoc($empQuery)){
			array_push($underEmpList, $empRow["EmpId"]);
		}

		$empIds = implode("','", $underEmpList);
		$filterSql .= " and ed.EmpId in ('".$empIds."')";
	}

	$sql = "SELECT ed.Id as id, e.Name as name, e.Mobile as mobile, ed.Basic as basic, ed.MonthYear as monthYear, ed.PaidDays as paidDays, ed.AfterLossOfPay as lossOfPay, ed.OtherDeductions as tds, ed.Reimbursements as reimbursements, ed.NetSalary as netSalary, ed.SalarySlipName as salarySlipName FROM EmployeeDeductions ed join EmployeeMaster e on ed.EmpId=e.EmpId and e.IsActive=1 WHERE 1=1  $filterSql order by ed.Id desc";

	$query=mysqli_query($conn,$sql);
	$dataList = array();
	while($row = mysqli_fetch_assoc($query)){
		array_push($dataList, $row);
	}
	$output = array('salarySlipList' => $dataList);
	echo json_encode($output);
}


//Close the connection 
// $conn->close();

?>

<?php
function moneyFormatIndia($num) {
    $explrestunits = "" ;
    if(strlen($num)>3) {
        $lastthree = substr($num, strlen($num)-3, strlen($num));
        $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits
        $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
        $expunit = str_split($restunits, 2);
        for($i=0; $i<sizeof($expunit); $i++) {
            // creates each of the 2's group and adds a comma to the end
            if($i==0) {
                $explrestunits .= (int)$expunit[$i].","; // if is first value , convert into integer
            } else {
                $explrestunits .= $expunit[$i].",";
            }
        }
        $thecash = $explrestunits.$lastthree;
    } else {
        $thecash = $num;
    }
    return $thecash; // writes the final format where $currency is the currency symbol.
}
function fnlGetDistance($json_data)
{
	$json_a=json_decode($json_data,true);
	$total_distance=0;
	foreach($json_a as $key => $value) 
	{
		if($key=="rows")
		{
			foreach($value as $key1 => $value1) 
			{
				foreach($value1 as $key2 => $value2) 
				{
					foreach($value2 as $key3 => $value3) 
					{
						foreach($value3 as $key4 => $value4) 
						{
							if($key4=="distance")
							{
								foreach($value4 as $key5 => $value5) 
								{
									if($key5=="text")
									{
										// $total_distance=$total_distance + str_replace(" km","",$value5);
										$dist = $value5;
										// echo $dist;
										if(strpos($dist, 'km') !== false){
											// echo $dist;
											$dist1 = str_replace(" km","",$dist);
											// echo $dist1.'--';
											$dist = $dist1*1000;
										}
										else{
											$dist1 = str_replace(" m","",$dist);
											// echo $dist1.'--';
											$dist = $dist1;
										}
										$total_distance = ($total_distance + $dist)/1000;
									}
								}
							}
						}
					}
				}
			}
		}
	}
	return $total_distance;
}
?>