<?php

define ("DB_HOST", "localhost"); // set database host
define ("DB_USER", "frodesig_fro"); // set database user
define ("DB_PASS","carmal87"); // set database password
define ("DB_NAME","frodesig_im"); // set database name

$link = mysql_connect(DB_HOST, DB_USER, DB_PASS) or die("Couldn't make connection.");
$db = mysql_select_db(DB_NAME, $link) or die("Couldn't select database");

$itemid = $_GET['itemid'];

$query2 = sprintf("SELECT users.full_name, itemuser.timestamp, itemuser.reporterid, itemuser.status FROM users, itemuser, items WHERE items.itemid = $itemid AND items.itemid = itemuser.itemid AND users.id = itemuser.id ORDER BY itemuser.timestamp DESC LIMIT 5");
$result2 = mysql_query($query2);

while ($row2 = mysql_fetch_assoc($result2)) {
		$date = date("F j, Y - g:i a", strtotime($row2['timestamp']));
		
		$reporterid = $row2['reporterid'];
	
		$query4 = sprintf("SELECT full_name FROM users WHERE id = $reporterid");
		$result4 = mysql_query($query4);
	
		while ($row4 = mysql_fetch_assoc($result4)) {
			$reportername = $row4['full_name'];
		}
		if ($row2['status'] == 0)
		{
			echo "<strong>" . $row2['full_name'] . "</strong> had this on " . $date . "<br /><span class='reporter'>Reported by <strong>$reportername</strong></span><br />";	
		} else {
			echo "<strong>" . $row2['full_name'] . "</strong> logged out with this item<br /><span class='reporter'>$date</span><br />";
		}
	}
		mysql_free_result($result2);
	
?>