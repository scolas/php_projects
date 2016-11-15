<?php
if (isset($_POST['name'], $_POST['lname'], $_POST['email'], $_POST['artistname'], $_POST['generes'], $_POST['country'], $_POST['states'] )) {

	print_r($_POST);
	echo 'Your name is ' . $_POST['state'];
}


?>