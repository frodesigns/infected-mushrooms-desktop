<?php
include 'dbc.php';
user_protect();
$currentuserid = $_SESSION['user_id'];

	$itemid = $_POST['itemidhidden'];
	
	mysql_select_db($db, $link);

	$sql = "UPDATE items SET missing = 0 WHERE itemid = $itemid";

	if (!mysql_query($sql,$link))
	{
		die('Error: ' . mysql_error());
	}
	echo "Success!";
?>