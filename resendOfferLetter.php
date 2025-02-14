<?php
include("dbConfiguration.php");
require('PDFGenerator/rotation.php');
include(dirname(__DIR__).'/PHPMailerAutoload.php');
$methodType = $_SERVER['REQUEST_METHOD'];
if($methodType != "POST"){
	return;
}
$json = file_get_contents('php://input');
$jsonData = json_decode($json);
$mobile = $jsonData->mobile;

$sql = "SELECT * FROM `OfferLetter` where `Mobile` = '$mobile'";
$query = mysqli_query($conn,$sql);
$row = mysqli_fetch_assoc($query);

// $date = date('d-F-Y', strtotime($row["OfferDate"]));
$name = $row["Name"];
$mobile = $row["Mobile"];
$emailId = $row["EmailId"];
$designation = $row["Designation"];
$officeLocation = $row["OfficeLocation"];
$add1 = $row["AddressLine1"];
$add2 = $row["AddressLine2"];
$joinDate = $row["DOJ"];
$doj = date("d-F-Y", strtotime($joinDate));
$dayName = date('l', strtotime($joinDate));
$expDoj = explode("-", $doj);
$joinDate = $expDoj[0].'th '.$expDoj[1].' '.$expDoj[2];
$lpa = $row["LPA"];
$validDate = date('d-F-Y', strtotime($row["OfferExpierDate"]));
$expValiDate = explode("-", $validDate);
$validDate = $expValiDate[0].'th '.$expValiDate[1].' '.$expValiDate[2];
$intervieweeId = $row["IntervieweeId"];

$officeTime = "";
$officeAddress = "";
if($officeLocation == "Noida"){
    $officeTime = "9:30 A.M.";
    $officeAddress = "Office No: B-417, Noida One, B-8,Sector-62, Noida (UP) - 201309";
}
else{
    $officeTime = "2:00 PM";
    $officeAddress = "11th Floor, A- Block, The First, Beside ITC Narmada, Vastrapur, Ahmedabad – 380015";
}


$dir = "OfferLetter";
$pdfFileName = $mobile.".pdf";
$empName = $name;
$toMailId = $emailId;
$msg = "<html><body>";
// $msg .= "<style>";
// $msg .= "p{font-size : 16px !important}";
// $msg .= "</style>";
$msg .= "<p style='font-size : 16px !important'>"."Dear $empName, "."</p>";
$msg .= "<p style='font-size : 16px !important'>"."Greetings of the day!!!"."</p>";
$msg .= "<p style='font-size : 16px !important'>"."On behalf of the entire company, I'd like to say that it brings me great pleasure to formally offer you the position of <b>$designation</b> at <b>Trinity Mobile App Lab Pvt</b>. A huge congratulations to you!"."</p>";
$msg .= "<p style='font-size : 16px !important'>"."Your employment start date is <b>$dayName, $joinDate</b>. You'll be greeted at <b>$officeTime at $officeAddress</b>. Please arrive a few minutes early on your first day."."</p>";
if($intervieweeId != "0"){
    $msg .= "<p style='font-size : 16px !important'>"."You must print, sign, and scan the form. Please email it back to us by EOD <b>$validDate</b>, to the email address."."<p>";
    $msg .= "<p style='font-size : 16px !important'>"."Directions on how to accept the offer: add an electronic signature (below) or print, sign and scan this letter back to us by EOD <b>$validDate</b>. Scan to the email address at the end of this letter. The offer expires on <b>$validDate</b>. The signing of the offer states that you understand your status as an at-will employee."."</p>";
}
$msg .= "<p style='font-size : 16px !important'>"."Welcome onboard! If you have questions about anything prior to your first day, don't hesitate to reach out: <a href='mailto:shruti@trinityapplab.co.in'>shruti@trinityapplab.co.in</a>"."</p>";
if($intervieweeId != "0"){
    $msg .= "<a style='text-decoration:none;padding:10px;background-color:green;color:white;border-radius:10px' href='www.trinityapplab.in/Company/offerLetterAction.php?mobile=$mobile&action=1' target='blank'>Approve</a>"."&nbsp;&nbsp;";
    $msg .= "<a style='text-decoration:none;padding:10px;background-color:red;color:white;border-radius:10px' href='www.trinityapplab.in/Company/offerLetterAction.php?mobile=$mobile&action=2' target='blank'>Reject</a>";
}
// $msg .= "PFA"."<br><br>";
// $msg .= "Regards"."<br>";
// $msg .= "Trinity Automation Team.";
$msg .= "</body></html>";
$response = sendMail($toMailId, $msg, "/var/www/trinityapplab.in/html/Company/files/".$dir."/".$pdfFileName);

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
function sendMail($toMailId, $msg, $attachment){
    $status = false;

    $message = $msg;
    
    $mail = new PHPMailer;
    
    $mail->isSMTP();                                      
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'communication@trinityapplab.co.in';
    $mail->Password = 'communication@Trinity';   
    $mail->Port = 587;
    $mail->SMTPSecure = 'tls';
    
    // To mail's
    $mail->addAddress($toMailId);

    $mail->setFrom("communication@trinityapplab.co.in","Trinity");
    $mail->addAttachment($attachment);
    $mail->isHTML(true);   

    // CC mail's
    $mail->addCC('shruti@trinityapplab.co.in');
    $mail->addCC('ayush.agarwal@trinityapplab.co.in');
    
    // BCC mail's
    // $mail->addBCC("jai.prakash@trinityapplab.co.in");

    
    $mail->Subject = 'Resend - Offer Letter';
    $mail->Body = "$message<br>";
    
        
    if(!$mail->send())
    {
        // echo 'Mailer Error: ' . $mail->ErrorInfo;
        // echo"<br>Could not send";
        $status = false;
    }
    else{
        // echo "mail sent";
        $status = true;
    }

    return $status;

}
?>