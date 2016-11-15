<?php
$con=mysqli_connect("jarvis.mlieou.com:61306","bluetooth","%P#CDurZh3cph/Nf","AttendanceTracking");


if (mysqli_connect_errno($con))
{
   echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
else{
	echo "we are inserting";


}


$date=$_POST['datetime'];
 
date_default_timezone_set('America/Los_Angeles');
$date1 = date('m/d/Y h:i:s', $date);
$a=date("Y-m-d H:i:s");

$sql="INSERT INTO Logs (dateTime, userid, via,courseid) VALUES ('{$a}','22','888',99)";
 $retval = mysql_query( $sql, $conn );
if (mysqli_query($con,$sql))
{
   echo "row inserted";
}

mysqli_close($con);
?>