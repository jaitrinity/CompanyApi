<?php 
include("dbConfiguration.php");
require('PDFGenerator/fpdf.php');

$todayDate = date('d-F-Y', strtotime('0 day'));
$expTodayDate = explode("-", $todayDate);
$todayDate = $expTodayDate[0].'th '.$expTodayDate[1].' '.$expTodayDate[2];

$methodType = $_SERVER['REQUEST_METHOD'];
if($methodType != "POST"){
	return;
}
$json = file_get_contents('php://input');
$jsonData = json_decode($json);
$invoiceId = $jsonData->invoiceId;

// $invoiceId = 1;
$sql = "SELECT cm.ContactPerson, cm.EmailId, cm.Mobile, im.PoNumber, cm.Name, cm.Address, cm.GSTNo, cm.PANNo, clm.Name as ClientName, clm.ContactPerson as ClientContact, clm.Address as ClientAddress, clm.GSTNo as ClientGST, cm.BankName, cm.AccountNo, cm.IfscNo, im.CGST, im.AfterCGST, im.SGST, im.AfterSGST, im.IGST, im.AfterIGST, im.GrantTotal FROM InvoiceMaster im join CorporateMaster cm on im.CorporateId = cm.CorporateId join ClientMaster clm on im.ClientId = clm.ClientId  where im.InvoiceId = $invoiceId";
$query = mysqli_query($conn,$sql);
$row = mysqli_fetch_assoc($query);

$contactPerson = $row["ContactPerson"];
$poNumber = $row["PoNumber"];
$emailId = $row["EmailId"];
$mobile = $row["Mobile"];
$name = $row["Name"];
$address = $row["Address"];
$expAdd = explode('---', $address);
$gst = $row["GSTNo"];
$pan = $row["PANNo"];
$clientName = $row["ClientName"];
$clientContact = $row["ClientContact"];
$clientAddress = $row["ClientAddress"];
$expCliAdd = explode('---', $clientAddress);
$clientGST = $row["ClientGST"];
$bankName = $row["BankName"];
$accountNo = $row["AccountNo"];
$ifscNo = $row["IfscNo"];
$cgst = $row["CGST"];
$afterCgst = $row["AfterCGST"];
$sgst = $row["SGST"];
$afterSgst = $row["AfterSGST"];
$igst = $row["IGST"];
$afterIgst = $row["AfterIGST"];
$grandTotal = $row["GrantTotal"];

$sql1 = "SELECT * FROM `InvoiceDescription` where `InvoiceId` = $invoiceId";
$query1 = mysqli_query($conn,$sql1);

