<?php
include 'dbc.php';
user_protect();

include 'header.php';	

if (isset($_SESSION['user_id'])) {
	$currentuserid = $_SESSION['user_id'];
	$query20 = sprintf("SELECT full_name FROM users WHERE id = $currentuserid");
	$result20 = mysql_query($query20);
	while ($row20 = mysql_fetch_assoc($result20)) {
			$currentusername = $row20['full_name'];
	}	
	
	mysql_select_db($db, $link);
	$activity = mysql_real_escape_string("Viewing Polls");
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
	
} else {
	if(isset($_COOKIE['guestname'])) {
		$guestnamecookie = $_COOKIE['guestname']; 
	}
	$currentuserid = 0;
}

$query3 = sprintf("SELECT COUNT(*) FROM users WHERE canvote = 1 AND banned = 0 AND approved = 1");
$result3 = mysql_query($query3);
while ($row3 = mysql_fetch_assoc($result3)) {
	$votetotal = (int)$row3['COUNT(*)'];
}

$query4 = sprintf("SELECT canvote FROM users WHERE id = $currentuserid");
$result4 = mysql_query($query4);
while ($row4 = mysql_fetch_assoc($result4)) {
	$canvote = (int)$row4['canvote'];
}

$query = sprintf("SELECT pollid, title, description, (SELECT COUNT(*) FROM votes b WHERE a.pollid = b.pollid AND vote = 0) AS 'no', (SELECT COUNT(*) FROM votes b WHERE a.pollid = b.pollid AND vote = 1) AS 'yes' FROM polls a WHERE isrecruit = 1 ORDER BY title ASC");
$result = mysql_query($query);
$num_recruits = mysql_num_rows($result);

if ($num_recruits > 0) {
	echo "<div class='recruits'>";
	$progressscripts = "";
		while ($row = mysql_fetch_assoc($result)) {
			$pollid = $row['pollid'];
			$title = $row['title'];
			$description = $row['description'];
			$yes = (int)$row['yes'];
			$no = (int)$row['no'];			
			$yespercent = round(($yes / $votetotal) * 100);
			$nopercent = round(($no / $votetotal) * 100);
			$percentincrease = round((1 / $votetotal) * 100);
			$noclickpercent = $nopercent + $percentincrease;
			$yesclickpercent = $yespercent + $percentincrease;
			if ($yespercent == 0) {
				$zeroyesclass = "zero";
			} else {
				$zeroyesclass = "";
			}
			if ($nopercent == 0) {
				$zeronoclass = "zero";
			} else {
				$zeronoclass = "";
			}
			$query11 = sprintf("SELECT id FROM votes WHERE pollid = $pollid and id = $currentuserid");
			$result11 = mysql_query($query11);
			$currentuser_votecount = mysql_num_rows($result11);
			$progressscripts .= "$('#$pollid .ui-green .progressbar').progressbar({ value: $yespercent }); ";
			$progressscripts .= "$('#$pollid .ui-red .progressbar').progressbar({ value: $nopercent }); ";
			
			echo "<div class='poll' id='$pollid'><span class='itemname'>$title</span><br />";
			echo "<div class='pollbars'><form name='pollform' action='votesubmit.php' method='post'><input type='hidden' name='hiddenpollid' value='$pollid' /><input type='hidden' name='votehidden' class='votehidden' value='' /><div class='ui-green'><span class='polltext'>Yes: </span><div class='progressbar $zeroyesclass'></div><div class='progresstext'>$yespercent%</div></div><br /><div class='ui-red'><span class='polltext'>No: </span><div class='progressbar $zeronoclass'></div><div class='progresstext'>$nopercent%</div></div>";
			if (isset($_SESSION['user_id'])) {
				if ($currentuser_votecount == 0 && $canvote == 1) {
					echo "<br /><div class='buttons'>Vote: <input type='submit' class='voteyes button green' name='voteyes' rel='$yesclickpercent' value='Yes' /> <input type='submit' class='voteno button red' name='voteno' rel='$noclickpercent' value='No' /></div>";
				} elseif ($canvote == 0) {
					echo "<br /><div class='buttons'>You don't have voting rights!</div>";				
				} else {
					echo "<br /><div class='buttons'>Thank you for voting!</div>";
				}
			}
			echo "</form>";
			if (checkAdmin()) {
				echo "<strong>Admin:</strong> <a class='votelist button blue'>Vote List</a> <a class='editpoll button blue'>Edit</a> <form class='deletepoll' name='deletepollform' action='deletepoll.php' method='post'><input type='hidden' name='deletepollid' value='$pollid'><input type='submit' class='deletepollbutton button blue' value='Delete'></form>";
			}
			echo "</div>";
			echo "<div class='polldescrip'>$description</div><br style='clear: both' /><br style='clear: both' /></div>";
		}
		echo "<script type='text/javascript'>$(document).ready(function(){ $progressscripts});</script>";
	echo "</div>";
} else {	
	echo "<div class='recruits'><strong>There are no new recruit polls at this time.</strong></div><br />";
}

