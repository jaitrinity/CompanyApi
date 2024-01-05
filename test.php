<?php 
// $mystring = "Twinkle twinkl4e little star";
// print_r(str_word_count($mystring));

// $mystring = "Twinkle twinkle little star";
// $aa = str_word_count($mystring, 1);
// for($i=0;$i<count($aa);$i++){
// 	echo $aa[$i].'----';
// }
$inLine = 5;
$word = "abc def ghi jkl mno pqr stu vwx yz 1 2 3 4 5 6 7 8 9 0";
$wordCount = str_word_count($word);
$wordCountArr = str_word_count($word,1);
echo count($wordCountArr);
// $workIndexArr = array();
// $workIndexStr = "";
// $inLine = 6;
// $len = 12;
// for($i=0;$i<$len;$i++){
// 	$workIndexStr .= $i;
// 	if(($i+1)%$inLine == 0){
// 		$workIndexStr .= ":";
// 	}
// 	else{
// 		if($i<$len-1){
// 			$workIndexStr .= ",";
// 		}
// 	}
// }
// echo $workIndexStr;
// for($j=0;$j<count($workIndexArr);$j++){
// 	echo $workIndexArr[$j].'-------';
// }
?>