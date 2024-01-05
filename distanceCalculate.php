<?php
include("dbConfiguration.php");
require 'AddressByLatLongClass.php';
$api_key = "AIzaSyDkCjzv4fVu7wlsp31Tu0AnpbyQaxm4Kz8";

$yesterdayDate = date('Y-m-d', strtotime('-1 day'));
// $filterEmpId = 'tr033';
// $yesterdayDate = '2024-01-04';

// $delSql = "DELETE FROM `DistanceTravel` where `Emp_Id` = '$filterEmpId' and `Visit_Date` = '$yesterdayDate'";
$delSql = "DELETE FROM `DistanceTravel` where `Visit_Date` = '$yesterdayDate'";
mysqli_query($conn,$delSql);
$successArr = array();
$errorArr = array();

// $sql = "SELECT `EmpId` FROM `EmployeeMaster` WHERE `EmpId`='$filterEmpId' and `IsActive`=1 and `Tenent_Id`=1 ";
$sql = "SELECT `EmpId` FROM `EmployeeMaster` WHERE `IsActive`=1 and `Tenent_Id`=1 ";
$query=mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
	$empId = $row["EmpId"];

	$sql="SELECT a.ActivityId, a.EmpId, a.MenuId, a.GeoLocation, a.Event, a.MobileDateTime from Activity a, (SELECT a.EmpId, date(a.MobileDateTime) as AttendanceDate, min(case when a.Event='Start' then a.ActivityId end) as StartActId, max(case when a.Event='Stop' then a.ActivityId end) StopActId FROM Activity a where a.EmpId='$empId' and date(a.MobileDateTime)='$yesterdayDate' and  a.Event in ('Start','Stop') GROUP by a.EmpId, date(a.MobileDateTime)) t where a.ActivityId >= t.StartActId and a.ActivityId <= t.StopActId and a.EmpId=t.EmpId and a.Event in ('Start','periodicData','Submit','Stop') order by a.ActivityId";

	// echo $sql.' --- ';

	$rsVisit = mysqli_query($conn,$sql);
	$cnt=0;
	$origin="";
	$distinations="";
	while($rowV=mysqli_fetch_assoc($rsVisit))
	{
		$event=$rowV['Event'];
		$mobileDateTime=$rowV['MobileDateTime'];
		$actId=$rowV['ActivityId'];
		$geoLocation = str_replace("/", ",", $rowV['GeoLocation']);
		$latitude= explode(",", $geoLocation)[0] ;
		$longitude= explode(",", $geoLocation)[1];
		$address = NULL;
		if($event == "Start" || $event == "Submit" || $event == "Stop"){
			$classObj = new AddressByLatLongClass();
			$address = $classObj->getAddressByLatLong($latitude, $longitude);
			// $address = "";
		}
		$cnt=$cnt+1;
		if($cnt==1)
		{
			$origin=$latitude.",".$longitude;
			$origin_lat=$latitude;
			$origin_long=$longitude;
			$dest_lat=$latitude;
			$dest_long=$longitude;
			$distance = 0;
			if($origin_lat != $dest_lat){
				$distinations=$latitude.",".$longitude;
				$url='https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins='.$origin.'&destinations='.$distinations.'&key='.$api_key;
				$json_data=file_get_contents($url);	
				$distance=fnlGetDistance($json_data);
			}
			// echo $json_data.'1--------';
			// echo $distance.', ';
			$distanceSql = "INSERT into `DistanceTravel` (`Activity_Id`, `Emp_Id`, `Visit_Date`, `Visit_Date_Time`, `Latitude_Start`, `Longitude_Start`, `Latitude_End`, `Longitude_End`, `Address`, `Distance_KM`, `Event`) values ('$actId', '$empId', '$yesterdayDate', '$mobileDateTime', '$origin_lat', '$origin_long', '$dest_lat', '$dest_long', '$address', '$distance', '$event')";
			if(mysqli_query($conn,$distanceSql)){
				array_push($successArr, $empId);
			}
			else{
				array_push($errorArr, $empId);
			}
		}
		else
		{
			if($latitude!="0")
			{
				$dest_lat=$latitude;
				$dest_long=$longitude;
				$distance = 0;
				if($origin_lat != $dest_lat){
					$distinations=$latitude.",".$longitude;
					$url='https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins='.$origin.'&destinations='.$distinations.'&key='.$api_key;
					$json_data=file_get_contents($url);	
					$distance=fnlGetDistance($json_data);
				}
				// echo $json_data.'2--------';
				// echo $distance.', ';
				$distanceSql = "INSERT into `DistanceTravel` (`Activity_Id`, `Emp_Id`, `Visit_Date`, `Visit_Date_Time`, `Latitude_Start`, `Longitude_Start`, `Latitude_End`, `Longitude_End`, `Address`, `Distance_KM`, `Event`) values ('$actId', '$empId', '$yesterdayDate', '$mobileDateTime', '$origin_lat', '$origin_long', '$dest_lat', '$dest_long', '$address', '$distance', '$event')";
				if(mysqli_query($conn,$distanceSql)){
					array_push($successArr, $empId);
				}
				else{
					array_push($errorArr, $empId);
				}
				$origin=$latitude.",".$longitude;
				$origin_lat=$latitude;
				$origin_long=$longitude;
				
			}
		}
	}
}

$output = new StdClass;
$output -> distanceResponse = array('date' => $yesterdayDate, 'successArr' => $successArr, 'errorArr' => $errorArr);
echo json_encode($output);

// file_put_contents('/var/www/trinityapplab.in/html/Company/log/distanceCalculatelog_'.date("Y").'.log', json_encode($output)."\n", FILE_APPEND);

?>
<?php
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
