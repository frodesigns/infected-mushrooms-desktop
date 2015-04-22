<?php
include 'dbc.php';
user_protect();
$currentuserid = $_SESSION['user_id'];

	$threadid = $_POST['deletethreadid'];
	
	mysql_select_db($db, $link);

	$sql = "DELETE FROM posts WHERE threadid = $threadid";

	if (!mysql_query($sql,$link))
	{
		die('Error: ' . mysql_error());
	}
	
	$sql = "DELETE FROM threads WHERE threadid = $threadid";

	if (!mysql_query($sql,$link))
	{
		die('Error: ' . mysql_error());
	}
	
	$sql = "DELETE FROM readthreads WHERE threadid = $threadid";

	if (!mysql_query($sql,$link))
	{
		die('Error: ' . mysql_error());
	}
	
	echo "Success!";
?>