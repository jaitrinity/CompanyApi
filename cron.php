<?php
// $path = dirname(__FILE__);
// echo $path;
// $cron = $path . "/startStopMail.php";
$cron = "https://trinityapplab.in/Company/startStopMail.php";
echo exec("19 11 * * * curl ".$cron);
?>