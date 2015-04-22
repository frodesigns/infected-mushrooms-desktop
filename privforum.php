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
	
	//online update
	$time = time();
	$updateonlinequery = "UPDATE online SET timeout = \"$time\" WHERE id = $currentuserid";
	if (!mysql_query($updateonlinequery,$link))
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
<ul class='forumpager'>

<?php
$query = sprintf("SELECT a.threadid, a.title, a.sticky, a.timestamp, a.authorid, a.updatedbyid, b.full_name as 'author', c.full_name as 'updatedby', d.isread, a.postcount
	FROM threads a 
	INNER JOIN users b on a.authorid = b.id
	INNER JOIN users c on a.updatedbyid = c.id
	INNER JOIN readthreads d ON d.id = 54 AND d.threadid = a.threadid
	WHERE a.private = 1 
	ORDER BY a.sticky DESC, a.timestamp DESC");
$result = mysql_query($query);
$num_rows = mysql_num_rows($result);
while ($row = mysql_fetch_assoc($result)) {
	$threadid = (int)$row['threadid'];
	$title = $row['title'];
	$lastupdated = date("F j, Y - g:i a", strtotime($row['timestamp']));
	$authorid = $row['authorid'];
	$lastauthorid = $row['updatedbyid'];
	$authorname = $row['author'];
	$lastauthorname = $row['updatedby'];
	$created = $row['created'];
	$read = $row['isread'];
	$sticky = (int)$row['sticky'];
	$replies = $row['postcount'];
	
	if ($read == 1) {
		$threadclass = "";
	} else {
		$threadclass = "unread";
	}
	
	if ($currentuserid == 0) {
		$threadclass = "";
	}
	
	$authorid = (int)$authorid;
	$authorcolor = "boldauth";

	$pagecount = ($num_rows / 20);
	if (!is_int($pagecount)) {
		$pagecount++;
	}
	$pagecount = (int)$pagecount;
	
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
	$(".forumpager").quickPager({pageSize:20, pagerLocation: 'both', currentPage: page });
	$(".simplePagerNav").before("<strong class='page'>Page:</strong> ");
	$(".simplePagerNav li a").click(function() {
		var hash = window.location.hash;
		hash = hash.replace("#", "");
		if ($(this).attr('rel') > hash) {
			$('html, body').animate({
				scrollTop: $("#account").offset().top
			}, 500);
		}
		location.hash = $(this).attr('rel');
	});
});
</script>

<?php
include 'footer.php';
?>