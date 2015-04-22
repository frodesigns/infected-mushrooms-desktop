<?php
include 'dbc.php';
page_protect();

$currentuserid = $_SESSION['user_id'];
$query20 = sprintf("SELECT full_name FROM users WHERE id = $currentuserid");
$result20 = mysql_query($query20);
while ($row20 = mysql_fetch_assoc($result20)) {
		$currentusername = $row20['full_name'];
}

	mysql_select_db($db, $link);
	$activity = mysql_real_escape_string("Viewing Item Tracker");
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

if ($_POST['updatesubmit'])
{
	if ($_POST['item'])
	{	
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
			
			//echo "Success!";
			
		} else {
			//echo "You can't give an item to the same person twice! Silly.";
		}		
	}
} else if ($_POST['flagsubmit']) {
	
	$itemid = $_POST['itemidhidden'];
	
	mysql_select_db($db, $link);

	$sql = "UPDATE items SET missing = 1, reporterid = $currentuserid WHERE itemid = $itemid";

	if (!mysql_query($sql,$link))
	{
		die('Error: ' . mysql_error());
	}
	
	//echo "<div id='message'><div id='close'>X</div>Item has successfully been flagged as missing!</div>";
	
} else if ($_POST['foundsubmit']) {

	$itemid = $_POST['itemidhidden'];
	
	mysql_select_db($db, $link);

	$sql = "UPDATE items SET missing = 0 WHERE itemid = $itemid";

	if (!mysql_query($sql,$link))
	{
		die('Error: ' . mysql_error());
	}	
} else if ($_POST['loggedsubmit']) {

		$itemid = $_POST['itemidhidden'];
		
		$query40 = sprintf("SELECT id, status FROM itemuser WHERE itemid = $itemid ORDER BY timestamp DESC LIMIT 1");
		$result40 = mysql_query($query40);
		while ($row40 = mysql_fetch_assoc($result40)) {
			$lastloggedid = $row40['id'];
			$status = $row40['status'];
		}
		
		if ($currentuserid == $lastloggedid && $status == 1) {
			//echo "You can't log out with the same item twice! Silly.";
		} else {
			mysql_select_db($db, $link);

			$sql = "INSERT INTO itemuser (id, itemid, reporterid, status) VALUES ($currentuserid, $itemid, $currentuserid, 1)";

			if (!mysql_query($sql,$link))
			{
				die('Error: ' . mysql_error());
			}
			
			//echo "Success!";
		}
}

include 'header.php';

echo "<input type='hidden' id='currentuserid' value='$currentuserid' />";
echo "<input type='hidden' id='currentusername' value='$currentusername' />";

$query = sprintf("SELECT itemid, itemname, history, missing, imageurl, reporterid FROM items WHERE status = 1 ORDER BY itemname ASC");
$result = mysql_query($query);

$query10 = sprintf("SELECT itemid, itemname, missing, imageurl, reporterid FROM items WHERE status = 1 ORDER BY itemname ASC");
$result10 = mysql_query($query10);	

echo "<div id='quickjump'><span class='itemname'>Quick Jump Menu</span><br />";
while ($row10 = mysql_fetch_assoc($result10)) {
	$itemname10 = $row10['itemname'];
	$imageurl10 = $row10['imageurl'];
	$itemid10 = $row10['itemid'];
	echo "<a rel='$itemid10' title='$itemname10'><img src='$imageurl10' alt='$itemname10' /></a>";
}

echo "</div><div id='itemlist'>";
while ($row = mysql_fetch_assoc($result)) {
	$itemid = (int)$row['itemid'];
	
	$query2 = sprintf("SELECT users.full_name, itemuser.timestamp, itemuser.reporterid, itemuser.status FROM users, itemuser, items WHERE items.itemid = $itemid AND items.itemid = itemuser.itemid AND users.id = itemuser.id ORDER BY itemuser.timestamp DESC LIMIT 3");
	$result2 = mysql_query($query2);
	
	$query3 = sprintf("SELECT full_name, id FROM users WHERE (approved = 1 AND banned = 0 AND canvote = 1) OR id = 56 OR id = 326 ORDER BY full_name ASC");
	$result3 = mysql_query($query3);	
	
    echo "<form method='post' id='$itemid' action='tracker.php'>";
	
	$itemname = $row['itemname'];
	$history = $row['history'];
	$imageurl = $row['imageurl'];
	
	echo "<div><span class='who'>Who currently has this item?</span> <select name='item' class='passedto'>";
	echo "<option value=''>-</option>";
	while ($row3 = mysql_fetch_assoc($result3)) {
		$id = $row3['id'];
		$value = $id;				
		echo "<option value='$value'>", $row3['full_name'] ,"</option>";		
	}
	echo "</select> ";
	echo "<input type='submit' name='updatesubmit' class='button blue updatesubmit' value='Update' />";
	echo "<input type='hidden' class='itemidhidden' name='itemidhidden' value='$itemid' />";
	echo "<br /><br /><span class='logging'>Are you logging out with this item?</span> <input type='hidden' name='logged' value='$itemid' /> <input type='submit' name='loggedsubmit' class='button blue loggedsubmit' value='Logging Out' />";
	if ($row['missing'] == 1)
	{
		$reporterid2 = $row['reporterid'];
	
		$query5 = sprintf("SELECT full_name FROM users WHERE id = $reporterid2");
		$result5 = mysql_query($query5);
	
		while ($row5 = mysql_fetch_assoc($result5)) {
			$reportername2 = $row5['full_name'];
		}
		
		echo "<br /><br /><span class='found'>Flagged as missing by <strong>" . $reportername2 . "</strong>!</span> <input type='hidden' name='found' value='$itemid' /> <input type='submit' class='button blue foundsubmit' name='foundsubmit' value='Found It!' />";
	}	
	else {
		echo "<br /><br /><span class='flag'>Is this item missing?</span> <input type='hidden' name='flag' value='$itemid' /> <input type='submit' class='button blue flagsubmit' name='flagsubmit' value='Flag It!' />";
	}
	echo "</div>";
	if ($imageurl)
	{
		echo "<div class='image'><img src='$imageurl' title='Scroll To Top' /></div>";
	} else {
		echo "<div class='image'></div>";
	}
	
	echo "<div class='info'>";
	if (!$history) {
		$history = "No history for this item.";
	}
	echo "<span class='itemname itemhistory' rel='$history'>$itemname</span>";
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
			echo "<span class='lineitem'><strong>" . $row2['full_name'] . "</strong> had this on " . $date . "<br /><span class='reporter'>Reported by <strong>$reportername</strong></span></span>";	
		} else {
			echo "<span class='lineitem'><strong>" . $row2['full_name'] . "</strong> logged out with this item<br /><span class='reporter'>$date</span></span>";
		}
	}
	
	echo "</div>";
	
	echo "<br style='clear:both;' /><!--[if lte IE 7]><br style='clear:both;' /><![endif]-->";
	echo "</form>";
	mysql_free_result($result2);
	mysql_free_result($result3);
}
echo "</div>";
?>

<?php
mysql_free_result($result);
?>

<?php include 'footer.php';  ?>