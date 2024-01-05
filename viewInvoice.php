<?php 
$invoiceId = $_REQUEST["invoiceId"];
$invStatus = $_REQUEST["invStatus"];

$dir = "Invoice";
$pdfFileName = "Inv-".$invoiceId.'.pdf';
if($invStatus == 1){
	$pdfFileName = "Inv-Sign-".$invoiceId.'.pdf';
}
$filePath = "/var/www/trinityapplab.in/html/Company/files/".$dir."/".$pdfFileName;
header("Content-type: application/pdf");
header("Content-Disposition: inline; filename=".$pdfFileName);
@readfile($filePath);
?>