<?php 
$date = strtotime('01-'.$period);
$lastDate = date("Y-M-t", $date);
$paidDays = date("t", $date);
?>