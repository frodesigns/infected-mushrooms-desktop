<?php
include 'dbc.php';
page_protect();

$createdbyid = $_SESSION['user_id'];
$eventname = sanitize($_POST['eventname']);
$startdate = $_POST['startDate'];
$enddate = $_POST['endDate'];
$description = stripslashes($_POST['description']);
$description = stripslashes($description);
$description = str_replace("'", "\'", $description);
$description = strip_tags($description, $allowedTags);

if (!$eventname) {
	echo "You have to fill in all of the fields!";
} else {
	mysql_select_db($db, $link);
	
	$sql = "INSERT INTO events (eventname, startdate, enddate, description, createdbyid) VALUES ('$eventname', '$startdate', '$enddate', '$description', $createdbyid)";
	if (!mysql_query($sql,$link))
	{
		die('Error: ' . mysql_error());
	}		
	
	$query = sprintf("SELECT eventid FROM events ORDER BY createddate DESC LIMIT 1");
	$result = mysql_query($query);
	while ($row = mysql_fetch_assoc($result)) {
		$eventid = $row['eventid'];
	}
	
	echo $eventid;		
}
?>