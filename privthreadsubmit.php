<?php
include 'dbc.php';
page_protect();

	$authorid = $_SESSION['user_id'];

	if(isset($_POST['sticky']) && $_POST['sticky'] == '1') {
	    $sticky = 1;
	} else {
	    $sticky = 0;
	}	
	
	$title = sanitize($_POST['title']);
	$content = stripslashes($_POST['content']);
	$content = stripslashes($content);
	$content = str_replace("'", "\'", $content);
	$content = strip_tags($content, $allowedTags);	
	
	if (!$title || !$content) {
		echo "You have to fill in all of the fields!";
	} else {
		mysql_select_db($db, $link);

		$sql = "INSERT INTO threads (title, private, sticky, authorid, updatedbyid) VALUES ('$title', 1, $sticky, $authorid, $authorid)";
		if (!mysql_query($sql,$link))
		{
			die('Error: ' . mysql_error());
		}
		
		$query = sprintf("SELECT threadid FROM threads WHERE private = 1 ORDER BY timestamp DESC LIMIT 1");
		$result = mysql_query($query);
		while ($row = mysql_fetch_assoc($result)) {
			$threadid = $row['threadid'];
		}
		
		$sql = "INSERT INTO posts (threadid, content, authorid) VALUES ($threadid, '$content', $authorid)";

		if (!mysql_query($sql,$link))
		{
			die('Error: ' . mysql_error());
		}
		
		$query2 = sprintf("SELECT id FROM users");
		$result2 = mysql_query($query2);
		while ($row2 = mysql_fetch_assoc($result2)) {
			$userid = $row2['id'];
			
			$sql = "INSERT INTO readthreads (id, threadid, isread) VALUES ($userid, $threadid, 0)";
			if (!mysql_query($sql,$link))
			{
				die('Error: ' . mysql_error());
			}
		}
		
		echo $threadid;			
	}					
?>