<?php 
/********************** MYSETTINGS.PHP**************************
This updates user settings and password
************************************************************/
include 'dbc.php';
page_protect();
$currentuserid = $_SESSION['user_id'];

mysql_select_db($db, $link);
	$activity = mysql_real_escape_string("Editing Profile Settings");
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

$header = file_get_contents('header.php');
$footer = file_get_contents('footer.php');

$err = array();
$msg = array();

if($_POST['doUpdate'] == 'Update')  
{


$rs_pwd = mysql_query("select pwd from users where id='$_SESSION[user_id]'");
list($old) = mysql_fetch_row($rs_pwd);
$old_salt = substr($old,0,9);

//check for old password in md5 format
	if($old === PwdHash($_POST['pwd_old'],$old_salt))
	{
	$newsha1 = PwdHash($_POST['pwd_new']);
	mysql_query("update users set pwd='$newsha1' where id='$_SESSION[user_id]'");
	$msg[] = "Your new password is updated";
	//header("Location: mysettings.php?msg=Your new password is updated");
	} else
	{
	 $err[] = "Your old password is invalid";
	 //header("Location: mysettings.php?msg=Your old password is invalid");
	}

}

if($_POST['doSave'] == 'Save')  
{
	if(isset($_POST['newsletter']) && $_POST['newsletter'] == '1') {
	    $newsletter = 1;
	} else {
	    $newsletter = 0;
	}	
// Filter POST data for harmful code (sanitize)
foreach($_POST as $key => $value) {
	$data[$key] = filter($value);
}


mysql_query("UPDATE users SET
			`full_name` = '$data[name]',
			`address` = '$data[address]',
			`tel` = '$data[tel]',
			`fax` = '$data[fax]',
			`country` = '$data[country]',
			`website` = '$data[web]',
			`newsletter` = $newsletter,
			`msn` = '$data[msn]',
			`real_name` = '$data[real_name]',
			`char1name` = '$data[char1name]',
			`char1lvl` = '$data[char1lvl]',
			`char1type` = '$data[char1type]',
			`char2name` = '$data[char2name]',
			`char2lvl` = '$data[char2lvl]',
			`char2type` = '$data[char2type]',
			`char3name` = '$data[char3name]',
			`char3lvl` = '$data[char3lvl]',
			`char3type` = '$data[char3type]',
			`char4name` = '$data[char4name]',
			`char4lvl` = '$data[char4lvl]',
			`char4type` = '$data[char4type]'
			 WHERE id='$_SESSION[user_id]'
			") or die(mysql_error());

//header("Location: mysettings.php?msg=Profile Sucessfully saved");
$msg[] = "Profile Sucessfully saved";
 }
 
$rs_settings = mysql_query("select * from users where id='$_SESSION[user_id]'"); 
?>

<?php include 'header.php';  ?>

<body>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="main">
  
  <tr>     
    <td width="100%" valign="top">
<h3 class="titlehdr">My Account - Settings</h3>
      <p> 
        <?php	
	if(!empty($err))  {
	   echo "<div class=\"msg\">";
	  foreach ($err as $e) {
	    echo "* Error - $e <br>";
	    }
	  echo "</div>";	
	   }
	   if(!empty($msg))  {
	    echo "<div class=\"msg\">" . $msg[0] . "</div>";

	   }
	  ?>
      </p>
      <p>Here you can make changes to your profile. Please note that you will 
        not be able to change your email which has been already registered.</p>
	  <?php while ($row_settings = mysql_fetch_array($rs_settings)) {
		if ($row_settings['newsletter'] == 1) {
			$checked = "checked";			
		  } else {
			$checked = "";
		  }	  
	  ?>
      <form action="mysettings.php" method="post" name="myform" id="myform">
        <table border="0" align="center" cellpadding="3" cellspacing="3" class="forms">
          <tr> 
            <td colspan="2">
				Enter the name members know you the best by. Your main character's name would be best:<br /> <input name="name" maxlength="14" type="text" id="name"  class="required" value="<? echo $row_settings['full_name']; ?>" size="50"> 
				<br /><br />
				Your Real Name: <input type="text" name="real_name" size="50" value="<? echo $row_settings['real_name']; ?>" /><br /><br />
				Your Location: <input type="text" name="country" size="50" value="<? echo $row_settings['country']; ?>" /><br /><br />
				Your MSN Address: <input type="text" name="msn" size="50" value="<? echo $row_settings['msn']; ?>" /><br /><br />
				Recieve Weekly Newsletter? <input type="checkbox" name="newsletter" value="1" <?php echo $checked; ?> /><br /><br />
				Character 1:<br />Name: <input type="text" name="char1name" value="<? echo $row_settings['char1name']; ?>" /> Level: <input type="text" name="char1lvl" value="<? echo $row_settings['char1lvl']; ?>" /> Type: <input type="text" name="char1type" value="<? echo $row_settings['char1type']; ?>" /><br /><br />
				Character 2:<br />Name: <input type="text" name="char2name" value="<? echo $row_settings['char2name']; ?>" /> Level: <input type="text" name="char2lvl" value="<? echo $row_settings['char2lvl']; ?>" /> Type: <input type="text" name="char2type" value="<? echo $row_settings['char2type']; ?>" /><br /><br />
				Character 3:<br />Name: <input type="text" name="char3name" value="<? echo $row_settings['char3name']; ?>" /> Level: <input type="text" name="char3lvl" value="<? echo $row_settings['char3lvl']; ?>" /> Type: <input type="text" name="char3type" value="<? echo $row_settings['char3type']; ?>" /><br /><br />
				Character 4:<br />Name: <input type="text" name="char4name" value="<? echo $row_settings['char4name']; ?>" /> Level: <input type="text" name="char4lvl" value="<? echo $row_settings['char4lvl']; ?>" /> Type: <input type="text" name="char4type" value="<? echo $row_settings['char4type']; ?>" />
			</td>
          </tr>     
          <tr> 
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr> 
            <td>User Name</td>
            <td><input name="user_name" type="text" id="web2" value="<? echo $row_settings['user_name']; ?>" disabled></td>
          </tr>
          <tr> 
            <td>Email</td>
            <td><input name="user_email" type="text" id="web3"  value="<? echo $row_settings['user_email']; ?>" disabled></td>
          </tr>
        </table>
        <p align="center"> 
          <input name="doSave" type="submit" id="doSave" value="Save">
        </p>
      </form>
	  <?php } ?>
      <h3 class="titlehdr">Change Password</h3>
      <p>If you want to change your password, please input your old and new password 
        to make changes.</p>
      <form name="pform" id="pform" method="post" action="">
        <table border="0" align="center" cellpadding="3" cellspacing="3" class="forms">
          <tr> 
            <td width="31%">Old Password</td>
            <td width="69%"><input name="pwd_old" type="password" class="required password"  id="pwd_old"></td>
          </tr>
          <tr> 
            <td>New Password</td>
            <td><input name="pwd_new" type="password" id="pwd_new" class="required password"  ></td>
          </tr>
        </table>
        <p align="center"> 
          <input name="doUpdate" type="submit" id="doUpdate" value="Update">
        </p>
        <p>&nbsp; </p>
      </form>
      <p>&nbsp; </p>
      <p>&nbsp;</p>
	   
      <p align="right">&nbsp; </p></td>
    <td width="196" valign="top">&nbsp;</td>
  </tr>
  <tr> 
    <td colspan="3">&nbsp;</td>
  </tr>
</table>

<?php include 'footer.php';  ?>