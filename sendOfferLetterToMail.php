<?php
include("dbConfiguration.php");
require('PDFGenerator/rotation.php');
require 'SendMailClass.php';

$methodType = $_SERVER['REQUEST_METHOD'];
if($methodType != "POST"){
	return;
}
$json = file_get_contents('php://input');
$jsonData = json_decode($json);
$mobile = $jsonData->mobile;

$date = date('d-F-Y', strtotime('0 day'));
$sql = "SELECT * FROM `OfferLetter` where `Mobile` = '$mobile'";
$query = mysqli_query($conn,$sql);
$row = mysqli_fetch_assoc($query);

$name = $row["Name"];
$mobile = $row["Mobile"];
$emailId = $row["EmailId"];
$designation = $row["Designation"];
$add1 = $row["AddressLine1"];
$add2 = $row["AddressLine2"];
$joinDate = $row["DOJ"];
$doj = date("d-F-Y", strtotime($joinDate));
$dayName = date('l', strtotime($joinDate));
$expDoj = explode("-", $doj);
$joinDate = $expDoj[0].'th '.$expDoj[1].' '.$expDoj[2];
$lpa = $row["LPA"];
$validDate = date('d-F-Y', strtotime('3 day'));
$expValiDate = explode("-", $validDate);
$validDate = $expValiDate[0].'th '.$expValiDate[1].' '.$expValiDate[2];

class PDF extends PDF_Rotate
{
	function Header()
	{
	    // Logo
	    $this->Image('files/offer_header.png', 40, 5, 130, 15);
	    // Line break
	    $this->Ln(20);

	    $this->SetFont('Arial','B',40);
		$this->SetTextColor(236,231,241);
		$this->RotatedText(20,200,'Trinity Mobile App Lab Pvt. Ltd.',35);

	}
	function RotatedText($x, $y, $txt, $angle)
	{
		//Text rotated around its origin
		$this->Rotate($angle,$x,$y);
		$this->Text($x,$y,$txt);
		$this->Rotate(0);
	}
	function Footer()
	{
		// Position at 1.5 cm from bottom
    	$this->SetY(-15);
	    $this->SetFont('Times','',12);
	    $this->Cell(95,5,'Office No: B-417, Noida One, B-8,',0);
		$this->Cell(95,5,'ayush.agarwal@trinityapplab.co.in',0,0,'R');
		$this->Ln(5);
		$this->Cell(95,5,'Sector-62, Noida (UP) - 201309.',0);
		$this->Cell(95,5,'www.trinityapplab.com',0,0,'R');

	}
}
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetTextColor(0);
$pdf->SetDrawColor(0);
$pdf->SetFont('Times','',12);
$pdf->Cell(0,5,'Date - '.$date,0);
$pdf->Ln(15);

$pdf->SetFont('Times','B',12);
// $pdf->Cell(0,5,'Mr. '.$name,0);
$pdf->Cell(0,5,'Dear '.$name,0);
$pdf->Ln(5);
$pdf->SetFont('Times','',12);
// $pdf->Cell(0,5,$designation,0);
// $pdf->Ln(5);
$pdf->MultiCell(0,5,$add1.PHP_EOL.$add2,0);
$pdf->Ln(10);

$pdf->SetFont('Times','B',12);
$pdf->Cell(0,5,'Offer Letter',0,0,'C');
$pdf->Ln(10);

$pdf->SetFont('Times','',12);
$pdf->Cell(0,5,'Dear '.$name.',',0);
$pdf->Ln(10);

$pdf->Cell(0,5,'Welcome to Trinity Mobile App Lab.',0);
$pdf->Ln(10);

// $pdf->MultiCell(0,5,'I am pleased to offer you employment in the position of '.$designation.' with Trinity Mobile App Lab Pvt. Ltd.',0);
$pdf->SetFont('Times','',12);
$pdf->write(5,'I am pleased to offer you employment in the position of ');
$pdf->SetFont('Times','B',12);
$pdf->write(5,$designation.' ');
$pdf->SetFont('Times','',12);
$pdf->write(5,'with ');
$pdf->SetFont('Times','B',12);
$pdf->write(5,'Trinity Mobile App Lab Pvt. Ltd. ');
$pdf->Ln(10);

