<?php
include 'dbc.php';
user_protect();
$currentuserid = $_SESSION['user_id'];
	if ($_POST['item']) {	
		$id = $_POST['item'];				
		$itemid = $_POST['itemidhidden'];	
		
		$query30 = sprintf("SELECT id FROM itemuser WHERE itemid = $itemid ORDER BY timestamp DESC LIMIT 1");
		$result30 = mysql_query($query30);
		while ($row30 = mysql_fetch_assoc($result30)) {
			$lastuserid = $row30['id'];
		}
		
		if ($id != $lastuserid) {
			mysql_select_db($db, $link);

			$sql = "INSERT INTO itemuser (id, itemid, reporterid, status) VALUES ($id, $itemid, $currentuserid, 0)";

			if (!mysql_query($sql,$link))
			{
				die('Error: ' . mysql_error());
			}
			
			echo "Tracker Updated!";
			
		} else {
			echo "You can't give an item to the same person twice! Silly.";
		}		
	} else {
	 echo "Please select a recipient!";
	}
?>