<?php
include("dbConfiguration.php");
require('CommonFunction.php');
require('PDFGenerator/rotation.php');
require 'SendMailClass.php';

$methodType = $_SERVER['REQUEST_METHOD'];
if($methodType != "POST"){
	return;
}
$json = file_get_contents('php://input');
$jsonData = json_decode($json);
$employeeId = $jsonData->employeeId;

$sql = "SELECT e.Name, e.Mobile, e.EmailId, e.DOJ, e.Address, ee.Basic, ee.RetentionBonus FROM EmployeeMaster e join EmployeeEarnings ee on e.EmpId = ee.EmpId where e.EmpId = '$employeeId'";
$query = mysqli_query($conn,$sql);
$row = mysqli_fetch_assoc($query);

$name = $row["Name"];
$mobile = $row["Mobile"];
$emailId = $row["EmailId"];
$doj = $row["DOJ"];
// $doj = date("d-F-Y", strtotime($doj));
$doj = date('d-F-Y', strtotime('0 day'));
$address = $row["Address"];

$classObj = new CommonFunction();
$expDoj = explode("-", $doj);
$ss = "th";
if($expDoj[0] == 1) $ss = "st";
else if($expDoj[0] == 2) $ss = "nd";
else if($expDoj[0] == 3) $ss = "rd";
$joinDate = $expDoj[0].$ss.' '.$expDoj[1].' '.$expDoj[2];
$position = "Quality Assurance & Support Executive";
$reportPerson = "Mrs. Nivedita Singh";
$monthly = $row["Basic"];
$monthlyFormat = $classObj->moneyFormatIndia($monthly);
// $monthlyInWord = $classObj->getIndianCurrency($monthly);
$ctc = $monthly*12;
$ctcFormat = $classObj->moneyFormatIndia($ctc);
$ctcInWord = $classObj->getIndianCurrency($ctc);
$retensionBonus = $row["RetentionBonus"];
$retensionBonusFormat = $classObj->moneyFormatIndia($retensionBonus);


class PDF extends PDF_Rotate
{
	// Page header
	function Header()
	{
	    // Logo
	    $this->Image('files/offer_header.png',40, 5, 130, 15);
	    $this->Ln(15);

	    // Watermark
	    $this->SetFont('Arial','B',40);
		$this->SetTextColor(236,231,241);
		$this->RotatedText(20,200,'Trinity Mobile App Lab Pvt. Ltd.',35);
	}
	// Watermark
	function RotatedText($x, $y, $txt, $angle)
	{
		//Text rotated around its origin
		$this->Rotate($angle,$x,$y);
		$this->Text($x,$y,$txt);
		$this->Rotate(0);
	}

