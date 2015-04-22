<?php
include 'dbc.php';
user_protect();
$currentuserid = $_SESSION['user_id'];

	$pollid = $_POST['deletepollid'];
	
	mysql_select_db($db, $link);

	$sql = "DELETE FROM polls WHERE pollid = $pollid";

	if (!mysql_query($sql,$link))
	{
		die('Error: ' . mysql_error());
	}
	
	$sql = "DELETE FROM votes WHERE pollid = $pollid";

	if (!mysql_query($sql,$link))
	{
		die('Error: ' . mysql_error());
	}
	
	echo "Success!";
?>