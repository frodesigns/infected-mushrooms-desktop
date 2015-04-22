<?php
include 'dbc.php';
include 'Akismet.class.php';
user_protect();

if (isset($_SESSION['user_id'])) 
{
	$authorid = $_SESSION['user_id'];
} else {
	$authorid = "NULL";
}
	$guestname = sanitize($_POST['guestname']);
	$guestemail = sanitize($_POST['guestemail']);
	$threadid = $_POST['threadid'];
	
	if ($guestname) {
		$inTwoMonths = 60 * 60 * 24 * 60 + time(); 
		setcookie('guestname', $guestname, $inTwoMonths);
	}
	
	if ($guestemail) {
		$inTwoMonths = 60 * 60 * 24 * 60 + time(); 
		setcookie('guestemail', $guestemail, $inTwoMonths);
	}
	
	$query2 = sprintf("SELECT full_name FROM users WHERE full_name = '$guestname' OR user_name = '$guestname'");
	$result2 = mysql_query($query2);
	$num_rows = mysql_num_rows($result2);

	if ($num_rows != 0) {
		echo "You can't pretend to be a guild member!  Please choose a different name.";
	} else {
		$content = stripslashes($_POST['content']);
		$content = stripslashes($content);
		$content = str_replace("'", "\'", $content);
		$content = strip_tags($content, $allowedTags);	
		
		if (!$content) {
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
				
					$sql = "INSERT INTO posts (threadid, content, authorid, guestname, guestemail, spam) VALUES ($threadid, '$content', $authorid, '$guestname', '$guestemail', 1)";
					if (!mysql_query($sql,$link))
					{
						die('Error: ' . mysql_error());
					}	
					
					echo "Spam Detected!";
				
				} else {

					$sql = "INSERT INTO posts (threadid, content, authorid, guestname, guestemail, spam) VALUES ($threadid, '$content', $authorid, '$guestname', '$guestemail', 0)";
					if (!mysql_query($sql,$link))
					{
						die('Error: ' . mysql_error());
					}			
					
					$sql = "UPDATE readthreads SET isread = 0 WHERE threadid = $threadid";
					if (!mysql_query($sql,$link))
					{
						die('Error: ' . mysql_error());
					}	
					
					$sql = "UPDATE threads SET timestamp = CURRENT_TIMESTAMP(), updatedbyid = $authorid, postcount = (postcount + 1) WHERE threadid = $threadid";
					if (!mysql_query($sql,$link))
					{
						die('Error: ' . mysql_error());
					}	

					echo $threadid;	

				}					
			}
		}
	}
	
?>