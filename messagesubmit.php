<?php
include 'dbc.php';
page_protect();

$fromid = $_SESSION['user_id'];
$toid = $_POST['item'];
$replytomessageid = $_POST['replytomessageid'];
$title = sanitize($_POST['messagetitle']);
$content = stripslashes($_POST['content']);
$content = stripslashes($content);
$content = str_replace("'", "\'", $content);
$content = strip_tags($content, $allowedTags);

if (!$replytomessageid) {
	$replytomessageid = 0;
}	

if (!$content || !$title || !$toid) {
	echo "You have to fill in all of the fields!";
} else {
	mysql_select_db($db, $link);
	
	if ($toid == "admins") {
		$query = sprintf("SELECT id FROM users WHERE user_level = 5 AND id != $fromid");
		$result = mysql_query($query);
		while ($row = mysql_fetch_assoc($result)) {
			$adminid = $row['id'];
			$sql = "INSERT INTO messages (replytomessageid, toid, fromid, title, content) VALUES ($replytomessageid, $adminid, $fromid, '$title', '$content')";
			if (!mysql_query($sql,$link))
			{
				die('Error: ' . mysql_error());
			}		
		}
	} else {
		$sql = "INSERT INTO messages (replytomessageid, toid, fromid, title, content) VALUES ($replytomessageid, $toid, $fromid, '$title', '$content')";
		if (!mysql_query($sql,$link))
		{
			die('Error: ' . mysql_error());
		}		
	}			
	
	echo "Message Sent!";		
}
?>