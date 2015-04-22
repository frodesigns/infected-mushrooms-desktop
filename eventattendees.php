<?php

include 'dbc.php';
user_protect();
$currentuserid = $_SESSION['user_id'];

mysql_select_db($db, $link);

$eventid = $_GET['eventid'];

if (!$eventid) {
	$eventid = 0;
}
$eventidint = (int)$eventid;	
if ($eventidint == 0) {
	$eventid = 0;
}
$query3 = sprintf("SELECT eventid FROM events WHERE eventid = $eventid");
$result3 = mysql_query($query3);
$pollrows = mysql_num_rows($result3);
if ($pollrows == 0) {
	$eventid = 0;
	echo "Invalid event!";
	die;
}

	$query = sprintf("SELECT id, (SELECT full_name FROM users b WHERE b.id = a.id) as name FROM eventattendees a WHERE eventid = $eventid ORDER BY name ASC");
	$result = mysql_query($query);
	$num_rows = mysql_num_rows($result);
	$disabled = "";
	
	$query2 = sprintf("SELECT * FROM events WHERE eventid = $eventid");
	$result2 = mysql_query($query2);
	while ($row2 = mysql_fetch_assoc($result2)) {
		$start = date("F j, Y - g:i a", strtotime($row2['startdate']));
		$end = date("F j, Y - g:i a", strtotime($row2['enddate']));
		$title = $row2['eventname'];
		
		echo "<span class='itemname'>$title</span><br /><span style='color: white;'>Starts: $start<br />Ends: $end</span><br /><br />";
	}	
	
	echo "<u>Event Attendees:</u><div class='attendeelist' style='color: white;'>";
	while ($row = mysql_fetch_assoc($result)) {
		$name = $row['name'];
		$id = $row['id'];
		echo "$name<br />";
		if ($id == $currentuserid) {
			$disabled = "disabled";
		}
	}	
	//if ($num_rows == 0) {
	//	echo "None Yet<br />";
	//}
	
	echo "</div><form style='display: inline; position: absolute; top: 10px; right: 10px;' name='joinevent' id='addeventform' action='joinevent.php' method='post'><input type='hidden' name='eventid' value='$eventid' /><input id='joinevent' type='submit' class='button blue' value='Join This Event' $disabled /></form>";

?>