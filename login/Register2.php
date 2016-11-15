<?php
    $con = mysqli_connect("jarvis.mlieou.com:61306", "bluetooth", "%P#CDurZh3cph/Nf", "AttendanceTracking");
    
    

    $username = $_POST["username"];
    $password = $_POST["password"];
	$studentid = $_POST["studentid"];

    $statement = mysqli_prepare($con, "INSERT INTO Users (username,password ,studentid) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($statement, "sss",$username, $password, $studentid);
    mysqli_stmt_execute($statement);
    
    $response = array();
    $response["success"] = true;  
    
    echo json_encode($response);
?>
