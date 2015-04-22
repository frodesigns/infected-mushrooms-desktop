<?php
include 'dbc.php';
user_protect();
if (isset($_SESSION['user_id'])) 
{
	$currentuserid = $_SESSION['user_id'];
	$query20 = sprintf("SELECT full_name FROM users WHERE id = $currentuserid");
	$result20 = mysql_query($query20);
	while ($row20 = mysql_fetch_assoc($result20)) {
			$currentusername = $row20['full_name'];
	}
	
	mysql_select_db($db, $link);
	$activity = mysql_real_escape_string("Viewing Medusa Gallery");
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
}

if ($_POST['viewall']) {
	$beginstamp = "2010-01-01 00:00:00";
	$endstamp = "2999-12-30 00:00:00";
	$currentview = "Viewing All";
} else {
	$currentmonth = date("m");
	$currentyear = date("Y");

	$month = $_POST['month'];

	$year = $_POST['year'];

	if (!$month || !$year) {
		$month = $currentmonth;
		$year = $currentyear;
	}
	$monthint = (int)$month;	
	$yearint = (int)$year;
	if ($monthint == 0 || $yearint == 0) {
		$month = $currentmonth;
		$year = $currentyear;
	}
	
	//$currentview = "Viewing " . $month . "/" . $year;
	


	$beginstamp = $year . "-" . $month . "-1 00:00:00";
	if ($month == 12) {
		$year++;
		$month = "01";
	} else {
		$month++;
		if ($month < 10) {
			$month = "0" . $month;
		}
	}
	$endstamp = $year . "-" . $month . "-1 00:00:00";
	
}

$deathmethod = array("annihilated","demolished","devastated","eradicated","gutted","obliterated","ravaged","ruined","shattered","wrecked","maimed","butchered","liquidated","mutilated","eviscerated","disemboweled","decimated");

include 'header.php';
echo "<input type='hidden' id='currentuserid' value='$currentuserid' />";
echo "<input type='hidden' id='currentusername' value='$currentusername' />";
echo "<input type='hidden' id='month' value='$month' />";
echo "<input type='hidden' id='year' value='$year' />";
?>
<h3 class="titlehdr">Medusa Kill Gallery</h3>
<div class="quote">"The object of war is not to die for your country, but to make the other bastard die for his." -George S. Patton</div>
<form action='medusa.php' method='post' style="text-align: center; color: black;">
	Month: 
	<select name='month' id='monthselect'>
		<option value="01">January</option>
		<option value="02">February</option>
		<option value="03">March</option>
		<option value="04">April</option>
		<option value="05">May</option>
		<option value="06">June</option>
		<option value="07">July</option>
		<option value="08">August</option>
		<option value="09">September</option>
		<option value="10">October</option>
		<option value="11">November</option>
		<option value="12">December</option>
	</select>
	Year: 
	<select name='year' id='yearselect'>
		<option value="2010">2010</option>
		<option value="2011">2011</option>
		<option value="2012">2012</option>
	</select>
	<input type='submit' value='Go' /> 
	<input type='submit' name="viewall" value='View All' /> 
	<strong><?php echo $currentview; ?></strong>
</form>
<br />
<div class="scoreboardcontainer">
<div id="slidercontainer">
<div id="slider">
<?php
	//echo "SELECT id, killname, imageurl, thumburl, timestamp FROM medusakills WHERE timestamp > '$beginstamp' AND timestamp < '$endstamp' ORDER BY timestamp DESC";
	$query = sprintf("SELECT id, killname, imageurl, thumburl, timestamp FROM medusakills WHERE timestamp > '$beginstamp' AND timestamp < '$endstamp' ORDER BY timestamp DESC");
	$result = mysql_query($query);
	$num_rows = mysql_num_rows($result);	
	while ($row = mysql_fetch_assoc($result)) {
		$id = (int)$row['id'];
		$killname = $row['killname'];
		$imageurl = str_replace("'", "&#39;", $row['imageurl']);
		$imagefixedurl = str_replace(" ", "%20", $imageurl);
		$thumburl = str_replace("'", "&#39;", $row['thumburl']);
		$date = date("F j, Y - g:i a", strtotime($row['timestamp']));
		$query2 = sprintf("SELECT full_name FROM users WHERE id = $id");
		$result2 = mysql_query($query2);
		while ($row2 = mysql_fetch_assoc($result2)) {
			$name = $row2['full_name'];						
		}
		$randmethod=array_rand($deathmethod);
		echo "<img src='$imagefixedurl' class='$name $killname' alt='$name killed $killname' title='$name $deathmethod[$randmethod] $killname<br /><small>Permalink: http://www.frodesigns.com/im/$imageurl</small>' rel='$thumburl' />";
	}
