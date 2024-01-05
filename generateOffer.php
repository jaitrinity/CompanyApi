<?php
// require('PDFGenerator/fpdf.php');
require('PDFGenerator/rotation.php');
$date = date('d-F-Y', strtotime('0 day'));
$name = "Jai Prakash";
$designation = "Software Developer";
$add1 = "H.No 31, Shivpuri";
$add2 = "Vijay Nagar, Ghaziabad- UP(201009)";
$joinDate = "8th November 2022";
$lpa = "400,000";
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
	    $this->Cell(95,5,'Office No: C-313, Noida One, B-8,',0);
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
$pdf->Cell(0,5,'Mr. '.$name,0);
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
// $pdf->Ln(5);
// $pdf->MultiCell(0,5,'I am eager to have you as part of our team. I foresee your potential skills as a valuable contribution to our company and clients. Your appointment as '.$designation.' will commence on '.$joinDate.'.',0);
// $pdf->Ln(5);
// $pdf->MultiCell(0,5,'As '.$designation.', you will be entitled to a yearly remuneration of Rs '.$lpa.'/- (Rupees 4 LPA only) which indicates cost to company. Regular performance review will be conducted to assess your performance and suitability.',0);
// $pdf->Ln(5);
// $pdf->MultiCell(0,5,'You are required to join us on '.$joinDate.', failing which this offer will be treated as cancelled.',0);
// $pdf->Ln(5);
// $pdf->MultiCell(0,5,'We are happy to welcome you to the Trinity Mobile App Lab family. With best wishes.',0);
// $pdf->Ln(5);
// $pdf->MultiCell(0,5,'Your signing this appointment letter confirms your acceptance that you would be joining Trinity Mobile App Lab on '.$joinDate.'. This offer is valid till EOD of '.$validDate.'.',0);
// $pdf->Ln(5);

$pdf->WriteHTML('I am pleased to offer you employment in the position of <b>'.$designation.'</b> with <b>Trinity Mobile App Lab Pvt. Ltd</b>.');
$pdf->Ln(10);
$pdf->WriteHTML('I am eager to have you as part of our team. I foresee your potential skills as a valuable contribution to our company and clients. Your appointment as <b>'.$designation.'</b> will commence on <b>'.$joinDate.'</b>.');
$pdf->Ln(10);
$pdf->WriteHTML('As <b>'.$designation.'</b>, you will be entitled to a yearly remuneration of <b>Rs '.$lpa.'/- (Rupees 4 LPA only)</b> which indicates cost to company. Regular performance review will be conducted to assess your performance and suitability.');
$pdf->Ln(10);
$pdf->WriteHTML('You are required to join us on <b>'.$joinDate.'</b>, failing which this offer will be treated as cancelled.');
$pdf->Ln(10);
$pdf->WriteHTML('We are happy to welcome you to the <b>Trinity Mobile App Lab</b> family. With best wishes.');
$pdf->Ln(10);
$pdf->WriteHTML('Your signing this appointment letter confirms your acceptance that you would be joining <b>Trinity Mobile App Lab</b> on <b>'.$joinDate.'</b>. This offer is valid till EOD of <b>'.$validDate.'</b>.');
$pdf->Ln(10);

// $pdf->write(5,'I am pleased to offer you employment in the position of ');
// $pdf->SetFont('Times','B',12);
// $pdf->write(5,$designation.' ');
// $pdf->SetFont('Times','',12);
// $pdf->write(5,'with ');
// $pdf->SetFont('Times','B',12);
// $pdf->write(5,'Trinity Mobile App Lab Pvt. Ltd. ');
// $pdf->Ln(10);

// $pdf->SetFont('Times','',12);
// $pdf->write(5,'I am eager to have you as part of our team. I foresee your potential skills as a valuable contribution to our company and clients. Your appointment as ');
// $pdf->SetFont('Times','B',12);
// $pdf->write(5,$designation.' ');
// $pdf->SetFont('Times','',12);
// $pdf->write(5,'will commence on ');
// $pdf->SetFont('Times','B',12);
// $pdf->write(5,$joinDate);
// $pdf->SetFont('Times','',12);
// $pdf->write(5,'.');
// $pdf->Ln(10);


// $pdf->SetFont('Times','',12);
// $pdf->write(5,'As ');
// $pdf->SetFont('Times','B',12);
// $pdf->write(5,$designation.'');
// $pdf->SetFont('Times','',12);
// $pdf->write(5,', you will be entitled to a yearly remuneration of ');
// $pdf->SetFont('Times','B',12);
// $pdf->write(5,'Rs '.$lpa.'/- (Rupees 4 LPA only) ');
// $pdf->SetFont('Times','',12);
// $pdf->write(5,'which indicates cost to company. Regular performance review will be conducted to assess your performance and suitability.');
// $pdf->Ln(10);

// $pdf->SetFont('Times','',12);
// $pdf->write(5,'You are required to join us on ');
// $pdf->SetFont('Times','B',12);
// $pdf->write(5,$joinDate.'');
// $pdf->SetFont('Times','',12);
// $pdf->write(5,', failing which this offer will be treated as cancelled. ');
// $pdf->Ln(10);

// $pdf->SetFont('Times','',12);
// $pdf->write(5,'We are happy to welcome you to the ');
// $pdf->SetFont('Times','B',12);
// $pdf->write(5,'Trinity Mobile App Lab ');
// $pdf->SetFont('Times','',12);
// $pdf->write(5,'family. With best wishes.');
// $pdf->Ln(10);

// $pdf->SetFont('Times','',12);
// $pdf->write(5,'Your signing this appointment letter confirms your acceptance that you would be joining ');
// $pdf->SetFont('Times','B',12);
// $pdf->write(5,'Trinity Mobile App Lab ');
// $pdf->SetFont('Times','',12);
// $pdf->write(5,'on ');
// $pdf->SetFont('Times','B',12);
// $pdf->write(5,$joinDate.'');
// $pdf->SetFont('Times','',12);
// $pdf->write(5,'. This offer is valid till EOD of ');
// $pdf->SetFont('Times','B',12);
// $pdf->write(5,$validDate);
// $pdf->SetFont('Times','',12);
// $pdf->write(5,'.');
// $pdf->Ln(10);

$pdf->SetFont('Times','B',12);
$pdf->Cell(0,5,'Yours Sincerely.',0);
$pdf->Ln(5);

$pdf->SetFont('Times','',12);
$pdf->MultiCell(0,5,'Ankita Verma'.PHP_EOL.'HR Manager'.PHP_EOL.'Trinity Mobile App Lab Pvt. Ltd.',0);
$pdf->Ln(20);

$pdf->Cell(95,5,'..............................................',0);
$pdf->Cell(95,5,'_______________________',0);
$pdf->Ln(5);
$pdf->Cell(95,5,'Signature',0);
$pdf->Cell(95,5,'Date',0);
$pdf->Ln(10);
$pdf->Cell(95,5,'('.$name.')',0);


$pdf->Output();
?>