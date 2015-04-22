<?php
include 'dbc.php';
user_protect();
$currentuserid = $_SESSION['user_id'];
	
	mysql_select_db($db, $link);

	$sql = "UPDATE readthreads SET isread = 1 WHERE id = $currentuserid";

	if (!mysql_query($sql,$link))
	{
		die('Error: ' . mysql_error());
	}
?>