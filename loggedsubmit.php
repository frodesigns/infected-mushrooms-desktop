<?php
include 'dbc.php';
user_protect();
$currentuserid = $_SESSION['user_id'];
	$itemid = $_POST['itemidhidden'];
		
	$query40 = sprintf("SELECT id, status FROM itemuser WHERE itemid = $itemid ORDER BY timestamp DESC LIMIT 1");
	$result40 = mysql_query($query40);
	while ($row40 = mysql_fetch_assoc($result40)) {
		$lastloggedid = $row40['id'];
		$status = $row40['status'];
	}
	
	if ($currentuserid == $lastloggedid && $status == 1) {
		echo "You can't log out with the same item twice! Silly.";
	} else {
		mysql_select_db($db, $link);

		$sql = "INSERT INTO itemuser (id, itemid, reporterid, status) VALUES ($currentuserid, $itemid, $currentuserid, 1)";

		if (!mysql_query($sql,$link))
		{
			die('Error: ' . mysql_error());
		}
		
		echo "Tracker Updated!";
	}
?>