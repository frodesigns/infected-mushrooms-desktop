<?php
include 'dbc.php';
user_protect();
$currentuserid = $_SESSION['user_id'];

	$messageid = $_GET['messageid'];
	
	mysql_select_db($db, $link);

	$sql = "DELETE FROM messages WHERE messageid = $messageid AND toid = $currentuserid";

	if (!mysql_query($sql,$link))
	{
		die('Error: ' . mysql_error());
	}
?>