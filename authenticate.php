<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers:content-type");
include("dbConfiguration.php");
$json = file_get_contents('php://input');
$jsonData=json_decode($json);

$mobile = $jsonData->username;
$password = $jsonData->password;

$empArr = array();

$sql = "SELECT em.EmpId, em.Name, em.RoleId, rm.RoleName FROM EmployeeMaster em join RoleMaster rm on em.RoleId = rm.RoleId WHERE em.Mobile = '$mobile' and em.Password = BINARY('$password') and em.IsActive = 1 ";
$query = mysqli_query($conn,$sql);

if(mysqli_num_rows($query) != 0){
	while($row = mysqli_fetch_assoc($query)){
		$empId = $row["EmpId"];
		$empName = $row["Name"];
		
		$json = array(
			'empId' => $empId,
			'empName' => $empName,
			'empRoleId' => $row["RoleId"],
			'empRole' => $row["RoleName"]
		);
		array_push($empArr,$json);
	}
	$output = array();
	$output = array('responseCode' => '100000','responseDesc' => 'SUCCESSFUL','wrappedList' => $empArr);
	echo json_encode($output);
}
else{
	$output = array();
	$output = array('responseCode' => '102001','responseDesc' => 'Either mobile or password is incorrect, please try again.','wrappedList' => $empArr);
	echo json_encode($output);
}

?>