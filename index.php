<?php
$mobile = $_GET['mobile'];

if (!$mobile && !isset($_COOKIE['mobile'])) {
	include 'detectmobilebrowser.php';
} else {
	setcookie("mobile", $mobile, time()+60*60*24*COOKIE_TIME_OUT, "/");
}

include 'dbc.php';
user_protect();

if (isset($_SESSION['user_id'])) {
	$currentuserid = $_SESSION['user_id'];
	$query20 = sprintf("SELECT full_name FROM users WHERE id = $currentuserid");
	$result20 = mysql_query($query20);
	while ($row20 = mysql_fetch_assoc($result20)) {
			$currentusername = $row20['full_name'];
	}			
	mysql_select_db($db, $link);
	$activity = "Viewing Dashboard";
	$sql = "UPDATE users SET lastactivity = '$activity', lastactivitytime = CURRENT_TIMESTAMP WHERE id = $currentuserid";

	if (!mysql_query($sql,$link))
	{
		die('Error: ' . mysql_error());
	}
} else {
	if(isset($_COOKIE['guestname'])) {
		$guestnamecookie = $_COOKIE['guestname']; 
	}
	$currentuserid = 0;
}

include 'header.php';

if (isset($_SESSION['user_id'])) {
	//$query1 = sprintf("SELECT title, threadid FROM threads WHERE private = 1 AND (not exists (SELECT * FROM readthreads WHERE threads.threadid = readthreads.threadid AND id = $currentuserid) OR exists (SELECT * FROM readthreads WHERE threads.threadid = readthreads.threadid AND id = $currentuserid AND isread = 0)) ORDER BY title ASC");
	$query1 = sprintf("SELECT title, threads.threadid, timestamp, users.full_name AS 'updatedby' 
		FROM threads 
		INNER JOIN users ON users.id = threads.updatedbyid
		INNER JOIN readthreads ON readthreads.id = $currentuserid AND readthreads.threadid = threads.threadid 
		WHERE private = 1 AND readthreads.isread != 1
		ORDER BY timestamp DESC
		LIMIT 80");
	$result1 = mysql_query($query1);
	$privthreads = mysql_num_rows($result1);

	//$query2 = sprintf("SELECT title, threadid FROM threads WHERE private = 0 AND (not exists (SELECT * FROM readthreads WHERE threads.threadid = readthreads.threadid AND id = $currentuserid) OR exists (SELECT * FROM readthreads WHERE threads.threadid = readthreads.threadid AND id = $currentuserid AND isread = 0)) ORDER BY title ASC");
	$query2 = sprintf("SELECT a.threadid, title, timestamp, c.full_name as 'updatedby', (SELECT guestname FROM posts b WHERE b.threadid = a.threadid AND b.spam = 0 ORDER BY timestamp DESC LIMIT 1) AS 'lastguestauthor'
		FROM threads a 
		LEFT JOIN users b on a.authorid = b.id
		LEFT JOIN users c on a.updatedbyid = c.id
		LEFT JOIN readthreads d ON d.id = $currentuserid AND d.threadid = a.threadid
		WHERE private = 0  AND d.isread != 1
		ORDER BY timestamp DESC
		LIMIT 80");
	$result2 = mysql_query($query2);
	$pubthreads = mysql_num_rows($result2);	
	
	$query3 = sprintf("SELECT title, pollid FROM polls WHERE not exists (SELECT * FROM votes WHERE polls.pollid = votes.pollid AND id = $currentuserid) ORDER BY title ASC");
	$result3 = mysql_query($query3);
	$polls = mysql_num_rows($result3);	
	
	$query4 = sprintf("SELECT full_name, id FROM users WHERE approved = 1 ORDER BY id DESC LIMIT 1");
	$result4 = mysql_query($query4);
	while ($row4 = mysql_fetch_assoc($result4)) {
		$newestmember = $row4['full_name'];
		$newestmemberid = $row4['id'];
	}
	
	$query5 = sprintf("SELECT itemname, users.full_name as 'reporter' 
		FROM items 
		INNER JOIN users ON items.reporterid = users.id
		WHERE missing = 1 
		ORDER BY itemname DESC");
	$result5 = mysql_query($query5);
	$missingitems = mysql_num_rows($result5);
	

	echo "<div class='dashboard'>";
	echo "<span class='headername'>My Dashboard</span><br />";	
	echo "<div style='margin-bottom: 20px;'>";
	echo "<div style='margin-bottom: 20px;'><span class='subheadername'>Announcements:</span><br />";
	echo "<ol>";
	echo "<li>Check out the new <a href='http://immobile.frodesigns.com'>Mobile Website</a>!</li>";
	echo "<li>Have you updated your <a href='/im/mysettings.php'>Profile</a> yet?</li>";
	echo "</ol>";
	echo "<ul>";
	if ($missingitems > 0) {
		while ($row5 = mysql_fetch_assoc($result5)) {
			echo "<li style='color: #dc3522;'>" . $row5['itemname'] . " has been flagged missing by " . $row5['reporter'] . "!</li>";
		}
	// } else {
		// echo "No new announcements.";
	}
	echo "</ul>";
	if ($privthreads > 0 || $pubthreads > 0) {
		echo "<br /><a class=\"markallread button blue\">Mark All Threads as Read</a>";
	}
	echo "</div><div class='threecolumn'><span class='subheadername'>Unread Private Forum Posts</span><br />";
	echo "<ul>";
	if ($privthreads > 0) {		
		while ($row1 = mysql_fetch_assoc($result1)) {
			$threadtitle = $row1['title'];
			$threadid = $row1['threadid'];			
			echo "<li><a href='thread.php?threadid=$threadid#last' title='Go To Last Post'>#</a> - <a href='thread.php?threadid=$threadid' title='Go To First Post'>$threadtitle</a></li>";
		}
	} else {
		echo "<li>No unread private posts.</li>";
	}
	echo "</ul></div>";
	echo "<div class='threecolumn'><span class='subheadername'>Unread Public Forum Posts</span><br />";
	echo "<ul>";
	if ($pubthreads > 0) {		
		while ($row2 = mysql_fetch_assoc($result2)) {
			$threadtitle = $row2['title'];
			$threadid = $row2['threadid'];			
			echo "<li><a href='thread.php?threadid=$threadid#last' title='Go To Last Post'>#</a> - <a href='thread.php?threadid=$threadid' title='Go To First Post'>$threadtitle</a></li>";
		}
	} else {
		echo "<li>No unread public posts.</li>";
	}
	echo "</ul></div>";
	echo "<div class='threecolumn lastcolumn'><span class='subheadername'>New Polls</span><br />";
	echo "<ul>";
	if ($polls > 0) {		
		while ($row3 = mysql_fetch_assoc($result3)) {
			$polltitle = $row3['title'];	
			$pollid = $row3['pollid'];				
			echo "<li><a href='polls.php#$pollid'>$polltitle</a></li>";
		}
	} else {
		echo "<li>No new polls.</li>";
	}
	echo "</ul></div></div><br style='clear: both' /><br style='clear: both' />";
	echo "<span class='subheadername'>Last Member to Register:</span> ";
	echo "<strong><a class='profile' rel='$newestmemberid'>$newestmember</a></strong><br /><br />";	
	echo "<span class='subheadername'>Who's Online:</span> <strong>";
	echo $onlinelist;
	echo "</strong></div>";
} else { ?>
<div class='guesthome'>
<span class='itemname'>Welcome to the Infected Mushrooms guild website!</span>
<p>
Infected Mushrooms is a guild which goes back to the old days on the International server in Korea. When Helbreath USA was launched and the International servers went Pay-2-Play, many players from Infected Mushrooms moved to Helbreath USA and started all over again. Not all left in the beginning but within a few months the rest moved as well. 

We are currently the only guild remaining since the beginning of the Helbreath USA server, and it is our values and friendship that have gotten us this far.  

If you have any unanswered questions about Infected Mushrooms after browsing this website, feel free to ask any of us in-game or on our <a href="/im/forum.php">Public Forum</a>.
</p>
<span class='itemname'>Want to join us?</span>
<p>
Players who wish to join the guild may post on our forum with information about themselves and their characters. Remember to go to the <a href="/im/rules.php">Rules</a> section and check to see if you will be able to obey them.

We generally recruit 1-3 new recruits every three months.  In certain cases, the guild will take a break from recruiting. The periods for when we stop and start recruiting will be announced in the <a href="/im/forum.php">Public Forum</a>.

We want you to feel comfortable and welcome in whatever guild you join, so if you do sign up for Infected Mushrooms - please try and get to know our members first.
</p>
<ol class='recruitinfo'>
<li>Post an application about yourself in our <a href="/im/forum.php">Public Forum</a>.</li>
<li>A poll will be made where 67% yes votes are needed to join the guild.</li>
<li>After a succesful vote, there will be a 3 month trial period where the guild and the new recruit can get to know each other. There will be no access to guild items in this period.</li>
<li>After the trial period there will be another poll where 80% yes votes are needed.</li>
<li>If the recruit gets the 80% yes votes, he/she becomes a full member. If not, he/she will be asked to leave the guild and will not be able to apply for the guild again.</li>
</ol>
</div>
<?php 
} 
include 'footer.php'; 
 ?>