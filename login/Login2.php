<?php
    $con = mysqli_connect("jarvis.mlieou.com:61306", "bluetooth", "%P#CDurZh3cph/Nf", "AttendanceTracking");
    
    $username = $_POST["username"];
    $password = $_POST["password"];
    
    $statement = mysqli_prepare($con, "SELECT * FROM Users WHERE username = ? AND password = ?");
    mysqli_stmt_bind_param($statement, "ss", $username, $password);
    mysqli_stmt_execute($statement);
    
    mysqli_stmt_store_result($statement);
    mysqli_stmt_bind_result($statement, $userID, $username, $password,$studentid);
    
    $response = array();
    $response["success"] = false;  
    
    while(mysqli_stmt_fetch($statement)){
        $response["success"] = true;  
        
        $response["username"] = $username;
		$response["studentid"] = $studentid;
        
        $response["password"] = $password;
    }
    
    echo json_encode($response);
?>