?>
</div>
</div>
<div id="scoreboard">
<h3 class="titlehdr" style="margin: 0 0 5px 0; display: inline-block; ">Scoreboard</h3>
<h4>Top Activations</h4>
<ul>
<?php
	$query3 = sprintf("SELECT full_name, id, (SELECT COUNT(*) FROM medusakills WHERE timestamp > '$beginstamp' AND timestamp < '$endstamp' AND medusakills.id = users.id) AS killcount FROM users WHERE approved = 1 AND banned = 0 ORDER BY killcount DESC");
	$result3 = mysql_query($query3);
	while ($row3 = mysql_fetch_assoc($result3)) {
		$name = $row3['full_name'];
		$id = $row3['id'];
		$killcount = $row3['killcount'];
		if ($killcount > 0) {
			echo "<li id='$name'>$name - $killcount</li>";
		}
	}
?>
</ul>
<br />
<h4>Top Deaths</h4>
<ul>
<?php
	$query4 = sprintf("SELECT DISTINCT killname, (SELECT COUNT(*) FROM medusakills a WHERE timestamp > '$beginstamp' AND timestamp < '$endstamp' AND a.killname = b.killname) as 'count' from medusakills b WHERE killname <> 'Unknown' ORDER BY count DESC");
	$result4 = mysql_query($query4);	
	while ($row4 = mysql_fetch_assoc($result4)) {
		$killname = $row4['killname'];
		$deathcount = $row4['count'];
		if ($deathcount > 0) {
			echo "<li id='$killname'>$killname - $deathcount</li>";
		}
	}
	$query5 = sprintf("SELECT DISTINCT killname, (SELECT COUNT(*) FROM medusakills a WHERE timestamp > '$beginstamp' AND timestamp < '$endstamp' AND a.killname = b.killname) as 'count' from medusakills b WHERE killname = 'Unknown' ORDER BY count DESC");
	$result5 = mysql_query($query5);	
	while ($row5 = mysql_fetch_assoc($result5)) {
		$killname = $row5['killname'];
		$deathcount = $row5['count'];
		if ($deathcount > 0) {
			echo "<li id='$killname'><br />$killname Kills - $deathcount</li>";
		}
	}
?>
</ul>
</div>
<br style="clear: both;" />
<br style="clear: both;" />
</div>

<?php 
	// $rowcount = ($num_rows / 10);
	// if (!is_int($rowcount)) {
		// $rowcount = $rowcount + 1;
	// }
	// $rowcount = (int)$rowcount;
	// for ($i = 1; $i <= $rowcount; $i++) {
		// echo "<br /><br /><br />";
	// }
?>

<?php 
if (isset($_SESSION['user_id'])) 
{
?>

<h3 class="titlehdr">Add a Screenshot</h3>
      <table width="80%" border="0" cellpadding="5" cellspacing="2" class="forms">
        <tr>
          <td><form id="medusaform" name="form" method="post" action="medusasubmit.php" enctype="multipart/form-data">
              <p>Who died in this screenshot?
                <input name="killname" type="text" id="killname"> <br />*Try to spell this exactly as it appears in the game.  If you don't know, please put <u>Unknown</u>.
			  </p>              
			  <p>Image
				<input type="file" name="image" id="file" /><br />*Please only upload images of dead Aresden when YOU have medusa activated.				
			  </p>
                <input type="submit" class='button blue' value="Add Screenshot">
              </p>
            </form>
          </td>
        </tr>
      </table>
<?php 
}
include 'footer.php';
?>