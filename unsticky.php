<?php
include 'dbc.php';
user_protect();
$currentuserid = $_SESSION['user_id'];

	$threadid = $_POST['threadid'];
	
	mysql_select_db($db, $link);

	$sql = "UPDATE threads SET sticky = 0 WHERE threadid = $threadid";

	if (!mysql_query($sql,$link))
	{
		die('Error: ' . mysql_error());
	}
	echo "Success!";
?>