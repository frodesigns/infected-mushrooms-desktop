<?php

include 'dbc.php';
user_protect();
$currentuserid = $_SESSION['user_id'];

mysql_select_db($db, $link);

	$query = sprintf("SELECT DISTINCT killname FROM medusakills ORDER BY killname ASC");
	$result = mysql_query($query);	
	$number = mysql_num_rows($result);
	$i = 1;
	while ($row = mysql_fetch_assoc($result)) {
		$killname = $row['killname'];
		echo "$killname";
		if($i < $number){
			echo ",";
		}
		$i++;
	}

?>