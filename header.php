<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Infected Mushrooms Portal - Helbreath USA Guild</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="description" content="Infected Mushrooms Portal. A Helbreath USA Guild." />
<meta name="keywords" content="infected mushrooms,helbreath,helbreath usa,free mmorpg" />

<link rel="stylesheet" href="styles.css" type="text/css" media="screen" />
<link rel="stylesheet" href="fullcalendar.css" type="text/css" media="screen" />
<link rel="stylesheet" href="smoothness/jquery-ui-1.8.6.custom.css" type="text/css" media="screen" />
<link rel="stylesheet" href="ui-red/jquery-ui-1.8.7.custom.css" type="text/css" media="screen" />
<link rel="stylesheet" href="ui-green/jquery-ui-1.8.7.custom.css" type="text/css" media="screen" />
<link rel="stylesheet" href="nivo-slider.css" type="text/css" media="screen" />
<link rel="stylesheet" href="js/anytime.css" type="text/css" media="screen" />

<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js'></script>
<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.6/jquery-ui.min.js'></script>
<script type='text/javascript' src='js/jquery.nivo.slider.pack.js'></script>
<script type='text/javascript' src='js/jquery.form.js'></script>
<script type='text/javascript' src='js/jquery.validate.js'></script>
<script type='text/javascript' src='js/quickpager.jquery.js'></script>
<script type="text/javascript" src="tiny_mce/jquery.tinymce.js"></script>
<script type='text/javascript' src='js/fullcalendar.min.js'></script>
<script type='text/javascript' src='js/anytimec.js'></script>
<script type='text/javascript' src='js/jquery.tipsy.js'></script>
<script type='text/javascript' src='js/main.js'></script>

<!--[if lt IE 9]>
<link rel="stylesheet" href="ie.css" type="text/css" media="screen">
<![endif]-->
<?php if (curPageName() <> "login.php") { ?>
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-9495256-2']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
<?php } ?>
</head>
<body>
<div class="container">
<!--[if IE]>
<div id="ie">I've detected that you are using Internet Explorer.  I HIGHLY reccommend upgrading to <a href="http://www.google.com/chrome/" target="_blank">Google Chrome</a>!</div>
<![endif]-->
<div id="account">
<?php if (isset($_SESSION['user_id'])) {
$currentuserid = $_SESSION['user_id'];
$time = time();
$previous = "300";
$timeout = $time-$previous;
$onlinequery = "SELECT * FROM online WHERE id=$currentuserid";
$verify = mysql_query($onlinequery);
$row_verify = mysql_fetch_assoc($verify);
if (!isset($row_verify['id'])) {
	$addonlinequery = "INSERT INTO online (id, timeout) VALUES ($currentuserid, \"$time\")";
	$insert = mysql_query($addonlinequery);
} else {
	$updateonlinequery = "UPDATE online SET timeout = \"$time\" WHERE id = $currentuserid";
	$update = mysql_query($updateonlinequery);
}
$whosonlinequery = "SELECT online.*, users.full_name AS 'username' 
	FROM online 
	INNER JOIN users ON users.id = online.id
	WHERE timeout > \"$timeout\" 
	ORDER BY username ASC";
$online = mysql_query($whosonlinequery);
$row_online = mysql_fetch_assoc($online);
$num_online = mysql_num_rows($online);
$onlinelist;	
if (isset($row_online['username'])) {
	$i = 1;
	do {			
		if ($i == $num_online) {
			$onlinelist .= "<a class='profile' rel='" . $row_online['id'] . "'>" . $row_online['username'] . "</a>";
		} else {
			$onlinelist .= "<a class='profile' rel='" . $row_online['id'] . "'>" . $row_online['username'] . "</a>, ";
		}
		$i++;
	} while($row_online = mysql_fetch_assoc($online)); 
} else { 
	$onlinelist = "There are no members online.";
} 
echo "Welcome Back "; 
echo $_SESSION['user_name'];
echo "! - "; 
$currentuserid = $_SESSION['user_id'];
$query111 = sprintf("SELECT title, messageid FROM messages WHERE toid = $currentuserid AND isread = 0");
$result111 = mysql_query($query111);
$unreadmessages = mysql_num_rows($result111);
if ($unreadmessages == 0) {
	echo "<a href='messages.php'>No Messages</a> - ";
} else {
	echo "<strong><a href='messages.php'>You have $unreadmessages new message(s)!</a></strong> - ";
}
?>
<a href="mysettings.php">Account Settings</a> - 
<a href="logout.php">Logout</a>
<?php } ?>
<?php if (!isset($_SESSION['user_id'])) { 
echo "Welcome Guest! - ";
?>
<a href="login.php">Login</a> - 
<a href="register.php">Register</a>
<?php } ?>
<?php if (checkAdmin()) { ?>
 - <a href="admin.php">Admin CP</a>
<?php } ?>
 - <a href="http://immobile.frodesigns.com">Mobile Site</a>
</div>
<div style="display: none;" id='message'><div id='close'>X</div></div>
<h1><a href="/im/">Infected Mushrooms Portal</a></h1>
<div id="wrapper">
<ul class="nav">
<?php if (isset($_SESSION['user_id'])) { ?>
<li><a href="/im/">Home</a></li><li><a href="/im/tracker.php">Item Tracker</a></li><li><a href="/im/polls.php">Polls</a></li><!--<li><a href="/im/calendar.php">Calendar Beta</a></li><li><a href="/im/privforum.php">Forums</a><ul>--><li><a href="/im/privforum.php">Private Forum</a></li><li><a href="/im/forum.php">Public Forum</a></li><!--</ul></li>--><li><a href="/im/medusa.php">Medusa Gallery</a></li><li><a href="/im/members.php">Members</a></li><li><a href="/im/rules.php">Rules</a></li>
<?php } else { ?>
<li><a href="/im/">Home</a></li><li><a href="/im/forum.php">Public Forum</a></li><li><a href="/im/polls.php">Polls</a></li><li><a href="/im/medusa.php">Medusa Gallery</a></li><li><a href="/im/members.php">Members</a></li><li><a href="/im/rules.php">Rules</a></li>
<?php } ?>
</ul>
