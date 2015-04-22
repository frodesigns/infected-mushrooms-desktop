<?php
include 'dbc.php';
user_protect();

if (isset($_SESSION['user_id'])) {
	$currentuserid = $_SESSION['user_id'];
	$query20 = sprintf("SELECT full_name FROM users WHERE id = $currentuserid");
	$result20 = mysql_query($query20);
	while ($row20 = mysql_fetch_assoc($result20)) {
			$currentusername = $row20['full_name'];
	}			
	mysql_select_db($db, $link);
	$activity = "Viewing Members List";
	$sql = "UPDATE users SET lastactivity = '$activity', lastactivitytime = CURRENT_TIMESTAMP WHERE id = $currentuserid";
	if (!mysql_query($sql,$link))
	{
		die('Error: ' . mysql_error());
	}
	
	//online update
	$time = time();
	$updateonlinequery = "UPDATE online SET timeout = \"$time\" WHERE id = $currentuserid";
	if (!mysql_query($updateonlinequery,$link))
	{
		die('Error: ' . mysql_error());
	}
	
} else {
	if(isset($_COOKIE['guestname'])) {
		$guestnamecookie = $_COOKIE['guestname']; 
	}
	$currentuserid = 0;
}

include 'header.php';

$query = sprintf("SELECT * FROM users WHERE approved = 1 AND banned = 0 AND id <> 58 ORDER BY user_level DESC, full_name ASC");
$result = mysql_query($query);

echo "<ul id='memberslist'>";
$i = 1;
while ($row = mysql_fetch_assoc($result)) {
	if ($i % 2 == 0) {
		$class = "even";
	} else {
		$class = "odd";
	}
	$active = "Inactive/Trial Member";
	$admin = "Regular Member";
	if ($row['canvote'] == 1) {
		$active = "<span class='textgreen'>Active/Full Member</span>";
	}
	if ($row['user_level'] == 5) {
		$admin = "<span class='textred'>Adminstrator</span>";
	}
	
	echo "<li class='$class'>";
	echo "<div>";
	echo $admin . "<br /><br />" . $active;
	echo "</div>";
	echo "<span class='itemname'>" . $row['full_name'] . "</span><br />" . $row['real_name'] . " - " . $row['country'];
	
	if (isset($_SESSION['user_id'])) {
	
		echo "<br /><br /><small>Last seen " . $row['lastactivity'] . " on " . date("F j, Y - g:i a", strtotime($row['lastactivitytime'])) . "</small>";
	
		if ($row['char1name'] == "" && $row['char2name'] == "" && $row['char3name'] == "" && $row['char4name'] == "") {
		
		} else {
			echo "<br /><br /><b><u>Characters</u></b>";
			if ($row['char1name'] != "") {
				echo "<br />" . $row['char1name'] . " - " . $row['char1lvl'] . " " . $row['char1type'];
			}
			if ($row['char2name'] != "") {
				echo "<br />" . $row['char2name'] . " - " . $row['char2lvl'] . " " . $row['char2type'];
			}
			if ($row['char3name'] != "") {
				echo "<br />" . $row['char3name'] . " - " . $row['char3lvl'] . " " . $row['char3type'];
			}
			if ($row['char4name'] != "") {
				echo "<br />" . $row['char4name'] . " - " . $row['char4lvl'] . " " . $row['char4type'];
			}
		}
		
	}

	echo "<br style='clear: both;' /><div style='clear: both;'>&nbsp;</div></li>";
	
	$i++;
}
echo "</ul>";

include 'footer.php'; 
?>