// $pdf->MultiCell(0,5,'I am eager to have you as part of our team. I foresee your potential skills as a valuable contribution to our company and clients. Your appointment as '.$designation.' will commence on '.$joinDate.'.',0);
$pdf->SetFont('Times','',12);
$pdf->write(5,'I am eager to have you as part of our team. I foresee your potential skills as a valuable contribution to our company and clients. Your appointment as ');
$pdf->SetFont('Times','B',12);
$pdf->write(5,$designation.' ');
$pdf->SetFont('Times','',12);
$pdf->write(5,'will commence on ');
$pdf->SetFont('Times','B',12);
$pdf->write(5,$joinDate);
$pdf->SetFont('Times','',12);
$pdf->write(5,'.');
$pdf->Ln(10);

// $pdf->MultiCell(0,5,'As '.$designation.', you will be entitled to a yearly remuneration of Rs '.moneyFormatIndia($lpa).'/- ('.getIndianCurrency($lpa).') which indicates cost to company. Regular performance review will be conducted to assess your performance and suitability.',0);
$pdf->SetFont('Times','',12);
$pdf->write(5,'As ');
$pdf->SetFont('Times','B',12);
$pdf->write(5,$designation);
$pdf->SetFont('Times','',12);
$pdf->write(5,', you will be entitled to a yearly remuneration of ');
$pdf->SetFont('Times','B',12);
$pdf->write(5,'Rs '.moneyFormatIndia($lpa).'/- ('.getIndianCurrency($lpa) .'only) ');
$pdf->SetFont('Times','',12);
$pdf->write(5,'which indicates cost to company. Regular performance review will be conducted to assess your performance and suitability.');
$pdf->Ln(10);

// $pdf->MultiCell(0,5,'You are required to join us on '.$joinDate.', failing which this offer will be treated as cancelled.',0);
$pdf->SetFont('Times','',12);
$pdf->write(5,'You are required to join us on ');
$pdf->SetFont('Times','B',12);
$pdf->write(5,$joinDate);
$pdf->SetFont('Times','',12);
$pdf->write(5,', failing which this offer will be treated as cancelled. ');
$pdf->Ln(10);

// $pdf->MultiCell(0,5,'We are happy to welcome you to the Company name family. With best wishes.',0);
$pdf->SetFont('Times','',12);
$pdf->write(5,'We are happy to welcome you to the ');
$pdf->SetFont('Times','B',12);
$pdf->write(5,'Trinity Mobile App Lab ');
$pdf->SetFont('Times','',12);
$pdf->write(5,'family. With best wishes.');
$pdf->Ln(10);

// $pdf->MultiCell(0,5,'Your signing this appointment letter confirms your acceptance that you would be joining Trinity Mobile App Lab on '.$joinDate.'. This offer is valid till EOD of '.$validDate.'.',0);
$pdf->SetFont('Times','',12);
$pdf->write(5,'Your signing this appointment letter confirms your acceptance that you would be joining ');
$pdf->SetFont('Times','B',12);
$pdf->write(5,'Trinity Mobile App Lab ');
$pdf->SetFont('Times','',12);
$pdf->write(5,'on ');
$pdf->SetFont('Times','B',12);
$pdf->write(5,$joinDate);
$pdf->SetFont('Times','',12);
$pdf->write(5,'. This offer is valid till EOD of ');
$pdf->SetFont('Times','B',12);
$pdf->write(5,$validDate);
$pdf->SetFont('Times','',12);
$pdf->write(5,'.');
$pdf->Ln(10);

$pdf->SetFont('Times','B',12);
$pdf->Cell(0,5,'Yours Sincerely.',0);
$pdf->Ln(5);

$pdf->SetFont('Times','',12);
// $pdf->MultiCell(0,5,'HR Manager'.PHP_EOL.'Trinity Mobile App Lab Pvt. Ltd.',0);
$pdf->MultiCell(0,5,'Shruti Singh'.PHP_EOL.'HR Manager'.PHP_EOL.'Trinity Mobile App Lab Pvt. Ltd.',0);
$pdf->Ln(20);

