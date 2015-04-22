<?php
include 'dbc.php';
user_protect();

$threadid = $_GET['threadid'];

if (!$threadid) {
	header("Location: forum.php");
}
$threadidint = (int)$threadid;	
if ($threadidint == 0) {
	header("Location: forum.php");
}
$query3 = sprintf("SELECT threadid FROM threads WHERE threadid = $threadid");
$result3 = mysql_query($query3);
$threadrows = mysql_num_rows($result3);
if ($threadrows == 0) {
	header("Location: forum.php");
}

$query = sprintf("SELECT title, private, sticky FROM threads WHERE threadid = $threadid");
$result = mysql_query($query);
while ($row = mysql_fetch_assoc($result)) {
	$threadtitle = $row['title'];
	$private = $row['private'];
	$sticky = $row['sticky'];
	if ($private == 1 && !isset($_SESSION['user_id'])) {
		header("Location: forum.php");
	}
}


if ($private == 1) {
	$deleteclass = "deleteprivthread";
} else {
	$deleteclass = "deletethread";
}

include 'header.php';

if (isset($_SESSION['user_id'])) {
	$currentuserid = $_SESSION['user_id'];
	
	mysql_select_db($db, $link);
	$activity = mysql_real_escape_string("Viewing <a href=/im/thread.php?threadid=$threadid>$threadtitle</a>");
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
	
	$query20 = sprintf("SELECT full_name FROM users WHERE id = $currentuserid");
	$result20 = mysql_query($query20);
	while ($row20 = mysql_fetch_assoc($result20)) {
			$currentusername = $row20['full_name'];
	}
	echo "<input type='hidden' id='currentuserid' value='$currentuserid' />";
	echo "<input type='hidden' id='currentusername' value='$currentusername' />";
	
	$query4 = sprintf("SELECT isread FROM readthreads WHERE threadid = $threadid AND id = $currentuserid");
	$result4 = mysql_query($query4);
	$readrows = mysql_num_rows($result4);
	if ($readrows == 0) {
		$sql = "INSERT INTO readthreads (id, threadid, isread) VALUES ($currentuserid, $threadid, 1)";
		if (!mysql_query($sql,$link))
		{
			die('Error: ' . mysql_error());
		}	
	} else {
		$sql = "UPDATE readthreads SET isread = 1 WHERE threadid = $threadid AND id = $currentuserid";
		if (!mysql_query($sql,$link))
		{
			die('Error: ' . mysql_error());
		}	
	}	
} else {
	if(isset($_COOKIE['guestname'])) {
		$guestnamecookie = $_COOKIE['guestname']; 
	}
}
?>

