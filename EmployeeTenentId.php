<?php
class EmployeeTenentId{
	function getTenentIdByEmpId($conn, $empId) {
		$sql = "SELECT * FROM `EmployeeMaster` where `EmpId` = '$empId' and `IsActive` = 1 ";
		$result = mysqli_query($conn,$sql);
		$tenentId  = 0;
		while($row = mysqli_fetch_assoc($result)){
			$tenentId = $row["Tenent_Id"];
		}
		return $tenentId;
	}
	function getTenentIdByMobile($conn, $mobile) {
		$sql = "SELECT * FROM `EmployeeMaster` where `Mobile` = '$mobile' and `IsActive` = 1 ";
		$result = mysqli_query($conn,$sql);
		$tenentId  = 0;
		while($row = mysqli_fetch_assoc($result)){
			$tenentId = $row["Tenent_Id"];
		}
		return $tenentId;
	}
	function getEmployeeDetails($conn, $empId){
		$sql = "SELECT * FROM `EmployeeMaster` where `EmpId` = '$empId' and `IsActive` = 1 ";
		$result = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($result)){
			$output = array('empName' => $row["Name"], 'tenentId' => $row["Tenent_Id"]);
		}
		return json_encode($output);
	}
}
?>