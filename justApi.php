<?php
$username = "demoapi";
$apiKey = "[apiKey]";
$senderId = "VRLIQS";
$mobile = "9557842898,9716744965";
$templateId = "";
$msg = "Your Total Sales Amount 45678 for VR LIQUORS - Sindhanur on . {#var#}";    //Message Here

$url = "https://text.justsms.co.in/api.php?username=$username&apikey=$apiKey&senderid=$senderId&mobile=$mobile&message=".urlencode($msg);    //Store data into URL variable

$ret = file($url);    //Call Url variable by using file() function

echo $ret;    //$ret stores the msg-id

?>