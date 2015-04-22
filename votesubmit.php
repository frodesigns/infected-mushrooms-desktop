<?php
include 'dbc.php';
user_protect();
$currentuserid = $_SESSION['user_id'];

$pollid = $_POST['hiddenpollid'];
$vote = $_POST['votehidden'];

mysql_select_db($db, $link);

$sql = "INSERT INTO votes (id, pollid, vote) VALUES ($currentuserid, $pollid, $vote)";

if (!mysql_query($sql,$link))
{
	die('Error: ' . mysql_error());
}

echo "Success!";
?>