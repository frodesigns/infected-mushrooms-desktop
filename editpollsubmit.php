<?php
include 'dbc.php';
user_protect();

$currentuserid = $_SESSION['user_id'];

	$pollid = $_POST['pollid'];
	$content = stripslashes($_POST['edit']);
	$content = stripslashes($content);
	$content = str_replace("'", "\'", $content);
	$content = strip_tags($content, $allowedTags);	

	mysql_select_db($db, $link);

	$sql = "UPDATE polls SET description = '$content' WHERE pollid = $pollid";

	if (!mysql_query($sql,$link))
	{
		die('Error: ' . mysql_error());
	}
	
	$query = sprintf("SELECT description FROM polls WHERE pollid = $pollid");
	$result = mysql_query($query);
	while ($row = mysql_fetch_assoc($result)) {
		$newcontent = $row['description'];
	}
	
	echo $newcontent;
?>