<div class='forumwrapper'>
<h3 class='forumtitle'><?php echo $threadtitle; ?></h3>
<div class='topthreadbuttons'>
<a class="scrollpost button blue">Reply</a>
<?php 
if (checkAdmin()) {
	if ($sticky == 1) {
		echo "<form class='unstickyform' name='unstickyform' action='unsticky.php' method='post'><input type='hidden' name='threadid' value='$threadid' /><input type='submit' class='unsticky button red' value='Unsticky' /></form>";
	} else {
		echo "<form class='stickyform' name='stickyform' action='sticky.php' method='post'><input type='hidden' name='threadid' value='$threadid' /><input type='submit' class='sticky button red' value='Sticky' /></form>";
	}
}
?>
<?php if ($currentuserid == 54) { ?>
<form class='deletethreadform' name='deletethreadform' action='deletethread.php' method='post'><input type='hidden' name='deletethreadid' value='<?php echo $threadid; ?>' /><input type='submit' class='<?php echo $deleteclass; ?> button red' value='Delete Thread' /></form>
<?php } ?>
</div>
<ul class='threadpager'>
<?php
$query2 = sprintf("SELECT postid, content, authorid, guestname, guestemail, timestamp FROM posts WHERE threadid = $threadid AND spam = 0 ORDER BY timestamp ASC");
$result2 = mysql_query($query2);
$num_rows = mysql_num_rows($result2);
$i = 1;
while ($row2 = mysql_fetch_assoc($result2)) {
	$postid = (int)$row2['postid'];
	$content = $row2['content'];
	$authorid = $row2['authorid'];
	$guestname = $row2['guestname'];
	$guestemail = $row2['guestemail'];
	$timestamp = date("F j, Y" ."<b\\r/>". "g:i a", strtotime($row2['timestamp']));
	
	if ($guestname != "") {
		$authorname = $guestname;
		$authorcolor = "italicauth";
		$emailhash = md5(strtolower(trim("$guestemail")));
		$authorid = "";
	} else {
		$authorid = (int)$authorid;
		$query19 = sprintf("SELECT full_name, user_email FROM users WHERE id = $authorid");
		$result19 = mysql_query($query19);
		while ($row19 = mysql_fetch_assoc($result19)) {
				$authorname = $row19['full_name'];
				$email = $row19['user_email'];
				$emailhash = md5(strtolower(trim("$email")));
				$defaultimage = urlencode('http://www.frodesigns.com/im/images/mushroom-small.png');
		}		
		$authorcolor = "boldauth";
	}

	$pagecount = ($num_rows / 20);
	if (!is_int($pagecount)) {
		$pagecount++;
	}
	$pagecount = (int)$pagecount;
	
		if ($i == $num_rows) {
			echo "<li class='last' id='$postid'><div class='threadpost'><div class='info'><span class='$authorcolor'>";
			if ($authorid != "" && isset($_SESSION['user_id'])) { echo "<a class='profile' rel='$authorid'>"; }
			echo $authorname;
			if ($authorid != "" && isset($_SESSION['user_id'])) { echo "</a>"; }
			echo "</span>";
			if ($guestname != "") {			
				echo "<br /><img src='http://www.gravatar.com/avatar/$emailhash?d=identicon' />";
			} else {
				echo "<br /><img src='http://www.gravatar.com/avatar/$emailhash?d=$defaultimage' />";
			}
			echo "<br /><small>$timestamp</small><br /><br /><a class='quotepost button blue' alt='$authorname' rel='$postid'>Quote</a>";
			if (checkAdmin() || $_SESSION['user_id'] == $authorid && isset($_SESSION['user_id'])) {
				echo " <a class='edit button blue' rel='$postid'>Edit</a>";
			}
			if ($currentuserid == 54) {
				echo "<br /><br /><form class='deletepost' name='deletepostform' action='deletepost.php' method='post'><input type='hidden' name='deletepostid' value='$postid' /><input type='submit' class='delete button red' value='Delete' /></form>";
			}
			echo "</div><div class='content'>$content</div></div></li>";
		} else {
			echo "<li id='$postid'><div class='threadpost'><div class='info'><span class='$authorcolor'>";
			if ($authorid != "" && isset($_SESSION['user_id'])) { echo "<a class='profile' rel='$authorid'>"; }
			echo $authorname;
			if ($authorid != "" && isset($_SESSION['user_id'])) { echo "</a>"; }
			echo "</span>";
			if ($guestname != "") {			
				echo "<br /><img src='http://www.gravatar.com/avatar/$emailhash?d=identicon' />";
			} else {
				echo "<br /><img src='http://www.gravatar.com/avatar/$emailhash?d=$defaultimage' />";
			}
			echo "<br /><small>$timestamp</small><br /><br /><a class='quotepost button blue' alt='$authorname' rel='$postid'>Quote</a>";
			if (checkAdmin() || $_SESSION['user_id'] == $authorid && isset($_SESSION['user_id'])) {
				echo " <a class='edit button blue' rel='$postid'>Edit</a>";
			}
			if ($currentuserid == 54) {
				echo "<br /><br /><form class='deletepost' name='deletepostform' action='deletepost.php' method='post'><input type='hidden' name='deletepostid' value='$postid' /><input type='submit' class='delete button red' value='Delete' /></form>";
			}
			echo "</div><div class='content'>$content</div></div></li>";
		}
	$i++;	
}
?>
</ul>
<div class='bottombuttons'>
<a class="scrolltop button blue">Back to Top</a>
</div>
</div>

      <table width="80%" border="0" cellpadding="5" cellspacing="2" class="forms">
		<tr><td><span class="itemname">Reply</span></td></tr>
        <tr>
          <td><form id="threadreply" name="form" method="post" action="replysubmit.php" enctype="multipart/form-data">			
			  <?php 
				echo "<input type='hidden' id='threadid' name='threadid' value='$threadid' />";
				if (!isset($_SESSION['user_id'])) { 
			  ?>
			  <p>Guest Username
                <input name="guestname" maxlength="14" type="text" id="guestname" value="<?php echo $guestnamecookie; ?>">
			  </p>    
			  <p>Guest Email
                <input name="guestemail" maxlength="50" type="text" id="guestemail" value="<?php echo $guestemailcookie; ?>"> Will not be published.
			  </p>   
			  <?php } ?>            
			  <p><div>
				<textarea class='tinymce' name="content" id="content" rows="15" cols="70"></textarea>		
			  </div></p>
                <input type="submit" class='button blue' id="replybutton" value="Post Reply"> <img src="/im/loading.gif" style="display: none;" id="ajaxloader" alt="working..." />
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
	$(".threadpager").quickPager({pageSize:20, pagerLocation: 'both', currentPage: page });
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
	if (pagestring == "last") {
		$(window).load(function() {
			$('html, body').animate({
				scrollTop: $(".last").offset().top
			}, 1000);
		});
	}
	<?php if ($private == 1) { ?>
	$(".nav").find("a[href='/im/privforum.php']").each(function(){
		$(this).addClass("current");
		//add your own logic here if needed
	});
	<?php } else { ?>	
	$(".nav").find("a[href='/im/forum.php']").each(function(){
		$(this).addClass("current");
		//add your own logic here if needed
	});
	<?php } ?>
});
</script>

<?php
include 'footer.php';
?>