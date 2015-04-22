<?php
include 'dbc.php';
	
	mysql_select_db($db, $link);	

	$query = sprintf("SELECT * FROM events");
	$result = mysql_query($query);		
	$num_rows = mysql_num_rows($result);
	$i = 1;	
	$eventarray = array();
		while ($row = mysql_fetch_assoc($result)) {	
			$id = $row['eventid'];
			$title = $row['eventname'];
			$start = $row['startdate'];
			$end = $row['enddate'];
			$eventarray[] = array('id' => "$id", 'title' => "$title", 'start' => "$start", 'end' => "$end" );
		}	
	echo json_encode($eventarray);

?>
