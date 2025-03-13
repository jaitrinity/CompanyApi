<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers:content-type");
include("dbConfiguration.php");
require 'EmployeeTenentId.php';
//echo 'hello';
$json = file_get_contents('php://input');
$jsonData=json_decode($json);

$empTenObj = new EmployeeTenentId();

// $tenentId=$jsonData->tenentId;
$mobile=$jsonData->mobile;
$tenentId=$empTenObj->getTenentIdByMobile($conn,$mobile);
$empName=$jsonData->empName;
if($empName == null) $empName = $jsonData->name;
$make=$jsonData->Make;
$model=$jsonData->Model;
$appVer=$jsonData->AppVer;
$os = $jsonData->os;
$token= $jsonData->token;
$osVer = $jsonData->osVer;
$networkType = $jsonData->networkType;

$empId = "";
$roleId = "";
$empRole = "";
$empName = "";
$empMobile = "";
$empEmailId = "";
$area = "";
$state = "";
$city = "";
$rmName = "";
$fieldUser = "";
$profileUrl = "";
$isActive = "";
$geofenceLatlong = "";
$geofenceDistance = "";
$isGeofence = "";
$attendanceStatus = "Stop";
$attendanceTime = "";
$otpCount = "";
$msgStatus = "";
$empStatus = "";
$otpStatus = "";
$output = "";

$confSql = "Select * from Configration";
$confQuery = mysqli_query($conn, $confSql);
$conf = mysqli_fetch_assoc($confQuery);
$sql = "SELECT e.*,r.RoleName as EmpRole,e1.Name as RMName,o.`OtpCount` FROM  EmployeeMaster e 
join RoleMaster r on (e.RoleId = r.RoleId) 
left join OTP o on (e.`Mobile` = o.`Mobile`) 
left join EmployeeMaster e1 on (e.RMId = e1.EmpId) 
WHERE e.`Mobile` = '$mobile' and e.`IsActive` = 1 ";
$query=mysqli_query($conn,$sql);
$rowcount=mysqli_num_rows($query);
if($rowcount > 0){
	$row = mysqli_fetch_assoc($query);
	$empId = $row['EmpId'];
	$roleId = $row['RoleId'];
	$empRole = $row["EmpRole"];
	$empName = $row["Name"];
	$empMobile = $row["Mobile"];
	$empEmailId = $row["EmailId"];
	$area = $row["Area"];
	$state = $row["State"];
	$city = $row["City"];
	$rmName = $row["RMName"];
	$fieldUser = $row['FieldUser'];
	$profileUrl = $row['ProfileURL'];
	$isActive = $row["IsActive"];
	$geofenceLatlong = $row["GeofenceLatlong"];
	$geofenceDistance = $row["GeofenceDistance"];
	$isGeofence = $row["IsGeofence"];

	$startStopSql = "SELECT a.Event, date_format(a.MobileDateTime,'%H:%i:%s') as AttTime FROM Activity a join EmployeeMaster e on a.EmpId = e.EmpId and e.IsActive = 1 where a.EmpId = '$empId' and a.Event in ('Start','Stop') ORDER by a.ActivityId DESC LIMIT 0,1";
	$startStopQuery = mysqli_query($conn, $startStopSql);
	$startStopRowcount=mysqli_num_rows($startStopQuery);
	if($startStopRowcount != 0){
		$startStopRow = mysqli_fetch_assoc($startStopQuery);
		$attendanceStatus = $startStopRow['Event'];
		if($attendanceStatus == 'Start')
			$attendanceTime = $startStopRow['AttTime'];
	}
	if($row['OtpCount'] == null ){
		$empStatus = "update";
		$otpStatus = "insert";
	}
	else if($row['OtpCount'] < $conf['OTPCount']){
		$empStatus = "update";
		$otpStatus = "update";
	}
	else{
		// failure
		$empStatus = "limitExceed";
	}
}
else{
	$empStatus = "";
}
if($empStatus == "insert"){
	$pass = substr($empName, 0,3).substr($mobile, 0,3);
	$empSql = "insert into `Employees` (`EmpId`,`Name`,`Password`,`Mobile`,`Tenent_Id`,`Registered`) values ('$mobile','$empName','$pass','$mobile',$tenentId,NOW())";
	mysqli_query($conn,$empSql);
	$empId = mysqli_insert_id($conn);
	$roleId = "1";
	$fieldUser = "1";	
}
else if($empStatus == "update"){
	// $empSql = "Update `Employees` set `Name` = '$empName',`Update` = NOW() where `Mobile` = '$mobile'";
	// $empSql = "Update `Employees` set `Update` = NOW() where `Mobile` = '$mobile'";
	// mysqli_query($conn,$empSql);
}
else if($empStatus == "limitExceed"){
	$output -> status = 'Limit Exceeded';
	$output -> code = 0;
	$output -> empId = $empId."";
	$output -> roleId = $roleId;
	echo json_encode($output);
	exit();
}

