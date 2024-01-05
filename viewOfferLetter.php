<?php
$mobile = $_REQUEST["mobile"];

$dir = "OfferLetter";
$pdfFileName = $mobile.".pdf";
$filePath = "/var/www/trinityapplab.in/html/Company/files/".$dir."/".$pdfFileName;
header("Content-type: application/pdf");
header("Content-Disposition: inline; filename=".$pdfFileName);
@readfile($filePath);
?>
