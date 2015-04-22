<?php

include 'dbc.php';
user_protect();
$currentuserid = $_SESSION['user_id'];

mysql_select_db($db, $link);

$pollid = $_GET['pollid'];

if (!$pollid) {
	$pollid = 0;
}
$pollidint = (int)$pollid;	
if ($pollidint == 0) {
	$pollid = 0;
}
$query3 = sprintf("SELECT pollid FROM polls WHERE pollid = $pollid");
$result3 = mysql_query($query3);
$pollrows = mysql_num_rows($result3);
if ($pollrows == 0) {
	$pollid = 0;
	echo "Invalid poll!";
	die;
}

if (checkAdmin()) {
	$query = sprintf("SELECT full_name FROM users WHERE canvote = 1 AND banned = 0 AND approved = 1 AND not exists (SELECT * FROM votes WHERE users.id = votes.id AND pollid = $pollid) ORDER BY full_name ASC");
	$result = mysql_query($query);
	$number = mysql_num_rows($result);
	echo "<u>List of members that have not voted on this poll yet:</u><div style='color: white;'>";
	while ($row = mysql_fetch_assoc($result)) {
		$name = $row['full_name'];
		echo "$name<br />";
	}	
	echo "</div>";
} else {
	echo "Only admins can see which members have not voted yet!";
}
?>