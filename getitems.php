<?php

define ("DB_HOST", "localhost"); // set database host
define ("DB_USER", "frodesig_fro"); // set database user
define ("DB_PASS","carmal87"); // set database password
define ("DB_NAME","frodesig_im"); // set database name

$link = mysql_connect(DB_HOST, DB_USER, DB_PASS) or die("Couldn't make connection.");
$db = mysql_select_db(DB_NAME, $link) or die("Couldn't select database");

$query = sprintf("SELECT itemid, itemname, missing, imageurl, reporterid FROM items ORDER BY itemname ASC");
$result = mysql_query($query);
$i = 1;
while ($row = mysql_fetch_assoc($result)) {
	$itemid = (int)$row['itemid'];
	
	
	$query3 = sprintf("SELECT full_name, id FROM users ORDER BY full_name ASC");
	$result3 = mysql_query($query3);	
	
	if ($i % 2) {
    echo "<form method='post' class='alternate'>";
	} else {
	echo "<form method='post'>";
	}	
	$i++;

	echo "<input type='hidden' name='currentuserid' value='$currentuserid' />";
	$itemname = $row['itemname'];
	$imageurl = $row['imageurl'];
	
	echo "<div>Who currently has this item? <select id='item' name='item'>";
	echo "<option value=''>-</option>";
	while ($row3 = mysql_fetch_assoc($result3)) {
		$id = $row3['id'];
		$value = $id." ".$itemid;				
		echo "<option value='$value'>", $row3['full_name'] ,"</option>";		
	}
	echo "</select>";
	echo " <input type='submit' name='updatesubmit' class='button blue' value='Update' />";
	echo "<br /><br />Are you logging out with this item? <input type='hidden' name='logged' value='$itemid' /> <input type='submit' name='loggedsubmit' class='button blue' value='Logging Out' />";
	if ($row['missing'] == 1)
	{
		$reporterid2 = $row['reporterid'];
	
		$query5 = sprintf("SELECT full_name FROM users WHERE id = $reporterid2");
		$result5 = mysql_query($query5);
	
		while ($row5 = mysql_fetch_assoc($result5)) {
			$reportername2 = $row5['full_name'];
		}
		
		echo "<br /><br /><span>Flagged as missing by <strong>" . $reportername2 . "</strong>!</span> <input type='hidden' name='found' value='$itemid' /> <input type='submit' class='button blue' name='foundsubmit' value='Found It!' />";
	}	
	else {
		echo "<br /><br />Is this item missing? <input type='hidden' name='flag' value='$itemid' /> <input type='submit' class='button blue' name='flagsubmit' value='Flag It!' />";
	}
	echo "</div>";
	
	if ($imageurl)
	{
		echo "<div class='image'><img src='$imageurl' /></div>";
	} else {
		echo "<div class='image'></div>";
	}
	echo "<div class='info'>";
	echo "<span class='itemname'>$itemname</span><br />";
	
	$_GET['id'] = $itemid;
	include 'getlog.php';
	
	echo "</div>";
	echo "<br style='clear:both;' /><!--[if lte IE 7]><br style='clear:both;' /><![endif]-->";
	echo "</form>";
	mysql_free_result($result3);
}

mysql_free_result($result);
?>