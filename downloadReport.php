<?php 
include("dbConfiguration.php");
function getSafeRequestValue($key){
	$val = $_REQUEST[$key];
	return isset($val)? $val:"";
}

$jsonData = getSafeRequestValue('jsonData');
$jsonData=json_decode($jsonData);
$loginEmpId = $jsonData->loginEmpId;
$loginEmpRole = $jsonData->loginEmpRole;
$loginEmpRoleId = $jsonData->loginEmpRoleId;
$fromDate = $jsonData->fromDate;
$toDate = $jsonData->toDate;
$reportType = $jsonData->reportType;
$millisecond = $jsonData->millisecond;
$currentTime = time();
// if($currentTime >= $millisecond){
// 	unauthorizedAccess();
// }
// // Uptime Report
// else {
	if($reportType == 1){
		$sql = "SELECT EmpId, Name, '' as TDS, '' as LossOfPay, '' as Reimbursements,  '' as RetentionBonus, '' as ProfessionTax, '' as IncomeTax, '' as OtherTax FROM `EmployeeMaster` where `RoleId` = 2 and `IsActive` = 1";
		$result = mysqli_query($conn,$sql);
		$row=mysqli_fetch_assoc($result);
		$columnName = array();
		foreach ($row as $key => $value) {
			array_push($columnName, $key);
		}

		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=Deduction.csv');
		$output = fopen('php://output', 'w');
		fputcsv($output, $columnName);

		mysqli_data_seek($result, 0);
		while($row=mysqli_fetch_assoc($result)){
			$exportData = array();
			foreach ($columnName as $key => $value) {
				array_push($exportData, $row[$value]);
			}
			fputcsv($output, $exportData);
		}

	}
	else if($reportType == 2){
		$todayDate = date('Y-m-d');
		$sql = "SELECT * FROM `Attendance` where `AttendanceDate` = '$todayDate'";
		$result = mysqli_query($conn,$sql);
		$row=mysqli_fetch_assoc($result);
		$columnName = array();
		foreach ($row as $key => $value) {
			array_push($columnName, $key);
		}

		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=Attendance.csv');
		$output = fopen('php://output', 'w');
		fputcsv($output, $columnName);

		mysqli_data_seek($result, 0);
		while($row=mysqli_fetch_assoc($result)){
			$exportData = array();
			foreach ($columnName as $key => $value) {
				array_push($exportData, $row[$value]);
			}
			fputcsv($output, $exportData);
		}
	}
	else if($reportType == 3){
		$filterSql = "";
		if($loginEmpRoleId != 1){
			// $filterSql .= " and `EmpId` = '$loginEmpId' ";
			$empIdList = array();
			array_push($empIdList, $loginEmpId);

			$empSql = "SELECT `EmpId` FROM `EmployeeMaster` where `RMId`='$loginEmpId' and `IsActive`=1";
			$empQuery = mysqli_query($conn,$empSql);
			while($empRow = mysqli_fetch_assoc($empQuery)){
				array_push($empIdList, $empRow["EmpId"]);
			}

			$empImp = implode("','", $empIdList);
			$filterSql .= " and `EmpId` in ('$empImp')";
		}

		if($fromDate != ''){
			$fromDate = str_replace('/', '-', $fromDate);
			$fromDate = date("Y-m-d", strtotime($fromDate));
			$filterSql .= " and date(`Submit Date`) >= '$fromDate'";
		}
		if($toDate != ''){
			$toDate = str_replace('/', '-', $toDate);
			$toDate = date("Y-m-d", strtotime($toDate));
			$filterSql .= " and date(`Submit Date`) <= '$toDate'";
		}
		$sql = "SELECT `ActivityId`, `Emp Name`, `Submit Date`, `Company Name`, `Address`, `Contact Person Name`, `Contact Person`, `Email Id`, `Remark` FROM `Prospect_Report` where 1=1 $filterSql order by `ActivityId` desc";
		$result = mysqli_query($conn,$sql);
		$row=mysqli_fetch_assoc($result);
		$columnName = array();
		foreach ($row as $key => $value) {
			array_push($columnName, $key);
		}

		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=Prospect_Report.csv');
		$output = fopen('php://output', 'w');
		fputcsv($output, $columnName);

		mysqli_data_seek($result, 0);
		while($row=mysqli_fetch_assoc($result)){
			$exportData = array();
			foreach ($columnName as $key => $value) {
				array_push($exportData, $row[$value]);
			}
			fputcsv($output, $exportData);
		}
	}
	// Incident Report
	// else if($reportType == 1){
	// 	$filterSql = "";
	// 	if($loginEmpRole != 'Admin' && $loginEmpRole != 'SpaceWorld' && $loginEmpRole != "Management" && $loginEmpRole != "Corporate OnM lead"){
	// 		$empSiteList = [];
	// 		$empLocSql = "SELECT l.Site_Id FROM EmployeeLocationMapping el join Location l on el.LocationId = l.LocationId where el.Emp_Id = '$loginEmpId' ";
	// 		$empLocQuery=mysqli_query($conn,$empLocSql);
	// 		while($empLocRow = mysqli_fetch_assoc($empLocQuery)){
	// 			array_push($empSiteList,$empLocRow["Site_Id"]);
	// 		}
	// 		$el = implode("','", $empSiteList);
	// 		$filterSql .= "and `Site Id` in ('".$el."') ";
	// 	}

	// 	$sql = "SELECT (@sr := @sr+1) as `Sr. No.`, `Circle`, `Site Name`, `Site Id`, `Incident category`, `Material Damaged`, `Incident Date`, `Incident Time`, `Incident description`, `Location (Lat Long)`, `Entered By`, `Approved status By L1`, `Approved status By L2` FROM (select @sr:=0) as sr, `Incident_Management_Report` where 1=1 ".$filterSql;
	// 	if($fromDate != "")
	// 		$sql .= "and `Incident Date` >= '$fromDate' ";
	// 	if($toDate != "")
	// 		$sql .= "and `Incident Date` <= '$toDate' ";
	// 	$sql .= "order by `ActivityId` desc";
	// 	$result = mysqli_query($conn,$sql);
	// 	$row=mysqli_fetch_assoc($result);
	// 	$columnName = array();
	// 	foreach ($row as $key => $value) {
	// 		array_push($columnName, $key);
	// 	}

	// 	header('Content-Type: text/csv; charset=utf-8');
	// 	header('Content-Disposition: attachment; filename=Incident_Management_Report.csv');
	// 	$output = fopen('php://output', 'w');
	// 	fputcsv($output, $columnName);

	// 	mysqli_data_seek($result, 0);
	// 	while($row=mysqli_fetch_assoc($result)){
	// 		$exportData = array();
	// 		foreach ($columnName as $key => $value) {
	// 			array_push($exportData, $row[$value]);
	// 		}
	// 		fputcsv($output, $exportData);
	// 	}
	// }
	// // PM Report
	// else if($reportType == 2){
	// 	$filterSql = "";
	// 	if($loginEmpRole != 'Admin' && $loginEmpRole != 'SpaceWorld' && $loginEmpRole != "Management" && $loginEmpRole != "Corporate OnM lead"){
	// 		$empSiteList = [];
	// 		$empLocSql = "SELECT l.Site_Id FROM EmployeeLocationMapping el join Location l on el.LocationId = l.LocationId where el.Emp_Id = '$loginEmpId' ";
	// 		$empLocQuery=mysqli_query($conn,$empLocSql);
	// 		while($empLocRow = mysqli_fetch_assoc($empLocQuery)){
	// 			array_push($empSiteList,$empLocRow["Site_Id"]);
	// 		}
	// 		$el = implode("','", $empSiteList);
	// 		$filterSql .= "and `Site_Id` in ('".$el."') ";
	// 	}

	// 	$sql = "SELECT `Circle`, `Site_Name`, `Site_Id`, `Site Type`, `PM Done Date`, `Airtel Site Id`, `Airtel Load`, `MTNL/BSNL Site Id`, `MTNL/BSNL Load`, `VIL Site Id`, `VIL Load`, `RJIO Site Id`, `RJIO Load`, `No. of FE`, `Serial No. OF FE 1`, `Refilling date of FE 1`, `Expiry date of FE 1`, `Serial No. OF FE 2`, `Refilling date of FE 2`, `Expiry date of FE 2`, `Serial No. OF FE 3`, `Refilling date of FE 3`, `Expiry date of FE 3`, `Serial No. OF FE 4`, `Refilling date of FE 4`, `Expiry date of FE 4`, `Serial No. OF FE 5`, `Refilling date of FE 5`, `Expiry date of FE 5`, `Pole Type`, `No. of Pole`, `Pole Height`, `Airtel RRH`, `Airtel MW`, `Airtel GSM`, `MTNL/BSNL RRH`, `MTNL/BSNL MW`, `MTNL/BSNL GSM`, `VIL RRH`, `VIL MW`, `VIL GSM`, `RJIO RRH`, `RJIO MW`, `RJIO GSM`, `SMPS Make`, `No. of RM`, `No. of faulty`, `BB Make & Model`, `No. of BB`, `SOC &SOH status`, `Capacity in AH 1`, `Capacity in AH 2`, `Capacity in AH 3`, `PM done By`, `PM approved By:L1` FROM `PM_Report` where 1=1 ".$filterSql;

	// 	if($fromDate != "")
	// 		$sql .= "and DATE_FORMAT(`PM Done Date`,'%Y-%m-%d') >= '$fromDate' ";
	// 	if($toDate != "")
	// 		$sql .= "and DATE_FORMAT(`PM Done Date`,'%Y-%m-%d') <= '$toDate' ";
	// 	$sql .= "order by `ActivityId` desc";
		
	// 	$result = mysqli_query($conn,$sql);
	// 	$row=mysqli_fetch_assoc($result);
	// 	$columnName = array();
	// 	foreach ($row as $key => $value) {
	// 		array_push($columnName, $key);
	// 	}

	// 	header('Content-Type: text/csv; charset=utf-8');
	// 	header('Content-Disposition: attachment; filename=PM_Report.csv');
	// 	$output = fopen('php://output', 'w');
	// 	fputcsv($output, $columnName);

	// 	mysqli_data_seek($result, 0);
	// 	while($row=mysqli_fetch_assoc($result)){
	// 		$exportData = array();
	// 		foreach ($columnName as $key => $value) {
	// 			array_push($exportData, $row[$value]);
	// 		}
	// 		fputcsv($output, $exportData);
	// 	}
	// }
	// // Outage Category Report
	// else if($reportType == 3){
	// 	$filterSql = "";
	// 	if($loginEmpRole != 'Admin' && $loginEmpRole != 'SpaceWorld' && $loginEmpRole != "Management" && $loginEmpRole != "Corporate OnM lead"){
	// 		$empSiteList = [];
	// 		$empLocSql = "SELECT l.Site_Id FROM EmployeeLocationMapping el join Location l on el.LocationId = l.LocationId where el.Emp_Id = '$loginEmpId' ";
	// 		$empLocQuery=mysqli_query($conn,$empLocSql);
	// 		while($empLocRow = mysqli_fetch_assoc($empLocQuery)){
	// 			array_push($empSiteList,$empLocRow["Site_Id"]);
	// 		}
	// 		$el = implode("','", $empSiteList);
	// 		$filterSql .= "and `site_Id` in ('".$el."') ";
	// 	}
	// 	$sql = "SELECT (@sr := @sr+1) as `Sr. No.`, `Circle`, `site_id` as `Site Id`, `site_name` as `Site Name`, `site_type` as `Site Type`, `opco_affected` as `OPCO Affected`, `outage_Category` as `Outage Category`, `outage_start_datetime` as `Outage Start Datetime`, `outage_end_datetime` as `Outage End Datetime`, `outage_minute` as `Outage Minute`, `outage_RCA` as `Outage RCA` FROM Uptime_Report, (select @sr:=0) as sr where 1=1 ".$filterSql;
	// 	if($fromDate != "")
	// 		$sql .= "and `outage_start_date` >= '$fromDate' ";
	// 	if($toDate != "")
	// 		$sql .= "and `outage_start_date` <= '$toDate' ";
	// 	$sql .= "order by `ActivityId` desc";

	// 	$result = mysqli_query($conn,$sql);
	// 	$row=mysqli_fetch_assoc($result);
	// 	$columnName = array();
	// 	foreach ($row as $key => $value) {
	// 		array_push($columnName, $key);
	// 	}

	// 	header('Content-Type: text/csv; charset=utf-8');
	// 	header('Content-Disposition: attachment; filename=Outage_Category_Report.csv');
	// 	$output = fopen('php://output', 'w');
	// 	fputcsv($output, $columnName);

	// 	mysqli_data_seek($result, 0);
	// 	while($row=mysqli_fetch_assoc($result)){
	// 		$exportData = array();
	// 		foreach ($columnName as $key => $value) {
	// 			array_push($exportData, $row[$value]);
	// 		}
	// 		fputcsv($output, $exportData);
	// 	}
	// }
	// // Meter Reading Report
	// else if($reportType == 4){
	// 	$filterSql = "";
	// 	if($loginEmpRole != 'Admin' && $loginEmpRole != 'SpaceWorld' && $loginEmpRole != "Management" && $loginEmpRole != "Corporate OnM lead"){
	// 		$empSiteList = [];
	// 		$empLocSql = "SELECT l.Site_Id FROM EmployeeLocationMapping el join Location l on el.LocationId = l.LocationId where el.Emp_Id = '$loginEmpId' ";
	// 		$empLocQuery=mysqli_query($conn,$empLocSql);
	// 		while($empLocRow = mysqli_fetch_assoc($empLocQuery)){
	// 			array_push($empSiteList,$empLocRow["Site_Id"]);
	// 		}
	// 		$el = implode("','", $empSiteList);
	// 		$filterSql .= "and m.`Site Id` in ('".$el."') ";
	// 	}

	// 	$sql = "SELECT m.`ActivityId`, m.`Circle`, m.`City`, m.`Site Name`, REPLACE(m.`Site Id`,' ','') as `Site Id`, m.`Site Type`, m.`Submit By`, m.`Submit Date`, m.`Do you have sub meter?`, m.`Main Meter Reading`, m.`Main Meter Pic`, m.`Sub Meter Reading`, m.`Sub Meter Pic`, m.`Remark` FROM `Meter_Reading_Report` m WHERE 1=1 ".$filterSql;

	// 	if($fromDate != "")
	// 		$sql .= "and DATE_FORMAT(m.`ServerDateTime`,'%Y-%m-%d') >= '$fromDate' ";
	// 	if($toDate != "")
	// 		$sql .= "and DATE_FORMAT(m.`ServerDateTime`,'%Y-%m-%d') <= '$toDate' ";
	// 	$sql .= "order by m.`ActivityId` desc";

	// 	$result = mysqli_query($conn,$sql);
	// 	$row=mysqli_fetch_assoc($result);
	// 	$columnName = array();
	// 	foreach ($row as $key => $value) {
	// 		array_push($columnName, $key);
	// 	}

	// 	header('Content-Type: text/csv; charset=utf-8');
	// 	header('Content-Disposition: attachment; filename=Meter_Reading_Report.csv');
	// 	$output = fopen('php://output', 'w');
	// 	fputcsv($output, $columnName);

	// 	mysqli_data_seek($result, 0);
	// 	while($row=mysqli_fetch_assoc($result)){
	// 		$exportData = array();
	// 		foreach ($columnName as $key => $value) {
	// 			array_push($exportData, $row[$value]);
	// 		}
	// 		fputcsv($output, $exportData);
	// 	}
	// }
	// // Training Report
	// else if($reportType == 5){
	// 	$filterSql = "";
	// 	if($loginEmpRole != 'Admin' && $loginEmpRole != 'SpaceWorld' && $loginEmpRole != "Management" && $loginEmpRole != "Corporate OnM lead"){
	// 		$filterSql .= "and `Emp Id` = '$loginEmpId' ";
	// 	}
	// 	$sql = "SELECT `Training Name`, `Submit By`, `Submit Date`, `Total Question`, `Correct`, `Incorrect`, `Percentage`, `Result` FROM `Training_Report` WHERE 1=1 ".$filterSql;

	// 	if($fromDate != "")
	// 		$sql .= "and DATE_FORMAT(`ServerDateTime`,'%Y-%m-%d') >= '$fromDate' ";
	// 	if($toDate != "")
	// 		$sql .= "and DATE_FORMAT(`ServerDateTime`,'%Y-%m-%d') <= '$toDate' ";
	// 	$sql .= "order by `ActivityId` desc";

	// 	$result = mysqli_query($conn,$sql);
	// 	$row=mysqli_fetch_assoc($result);
	// 	$columnName = array();
	// 	foreach ($row as $key => $value) {
	// 		array_push($columnName, $key);
	// 	}

	// 	header('Content-Type: text/csv; charset=utf-8');
	// 	header('Content-Disposition: attachment; filename=Training_Report.csv');
	// 	$output = fopen('php://output', 'w');
	// 	fputcsv($output, $columnName);

	// 	mysqli_data_seek($result, 0);
	// 	while($row=mysqli_fetch_assoc($result)){
	// 		$exportData = array();
	// 		foreach ($columnName as $key => $value) {
	// 			array_push($exportData, $row[$value]);
	// 		}
	// 		fputcsv($output, $exportData);
	// 	}
	// }
	// // Punchpoint Report
	// else if($reportType == 6){
	// 	$filterSql = "";
	// 	if($loginEmpRole != 'Admin' && $loginEmpRole != 'SpaceWorld' && $loginEmpRole != "Management" && $loginEmpRole != "Corporate OnM lead"){
	// 		$empSiteList = [];
	// 		$empLocSql = "SELECT l.Site_Id FROM EmployeeLocationMapping el join Location l on el.LocationId = l.LocationId where el.Emp_Id = '$loginEmpId' ";
	// 		$empLocQuery=mysqli_query($conn,$empLocSql);
	// 		while($empLocRow = mysqli_fetch_assoc($empLocQuery)){
	// 			array_push($empSiteList,$empLocRow["Site_Id"]);
	// 		}
	// 		$el = implode("','", $empSiteList);
	// 		$filterSql .= "and `Site Id` in ('".$el."') ";
	// 	}
	// 	$sql = "SELECT `ActivityId` as Report_Id, `Site Id`, `Site Name`, `Submit By`, `Submit Date`, `Description`, `Status`, `Remark` 
	// 	FROM `Punchpoint_Report` WHERE 1=1 ".$filterSql;

	// 	if($fromDate != "")
	// 		$sql .= "and DATE_FORMAT(`MobileDateTime`,'%Y-%m-%d') >= '$fromDate' ";
	// 	if($toDate != "")
	// 		$sql .= "and DATE_FORMAT(`MobileDateTime`,'%Y-%m-%d') <= '$toDate' ";
	// 	$sql .= "order by `ActivityId` desc";

	// 	$result = mysqli_query($conn,$sql);
	// 	$row=mysqli_fetch_assoc($result);
	// 	$columnName = array();
	// 	foreach ($row as $key => $value) {
	// 		array_push($columnName, $key);
	// 	}

	// 	header('Content-Type: text/csv; charset=utf-8');
	// 	header('Content-Disposition: attachment; filename=Punchpoint_Report.csv');
	// 	$output = fopen('php://output', 'w');
	// 	fputcsv($output, $columnName);

	// 	mysqli_data_seek($result, 0);
	// 	while($row=mysqli_fetch_assoc($result)){
	// 		$exportData = array();
	// 		foreach ($columnName as $key => $value) {
	// 			array_push($exportData, $row[$value]);
	// 		}
	// 		fputcsv($output, $exportData);
	// 	}
	// }
	// // Export location
	// else if($reportType == 7){
	// 	$locType = $jsonData->locType;
	// 	$filterSql = "";
	// 	if($locType == "NBS"){
	// 		$filterSql .= " and `Is_NBS_Site` = 1 ";
	// 	}
	// 	else{
	// 		$filterSql .= " and `Is_NBS_Site` = 0 ";
	// 	}
	// 	$sql = "SELECT `LocationId`, `State`, `Name` as `Site Name`, `Site_Id`, `Site_Type` as `Site Type`, `Site_CAT` as `Site Category`, `Airport_Metro` as `Airport/Metro`, `RFI_date`, `High_Revenue_Site`, `ISQ`, `Retail_IBS`, `GeoCoordinates`, `Is_Active` FROM `Location` where LocationId != 1 ".$filterSql." and Tenent_Id = 2";
	// 	$result = mysqli_query($conn,$sql);
	// 	$row=mysqli_fetch_assoc($result);
	// 	$columnName = array();
	// 	foreach ($row as $key => $value) {
	// 		array_push($columnName, $key);
	// 	}

	// 	header('Content-Type: text/csv; charset=utf-8');
	// 	header('Content-Disposition: attachment; filename=Location.csv');
	// 	$output = fopen('php://output', 'w');
	// 	fputcsv($output, $columnName);

	// 	mysqli_data_seek($result, 0);
	// 	while($row=mysqli_fetch_assoc($result)){
	// 		$exportData = array();
	// 		foreach ($columnName as $key => $value) {
	// 			array_push($exportData, $row[$value]);
	// 		}
	// 		fputcsv($output, $exportData);
	// 	}
	// }
	// // Export Employee location mapping
	// else if($reportType == 8){
	// 	$locType = $jsonData->locType;
	// 	$filterSql = "";
	// 	if($locType == "NBS"){
	// 		$filterSql .= " and loc.`Is_NBS_Site` = 1 ";
	// 	}
	// 	else{
	// 		$filterSql .= " and loc.`Is_NBS_Site` = 0 ";
	// 	}

	// 	$sql = "SELECT loc.State, loc.Name as `Site Name`, loc.Site_Id as `Site Id`, emp.Name as `Employee Name`, empLoc.Role FROM EmployeeLocationMapping empLoc join Location loc on empLoc.LocationId = loc.LocationId left join Employees emp on empLoc.Emp_Id = emp.EmpId left join Role ro on empLoc.Role = ro.Role where empLoc.`Tenent_Id` = 2 and empLoc.Is_Active = 1 ".$filterSql;

	// 	$result = mysqli_query($conn,$sql);
	// 	$row=mysqli_fetch_assoc($result);
	// 	$columnName = array();
	// 	foreach ($row as $key => $value) {
	// 		array_push($columnName, $key);
	// 	}

	// 	header('Content-Type: text/csv; charset=utf-8');
	// 	header('Content-Disposition: attachment; filename=EmployeeLocationMapping.csv');
	// 	$output = fopen('php://output', 'w');
	// 	fputcsv($output, $columnName);

	// 	mysqli_data_seek($result, 0);
	// 	while($row=mysqli_fetch_assoc($result)){
	// 		$exportData = array();
	// 		foreach ($columnName as $key => $value) {
	// 			array_push($exportData, $row[$value]);
	// 		}
	// 		fputcsv($output, $exportData);
	// 	}
	// }
	// // Export Exployee
	// else if($reportType == 9){
	// 	$sql = "SELECT e.`Name` as `Emp Name`, e.`Mobile`, r.`Role`, e.`State`, e.`Active` FROM `Employees` e left join `Role` r on e.`RoleId` = r.`RoleId` where e.`Tenent_Id` = 2 ";

	// 	$result = mysqli_query($conn,$sql);
	// 	$row=mysqli_fetch_assoc($result);
	// 	$columnName = array();
	// 	foreach ($row as $key => $value) {
	// 		array_push($columnName, $key);
	// 	}

	// 	header('Content-Type: text/csv; charset=utf-8');
	// 	header('Content-Disposition: attachment; filename=Employee.csv');
	// 	$output = fopen('php://output', 'w');
	// 	fputcsv($output, $columnName);

	// 	mysqli_data_seek($result, 0);
	// 	while($row=mysqli_fetch_assoc($result)){
	// 		$exportData = array();
	// 		foreach ($columnName as $key => $value) {
	// 			array_push($exportData, $row[$value]);
	// 		}
	// 		fputcsv($output, $exportData);
	// 	}
	// }
	// // Site Survey Report
	// else if($reportType == 10){
	// 	$filterSql = "";
	// 	if($loginEmpRole != 'CBH'){
	// 		$filterSql .= " and t.EmpId = '$loginEmpId' ";
	// 	}
	// 	if($fromDate != ""){
	// 		$filterSql .= " and DATE_FORMAT(t.`Date & Time`,'%Y-%m-%d') >= '$fromDate' ";
	// 	}
	// 	if($toDate != ""){
	// 		$filterSql .= " and DATE_FORMAT(t.`Date & Time`,'%Y-%m-%d') <= '$toDate' ";
	// 	}

	// 	$sql = "select t.ActivityId, t.EmpId, t.Name, t.`STIPL Id`, t.`OPCO Id`, t.`SAQ Assigned`, t.`Date & Time` as `Assign Date`, a1.MobileDateTime as `Visit Date`, t.Status, max(case when d.ChkId = 4939 then d.Value end) `Owner`, max(case when d.ChkId = 4942 then d.Value end) `Mobile`, max(case when d.ChkId = 4956 then d.Value end) `Site Latitude`, max(case when d.ChkId = 4957 then d.Value end) `Site Longitude`, max(case when d.ChkId = 5048 then d.Value end) `Remark`  from (SELECT a.ActivityId, a.EmpId, e.Name, max(case when d.ChkId = 4928 then d.Value end) as `STIPL Id`, max(case when d.ChkId = 4927 then d.Value end) as `OPCO Id`, h.Assign_To as `SAQ Assigned`, a.MobileDateTime as `Date & Time`, h.VerifierActivityId, (case when h.`Verify_Final_Submit` is null and h.`TransactionStatus` = 1 then 'Pending' when h.`Verify_Final_Submit` = 'No' and h.`TransactionStatus` = 1 then 'In Progress'  when h.`Verify_Final_Submit` = 'Yes' and h.`TransactionStatus` = 1 then 'Completed' else 'Closed' end) as `Status` FROM Activity a join TransactionHDR h on a.ActivityId = h.ActivityId left join TransactionDTL d on h.ActivityId = d.ActivityId left join Employees_Reference e on a.EmpId = e.EmpId and a.RefId = e.RefId WHERE MenuId = 279 AND Event = 'Submit' GROUP by a.ActivityId) t left join TransactionDTL d on t.VerifierActivityId = d.ActivityId left join Activity a1 on t.VerifierActivityId = a1.ActivityId where 1=1 ".$filterSql." GROUP by t.ActivityId";

	// 	$result = mysqli_query($conn,$sql);
	// 	$row=mysqli_fetch_assoc($result);
	// 	$columnName = array();
	// 	foreach ($row as $key => $value) {
	// 		array_push($columnName, $key);
	// 	}

	// 	header('Content-Type: text/csv; charset=utf-8');
	// 	header('Content-Disposition: attachment; filename=SiteSurveyReport.csv');
	// 	$output = fopen('php://output', 'w');
	// 	fputcsv($output, $columnName);

	// 	mysqli_data_seek($result, 0);
	// 	while($row=mysqli_fetch_assoc($result)){
	// 		$exportData = array();
	// 		foreach ($columnName as $key => $value) {
	// 			array_push($exportData, $row[$value]);
	// 		}
	// 		fputcsv($output, $exportData);
	// 	}
	// }
	// // Management Visit report
	// else if($reportType == 11){
	// 	header('Content-Type: text/csv; charset=utf-8');
	// 	header('Content-Disposition: attachment; filename=ManagementVisitReport.csv');
	// 	$output = fopen('php://output', 'w');
	// 	fputcsv($output,array("ActivityId","SiteName","SiteID","SiteTYPE(IBS/OD/IBS+OD)","Circle","LastPMdoneBy","LastPMdoneDate","SitevisitedBy","Date","Site Lat,long","Airtel","Voda","JIO","BSNL/MTNL","EMF Signage Board at Site","Pole condition at Site(Check for rusting/damage of pole/Nut & Bolts)","No. of Poles at Site","Is the site free from unwanted materials and garbage ?? (Leftover materials during deployment / O&M shall not be left at the site)","Fire extinguisher provided and maintained in working condition","24*7 access at site","Met with Owner/ Builder/LO. or Owner/ Builder representative at site","Is the tower maintained free from bee-hives or bird nests?? (Inspection is needed from four positions to identify the location of bee-hives and birds nests)","Gasket of ODC (GAP & Crack should not be present)/Proper door alignment for ODC","Is there any other punch point?","Punch points (Remarks)"));

	// 	$filterSql = "";
	// 	if($loginEmpRole != 'Admin' && $loginEmpRole != 'SpaceWorld' && $loginEmpRole != "Management" && $loginEmpRole != "Corporate OnM lead"){
	// 		$empSiteList = [];
	// 		$empLocSql = "SELECT l.Site_Id FROM EmployeeLocationMapping el join Location l on el.LocationId = l.LocationId where el.Emp_Id = '$loginEmpId' ";
	// 		$empLocQuery=mysqli_query($conn,$empLocSql);
	// 		while($empLocRow = mysqli_fetch_assoc($empLocQuery)){
	// 			array_push($empSiteList,$empLocRow["Site_Id"]);
	// 		}
	// 		$el = implode("','", $empSiteList);
	// 		$filterSql .= "and `Site_Id` in ('".$el."') ";
	// 	}
	// 	$sql = "SELECT `ActivityId`, `Site_Name`, `Site_Id`, `Site_Type`, `Circle`, `SitevisitedBy`, `Date`, `Site Lat,long`, `Airtel`, `Voda`, `Jio`, `MTNL/BSNL`, `Col_2`, `Col_3`, `Col_4`, `Col_5`, `Col_6`, `Col_7`, `Col_8`, `Col_9`, `Col_10`, `Col_11`, `Punchpoint Remark` FROM `Management_Visit_Report` WHERE 1=1 ".$filterSql;

	// 	if($fromDate != ""){
	// 		$sql .= " and DATE_FORMAT(`Date`,'%Y-%m-%d') >= '$fromDate' ";
	// 	}
	// 	if($toDate != ""){
	// 		$sql .= " and DATE_FORMAT(`Date`,'%Y-%m-%d') <= '$toDate' ";
	// 	}
	// 	$result = mysqli_query($conn,$sql);
	// 	while($row=mysqli_fetch_assoc($result)){
	// 		$siteId = $row["Site_Id"];

	// 		$sql1 = "SELECT e.Name, a.MobileDateTime FROM TransactionHDR h join Activity a on h.ActivityId = a.ActivityId join Employees_Reference e on a.EmpId = e.EmpId and a.RefId = e.RefId where h.Site_Id = '$siteId' and a.MenuId = '274' and a.Event = 'Submit' ORDER by a.MobileDateTime desc  LIMIT 0,1";
	// 		$result1 = mysqli_query($conn,$sql1);
	// 		$row1 = mysqli_fetch_assoc($result1);
	// 		$lastPMDoneBy = $row1["Name"];
	// 		$lastPMDoneDate = $row1["MobileDateTime"];

	// 		$jsonData = array('col0'=> $row["ActivityId"], 'col1' => $row["Site_Name"], 'col2'=> $siteId, 'col3'=> $row["Site_Type"], 'col4'=> $row["Circle"], 'col5'=> $lastPMDoneBy, 'col6'=> $lastPMDoneDate, 'col7'=> $row["SitevisitedBy"], 'col8'=> $row["Date"], 'col9'=> $row["Site Lat,long"], 'col10'=> $row["Airtel"], 'col11'=> $row["Voda"], 'col12'=> $row["Jio"], 'col13'=> $row["MTNL/BSNL"], 'col14'=> $row["Col_2"], 'col15'=> $row["Col_3"], 'col16'=> $row["Col_4"], 'col17'=> $row["Col_5"], 'col18'=> $row["Col_6"], 'col19'=> $row["Col_7"], 'col20'=> $row["Col_8"], 'col21'=> $row["Col_9"], 'col22'=> $row["Col_10"], 'col23'=> $row["Col_11"], 'col24'=> $row["Punchpoint Remark"]);
	// 		fputcsv($output, $jsonData);
	// 	}
		
	// }
	// // Export Vendor
	// else if($reportType == 12){
	// 	$sql = "SELECT `EmpId` as `Code`, `Name`, `VendorType` as `Type`, `State`, `Mobile`, `Active` FROM `Employees` where `RoleId` = 53 and `Tenent_Id` = 2 ";

	// 	$result = mysqli_query($conn,$sql);
	// 	$row=mysqli_fetch_assoc($result);
	// 	$columnName = array();
	// 	foreach ($row as $key => $value) {
	// 		array_push($columnName, $key);
	// 	}

	// 	header('Content-Type: text/csv; charset=utf-8');
	// 	header('Content-Disposition: attachment; filename=Vendor.csv');
	// 	$output = fopen('php://output', 'w');
	// 	fputcsv($output, $columnName);

	// 	mysqli_data_seek($result, 0);
	// 	while($row=mysqli_fetch_assoc($result)){
	// 		$exportData = array();
	// 		foreach ($columnName as $key => $value) {
	// 			array_push($exportData, $row[$value]);
	// 		}
	// 		fputcsv($output, $exportData);
	// 	}
	// }
	// // Export Raise
	// else if($reportType == 13){
	// 	$rmId = $jsonData->rmId;
	// 	$filterSql = "";
	// 	if($loginEmpRole != "SpaceWorld" && $loginEmpRole != "PTW Admin"){
	// 		$filterSql = " and (e1.`RMId` = '$loginEmpId' or e1.`RMId` = '$rmId') ";
	// 	}
	// 	$sql = "SELECT e1.`Name`, e1.`Mobile`, e1.`Whatsapp_Number` as `Whatsapp`, e1.`AadharCard_Number` as `Aadhar card`, e2.`Name` as `Vendor Name`, e1.`Active` FROM `Employees` e1 join `Employees` e2 on e1.`RMId` = e2.`EmpId` where e1.`RoleId` = 61 ".$filterSql." ";

	// 	$result = mysqli_query($conn,$sql);
	// 	$row=mysqli_fetch_assoc($result);
	// 	$columnName = array();
	// 	foreach ($row as $key => $value) {
	// 		array_push($columnName, $key);
	// 	}

	// 	header('Content-Type: text/csv; charset=utf-8');
	// 	header('Content-Disposition: attachment; filename=Raiser.csv');
	// 	$output = fopen('php://output', 'w');
	// 	fputcsv($output, $columnName);

	// 	mysqli_data_seek($result, 0);
	// 	while($row=mysqli_fetch_assoc($result)){
	// 		$exportData = array();
	// 		foreach ($columnName as $key => $value) {
	// 			array_push($exportData, $row[$value]);
	// 		}
	// 		fputcsv($output, $exportData);
	// 	}
	// }
	// // Export Supervisor
	// else if($reportType == 14){
	// 	$rmId = $jsonData->rmId;
	// 	$filterSql = "";
	// 	if($loginEmpRole != "SpaceWorld" && $loginEmpRole != "PTW Admin"){
	// 		$filterSql = " and (e1.`RMId` = '$loginEmpId' or e1.`RMId` = '$rmId') ";
	// 	}
	// 	$sql = "SELECT e1.`Name`, e1.`Mobile`, e1.`Whatsapp_Number` as `Whatsapp`, e1.`AadharCard_Number` as `Aadhar card`, e2.`Name` as `Vendor Name`, e1.`Active` FROM `Employees` e1 join `Employees` e2 on e1.`RMId` = e2.`EmpId` where e1.`RoleId` = 58 ".$filterSql." ";
		
	// 	$result = mysqli_query($conn,$sql);
	// 	$row=mysqli_fetch_assoc($result);
	// 	$columnName = array();
	// 	foreach ($row as $key => $value) {
	// 		array_push($columnName, $key);
	// 	}

	// 	header('Content-Type: text/csv; charset=utf-8');
	// 	header('Content-Disposition: attachment; filename=Supervisor.csv');
	// 	$output = fopen('php://output', 'w');
	// 	fputcsv($output, $columnName);

	// 	mysqli_data_seek($result, 0);
	// 	while($row=mysqli_fetch_assoc($result)){
	// 		$exportData = array();
	// 		foreach ($columnName as $key => $value) {
	// 			array_push($exportData, $row[$value]);
	// 		}
	// 		fputcsv($output, $exportData);
	// 	}
	// }
	// // Meter Reading Report 2
	// else if($reportType == 15){

	// 	$filterSql = "";
	// 	if($loginEmpRole != 'Admin' && $loginEmpRole != 'SpaceWorld' && $loginEmpRole != "Management" && $loginEmpRole != "Corporate OnM lead"){
	// 		$empSiteList = [];
	// 		$empLocSql = "SELECT l.Site_Id FROM EmployeeLocationMapping el join Location l on el.LocationId = l.LocationId where el.Emp_Id = '$loginEmpId' ";
	// 		$empLocQuery=mysqli_query($conn,$empLocSql);
	// 		while($empLocRow = mysqli_fetch_assoc($empLocQuery)){
	// 			array_push($empSiteList,$empLocRow["Site_Id"]);
	// 		}
	// 		$el = implode("','", $empSiteList);
	// 		$filterSql .= "and `Site Id` in ('".$el."') ";
	// 	}

	// 	$sql = "SELECT `ActivityId`, `Circle`, `Site Name`, REPLACE(`Site Id`, ' ','') as `Site Id`, `Site Type`, `Submit By`, `Submit Date`, `Reading Date`, `Previous Submit Date`, `Previous Reading Date`, `Do you have sub meter?`, `Current Main Meter Reading`, `Previous Main Meter Reading`, `Diff Main Meter Reading`, `Main Meter Billing`, `Current Main Meter Pic`, `Current Sub Meter Reading`, `Previous Sub Meter Reading`, `Diff Sub Meter Reading`, `Sub Meter Billing`, `Current Sub Meter Pic`, `Current Remark` FROM `DiffMeterReading` where 1=1 ".$filterSql;

	// 	if($fromDate != "")
	// 		$sql .= "and DATE_FORMAT(`Submit Date`,'%Y-%m-%d') >= '$fromDate' ";
	// 	if($toDate != "")
	// 		$sql .= "and DATE_FORMAT(`Submit Date`,'%Y-%m-%d') <= '$toDate' ";
		
	// 	$result = mysqli_query($conn,$sql);
	// 	$row=mysqli_fetch_assoc($result);
	// 	$columnName = array();
	// 	foreach ($row as $key => $value) {
	// 		array_push($columnName, $key);
	// 	}

	// 	header('Content-Type: text/csv; charset=utf-8');
	// 	header('Content-Disposition: attachment; filename=Meter_Reading_Report_2.csv');
	// 	$output = fopen('php://output', 'w');
	// 	fputcsv($output, $columnName);

	// 	mysqli_data_seek($result, 0);
	// 	while($row=mysqli_fetch_assoc($result)){
	// 		$exportData = array();
	// 		foreach ($columnName as $key => $value) {
	// 			array_push($exportData, $row[$value]);
	// 		}
	// 		fputcsv($output, $exportData);
	// 	}
	// }
	// // PTW Report
	// else if($reportType == 16){

	// 	header('Content-Type: text/csv; charset=utf-8');
	// 	header('Content-Disposition: attachment; filename=PTWReport.csv');
	// 	$output = fopen('php://output', 'w');

	// 	$sql = "SELECT rd.ActivityId, sd.ApproverActivityId, rd.Circle, rd.Site_Id as `Site Id`, rd.Site_Name as `Site Name`, rd.Site_Type as `Site Type`, rd.PtwType as `PTW Type`, aty.ActivityType as `Activity`, rd.PtwRaiseDateTime as `PTW Raise Date`, rd.WorkStartDatetime as `Work Start Datetime`, rd.WorkEndDatetime as `Work End Datetime`, (case when rd.PtwStatus = 'Approved by' or rd.PtwStatus = 'Rejected by' then concat(rd.PtwStatus,' ',ad.AppByRole) else rd.PtwStatus end) as `Status of PTW`, (case when rd.PtwStatus = 'Cancel by Auditor' then audd.Observation when (rd.PtwStatus = 'Rejected by' or rd.PtwStatus = 'Cancelled') then ad.AppRemark else ad.ReasonOfCancel end)  as `Reason of Rejection`, rd.VendorName as `Vendor Name`, rd.PartnerType as `Partner Type`, rd.PtwRaiserName as `PTW Raiser Name`, rd.PtwRaiserMobileNo as `PTW Raiser Mobile Number`, rd.SupervisorName as `Supervisor Name`, rd.SupervisorAadhar as `Supervisor Aadhar Card Number`, rd.SupervisorMobile as `Supervisor Mobile`, rd.SuperVisorWhatsapp as `Supervisor Whatsapp`, sd.WorkStartDateTime as `Site Assessment Datetime`, se.SiteEvaluateDatetime as `Site Risk Assessment Datetime`, rd.IsPoAvailable as `Is PO Available ?`, rd.PoNumber as `PO Number`, rd.NoOfWorkersRequiredAtRaiserStage as `No of Workers required at raiser stage`, ad.AppByName as `Approved by Name`, ad.AppByMobile as `Mobile Number`, ad.AppRemark as `Remarks by Approver`, ad.AssignTechNameMobile as `Assign Technician Mobile and Name`, sd.TotalCheckpoint as `PTW Check list Total Points`, sd.YesCount as `PTW Check list Yes Points Counts`, sd.NoCount as `PTW Check list No Points Counts`, sd.NaCount as `PTW Check list NA Points Counts`, se.RiskCount as `Risk Level Initial count`, se.RiskAppBy as `Risk Level Approved by Name`, pc.ReasonForClosure as `Reason for Closure`, sd.TotalWorker as `Total Workers`, aud.FirstAuditBy as `Auditer Name 1`, aud.FirstMobile as `Auditer Mobile No 1`, aud.FirstEmpRole as `Auditer Role 1`, aud.FirstCircle as `Circle of Auditer 1`, aud.FirstAudDatetime as `Audit Date 1`, aud.FirstModeOfAudit as `Mode of Audit 1`, aud.FirstObservation as `Audit Remarks 1`, aud.SecondAuditBy as `Auditer Name 2`, aud.SecondMobile as `Auditer Mobile No 2`, aud.SecondEmpRole as `Auditer Role 2`, aud.SecondCircle as `Circle of Auditer 2`, aud.SecondAudDatetime as `Audit Date 2`, aud.SecondModeOfAudit as `Mode of Audit 2`, aud.SecondObservation as `Audit Remarks 2`, aud.ThirdAuditBy as `Auditer Name 3`, aud.ThirdMobile as `Auditer Mobile No 3`, aud.ThirdEmpRole as `Auditer Role 3`, aud.ThirdCircle as `Circle of Auditer 3`, aud.ThirdAudDatetime as `Audit Date 3`, aud.ThirdModeOfAudit as `Mode of Audit 3`, aud.ThirdObservation as `Audit Remarks 3`, aud.FourthAuditBy as `Auditer Name 4`, aud.FourthMobile as `Auditer Mobile No 4`, aud.FourthEmpRole as `Auditer Role 4`, aud.FourthCircle as `Circle of Auditer 4`, aud.FourthAudDatetime as `Audit Date 4`, aud.FourthModeOfAudit as `Mode of Audit 4`, aud.FourthObservation as `Audit Remarks 4`, aud.FifthAuditBy as `Auditer Name 5`, aud.FifthMobile as `Auditer Mobile No 5`, aud.FifthEmpRole as `Auditer Role 5`, aud.FifthCircle as `Circle of Auditer 5`, aud.FifthAudDatetime as `Audit Date 5`, aud.FifthModeOfAudit as `Mode of Audit 5`, aud.FifthObservation as `Audit Remarks 5`  FROM PTWRaiseDetails rd left join PTWActivityType aty on rd.ActivityId = aty.ActivityId left join PTWApprovedDetails ad on rd.ActivityId = ad.ActivityId left join PTWStartDetails sd on rd.ActivityId = sd.ActivityId left join PTWSiteEvaluate se on rd.ActivityId = se.ActivityId left join PTWClosure pc on ad.ActivityId = pc.ActivityId left join PTWAuditDetails aud on ad.ActivityId = aud.ActivityId left join PTWAudits audd on ad.ActivityId = audd.ActivityId and audd.AuditStatus = 'Reject' where 1=1";

	// 	// $sql = "SELECT rd.ActivityId, sd.ApproverActivityId, rd.Circle, rd.Site_Id as `Site Id`, rd.Site_Name as `Site Name`, rd.Site_CAT as `Site Category`, rd.Site_Type as `Site Type`, rd.PtwType as `PTW Type`, aty.ActivityType as `Activity`, rd.PtwRaiseDate as `PTW Raise Date`, rd.PtwRaiseTime as `PTW Raise Time`, rd.WorkStartDate as `Work Start Date`, rd.WorkStartTime as `Work Start Time`, rd.WorkEndDate as `Work End Date`, rd.WorkEndtime as `Work End Time`, (case when rd.PtwStatus = 'Approved by' or rd.PtwStatus = 'Rejected by' then concat(rd.PtwStatus,' ',ad.AppByRole) else rd.PtwStatus end) as `Status of PTW`, sd.StatusStageDate as `Status stage Date`, sd.StatusStageTime as `Status stage Time`, (case when rd.PtwStatus = 'Cancel by Auditor' then audd.Observation when (rd.PtwStatus = 'Rejected by' or rd.PtwStatus = 'Cancelled') then ad.AppRemark else ad.ReasonOfCancel end)  as `Reason of Rejection`, rd.VendorName as `Vendor Name`, rd.PartnerType as `Partner Type`, rd.PtwRaiserName as `PTW Raiser Name`, rd.PtwRaiserMobileNo as `PTW Raiser Mobile Number`, rd.SupervisorName as `Supervisor Name`, rd.SupervisorAadhar as `Supervisor Aadhar Card Number`, rd.SupervisorMobile as `Supervisor Mobile`, rd.SuperVisorWhatsapp as `Supervisor Whatsapp`, ad.AppByName as `Approved by Name`, ad.AppByMobile as `Mobile Number`, ad.AppRemark as `Remarks by Approver`, sd.WorkStartDate as `Site Assessment Date`, sd.WorkStartTime as `Site Assessment Time`, se.SiteEvaluateDate as `Site Risk Assessment Date`, se.SiteEvaluateTime as `Site Risk Assessment Time`, rd.IsPoAvailable as `Is PO Available ?`, rd.PoNumber as `PO Number`, rd.NoOfWorkersRequiredAtRaiserStage as `No of Workers required at raiser stage`, ad.AssignTechNameMobile as `Assign Technician Mobile and Name`, sd.TotalCheckpoint as `PTW Check list Total Points`, sd.YesCount as `PTW Check list Yes Points Counts`, sd.NoCount as `PTW Check list No Points Counts`, sd.NaCount as `PTW Check list NA Points Counts`, se.RiskCount as `Risk Level Initial count`, se.RiskAppBy as `Risk Level Approved by Name`, pc.ReasonForClosure as `Reason for Closure`, sd.TotalWorker as `Total Workers`, aud.FirstAuditBy as `Auditer Name 1`, aud.FirstMobile as `Auditer Mobile No 1`, aud.FirstEmpRole as `Auditer Role 1`, aud.FirstCircle as `Circle of Auditer 1`, aud.FirstAudDate as `Audit Date 1`, aud.FirstAudTime as `Audit Time 1`, aud.FirstModeOfAudit as `Mode of Audit 1`, aud.FirstObservation as `Audit Remarks 1`, aud.SecondAuditBy as `Auditer Name 2`, aud.SecondMobile as `Auditer Mobile No 2`, aud.SecondEmpRole as `Auditer Role 2`, aud.SecondCircle as `Circle of Auditer 2`, aud.SecondAudDate as `Audit Date 2`, aud.SecondAudTime as `Audit Time 2`, aud.SecondModeOfAudit as `Mode of Audit 2`, aud.SecondObservation as `Audit Remarks 2`, aud.ThirdAuditBy as `Auditer Name 3`, aud.ThirdMobile as `Auditer Mobile No 3`, aud.ThirdEmpRole as `Auditer Role 3`, aud.ThirdCircle as `Circle of Auditer 3`, aud.ThirdAudDate as `Audit Date 3`, aud.ThirdAudTime as `Audit Time 3`, aud.ThirdModeOfAudit as `Mode of Audit 3`, aud.ThirdObservation as `Audit Remarks 3`, aud.FourthAuditBy as `Auditer Name 4`, aud.FourthMobile as `Auditer Mobile No 4`, aud.FourthEmpRole as `Auditer Role 4`, aud.FourthCircle as `Circle of Auditer 4`, aud.FourthAudDate as `Audit Date 4`, aud.FourthAudTime as `Audit Time 4`, aud.FourthModeOfAudit as `Mode of Audit 4`, aud.FourthObservation as `Audit Remarks 4`, aud.FifthAuditBy as `Auditer Name 5`, aud.FifthMobile as `Auditer Mobile No 5`, aud.FifthEmpRole as `Auditer Role 5`, aud.FifthCircle as `Circle of Auditer 5`, aud.FifthAudDate as `Audit Date 5`, aud.FifthAudTime as `Audit Time 5`, aud.FifthModeOfAudit as `Mode of Audit 5`, aud.FifthObservation as `Audit Remarks 5`  FROM PTWRaiseDetails rd left join PTWActivityType aty on rd.ActivityId = aty.ActivityId left join PTWApprovedDetails ad on rd.ActivityId = ad.ActivityId left join PTWStartDetails sd on rd.ActivityId = sd.ActivityId left join PTWSiteEvaluate se on rd.ActivityId = se.ActivityId left join PTWClosure pc on ad.ActivityId = pc.ActivityId left join PTWAuditDetails aud on ad.ActivityId = aud.ActivityId left join PTWAudits audd on ad.ActivityId = audd.ActivityId and audd.AuditStatus = 'Reject' where 1=1";
		
	// 	if($fromDate != ""){
	// 		$sql .= " and rd.WorkStartDate >= '$fromDate'";
	// 	}
	// 	if($toDate != ""){
	// 		$sql .= " and rd.WorkStartDate <= '$toDate'";
	// 	}
	// 	$sql .= " order by rd.ActivityId desc";
		
	// 	$result = mysqli_query($conn,$sql);
	// 	$row=mysqli_fetch_assoc($result);
	// 	$columnName = array();
	// 	foreach ($row as $key => $value) {
	// 		if($key !="ApproverActivityId"){
	// 			array_push($columnName, $key);
	// 		}
	// 	}

	// 	// -- Start --
	// 	// $chkArr = [];
	// 	// $actSql = "SELECT * FROM `PTWCheckpoints` order by `ID`";
	// 	// $actResult = mysqli_query($conn,$actSql);
	// 	// while($actRow = mysqli_fetch_assoc($actResult)){
	// 	// 	$actType = $actRow["ActType"];
	// 	// 	$chkId = $actRow["CheckpointId"];
	// 	// 	array_push($chkArr, $chkId);

	// 	// 	$j=1;
	// 	// 	$chkDescSql = "SELECT `Description` FROM `Checkpoints` where `CheckpointId` in ($chkId) ORDER BY FIELD(`CheckpointId`,$chkId)";
	// 	// 	$chkDescQuery = mysqli_query($conn,$chkDescSql);
	// 	// 	while($chkDescRow = mysqli_fetch_assoc($chkDescQuery)){
	// 	// 		$desc = $actType.'.'.$j.' - '.$chkDescRow["Description"];
	// 	// 		array_push($columnName, $desc);
	// 	// 		$j++;
	// 	// 	}
	// 	// }
	// 	// -- End --
	// 	fputcsv($output,$columnName);
		

	// 	mysqli_data_seek($result, 0);
	// 	while($row=mysqli_fetch_assoc($result)){
	// 		$exportData = array();
	// 		foreach ($columnName as $key => $value) {
	// 			if(strpos($value, ".") == false){
	// 				array_push($exportData, $row[$value]);
	// 			}
	// 		}

	// 		// -- Start --
	// 		// $actChkId = "";
	// 		// $appActId = $row["ApproverActivityId"];
	// 		// $act = $row["PTW Type"];
	// 		// if($act == "Height"){
	// 		// 	$actChkId = $chkArr[0];
	// 		// }
	// 		// else if($act == "Electrical"){
	// 		// 	$actChkId = $chkArr[1];

	// 		// 	$heiChk = explode(",", $chkArr[0]);
	// 		// 	for($i=0;$i<count($heiChk);$i++){
	// 		// 		array_push($exportData, "-");
	// 		// 	}
	// 		// }
	// 		// else if($act == "Material Handling"){
	// 		// 	$actChkId = $chkArr[2];

	// 		// 	$heiChk = explode(",", $chkArr[0]);
	// 		// 	for($i=0;$i<count($heiChk);$i++){
	// 		// 		array_push($exportData, "-");
	// 		// 	}

	// 		// 	$eleChk = explode(",", $chkArr[1]);
	// 		// 	for($j=0;$j<count($eleChk);$j++){
	// 		// 		array_push($exportData, "-");
	// 		// 	}
	// 		// }
	// 		// else if($act == "OFC-Route Work"){
	// 		// 	$actChkId = $chkArr[3];

	// 		// 	$heiChk = explode(",", $chkArr[0]);
	// 		// 	for($i=0;$i<count($heiChk);$i++){
	// 		// 		array_push($exportData, "-");
	// 		// 	}

	// 		// 	$eleChk = explode(",", $chkArr[1]);
	// 		// 	for($j=0;$j<count($eleChk);$j++){
	// 		// 		array_push($exportData, "-");
	// 		// 	}

	// 		// 	$matChk = explode(",", $chkArr[2]);
	// 		// 	for($k=0;$k<count($matChk);$k++){
	// 		// 		array_push($exportData, "-");
	// 		// 	}
	// 		// }
	// 		// else if($act == "Confined Space Work"){
	// 		// 	$actChkId = $chkArr[4];

	// 		// 	$heiChk = explode(",", $chkArr[0]);
	// 		// 	for($i=0;$i<count($heiChk);$i++){
	// 		// 		array_push($exportData, "-");
	// 		// 	}

	// 		// 	$eleChk = explode(",", $chkArr[1]);
	// 		// 	for($j=0;$j<count($eleChk);$j++){
	// 		// 		array_push($exportData, "-");
	// 		// 	}

	// 		// 	$matChk = explode(",", $chkArr[2]);
	// 		// 	for($k=0;$k<count($matChk);$k++){
	// 		// 		array_push($exportData, "-");
	// 		// 	}

	// 		// 	$ofcChk = explode(",", $chkArr[3]);
	// 		// 	for($l=0;$l<count($ofcChk);$l++){
	// 		// 		array_push($exportData, "-");
	// 		// 	}
	// 		// }
	// 		// else if($act == "Hot Work"){
	// 		// 	$actChkId = $chkArr[5];

	// 		// 	$heiChk = explode(",", $chkArr[0]);
	// 		// 	for($i=0;$i<count($heiChk);$i++){
	// 		// 		array_push($exportData, "-");
	// 		// 	}

	// 		// 	$eleChk = explode(",", $chkArr[1]);
	// 		// 	for($j=0;$j<count($eleChk);$j++){
	// 		// 		array_push($exportData, "-");
	// 		// 	}

	// 		// 	$matChk = explode(",", $chkArr[2]);
	// 		// 	for($k=0;$k<count($matChk);$k++){
	// 		// 		array_push($exportData, "-");
	// 		// 	}

	// 		// 	$ofcChk = explode(",", $chkArr[3]);
	// 		// 	for($l=0;$l<count($ofcChk);$l++){
	// 		// 		array_push($exportData, "-");
	// 		// 	}

	// 		// 	$conChk = explode(",", $chkArr[4]);
	// 		// 	for($m=0;$m<count($conChk);$m++){
	// 		// 		array_push($exportData, "-");
	// 		// 	}
	// 		// }
	// 		// else if($act == "Site Access"){
	// 		// 	$actChkId = $chkArr[6];

	// 		// 	$heiChk = explode(",", $chkArr[0]);
	// 		// 	for($i=0;$i<count($heiChk);$i++){
	// 		// 		array_push($exportData, "-");
	// 		// 	}

	// 		// 	$eleChk = explode(",", $chkArr[1]);
	// 		// 	for($j=0;$j<count($eleChk);$j++){
	// 		// 		array_push($exportData, "-");
	// 		// 	}

	// 		// 	$matChk = explode(",", $chkArr[2]);
	// 		// 	for($k=0;$k<count($matChk);$k++){
	// 		// 		array_push($exportData, "-");
	// 		// 	}

	// 		// 	$ofcChk = explode(",", $chkArr[3]);
	// 		// 	for($l=0;$l<count($ofcChk);$l++){
	// 		// 		array_push($exportData, "-");
	// 		// 	}

	// 		// 	$conChk = explode(",", $chkArr[4]);
	// 		// 	for($m=0;$m<count($conChk);$m++){
	// 		// 		array_push($exportData, "-");
	// 		// 	}

	// 		// 	$hotChk = explode(",", $chkArr[5]);
	// 		// 	for($n=0;$n<count($hotChk);$n++){
	// 		// 		array_push($exportData, "-");
	// 		// 	}
	// 		// }


	// 		// if($appActId != null){
	// 		// 	$detSql = "SELECT `Value` FROM `TransactionDTL` where `ActivityId` = $appActId and `ChkId` in ($actChkId) ORDER by FIELD(`ChkId`,$actChkId)";
	// 		// 	$detResult = mysqli_query($conn,$detSql);
	// 		// 	while($detRow = mysqli_fetch_assoc($detResult)){
	// 		// 		$val = $detRow["Value"];
	// 		// 		array_push($exportData, $val);
	// 		// 	}
	// 		// }
	// 		// -- End --
	// 		fputcsv($output, $exportData);

	// 	}
	// }
	// // Management Visit
	// else if($reportType == 17){
	// 	$fileName = "";
	// 	$chkId = "";

	// 	$menuId = $jsonData->menuId;
	// 	$sql = "SELECT `ActivityId`, `Circle`, `Site Id`, `Site Name`, `Employee Name`, `Submit Datetime`, `MenuName`, `ReportCheckpointId` FROM `Mgmt_Visit_Report` where `MenuId` = $menuId ";
	// 	if($fromDate != "")
	// 		$sql .= "and date(`Submit Datetime`) >= '$fromDate' ";
	// 	if($toDate != "")
	// 		$sql .= "and date(`Submit Datetime`) <= '$toDate' ";
	// 	$sql .= "order by `ActivityId` desc";

	// 	$result = mysqli_query($conn,$sql);
	// 	$row = mysqli_fetch_assoc($result);
	// 	$columnName = array();
	// 	foreach ($row as $key => $value) {
	// 		if($key == "ReportCheckpointId"){
	// 			$chkId = $value;
	// 		}
	// 		else if($key == "MenuName"){
	// 			$fileName = $value;
	// 		}
	// 		else{
	// 			array_push($columnName, $key);
	// 		}
	// 	}

	// 	header('Content-Type: text/csv; charset=utf-8');
	// 	header('Content-Disposition: attachment; filename='.$fileName.'.csv');
	// 	$output = fopen('php://output', 'w');

	// 	$ii=1;
	// 	$chkDescSql = "SELECT `Description` FROM `Checkpoints` where `CheckpointId` in ($chkId) ORDER BY FIELD(`CheckpointId`,$chkId)";
	// 	$chkDescQuery = mysqli_query($conn,$chkDescSql);
	// 	while($chkDescRow = mysqli_fetch_assoc($chkDescQuery)){
	// 		$desc = $ii.' - '.$chkDescRow["Description"];
	// 		array_push($columnName, $desc);
	// 		$ii++;
	// 	}
	// 	fputcsv($output,$columnName);

	// 	mysqli_data_seek($result, 0);

	// 	while($row=mysqli_fetch_assoc($result)){
	// 		$exportData = array();
	// 		$actId = $row["ActivityId"];
	// 		foreach ($columnName as $key => $value) {
	// 			if(strpos($value, " - ") == false){
	// 				array_push($exportData, $row[$value]);
	// 			}
	// 		}

	// 		$detSql = "SELECT `Value` FROM `TransactionDTL` where `ActivityId` = $actId and `ChkId` in ($chkId) ORDER by FIELD(`ChkId`,$chkId)";
	// 		$detResult = mysqli_query($conn,$detSql);
	// 		while($detRow = mysqli_fetch_assoc($detResult)){
	// 			$val = $detRow["Value"];
	// 			array_push($exportData, $val);
	// 		}
	// 		fputcsv($output, $exportData);
	// 	}

	// }
// }

?>
<?php
header('Content-Type: text/html');
function unauthorizedAccess(){
	echo "<h1>Session Expired.</h1>";
}
?>