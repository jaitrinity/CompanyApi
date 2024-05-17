<?php 
$conn = mysqli_connect("[hostname]","[username]","[password]","[dbname]");
// $currentYear = "2024";
$currentYear = date('Y');
$sql = "SELECT `Name`, date_format(`Date`,'%W') as `Dayy`, date_format(`Date`,'%d-%b-%Y') as `Datee` FROM `Holidays` WHERE date_format(`Date`,'%Y') = '$currentYear' ORDER by `Date`";
$query=mysqli_query($conn,$sql);
$rowCount=mysqli_num_rows($query);
if($rowCount == 0){
	echo "<span style='font-size:100px'>"."Holiday list not found of $currentYear, please contact to HR..."."</span>";
	return;
}
$table = "<table style='font-size:40px' border=1 cellspacing=0 cellpadding=5>
	<thread>
		<tr>
			<th>Name</th>
			<th>Day</th>
			<th>Date</th>
		</tr>
	</thead>
	<tbody>";
while($row = mysqli_fetch_assoc($query)){
	$table .= "<tr> 
				<td>".$row["Name"]."</td>
				<td>".$row["Dayy"]."</td>
				<td>".$row["Datee"]."</td>
			</tr>";
}
$table .= "</tbody></table>";

echo $table;
?>