if (isset($_SESSION['user_id'])) {

	$query2 = sprintf("SELECT pollid, title, description, (SELECT COUNT(*) FROM votes b WHERE a.pollid = b.pollid AND vote = 0) AS 'no', (SELECT COUNT(*) FROM votes b WHERE a.pollid = b.pollid AND vote = 1) AS 'yes' FROM polls a WHERE isrecruit = 0 ORDER BY title ASC");
	$result2 = mysql_query($query2);
	$num_polls = mysql_num_rows($result2);
	
	if ($num_polls < 1) {
		echo "<div class='polls'><strong>There are no new private guild polls at this time.</strong></div>";
	} else {
		echo "<div class='polls'>";
		$progressscripts = "";
		while ($row2 = mysql_fetch_assoc($result2)) {
			$pollid = $row2['pollid'];
			$title = $row2['title'];
			$description = $row2['description'];
			$yes = (int)$row2['yes'];
			$no = (int)$row2['no'];			
			$yespercent = round(($yes / $votetotal) * 100);
			$nopercent = round(($no / $votetotal) * 100);
			$percentincrease = round((1 / $votetotal) * 100);
			$noclickpercent = $nopercent + $percentincrease;
			$yesclickpercent = $yespercent + $percentincrease;
			if ($yespercent == 0) {
				$zeroyesclass = "zero";
			} else {
				$zeroyesclass = "";
			}
			if ($nopercent == 0) {
				$zeronoclass = "zero";
			} else {
				$zeronoclass = "";
			}			
			$query22 = sprintf("SELECT id FROM votes WHERE pollid = $pollid and id = $currentuserid");
			$result22 = mysql_query($query22);
			$currentuser_votecount = mysql_num_rows($result22);
			$progressscripts .= "$('#$pollid .ui-green .progressbar').progressbar({ value: $yespercent }); ";
			$progressscripts .= "$('#$pollid .ui-red .progressbar').progressbar({ value: $nopercent }); ";
			
			echo "<div class='poll' id='$pollid'><span class='itemname'>$title</span><br />";
			echo "<div class='pollbars'><form name='pollform' action='votesubmit.php' method='post'><input type='hidden' name='hiddenpollid' value='$pollid' /><input type='hidden' name='votehidden' class='votehidden' value='' /><div class='ui-green'><span class='polltext'>Yes: </span><div class='progressbar $zeroyesclass'></div><div class='progresstext'>$yespercent%</div></div><br /><div class='ui-red'><span class='polltext'>No: </span><div class='progressbar $zeronoclass'></div><div class='progresstext'>$nopercent%</div></div>";
			if ($currentuser_votecount == 0 && $canvote == 1) {
				echo "<br /><div class='buttons'>Vote: <input type='submit' class='voteyes button green' name='voteyes' rel='$yesclickpercent' value='Yes' /> <input type='submit' class='voteno button red' name='voteno' rel='$noclickpercent' value='No' /></div>";
			} elseif ($canvote == 0) {
					echo "<br /><div class='buttons'>You don't have voting rights!</div>";				
			} else {
				echo "<br /><div class='buttons'>Thank you for voting!</div>";
			}
			echo "</form>";
			if (checkAdmin()) {
				echo "<strong>Admin:</strong> <a class='votelist button blue'>Vote List</a> <a class='editpoll button blue'>Edit</a> <form class='deletepoll' name='deletepollform' action='deletepoll.php' method='post'><input type='hidden' name='deletepollid' value='$pollid'><input type='submit' class='deletepollbutton button blue' value='Delete'></form>";
			}
			echo "</div>";
			echo "<div class='polldescrip'>$description</div><br style='clear: both' /><br style='clear: both' /></div>";
		}
		echo "<script type='text/javascript'>$(document).ready(function(){ $progressscripts});</script>";
		echo "</div>";
	}
?>

<h3 class="titlehdr">Add a Poll or New Recruit</h3>
      <table width="80%" border="0" cellpadding="5" cellspacing="2" class="forms">
        <tr>
          <td><form id="pollform" name="pollform" method="post" action="pollsubmit.php" enctype="multipart/form-data">
              <p>Poll Title
                <input name="polltitle" maxlength="50" type="text" id="polltitle">
			  </p>              
			  <p>Description
				<textarea class='tinymce' name="content" id="content" rows="15" cols="70"></textarea>				
			  </p>
			  <p>Is this for a new recruit? (Makes a publically visible poll)
				<input name="newrecruit" type="checkbox" id="newrecruit" value="1">
			  </p>
                <input type="submit" class='button blue' value="Add Poll">
              </p>
            </form>
          </td>
        </tr>
      </table>
<?php 
}
include 'footer.php';
?>