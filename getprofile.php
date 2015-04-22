<?php

include 'dbc.php';
user_protect();
$currentuserid = $_SESSION['user_id'];

$id = $_GET['id'];

if (!$id) {
	$id = 54;
}
$idint = (int)$id;	
if ($idint == 0) {
	$id = 54;
}

mysql_select_db($db, $link);

	$query = sprintf("SELECT * FROM users WHERE id = $id");
	$result = mysql_query($query);	
	$number = mysql_num_rows($result);
	
	while ($row = mysql_fetch_assoc($result)) {
		$profile = array(
			"full_name" => $row['full_name'],
			"real_name" => $row['real_name'],
			"country" => $row['country'],
			"msn" => $row['msn'],
			"user_email" => $row['user_email'],
			"lastactivity" => $row['lastactivity'],
			"lastactivitytime" => date("F j, Y" ." - ". "g:i a", strtotime($row['lastactivitytime'])),
			"char1name" => $row['char1name'],
			"char1lvl" => $row['char1lvl'],
			"char1type" => $row['char1type'],
			"char2name" => $row['char2name'],
			"char2lvl" => $row['char2lvl'],
			"char2type" => $row['char2type'],
			"char3name" => $row['char3name'],
			"char3lvl" => $row['char3lvl'],
			"char3type" => $row['char3type'],
			"char4name" => $row['char4name'],
			"char4lvl" => $row['char4lvl'],
			"char4type" => $row['char4type']
		);
	}
	
	$profileJSON = json_encode($profile);
	
	echo $profileJSON;

?>