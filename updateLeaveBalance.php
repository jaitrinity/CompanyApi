<?php
include("dbConfiguration.php");
require 'LeaveBalanceClass.php';

$classObj = new LeaveBalanceClass();

$leaveIds=$_REQUEST['leaveIds']; // Commaseperate leave id..
$leaveIdList = explode(",", $leaveIds);
$remainLeaveList = array();
for($i=0;$i<count($leaveIdList);$i++){
	$leaveId = $leaveIdList[$i];
	$remainEmpLeave = $classObj->getLeaveBalance($leaveId);
	$remainLeaveJson = array('leaveId' => $leaveId, 'remainEmpLeave' => $remainEmpLeave);
	// echo $leaveId.'--';
	array_push($remainLeaveList, $remainLeaveJson);
}
echo json_encode($remainLeaveList);
?>