	// Page footer
	function Footer()
	{
	    // Position at 1.5 cm from bottom
	    $this->SetY(-15);
	    // Arial italic 8
	    $this->SetFont('Arial','I',8);
	    // Page number
	    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');

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

$pdf->SetFont('Times','',12);
$pdf->WriteHTML('<b>'.$joinDate.'<br>'.$name.'<br>'.$address.'</b>.');
$pdf->Ln(15);

$pdf->SetFont('Times','BU',15);
$pdf->Cell(0,5,'APPOINTMENT LETTER',0,0,'C');
$pdf->Ln(10);

$pdf->SetFont('Times','',12);
$pdf->Cell(0,5,'Dear '.$name.',',0);
$pdf->Ln(10);

$pdf->WriteHTML('<p>We are pleased to offer you the position of <b>'.$position.'</b> at <b>Trinity Mobile App Lab Pvt. Ltd, Ghaziabad</b>. Please note that the employment terms contained in this letter are subject to Company Policy as amended from time to time.</p>');
$pdf->Ln(10);

$pdf->WriteHTML('<p>You shall join the services of the Company on <b>'.$joinDate.'</b> and shall report <b>'.$reportPerson.'</b> at Noida location. Your Reporting Manager initially shall be Mrs. Nivedita Singh, who may change at the discretion of the Company. Also, your work location may be changed to any other location as per the discretion and policy of the Company. During the period of employment, you shall devote on best effort basis exclusively use your time, attention and abilities for the performance of the Services for the Company and shall use the best endeavor to promote the interest and business of the Company. You shall upon and during the employment period adhere to the Company\'s Code of Conduct as applicable from time to time. During the period of your employment with Company you shall not directly or indirectly take up any other employment or assignment on full time or part time basis.</p>');
$pdf->Ln(10);

$pdf->WriteHTML('<p>The total monthly emoluments payable to you will be Rs <b>'.$monthlyFormat.'/-per month (TCTC will be Rs '.$ctcFormat.'/- ('.$ctcInWord.') per annum), in which '.$retensionBonusFormat.'/- per month is your Retention bonus which will hand over after successful completion of one & half year of service</b>. The salary shall be paid on the 7th of each month.</p>');
$pdf->Ln(10);

$pdf->WriteHTML('<p>You will be entitled to avail weekly holiday on Sundays. You will also be entitled to observe holiday on all the declared holiday(s) of the Company. In addition from 10 (Ten) holidays, employees will be entitled for 12 (Twelve) paid leaves (8 Casual and 4 Sick leaves) in a year on pro rata basis, post which it will lead to deduction from salary. To avail such leaves employees inform and get written approval at-least 3 (Three) days in advance from your RM and HR with no exception. Paid leaves can be carried forward for one year only after which it will get lapsed. A maximum of 5 (Five) leaves can be availed in a stretch with prior intimation and written approval at-least 2-3 weeks in advance from your RM and HR with no exception. Employees are bound to serve 9 (Nine) hours (including lunch break) on all working days with an exception of 60 minutes in a week, after which every 15 minutes of delay will be treated as Half day leave. If any information provided by you like last salary, reason of resignation, etc found wrong, your appointment will be cancelled with immediate effect.</p>');
$pdf->Ln(10);

$pdf->WriteHTML('<b><u>Confidential Information</u></b>');
$pdf->Ln(10);
$pdf->WriteHTML('<p>1. You acknowledge and agrees that in the performance of the Services for the Company, you may have access to, and/or became or may become informed of confidential information of the Company and/or information that is a competitive asset of the Company (collectively, <b>"Confidential Information"</b>) and the disclosure of which would be harmful to the interests of the Company or its subsidiaries.</p>');
$pdf->Ln(10);

$pdf->WriteHTML("<p>2. You shall keep in strict confidence, and will not, directly or indirectly, at any time, disclose, furnish, disseminate, make available, use or suffer to be used in any manner any Confidential Information of the Company without limitation as to when or how you may have acquired such Confidential Information. You specifically acknowledges that Confidential Information includes any and all information, whether reduced to writing (or in a form from which information can be obtained, translated, or derived into reasonably usable form), or maintained in the mind or memory of You and whether compiled or created by the Company, and that reasonable efforts have been put forth by the Company to maintain the secrecy of confidential or proprietary or trade secret information, that such information is and will remain the sole property of the Company, and that any retention or use by you of confidential or proprietary or trade secret information after the termination of your employment with the Company shall constitute misappropriation of the Company's Confidential Information.</p>");
$pdf->Ln(10);

$pdf->WriteHTML("<p>3. You undertake not to make or keep any copies of the Confidential and/or Proprietary Information and agree that all work and use of Confidential and/or Proprietary Information shall be exclusively for the employment mandates under this Agreement and shall never be used for any personal purpose. You further acknowledge and undertake that the obligation of confidentiality shall survive, regardless of any other breach or expiry/termination of this Agreement. Your obligations under this Agreement are in addition to, and not in limitation or preemption of, all other obligations of confidentiality which you may have to the Company under general legal or equitable principles or statutes.</p>");
$pdf->Ln(10);

$pdf->WriteHTML('<b><u>Proprietary Information</u></b>');
$pdf->Ln(10);
$pdf->WriteHTML("<p>4. You acknowledge that your relationship with the Company is one of high trust and confidence and that in the course of your service the Company, you will have access and contact with proprietary, trade secret information of the Company (<b>\"Proprietary Information\"</b>). Such Proprietary Information relates to the Company's present and planned products, services, technological information, solutions, intellectual property or any other information. You agree and affirm that during the term of this Agreement and thereafter in perpetuity you shall not use Proprietary Information of the Company in any manner other than for the employment purposes with the Company and shall not disclose in any way to any person, firm or corporation any Proprietary Information of the Company.</p>");
$pdf->Ln(10);

$pdf->WriteHTML('<p>5. You acknowledge and agree that during the employment period all the intellectual property rights that may arise out of the Services provided by you shall vest solely with the Company and you shall have no rights, claims or interests, of whatsoever nature, in the same. You hereby waive any such rights (moral, legal, equitable or otherwise) and assign the same to the Company in view of the salary paid and payable to you under this Agreement. You shall not keep any personal copies of Confidential and/or Proprietary Information.</p>');
$pdf->Ln(10);

$pdf->WriteHTML('<p>6. You shall not at any time do or cause to be done any act of thing that in any way impairs or which may tend to impair the Company\'s ownership, title and/or interest in the Intellectual Property Rights or Work Product. Upon termination of your employment in any manner provided herein, you shall cease to and desist from all use of the Intellectual Property Rights or Work Product.</p>');
$pdf->Ln(10);

$pdf->WriteHTML('<p>7. You shall not do anything during the course of your employment that would in any way breach, violate or infringe any applicable laws, regulations, rules, directives, circulars, notices or directions relating to any/or governing the Intellectual Property Rights of any third parties. Without limitation to the foregoing, you shall not download any material that infringes any Intellectual Property Rights, or use any unauthorized or infringing copies of software in the course of performing your duties.</p>');
$pdf->Ln(10);

$pdf->WriteHTML('<p>8. Employees on Probation or permanent cannot join our clients or cannot start their own business or advise their relatives who are in the same line of business for at least a period of one year.</p>');
$pdf->Ln(10);

$pdf->AddPage();
$pdf->WriteHTML('<b><u>Indemnification</u></b>');
$pdf->Ln(10);
$pdf->WriteHTML('<p>9. You acknowledge that the breach of the obligations under clauses 7 and 8 of this Agreement shall cause irreparable loss and harm to the Company which cannot be reasonably or adequately compensated by damages in an action at law, and accordingly, the Company will be entitled to injunctive and other equitable relief to prevent or cure any breach or threatened breach thereof, but no action for any such relief shall be deemed to waive the right of the Company to an action for damages.</p>');
$pdf->Ln(10);

$pdf->WriteHTML('<p>10. You promise to indemnify the Company against, any and all claims (including claims for attorney\'s fees), demands, damages, suits, proceedings, actions and/or causes of action of any kind and every description, whether known or unknown, arising out of breach of any representation or any obligations pertaining to Confidential and/or Proprietary Information under this Agreement by act or omission of you.</p>');
$pdf->Ln(10);

$pdf->WriteHTML('<b><u>Termination and Effects of Termination</u></b>');
$pdf->Ln(10);
$pdf->WriteHTML('<p>11. For 6 months after successful confirmation of your probation, you shall not terminate this Agreement for any reason considering the training you received and the important nature of the work that is to be performed by you under this Agreement. After 6 months of confirmation, you may terminate this Agreement by providing two month\'s written notice. In case of any breach of this clause, you shall be required to pay to the Company an amount equal to two month of your total cost to the Company.</p>');
$pdf->Ln(10);

$pdf->WriteHTML('<p>12. Your employment may be terminated by either Party by giving a written notice of 2 (Two) month. Incase employee choose to leave before 60 day notice period, the equivalent to be adjusted. In case of probation the notice will be of 1 Month and in case employee choose to leave before 30 day notice period, the equivalent to be adjusted. During the notice period employee shall not be entitled for any paid leaves. In case of resignation/termination, employee salary will be paid as part of full and final settlement within 45 days from the last working day in the organization.</p>');
$pdf->Ln(10);

$pdf->WriteHTML('<p>13. You shall continue to perform the Services during the notice period and shall be paid salary in accordance with the terms of this Agreement.</p>');
$pdf->Ln(10);

$pdf->WriteHTML('<p>14. If at any point of time during the employment, you are found to be guilty of misconduct, negligence, non-performance or refusal to faithfully and diligently perform the Services or indulging in any acts which directly and materially harm the business or reputation of the Company and/or its associate or group Companies thereof, or dereliction or willful breach of any of the term(s) of this Agreement or otherwise, the Company may, without prejudice to other remedies, terminate the Agreement forthwith without notice, and without penalty and/or liability to the Company.</p>');
$pdf->Ln(10);

$pdf->WriteHTML('<p>15. You understand that in case of breach of the conditions under the Confidentiality and/or Proprietary Information clause, even by inadvertence, the Company shall without prejudice to other remedies terminate the agreement forthwith, without notice and without penalty and/or liability to the Company. In addition the Company may take suitable action including claim for damages.</p>');
$pdf->Ln(10);

$pdf->WriteHTML('<p>16. At the conclusion of the employment period, you will immediately return to the Company (to the extent he has not already returned), equipment, software, electronic files, mobile handset, SIM, computers, including any laptop, in good condition, all property of the Company, including, without limitation, property, documents and/or all other materials (including copies, reproductions, summaries and/or analyses) which constitute, refer or relate to Confidential Information of the Company.</p>');
$pdf->Ln(10);

$pdf->AddPage();
$pdf->WriteHTML('<p>17. If employee is absent from work/office for a continuous period of 3 (Three) days without prior approval from RM and HR, it shall be treated as abandonment of services by employee and it shall be assumed that you are no longer interested in working for the company and the employment shall automatically come to an end without any notice or intimation. In such case employee shall not be entitled to any salary/compensation whatsoever including statutory compensation, if any.</p>');
$pdf->Ln(10);

$pdf->WriteHTML('<b><u>Call Free Allowance</u></b>');
$pdf->Ln(10);
$pdf->WriteHTML('<p>18. Official Mobile & SIM will be issued to employees who will be on testing/helpdesk related profiles. Employees will use these services for official purposes only. Employees shall be issued assets like Laptop, Desktop, Tab, Handsets, IT peripherals etc for official work. Employees will be responsible incase of any kind of negligence, misuse, damage, breakage, theft of assets/services and action will be taken accordingly. In such scenarios amount will deducted from the salary of the employee.</p>');
$pdf->Ln(10);

$pdf->WriteHTML('<b><u>Miscellaneous</u></b>');
$pdf->Ln(10);
$pdf->WriteHTML('<p>19. For all purposes of this Agreement, all communications provided for herein shall be in writing and shall be deemed to have been duly given when delivered through Registered Post, addressed to the Company (to the attention of Trinity Mobile App Lab Pvt. Ltd. at Noida, Ghaziabad and to you at_________________________________________________________________________________________, or to such other address as any Party may have furnished to the other in writing and in accordance herewith. Notices of change of address shall be effective only upon receipt.</p>');
$pdf->Ln(10);
$pdf->WriteHTML('<p>No provision of this Agreement may be modified, waived or discharged unless such modification, waiver or discharge is agreed to in writing by the authorized signatory of the Company.</p>');
$pdf->Ln(10);
$pdf->WriteHTML('<p>This Agreement shall be governed by the laws of India and courts in New Delhi shall have exclusive jurisdiction over matters relating to or arising from this Agreement.</p>');
$pdf->Ln(10);
$pdf->WriteHTML('<p>This Agreement constitutes the entire understanding between the Parties and supersedes all previous understanding and communication and is intended as a final and complete settlement of the terms thereof. All the previous correspondence or agreement(s) between the parties with regard to the subject matter shall be treated as terminated and shall have no effect. It shall not be modified except by an instrument in writing signed by the parties hereto.</p>');
$pdf->Ln(10);

$pdf->WriteHTML('<p>We welcome you and wish you every success in your career with <b>Trinity Mobile App Lab Pvt. Ltd</b>.</p>');
$pdf->Ln(10);

$pdf->WriteHTML('<p>Yours Sincerely</p>');
$pdf->Ln(5);
$pdf->WriteHTML('<b>FOR Trinity Mobile App Lab Pvt. Ltd.</b>');
$pdf->Ln(40);

$pdf->AddPage();
$pdf->WriteHTML('<p>I HEREBY DECLARE THAT I HAVE CAREFULLY READ AND UNDERSTOOD ALL THE TERMS OF THIS LETTER. I HAVE DISCUSSED THIS LETTER AND SOUGHT APPROPRIATE CLARIFICATIONS FROM THE COMPANY CONCERNING THE TERMS CONTAINED IN THIS LETTER. I UNDERSTTAND AND ACCEPT THAT MY OFFER OF EMPLOYMENT WITH <b>TRINITY MOBILE APP LAB PVT. LTD.</b> IS SUBJECT TO A CLEAR / UNQUALIFIED BACKGROUND CHECK. I HEREBY CONVEY MY ACCEPTANCE AND AGREE TO BE UNCONDITIONALLY BOUND BY THE TERMS OF THIS LETTER AND OTHER TERMS OF EMPLOYMEENT WITH THE COMPANY BY SIGNING A COPY OF THIS LETTER AND RETURNING THE SAME TO THE COMPANY ALONG WITH COPY OF YOUR QUAILIFICATION DOCUMENTS, PREVIOUS COMPANY EXPERIENCE LETTER, RESIGNATION LETTER, SALARY SLIP, BANK STATEMENT, PAN CARD, CURRENT & PERMANENT ADDRESS PROOF AND TWO PASSPORT SIZE PHOTOGRAPHS.</p>');
$pdf->Ln(20);

$pdf->SetFont('Times','',12);
$pdf->Cell(95,5,'Signature : ____________________________',0);
$pdf->Cell(95,5,'Dated : ______________________________',0,0,'R');
$pdf->Ln(10);
$pdf->Cell(95,5,'Name : _____________________________',0);
$pdf->Cell(95,5,'',0,0,'R');

$dir = "JoiningLetter";
if (!file_exists('/var/www/trinityapplab.in/html/Company/files/'.$dir)) {
    mkdir('/var/www/trinityapplab.in/html/Company/files/'.$dir, 0777, true);
}
$pdfFileName = $mobile.".pdf";
$pdf->Output("/var/www/trinityapplab.in/html/Company/files/".$dir."/".$pdfFileName,"F");

$toMailId = $emailId;
$portalUrl = "https://www.trinityapplab.in/Company/Portal/#/login";
$msg = "Hi $name,"."<br>";
$msg .= "Thanks for joining <b>Trinity Mobile App Lab</b> family."."<br>";
$msg .= "Please use link <a href='$portalUrl'>Company</a> for portal. Password is same as mobile number. You can change at anytime"."<br>";

$subject = "Joining Letter";
$classObj = new SendMailClass();
// $response = $classObj->sendMail($toMailId, $subject, $msg, "/var/www/trinityapplab.in/html/Company/files/".$dir."/".$pdfFileName);
$response = $classObj->sendMailTest($toMailId, $subject, $msg, "/var/www/trinityapplab.in/html/Company/files/".$dir."/".$pdfFileName);

$output = "";
if($response){
	$output -> responseCode = "100000";
	$output -> responseDesc = "Joining letter send to your mail id";
}
else{
	$output -> responseCode = "0";
	$output -> responseDesc = "Something wrong";
}
echo json_encode($output);


?>