<?php

$mysql_host='localhost';
$mysql_user='root';
$mysql_password='';



if(!@mysql_connect($mysql_host,$mysql_user,$mysql_password))
{
	
		die('Cannot Connect to database');

}
else
{
	if(@mysql_select_db('venmuse')){
       echo 'connection ok';
}

else{
	echo "cannot connect to database";
}

}

?>