$pdf->Cell(95,5,'..............................................',0);
$pdf->Cell(95,5,'_______________________',0);
$pdf->Ln(5);
$pdf->Cell(95,5,'Signature',0);
$pdf->Cell(95,5,'Date',0);
$pdf->Ln(10);
$pdf->Cell(95,5,'('.$name.')',0);


// $pdf->Output();
$dir = "OfferLetter";
if (!file_exists('/var/www/trinityapplab.in/html/Company/files/'.$dir)) {
    mkdir('/var/www/trinityapplab.in/html/Company/files/'.$dir, 0777, true);
}
$pdfFileName = $mobile.".pdf";
$pdf->Output("/var/www/trinityapplab.in/html/Company/files/".$dir."/".$pdfFileName,"F");
$empName = $name;
$toMailId = $emailId;
$msg = "<html><body>";
// $msg .= "<style>";
// $msg .= "p{font-size : 16px !important}";
// $msg .= "</style>";
$msg .= "<p style='font-size : 16px !important'>"."Dear $empName, "."</p>";
$msg .= "<p style='font-size : 16px !important'>"."Greetings of the day!!!"."</p>";
$msg .= "<p style='font-size : 16px !important'>"."On behalf of the entire company, I'd like to say that it brings me great pleasure to formally offer you the position of <b>$designation</b> at <b>Trinity Mobile App Lab Pvt</b>. A huge congratulations to you!"."</p>";
$msg .= "<p style='font-size : 16px !important'>"."Your employment start date is <b>$dayName, $joinDate</b>. You'll be greeted at <b>9:30 A.M. at Office No: B-417, Noida One, B-8,Sector-62, Noida (UP) - 201309</b>. Please arrive a few minutes early on your first day."."</p>";
$msg .= "<p style='font-size : 16px !important'>"."You must print, sign, and scan the form. Please email it back to us by EOD <b>$validDate</b>, to the email address."."<p>";
$msg .= "<p style='font-size : 16px !important'>"."Directions on how to accept the offer: add an electronic signature (below) or print, sign and scan this letter back to us by EOD <b>$validDate</b>. Scan to the email address at the end of this letter. The offer expires on <b>$validDate</b>."."</p>";
$msg .= "<p style='font-size : 16px !important'>"."Welcome onboard! If you have questions about anything prior to your first day, don't hesitate to reach out: <a href='mailto:pushkar.tyagi@trinityapplab.co.in'>pushkar.tyagi@trinityapplab.co.in</a>"."</p>";
$msg .= "<a style='text-decoration:none;padding:10px;background-color:green;color:white;border-radius:10px' href='www.trinityapplab.in/Company/offerLetterAction.php?mobile=$mobile&action=1' target='blank'>Accept</a>"."&nbsp;&nbsp;";
$msg .= "<a style='text-decoration:none;padding:10px;background-color:red;color:white;border-radius:10px' href='www.trinityapplab.in/Company/offerLetterAction.php?mobile=$mobile&action=2' target='blank'>Reject</a>";
// $msg .= "PFA"."<br><br>";
// $msg .= "Regards"."<br>";
// $msg .= "Trinity Automation Team.";
$msg .= "</body></html>";

$subject = "Offer Letter";
$classObj = new SendMailClass();
$response = $classObj->sendMail($toMailId, $subject, $msg, "/var/www/trinityapplab.in/html/Company/files/".$dir."/".$pdfFileName);

$output = "";
if($response){
	$output -> responseCode = "100000";
	$output -> responseDesc = "Offer letter send to your mail id";
}
else{
	$output -> responseCode = "0";
	$output -> responseDesc = "Something wrong";
}
echo json_encode($output);
?>

<?php
function getIndianCurrency(float $number)
{
    $decimal = round($number - ($no = floor($number)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(0 => '', 1 => 'One', 2 => 'Two',
        3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
        7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
        13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
        16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
        19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
        40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
        70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');
    $digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
    while( $i < $digits_length ) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
        } else $str[] = null;
    }
    $Rupees = implode('', array_reverse($str));
    $paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
    return ($Rupees ? $Rupees . 'Rupees ' : '') . $paise;
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