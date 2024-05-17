<?php
include("dbConfiguration.php");
require 'AddressByLatLongClass.php';
$api_key = "[api_key]";

$yesterdayDate = date('Y-m-d', strtotime('-1 day'));
// $filterEmpId = 'tr033';
// $yesterdayDate = '2024-01-03';

$delSql = "DELETE FROM `DistanceTravel` where `Visit_Date` = '$yesterdayDate'";
// $delSql = "DELETE FROM `DistanceTravel` where `Emp_Id` = '$filterEmpId' and `Visit_Date` = '$yesterdayDate'";
mysqli_query($conn,$delSql);
$successArr = array();
$errorArr = array();

// $sql = "SELECT `EmpId` FROM `EmployeeMaster` WHERE `EmpId`='$filterEmpId' and `IsActive`=1 and `Tenent_Id`=1 ";
// $sql = "SELECT `EmpId` FROM `EmployeeMaster` WHERE `RoleId`=5 and `IsActive`=1 and `Tenent_Id`=1 ";
$sql = "SELECT `EmpId` FROM `EmployeeMaster` WHERE `IsActive`=1 and `Tenent_Id`=1 ";
$query=mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
	$empId = $row["EmpId"];

	$startSql = "SELECT `ActivityId`, `EmpId`, `MenuId`, `GeoLocation`, `Event`, `MobileDateTime` FROM `Activity` where `EmpId`='$empId' and `Event`='Start' and date_format(`MobileDateTime`,'%Y-%m-%d') = '$yesterdayDate' order by `ActivityId` limit 0,1";
	$startQuery = mysqli_query($conn,$startSql);
	$startRowCount = mysqli_num_rows($startQuery);
	if($startRowCount !=0){
		$startRow = mysqli_fetch_assoc($startQuery);
		$startActId = $startRow["ActivityId"];
		$startDatetime = $startRow["MobileDateTime"];
		
		$stopSql="SELECT `ActivityId`, `EmpId`, `MenuId`, `GeoLocation`, `Event`, `MobileDateTime` FROM `Activity` where `EmpId`='$empId' and `Event`='Stop' and date_format(`MobileDateTime`,'%Y-%m-%d') = '$yesterdayDate' order by `ActivityId` desc limit 0,1";
		$stopQuery = mysqli_query($conn,$stopSql);
		$stopRowCount = mysqli_num_rows($stopQuery);
		$stopDatetime = "";
		$stopActId = "";
		if($stopRowCount !=0){
			$stopRow = mysqli_fetch_assoc($stopQuery);
			$stopActId = $stopRow["ActivityId"];
			$stopDatetime = $stopRow["MobileDateTime"];
		}
		else{
			$stopActId = 0;
			$stopDatetime = $yesterdayDate." 22:00:00"; 
		}

		// $actSql="SELECT `ActivityId`, `EmpId`, `MenuId`, `GeoLocation`, `Event`, `MobileDateTime` FROM `Activity` where `EmpId`='$empId' and `Event` in ('periodicData','Submit') and `MobileDateTime`>='$startDatetime' and `MobileDateTime`<='$stopDatetime' order by `MobileDateTime`";
		$actSql="SELECT `ActivityId`, `EmpId`, `MenuId`, `GeoLocation`, `Event`, `MobileDateTime` FROM `Activity` where `ActivityId`>=$startActId and `ActivityId`<=$stopActId and `EmpId`='$empId' and `Event` in ('periodicData','Submit') order by `ActivityId`";


		$sql = '('.$startSql.') UNION ALL ('.$actSql.') UNION ALL ('.$stopSql.')';

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
}

$output = new StdClass;
$output -> distanceResponse = array('date' => $yesterdayDate, 'successArr' => $successArr, 'errorArr' => $errorArr);

// $taDaResponse = CallAPI("GET","http://www.trinityapplab.co.in/NVGroup/taDaReport.php?yesterdayDate=$yesterdayDate","");
// $output -> taDaResponse = json_decode($taDaResponse);
echo json_encode($output);

// file_put_contents('/var/www/trinityapplab.co.in/NVGroup/log/distanceCalculatelog_'.date("Y").'.log', json_encode($output)."\n", FILE_APPEND);

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
function CallAPI($method, $url, $data)
{
    $curl = curl_init();

    switch ($method)
    {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);
	//echo $result."\n";
    curl_close($curl);

    return $result;
}
?>
