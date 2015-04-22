<?php
include 'dbc.php';
page_protect();

$currentuserid = $_SESSION['user_id'];
$query20 = sprintf("SELECT full_name FROM users WHERE id = $currentuserid");
$result20 = mysql_query($query20);
while ($row20 = mysql_fetch_assoc($result20)) {
		$currentusername = $row20['full_name'];
}

include 'header.php';

echo "<input type='hidden' id='currentname' value='$currentusername' />";

?>
<div style="display: none;" id='eventform'><div id='close'>X</div>
<span class='itemname' style='font-weight: normal;'>Add Event</span><br />
	<span style='color: white; font-weight: normal;'>
		<form id='addeventform' name='addeventform' action='addevent.php' method='post'>
			Event Name: <input id="eventname" type='text' maxlength='50' style="width: 300px;" name='eventname' /><br /><br />
			Start Time: <input type="text" id="rangeDemoStart" size="14" name="startDate" style="width: 180px;" /> End Time: <input type="text" id="rangeDemoFinish" name="endDate" size="14" style="width: 180px;" /><br /><br />
			<input type='submit' class='button blue' value='Submit' />
		</form>
	</span>
</div>
<div class='ui-red'>
<div id='loading' style='display:none'>loading...</div>
<div id='calendar'></div>
</div>

<?php
include 'footer.php';  
?>