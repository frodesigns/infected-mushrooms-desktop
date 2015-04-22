<?php
include 'dbc.php';
page_protect();

include 'header.php';	

$currentuserid = $_SESSION['user_id'];	
$query20 = sprintf("SELECT full_name FROM users WHERE id = $currentuserid");	
$result20 = mysql_query($query20);	
while ($row20 = mysql_fetch_assoc($result20)) {			
	$currentusername = $row20['full_name'];	
}	

	mysql_select_db($db, $link);
	$activity = mysql_real_escape_string("Viewing Private Forum");
	$sql = "UPDATE users SET lastactivity = '$activity', lastactivitytime = CURRENT_TIMESTAMP WHERE id = $currentuserid";

	if (!mysql_query($sql,$link))
	{
		die('Error: ' . mysql_error());
	}

echo "<input type='hidden' id='currentuserid' value='$currentuserid' />";	
echo "<input type='hidden' id='currentusername' value='$currentusername' />";
?>

<div class='forumwrapper'>
<h3 class='forumtitle'>Private Forum</h3>
<div class='topbuttons'>
<a class="scrollpost button blue">Create Post</a>
</div>
<ul class='forum'>
	<li><div class='thread'>Thread Title</div><div class='author'>Author</div> <div class='updated'>Last Updated</div> <div class='replies'>Replies</div></li>
</ul>

<?
	$query0 = sprintf("SELECT COUNT(*) AS 'count' FROM threads WHERE private = 1");	
	$result0 = mysql_query($query0);	
	while ($row0 = mysql_fetch_assoc($result0)) {			
		$threadcount = $row0['count'];	
	}	

	$pagecount = ceil($threadcount / 20);

	$currentpage = $_GET['page'];
	if (!$currentpage) {
		$currentpage = 1;
	}
	
	$start = ($currentpage - 1) * 20;

	echo "<strong class='page'>Page:</strong> <ul class=\"simplePagerNav\">";
	for ($i = 1; $i <= $pagecount; $i++) {
		if ($i == $currentpage) {
			echo "<li class=\"currentPage simplePageNav$i\"><a rel=\"$i\" href=\"privforum.php?page=$i\">$i</a></li>";
		} else {
			echo "<li class=\"simplePageNav$i\"><a rel=\"$i\" href=\"privforum.php?page=$i\">$i</a></li>";
		}
	}
	echo "</ul>";
?>

<ul class='forumpager'>

<?php
$query = sprintf("SELECT threadid, title, sticky, timestamp, (SELECT authorid FROM posts b WHERE b.threadid = a.threadid ORDER BY timestamp ASC LIMIT 1) AS 'authorid' , (SELECT authorid FROM posts b WHERE b.threadid = a.threadid ORDER BY timestamp DESC LIMIT 1) AS 'lastauthorid', (SELECT timestamp FROM posts b WHERE b.threadid = a.threadid ORDER BY timestamp ASC LIMIT 1) AS 'created', (SELECT timestamp FROM posts b WHERE b.threadid = a.threadid ORDER BY timestamp DESC LIMIT 1) AS 'updated', (SELECT isread FROM readthreads c WHERE c.threadid = a.threadid AND c.id = $currentuserid) AS 'read', (SELECT full_name FROM users WHERE id = authorid) AS 'authorname', (SELECT full_name FROM users WHERE id = lastauthorid) AS 'lastauthorname', (SELECT COUNT(*) FROM posts b WHERE a.threadid = b.threadid) AS 'postcount' FROM threads a WHERE private = 1 ORDER BY sticky DESC, updated DESC LIMIT $start, 20");
$result = mysql_query($query);
$num_rows = mysql_num_rows($result);
while ($row = mysql_fetch_assoc($result)) {
	$threadid = (int)$row['threadid'];
	$title = $row['title'];
	$lastupdated = date("F j, Y - g:i a", strtotime($row['updated']));
	$authorid = $row['authorid'];
	$lastauthorid = $row['lastauthorid'];
	$created = $row['created'];
	$read = $row['read'];
	$sticky = (int)$row['sticky'];
	
	if ($read == 1) {
		$threadclass = "";
	} else {
		$threadclass = "unread";
	}
	
	if ($currentuserid == 0) {
		$threadclass = "";
	}
	
	$authorid = (int)$authorid;
	$authorname = $row['authorname'];
	$authorcolor = "boldauth";
	
	if ($lastguestauthor != "") {
		$lastauthorname = $lastguestauthor;
	} else {
		$lastauthorid = (int)$lastauthorid;
		$lastauthorname = $row['lastauthorname'];	
	}

	$threadposts = $row['postcount'];
	$replies = (int)$threadposts;
	$replies--;	
	
	$pagecount = ($num_rows / 20);
	if (!is_int($pagecount)) {
		$pagecount++;
	}
	$pagecount = (int)$pagecount;
	
	$threadpages = ($threadposts / 20);
	if (!is_int($threadpages)) {
		$threadpages++;
	}
	$threadpages = (int)$threadpages;
		
	
	if ($sticky == 1) {
		echo "<li><div class='thread'>Sticky: <a href='thread.php?threadid=$threadid' class='$threadclass'>$title</a></div><div class='author $authorcolor'>$authorname</div> <div class='updated'><a href='thread.php?threadid=$threadid#last'><em>$lastupdated</em><br />by <strong>$lastauthorname</strong></a></div> <div class='replies'>$replies</div></li>";
	} else {
		echo "<li><div class='thread'><a href='thread.php?threadid=$threadid' class='$threadclass'>$title</a></div><div class='author $authorcolor'>$authorname</div> <div class='updated'><a href='thread.php?threadid=$threadid#last'><em>$lastupdated</em><br />by <strong>$lastauthorname</strong></a></div> <div class='replies'>$replies</div></li>";
	}

}
?>

</ul>
<div class='bottombuttons'>
<a class="scrolltop button blue">Back to Top</a>
</div>
</div>

<table width="80%" border="0" cellpadding="5" cellspacing="2" class="forms">
		<tr><td><span class="itemname">New Private Thread</span></td></tr>
        <tr>
          <td><form id="newprivthread" name="form" method="post" action="privthreadsubmit.php" enctype="multipart/form-data">
              <p>Thread Title
                <input name="title" maxlength="50" type="text" id="title">
			  </p>              
			  <p><div>
				<textarea class='tinymce' name="content" id="content" rows="15" cols="70"></textarea>		
			  </div></p>
			  <?php if (checkAdmin()) { ?>
			  <p>Sticky?
				<input name="sticky" type="checkbox" id="sticky" value="1">
			  </p>
			  <?php } ?>
                <input type="submit" class='button blue' value="Create Thread">
              </p>
            </form>
          </td>
        </tr>
      </table>

<script type='text/javascript'>
$(document).ready(function(){
	var page = window.location.hash;
	var pagestring;
	if ( page == "" ) {
		page = 1;
	} else {
		page = page.replace("#", "");
		if ( page > <?php echo $pagecount; ?> ) {
			page = 1;
		}
	}	
	if (page != parseInt(page)) {
		if ( page == "last") {
			page = <?php echo $pagecount; ?>;
			pagestring = "last";
		} else {
			page = 1;
		}
	}	
});
</script>

<?php
include 'footer.php';
?>