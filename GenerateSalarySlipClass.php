<?php 
require('PDFGenerator/fpdf.php');
class GenerateSalarySlipClass extends FPDF{
	public function generateSS($empId, $monthYear){
		include("dbConfiguration.php");

		$sql = "SELECT `Line1`, `Line2` FROM `OfficeMaster` where `Id`=1";
		$query=mysqli_query($conn,$sql);
		$row = mysqli_fetch_assoc($query);
		$line1 = $row["Line1"];
		$line2 = $row["Line2"];

		// $sql = "SELECT e.Name, e.FatherHusbandName, e.Designation, e.Mobile, e.EmailId, date_format(e.DOB,'%d-%b-%Y') as DOB, date_format(e.DOJ,'%d-%b-%Y') as DOJ, e.PAN, ed.Basic, ee.HRA, ee.ConveyanceAllowance, ee.MedicalAllowance, ee.TelephoneAllowance, ee.SpecialAllowance, ee.OtherAllowance, ed.GrossSalary, ed.MonthYear, ed.PaidDays, ed.RetentionBonus, ed.ProfessionTax, ed.AfterProfessionTax, ed.LossOfPay, ed.AfterLossOfPay, ed.OtherDeductions, ed.IncomeTax, ed.AfterIncomeTax, ed.OtherTax, ed.AfterOtherTax, ed.TotalDeductions, ed.Reimbursements, ed.NetSalary, ed.SalarySlipName FROM EmployeeMaster e join EmployeeEarnings ee on e.EmpId = ee.EmpId left join EmployeeDeductions ed on e.EmpId = ed.EmpId where e.EmpId = '$empId' and ed.MonthYear = '$monthYear' ";
		$sql = "SELECT e.Name, e.FatherHusbandName, e.Designation, e.Mobile, e.EmailId, date_format(e.DOB,'%d-%b-%Y') as DOB, date_format(e.DOJ,'%d-%b-%Y') as DOJ, e.PAN, ed.Basic, ee.HRA, ee.ConveyanceAllowance, ee.MedicalAllowance, ee.TelephoneAllowance, ee.SpecialAllowance, ee.OtherAllowance, ed.GrossSalary, ed.MonthYear, ed.PaidDays, ed.RetentionBonus, ed.ProfessionTax, ed.AfterProfessionTax, ed.LossOfPay, ed.AfterLossOfPay, round(ed.OtherDeductions) as OtherDeductions, ed.IncomeTax, ed.AfterIncomeTax, ed.OtherTax, ed.AfterOtherTax, round(ed.TotalDeductions) as TotalDeductions, ed.Reimbursements, round(ed.NetSalary) as NetSalary, ed.SalarySlipName FROM EmployeeMaster e join EmployeeEarnings ee on e.EmpId = ee.EmpId left join EmployeeDeductions ed on e.EmpId = ed.EmpId where e.EmpId = '$empId' and ed.MonthYear = '$monthYear'";
		
		$query=mysqli_query($conn,$sql);
		$row = mysqli_fetch_assoc($query);


		$pdf = new GenerateSalarySlipClass();
		$pdf->AliasNbPages();
		$pdf->AddPage();

		$pdf->SetTextColor(0);
		$pdf->SetDrawColor(0);
		$pdf->SetFont('Times','B',12);
		$pdf->Cell(0,7,'TRINITY MOBILE APP LAB PVT. LTD.',1,0,'C');
		$pdf->Ln(7);
		$pdf->Cell(0,7,$line1,1,0,'C');
		$pdf->Ln(7);
		$pdf->Cell(0,7,$line2,1,0,'C');
		$pdf->Ln(7);
		$pdf->SetFont('Times','',12);
		$pdf->Cell(0,7,'Pay Slip for the Month of '.$row["MonthYear"],1,0,'C');
		$pdf->Ln(7);
		$pdf->Ln(7);

		$pdf->Cell(40,7,'Employee Name',1);
		$pdf->SetFont('Times','B',12);
		$pdf->Cell(70,7,$row["Name"],1);
		$pdf->SetFont('Times','',12);
		$pdf->Cell(40,7,'Paid Days',1);
		$pdf->SetFont('Times','B',12);
		$pdf->Cell(40,7,$row["PaidDays"],1);
		$pdf->Ln(7);

		$pdf->SetFont('Times','',12);
		$pdf->Cell(40,7,'Father/Husband Name',1);
		$pdf->SetFont('Times','B',12);
		$pdf->Cell(70,7,$row["FatherHusbandName"],1);
		$pdf->SetFont('Times','',12);
		$pdf->Cell(40,7,'DOJ',1);
		$pdf->SetFont('Times','B',12);
		$pdf->Cell(40,7,$row["DOJ"],1);
		$pdf->Ln(7);

		$pdf->SetFont('Times','',12);
		$pdf->Cell(40,7,'Designation',1);
		$pdf->SetFont('Times','B',12);
		$pdf->Cell(70,7,$row["Designation"],1);
		$pdf->SetFont('Times','',12);
		$pdf->Cell(40,7,'PAN No',1);
		$pdf->SetFont('Times','B',12);
		$pdf->Cell(40,7,$row["PAN"],1);
		$pdf->Ln(7);
		$pdf->Ln(7);

		$pdf->SetFillColor(255,255,0);
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFont('Times','B',12);
		$pdf->Cell(55,7,'Earnings',1,0,'C',true);
		$pdf->Cell(40,7,'Amount',1,0,'C',true);
		$pdf->Cell(55,7,'Deduction',1,0,'C',true);
		$pdf->Cell(15,7,'',1,0,'C',true);
		$pdf->Cell(25,7,'Amount',1,0,'C',true);
		$pdf->Ln(7);

		$pdf->SetFont('Times','',12);
		$pdf->Cell(55,7,'Basic',1);
		$pdf->SetFont('Times','B',12);
		$pdf->Cell(40,7,$this->moneyFormatIndia($row["Basic"]),1,0,'R');
		$pdf->SetFont('Times','',12);
		$pdf->Cell(55,7,'Profession Tax',1);
		$pdf->SetFont('Times','B',12);
		$pdf->Cell(15,7,$row['ProfessionTax'].' %' ,1,0,'R');
		$pdf->Cell(25,7,$this->moneyFormatIndia($row["AfterProfessionTax"]),1,0,'R');
		$pdf->Ln(7);

		$pdf->SetFont('Times','',12);
		$pdf->Cell(55,7,'HRA',1);
		$pdf->SetFont('Times','B',12);
		$pdf->Cell(40,7,$this->moneyFormatIndia($row["HRA"]),1,0,'R');
		$pdf->SetFont('Times','',12);
		$pdf->Cell(55,7,'Loss of Pay(Leave)',1);
		$pdf->SetFont('Times','B',12);
		$pdf->Cell(15,7,$row['LossOfPay'] ,1,0,'R');
		$pdf->Cell(25,7,$this->moneyFormatIndia($row["AfterLossOfPay"]),1,0,'R');
		$pdf->Ln(7);

		$pdf->SetFont('Times','',12);
		$pdf->Cell(55,7,'Conveyance Allowance',1);
		$pdf->SetFont('Times','B',12);
		$pdf->Cell(40,7,$this->moneyFormatIndia($row["ConveyanceAllowance"]),1,0,'R');
		$pdf->SetFont('Times','',12);
		$pdf->Cell(55,7,'TDS',1);
		$pdf->SetFont('Times','B',12);
		$pdf->Cell(40,7,$this->moneyFormatIndia($row["OtherDeductions"]),1,0,'R');
		$pdf->Ln(7);

		$pdf->SetFont('Times','',12);
		$pdf->Cell(55,7,'Medical Allowance',1);
		$pdf->SetFont('Times','B',12);
		$pdf->Cell(40,7,$this->moneyFormatIndia($row["MedicalAllowance"]),1,0,'R');
		$pdf->SetFont('Times','',12);
		$pdf->Cell(55,7,'Income Tax',1);
		$pdf->SetFont('Times','B',12);
		$pdf->Cell(15,7,$row["IncomeTax"].' %',1,0,'R');
		$pdf->Cell(25,7,$this->moneyFormatIndia($row["AfterIncomeTax"]),1,0,'R');
		$pdf->Ln(7);

		$pdf->SetFont('Times','',12);
		$pdf->Cell(55,7,'Telephone Allowance',1);
		$pdf->SetFont('Times','B',12);
		$pdf->Cell(40,7,$this->moneyFormatIndia($row["TelephoneAllowance"]),1,0,'R');
		$pdf->SetFont('Times','',12);
		$pdf->Cell(55,7,'Total Deductions',1);
		$pdf->SetFont('Times','B',12);
		$pdf->Cell(40,7,$this->moneyFormatIndia($row["TotalDeductions"]),1,0,'R');
		$pdf->Ln(7);

		$pdf->SetFont('Times','',12);
		$pdf->Cell(55,7,'Special Allowance',1);
		$pdf->SetFont('Times','B',12);
		$pdf->Cell(40,7,$this->moneyFormatIndia($row["SpecialAllowance"]),1,0,'R');
		$pdf->SetFont('Times','',12);
		$pdf->Cell(55,7,'Reimbursements',1);
		$pdf->SetFont('Times','B',12);
		$pdf->Cell(40,7,$this->moneyFormatIndia($row["Reimbursements"]),1,0,'R');
		$pdf->Ln(7);

		$pdf->SetFont('Times','',12);
		$pdf->Cell(55,7,'Other Allowance',1);
		$pdf->SetFont('Times','B',12);
		$pdf->Cell(40,7,$this->moneyFormatIndia($row["OtherAllowance"]),1,0,'R');
		$pdf->SetFont('Times','',12);
		$pdf->Cell(55,7,'Retention Bonus',1);
		$pdf->SetFont('Times','B',12);
		$pdf->Cell(40,7,$this->moneyFormatIndia($row["RetentionBonus"]),1,0,'R');
		$pdf->Ln(7);

		$pdf->SetFont('Times','',12);
		$pdf->Cell(55,7,'',1);
		$pdf->SetFont('Times','B',12);
		$pdf->Cell(40,7,'',1);
		$pdf->SetFont('Times','',12);
		$pdf->Cell(55,7,'',1);
		$pdf->SetFont('Times','B',12);
		$pdf->Cell(40,7,'',1);
		$pdf->Ln(7);

		$pdf->SetFont('Times','B',12);
		$pdf->Cell(55,7,'Gross Salary',1);
		$pdf->SetFont('Times','B',12);
		$pdf->Cell(40,7,$this->moneyFormatIndia($row["GrossSalary"]),1,0,'R');
		$pdf->SetFont('Times','B',12);
		$pdf->Cell(55,7,'Net Salary',1);
		$pdf->SetFont('Times','B',12);
		$pdf->Cell(40,7,$this->moneyFormatIndia($row["NetSalary"]),1,0,'R');
		$pdf->Ln(7);
		$pdf->Ln(7);

		$pdf->Cell(50,7,'Take Home Pay',1);
		$pdf->Cell(0,7,$this->moneyFormatIndia($row["NetSalary"]),1);
		$pdf->Ln(7);
		$pdf->Cell(50,7,'In Words',1);
		$pdf->Cell(0,7,$this->getIndianCurrency($row["NetSalary"]),1);
		$pdf->Ln(7);
		$pdf->Ln(7);
		$pdf->Ln(7);

		// $pdf->SetFont('Times','',12);
		// $pdf->Cell(0,7,'Date :- '.$todayDate,0);
		$pdf->Ln(7);
		$pdf->Ln(7);

		$pdf->SetFont('Times','B',12);
		$pdf->Cell(0,7,'Note : This is computer generated, signature not require.',0);


		$dir = "SalarySlip_".$monthYear;
		if (!file_exists('/var/www/trinityapplab.in/html/Company/files/'.$dir)) {
		    mkdir('/var/www/trinityapplab.in/html/Company/files/'.$dir, 0777, true);
		}
		$pdfFileName = $row["SalarySlipName"].".pdf";
		$pdf->Output("/var/www/trinityapplab.in/html/Company/files/".$dir."/".$pdfFileName,"F");
		return true;
	}

	function Header()
    {
        // Logo
        $this->Image('files/offer_header.png', 40, 5, 130, 15);
        // Line break
        $this->Ln(20);
    }

    function Footer()
    {
        include("dbConfiguration.php");

		$sql = "SELECT `Line1`, `Line2` FROM `OfficeMaster` where `Id`=1";
		$query=mysqli_query($conn,$sql);
		$row = mysqli_fetch_assoc($query);
		$line1 = $row["Line1"];
		$line2 = $row["Line2"];
		
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        $this->SetFont('Times','',12);
        $this->Cell(95,5,$line1,0);
        // $this->Cell(95,5,'ayush.agarwal@trinityapplab.co.in',0,0,'R');
        $this->Ln(5);
        $this->Cell(95,5,$line2,0);
        $this->Cell(95,5,'www.trinityapplab.com',0,0,'R');

    }

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
}
?>