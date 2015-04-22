<?php
include 'dbc.php';
user_protect();

$currentuserid = $_SESSION['user_id'];

	$title = sanitize($_POST['polltitle']);
	$content = stripslashes($_POST['content']);
	$content = stripslashes($content);
	$content = str_replace("'", "\'", $content);
	$content = strip_tags($content, $allowedTags);	
	if(isset($_POST['newrecruit']) && $_POST['newrecruit'] == '1') {
	    $newrecruit = 1;
	} else {
	    $newrecruit = 0;
	}

	if (!title || !$content) {
		echo "You have to fill in all of the fields!";
	} else {
		mysql_select_db($db, $link);
		$sql = "INSERT INTO polls (title, description, isrecruit, createdbyid) VALUES ('$title', '$content', $newrecruit, $currentuserid)";
		if (!mysql_query($sql,$link))
		{
			die('Error: ' . mysql_error());
		}	
	}	
?>