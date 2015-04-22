<?php
include 'dbc.php';
user_protect();

$currentuserid = $_SESSION['user_id'];

	$postid = $_POST['postid'];
	$content = stripslashes($_POST['edit']);
	$content = stripslashes($content);
	$content = str_replace("'", "\'", $content);
	$content = strip_tags($content, $allowedTags);	

	mysql_select_db($db, $link);

	$sql = "UPDATE posts SET content = '$content' WHERE postid = $postid";

	if (!mysql_query($sql,$link))
	{
		die('Error: ' . mysql_error());
	}
	
	$query = sprintf("SELECT content FROM posts WHERE postid = $postid");
	$result = mysql_query($query);
	while ($row = mysql_fetch_assoc($result)) {
		$newcontent = $row['content'];
	}
	
	echo $newcontent;
?>