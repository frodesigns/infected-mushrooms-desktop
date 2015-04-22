<?php
include 'dbc.php';
user_protect();
$currentuserid = $_SESSION['user_id'];

	$messageid = $_POST['messageidhidden'];
	
	mysql_select_db($db, $link);

	$sql = "UPDATE messages SET isread = 1 WHERE messageid = $messageid";

	if (!mysql_query($sql,$link))
	{
		die('Error: ' . mysql_error());
	}
	echo "Success!";
?>