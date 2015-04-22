<?php
include 'dbc.php';
page_protect();

$currentuserid = $_SESSION['user_id'];
$eventid = $_POST['eventid'];

mysql_select_db($db, $link);

$sql = "INSERT INTO eventattendees (eventid, id) VALUES ($eventid, $currentuserid)";
if (!mysql_query($sql,$link))
{
	die('Error: ' . mysql_error());
}	

echo "Success!";

?>