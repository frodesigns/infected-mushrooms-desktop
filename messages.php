<?php
include 'dbc.php';
page_protect();

$currentuserid = $_SESSION['user_id'];
$query20 = sprintf("SELECT full_name FROM users WHERE id = $currentuserid");
$result20 = mysql_query($query20);
while ($row20 = mysql_fetch_assoc($result20)) {
		$currentusername = $row20['full_name'];
}

	$activity = "Viewing Messages";
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

include 'header.php';

echo "<div class='ui-red'><div class='messages' id='tabs'>";

echo "<ul><li><a href='#tabs-1'>Inbox</a></li><li><a href='#tabs-2'>Sent</a></li></li></ul>";
$query1 = sprintf("SELECT messages.*, users.full_name AS 'sender' 
	FROM messages 
	INNER JOIN users ON users.id = messages.fromid
	WHERE toid = $currentuserid 
	ORDER BY timestamp DESC");
$result1 = mysql_query($query1);
echo "<div class='inbox' id='tabs-1'><ul class='inboxlist'>";
while ($row1 = mysql_fetch_assoc($result1)) {
	$messageid = $row1['messageid'];
	$replytomessageid = $row1['replytomessageid'];
	$sender = $row1['sender'];
	$title = $row1['title'];
	$content = $row1['content'];
	$isread = (int)$row1['isread'];
	if ($isread == 0) {
		$boldclass = "boldauth";
	} else {
		$boldclass = "";
	}
	$date = date("F j, Y - g:i a", strtotime($row1['timestamp']));
	
	echo "<li class='message' id='$messageid'><form class='updateread' name='updatereadform$messageid' action='messageread.php' method='post'><input type='hidden' name='messageidhidden' value='$messageid' /><a class='messagetitle itemname $boldclass'>$title</a></form>Sent by <strong>$sender</strong> on <em>$date</em><div class='messagecontent'><div class='themessage'>$content</div><a class='messagereplynoquote button blue' style='color: black !important;' alt='$sender' rel='$messageid' />Reply</a> <a class='messagereply button blue' style='color: black !important;' alt='$sender' rel='$messageid' />Reply With Quote</a> <a class='messagedelete button blue' style='color: black !important;' rel='$messageid' />Delete</a></div></li>";
}
echo "</ul></div>";

$query2 = sprintf("SELECT messages.*, users.full_name AS 'receiver' 
	FROM messages 
	INNER JOIN users ON users.id = messages.toid
	WHERE fromid = $currentuserid 
	ORDER BY timestamp DESC");
$result2 = mysql_query($query2);
echo "<div class='sent' id='tabs-2'><ul class='sentlist'>";
while ($row2 = mysql_fetch_assoc($result2)) {
	$messageid = $row2['messageid'];
	$replytomessageid = $row2['replytomessageid'];
	$receiver = $row2['receiver'];
	$title = $row2['title'];
	$content = $row2['content'];
	$isread = (int)$row2['isread'];
	if ($isread == 0) {
		$boldclass = "boldauth";
	} else {
		$boldclass = "";
	}
	$date = date("F j, Y - g:i a", strtotime($row2['timestamp']));
	
	echo "<li class='message' id='$messageid'><a class='messagesenttitle itemname $boldclass'>$title</a><br />Sent to <strong>$receiver</strong> on <em>$date</em><div class='messagecontent'>$content</div></li>";
}
echo "</ul></div>";

echo "</div></div>";

echo "<div class=\"bottombuttons\">
<a class=\"scrolltop button blue\">Back to Top</a>
</div>";

$query3 = sprintf("SELECT full_name, id FROM users WHERE approved = 1 AND banned = 0 ORDER BY full_name ASC");
$result3 = mysql_query($query3);	
?>

      <table width="80%" border="0" cellpadding="5" cellspacing="2" class="forms">
		<tr><td><span class="itemname">New Message</span></td></tr>
        <tr>
          <td><form id="newmessage" name="form" method="post" action="messagesubmit.php" enctype="multipart/form-data">			
			  <?php 
			  echo "<input type='hidden' class='replytomessageid' name='replytomessageid' value='0' />"; 
			  echo "<input type='hidden' class='sendername' name='sendername' value='$currentusername' />";
			  ?>
			  <p>
               <?php
				echo "Send To: <select name='item' class='passedto'>";
				echo "<option value=''>-</option>";
				if (checkAdmin()) {
					echo "<option value='admins'>All Admins</option>";
				}				
				while ($row3 = mysql_fetch_assoc($result3)) {
					$id = $row3['id'];
					$value = $id;				
					echo "<option value='$value'>", $row3['full_name'] ,"</option>";		
				}
				echo "</select> ";
				?>
			  </p>   
			  <p>Message Title:
                <input name="messagetitle" maxlength="50" type="text" id="messagetitle">
			  </p>			  
			  <p><div>
				<textarea class='tinymce' name="content" id="content" rows="15" cols="70"></textarea>		
			  </div></p>
                <input type="submit" class='button blue' value="Send">
              </p>
            </form>
          </td>
        </tr>
      </table>

<?php include 'footer.php';  ?>