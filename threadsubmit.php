<?php
include 'dbc.php';
include 'Akismet.class.php';
user_protect();

if (isset($_SESSION['user_id'])) 
{
	$authorid = $_SESSION['user_id'];
} else {
	$authorid = 0;
}
	$guestname = sanitize($_POST['guestname']);
	$guestemail = sanitize($_POST['guestemail']);
	
	if ($guestname) {
		$inTwoMonths = 60 * 60 * 24 * 60 + time(); 
		setcookie('guestname', $guestname, $inTwoMonths);
	}
	
	if ($guestemail) {
		$inTwoMonths = 60 * 60 * 24 * 60 + time(); 
		setcookie('guestemail', $guestemail, $inTwoMonths);
	}
	
	if(isset($_POST['sticky']) && $_POST['sticky'] == '1') {
	    $sticky = 1;
	} else {
	    $sticky = 0;
	}	
	
	$query2 = sprintf("SELECT full_name FROM users WHERE full_name = '$guestname' OR user_name = '$guestname'");
	$result2 = mysql_query($query2);
	$num_rows = mysql_num_rows($result2);

	if ($num_rows != 0) {
		echo "You can't pretend to be a guild member!  Please choose a different name.";
	} else {
		$title = sanitize($_POST['title']);
		$content = stripslashes($_POST['content']);
		$content = stripslashes($content);
		$content = str_replace("'", "\'", $content);
		$content = strip_tags($content, $allowedTags);	
		
		if (!$title || !$content) {
			echo "You have to fill in all of the fields!";
		} else {
			if ((!$guestname || !$guestemail) && $authorid == "NULL") {
				echo "You have to fill in all of the fields!";
			} else {
			
				$akismet = new Akismet($MyURL ,$AkismetAPIKey);
				if ($guestname) {					
					$akismet->setCommentAuthor($guestname);
					$akismet->setCommentAuthorEmail($guestemail);
					$akismet->setCommentContent($content);
				}
				
				mysql_select_db($db, $link);
				
				if($akismet->isCommentSpam() && $guestname) {
					echo "Spam Detected!";
				} else {

					$sql = "INSERT INTO threads (title, private, sticky, authorid, updatedbyid) VALUES ('$title', 0, $sticky, $authorid, $authorid)";
					if (!mysql_query($sql,$link))
					{
						die('Error: ' . mysql_error());
					}
					
					$query = sprintf("SELECT threadid FROM threads ORDER BY timestamp DESC LIMIT 1");
					$result = mysql_query($query);
					while ($row = mysql_fetch_assoc($result)) {
						$threadid = $row['threadid'];
					}
					
					$sql = "INSERT INTO posts (threadid, content, authorid, guestname, guestemail) VALUES ($threadid, '$content', $authorid, '$guestname', '$guestemail')";

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
			}
		}				
	}	
?>