if($empStatus != ""){
	$taskotp = "";
	// Default OTP(1234) to given number. Check number in `DefaultOtpNumber` column of `configuration` table.
	$mobileStr = $conf["DefaultOtpNumber"];
	$mobileArr = explode(",", $mobileStr);
	if(in_array($mobile,$mobileArr)){
		$taskotp = 1234;	
	}
	else{
		$randomotp = rand(1000,9999);
		$taskotp = $randomotp;	
	}
	// for not send OTP to given number.
	if(in_array($mobile,$mobileArr)){
		$msgStatus = true;
	}
	else{

		require 'SendOtpClass.php';
		$classObj = new SendOtpClass();
		$otpResponse = $classObj->sendOtp($mobile, $taskotp, 'Trinity');
		$responseData = json_decode($otpResponse);
		$msgStatus = $responseData->return;
	}
	
	if($msgStatus == true){
		if($otpStatus == "insert"){
			$otpSql = "insert into `OTP` (`Mobile`,`OTP`,`OtpCount`) values ('$mobile', '$taskotp', 1)";
		}
		else if($otpStatus == "update"){
			$otpSql = " update `OTP` set `OTP` = '$taskotp', `OtpCount` = `OtpCount` + 1 where `Mobile_Number` = '$mobile' ";
		}
		mysqli_query($conn,$otpSql);
		$deviceId = "";
		$deviceStatus = "";
		$chkDeviceQuery = mysqli_query($conn,"select * from Devices where EmpId = '$empId' and Mobile = '$mobile' and Model = '$model'");
		if(mysqli_num_rows($chkDeviceQuery)>0)
		{
			//echo "updated";
			$deviceStatus = "Updated";
			$deviceSql = "Update Devices set Token = '$token', Name='$empName', Make = '$make', OS = '$os', OSVer = '$osVer',
			AppVer = '$appVer', NetworkType = '$networkType', Active = 1,`Update` = Now(), `Tenent_Id` = $tenentId
			where EmpId = '$empId' and Mobile = '$mobile' and Model = '$model'";
			//echo $deviceSql;
		}
		else
		{
			//echo "inserted";
			$deviceStatus = "Inserted";
			$deviceSql = "insert into Devices (`EmpId`,`Mobile`,`Token`,`Name`,`Make`,`Model`,`OS`,`OSVer`,`AppVer`,`NetworkType`,`Active`,`Registered`,`Update`,`Tenent_Id`)
			values ('$empId','$mobile','$token','$empName','$make','$model','$os','$osVer','$appVer','$networkType',1,Now(),Now(),$tenentId)";
										
		}

		// echo $deviceSql;
				
		if(mysqli_query($conn,$deviceSql)){
			if($deviceStatus == "Updated"){
				$dRow = mysqli_fetch_assoc($chkDeviceQuery);
				$deviceId = $dRow['DeviceId'];
			}
			else{
				$deviceId = mysqli_insert_id($conn);
			}

			$empConf = "SELECT * FROM `EmpProfileConfigration`";
			$empConfQuery = mysqli_query($conn, $empConf);
			$empConfRow = mysqli_fetch_assoc($empConfQuery);

			$output -> status = 'Success';
			$output -> code = 200;
			$output -> empId = $empId.','.$empConfRow["EmpId"];
			$output -> roleId = $roleId.','.$empConfRow["RoleId"];
			$output -> empRole = $empRole.','.$empConfRow["RoleName"];
			$output -> empName = $empName.','.$empConfRow["Name"];
			$output -> empMobile = $empMobile.','.$empConfRow["Mobile"];
			$output -> empEmailId = $empEmailId.','.$empConfRow["EmailId"];
			$output -> area = $area.','.$empConfRow["Area"];
			$output -> state = $state.','.$empConfRow["State"];
			$output -> city = $city.','.$empConfRow["City"];
			$output -> rmName = $rmName.','.$empConfRow["RmName"];
			$output -> fieldUser = $fieldUser.','.$empConfRow["FieldUser"];
			$output -> profileUrl = $profileUrl.','.$empConfRow["ProfileURL"];
			$output -> isActive = $isActive;
			$output -> attendanceStatus = $attendanceStatus;
			$output -> attendanceTime = $attendanceTime;
			$output -> geofenceLatlong = $geofenceLatlong;
			$output -> geofenceDistance = $geofenceDistance;
			$output -> isGeofence = $isGeofence;
			$output -> inf = $conf['Inf'];
			$output -> conn = $conf['Conf'];
			$output -> Start = $conf['Start'];
			$output -> End = $conf['End'];
			$output -> Battery = $conf['Battery'];
			$output -> did = "$deviceId";
			$output -> otp = $taskotp;
		}
		else{
			$output -> status = 'Device Failure';
			$output -> code = 0;
			$output -> empId = $empId."";
			$output -> roleId = $roleId;
		}
		
	}
	else{
		$output -> status = 'Otp Failure';
		$output -> code = 0;
		$output -> empId = $empId."";
		$output -> roleId = $roleId;
	}
}
else{
	$output -> status = 'No record found';
	$output -> code = 0;
}

echo json_encode($output);



	
?>