class PDF extends FPDF
// class PDF extends PDF_Rotate
{
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Times','B',15);
$pdf->Cell(0,5,'Tax Invoice',0,0,'C');
$pdf->Ln(15);

$pdf->SetFont('Times','B',12);
$pdf->Cell(130,4,$contactPerson,0,0);
$pdf->Cell(60,4,'PO No : '.$poNumber,0,0);
$pdf->Ln(7);
$pdf->SetFont('Times','I',12);
$pdf->Cell(130,4,$emailId,0,0);
$pdf->Ln(7);
$pdf->Cell(130,4,$mobile,0,0);
$pdf->Ln(14);

$pdf->SetFont('Times','I',12);
$pdf->Cell(130,4,'Corporate Address:',0,0);
$pdf->Ln(7);
$pdf->SetFont('Times','B',12);
$pdf->Cell(130,4,$name,0,0);
$pdf->Cell(60,4,'Date : '.$todayDate,0,0);
$pdf->Ln(7);
$pdf->SetFont('Times','',12);
$pdf->Cell(130,4,$expAdd[0],0,0);
$pdf->Cell(60,4,'Invoice No : '.getFinancialYear().'/'.$invoiceId,0,0);
$pdf->Ln(7);
$pdf->Cell(130,4,$expAdd[1],0,0);
$pdf->Cell(60,4,'PAN No : '.$pan,0,0);
$pdf->Ln(7);
$pdf->Cell(130,4,'GST No : '.$gst,0,0);
$pdf->Ln(7);
$pdf->SetFont('Times','B',12);
$pdf->Cell(130,4,'SAC : 9983',0,0);
$pdf->SetFont('Times','',12);
$pdf->Ln(14);

$pdf->Cell(130,4,'Bill To : ',0,0);
$pdf->SetFont('Times','B',12);
$pdf->Cell(60,4,'Bank Details : ',0,0);
$pdf->SetFont('Times','',12);
$pdf->Ln(7);
$pdf->SetFont('Times','B',12);
$pdf->Cell(130,4,$clientName,0,0);
$pdf->SetFont('Times','',12);
$pdf->Cell(60,4,'Bank : '.$bankName,0,0);
$pdf->Ln(7);
$pdf->Cell(130,4,$clientContact,0,0);
$pdf->Cell(60,4,'A/C No : '.$accountNo,0,0);
$pdf->Ln(7);
$pdf->Cell(130,4,$expCliAdd[0],0,0);
$pdf->Cell(60,4,'IFSC No : '.$ifscNo,0,0);
$pdf->Ln(7);
$pdf->Cell(130,4,$expCliAdd[1],0,0);
$pdf->Ln(7);
$pdf->Cell(130,4,'GST No : '.$clientGST,0,0);
$pdf->Ln(14);

$pdf->SetFillColor(198,103,79);
$pdf->SetTextColor(255,255,255);
$pdf->SetDrawColor(198,103,79);
$pdf->SetFont('Times','B',12);
$pdf->Cell(80,14,'Description of Work',1,0,'C',true);
$pdf->Cell(40,14,'Quantity',1,0,'C',true);
$pdf->Cell(40,14,'Unit Price (Rs.)',1,0,'C',true);
$pdf->Cell(30,14,'Sub Total (Rs.)',1,0,'C',true);
$pdf->Ln(14);

$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Times','',12);

while($row1 = mysqli_fetch_assoc($query1)){
	$pdf->Cell(80,7,$row1["Description"],1,0,'C');
	$pdf->Cell(40,7,$row1["Quantity"],1,0,'C');
	$pdf->Cell(40,7,moneyFormatIndia($row1["UnitPrice"]),1,0,'C');
	$pdf->Cell(30,7,moneyFormatIndia($row1["SubTotal"]),1,0,'C');
	$pdf->Ln(7);
}

$pdf->Cell(190,25,'','LR',0,'C');
$pdf->Ln(25);

if($igst == 0){
	$pdf->Cell(80,7,'CGST @ '.$cgst.'%','LTR',0,'C');
	$pdf->Cell(40,7,'','LTR',0,'C');
	$pdf->Cell(40,7,'','LTR',0,'C');
	$pdf->Cell(30,7,moneyFormatIndia($afterCgst),'LTR',0,'C');
	$pdf->Ln(7);
	$pdf->Cell(80,7,'SGST @ '.$sgst.'%','LBR',0,'C');
	$pdf->Cell(40,7,'','LBR',0,'C');
	$pdf->Cell(40,7,'','LBR',0,'C');
	$pdf->Cell(30,7,moneyFormatIndia($afterSgst),'LBR',0,'C');
	$pdf->Ln(7);
}
else{
	$pdf->Cell(80,7,'IGST @ '.$igst.'%',1,0,'C');
	$pdf->Cell(40,7,'',1,0,'C');
	$pdf->Cell(40,7,'',1,0,'C');
	$pdf->Cell(30,7,moneyFormatIndia($afterIgst),1,0,'C');
	$pdf->Ln(7);
}

$pdf->SetFont('Times','B',12);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(80,14,'Grand Total (Rs.)',1,0,'C',true);
$pdf->SetFont('Times','',12);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(40,14,'','LTB',0,'C');
$pdf->Cell(40,14,'','TB',0,'C');
$pdf->Cell(30,14,moneyFormatIndia($grandTotal),'TRB',0,'C');
// $pdf->Output();
$dir = "Invoice";
$pdfFileName = "Inv-".$invoiceId.'.pdf';
if (!file_exists('/var/www/trinityapplab.in/html/Company/files/'.$dir)) {
    mkdir('/var/www/trinityapplab.in/html/Company/files/'.$dir, 0777, true);
}
$pdf->Output("/var/www/trinityapplab.in/html/Company/files/".$dir."/".$pdfFileName,"F");
?>

<?php 
function getFinancialYear(){
	$monthNumber = date('m');
	$currentYear = date('Y');
	$previousYear = $currentYear - 1;
	$nextYear = $currentYear + 1;
	$financialYear = "";

	if($monthNumber < 3){
		$financialYear = $previousYear." - ".$currentYear;
	}
	else{
		$financialYear = $currentYear." - ".$nextYear;
	}
	return $financialYear;
}
function moneyFormatIndia($num) {
    $explrestunits = "" ;
    if(strlen($num)>3) {
        $lastthree = substr($num, strlen($num)-3, strlen($num));
        $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits
        $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
        $expunit = str_split($restunits, 2);
        for($i=0; $i<sizeof($expunit); $i++) {
            // creates each of the 2's group and adds a comma to the end
            if($i==0) {
                $explrestunits .= (int)$expunit[$i].","; // if is first value , convert into integer
            } else {
                $explrestunits .= $expunit[$i].",";
            }
        }
        $thecash = $explrestunits.$lastthree;
    } else {
        $thecash = $num;
    }
    return $thecash; // writes the final format where $currency is the currency symbol.
}
?>