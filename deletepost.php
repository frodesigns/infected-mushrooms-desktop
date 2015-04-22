<?php
include 'dbc.php';
user_protect();
$currentuserid = $_SESSION['user_id'];

	$postid = $_POST['deletepostid'];
	
	mysql_select_db($db, $link);

	$sql = "DELETE FROM posts WHERE postid = $postid";

	if (!mysql_query($sql,$link))
	{
		die('Error: ' . mysql_error());
	}
	
	$sql = "UPDATE threads SET postcount = (postcount - 1) WHERE threadid = $threadid";
	
	if (!mysql_query($sql,$link))
	{
		die('Error: ' . mysql_error());
	}	
	
	echo "Success!";
?>