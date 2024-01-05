<?php 
include("dbConfiguration.php");
require 'SendMailClass.php';

$mobile = $_REQUEST["mobile"];
$action = $_REQUEST["action"];
$sql = "SELECT * FROM `OfferLetter` where `Mobile` = '$mobile' and `Status` !=0 ";
$query = mysqli_query($conn,$sql);
$rowCount = mysqli_num_rows($query);
if($rowCount != 0){
	$row = mysqli_fetch_assoc($query);
	$status = $row["Status"];
	if($status == 1)
		$msg = "<h1 style='color:green'>Offer already Accepted<h1>";
	else if($status == 2)
		$msg = "<h1 style='color:red'>Offer already Rejected</h1>";
	else if($status == 3)
		$msg = "<h1 style='color:yellow'>Offer expired<h1>";
}else{
	$updateSql = "UPDATE `OfferLetter` set `Status` = $action where `Mobile` = '$mobile'";
	$msg = "";
	if(mysqli_query($conn,$updateSql)){
		$sql = "SELECT * FROM `OfferLetter` where `Mobile` = '$mobile' ";
		$query = mysqli_query($conn,$sql);
		$row = mysqli_fetch_assoc($query);
		$name = $row["Name"];
		if($action == 1){
			$msg = "<h1 style='color:green'>Offer Accepted</h1>";
		}
		else{
			$msg = "<h1 style='color:red'>Offer Rejected</h1>";
		}

		$classObj = new SendMailClass();
		$subject = "Offer Letter";
		$mailMsg = "Dear Mam,"."<br>";
		$mailMsg .= "$msg by $name"."<br>";
		$mailMsg .= "Please do the needful.";
		$mailStatus = $classObj->sendMail("", $subject, $mailMsg, null);
	}
	else{
		$msg = "<h1 style='color:yellow'>Something went wrong</h1>";
	}

	
}
textToHtml($msg);
?>
<?php 
header('Content-Type: text/html');
function textToHtml($text){
	echo "$text";
	echo "<script>
	setInterval(function(){
		window.close();
	},5000);
	</script>";
}
?>