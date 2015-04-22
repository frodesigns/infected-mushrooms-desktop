<?php
include 'dbc.php';
user_protect();
$currentuserid = $_SESSION['user_id'];
if ($_POST['killname'])
{
	$killname = trim($_POST['killname']);	
	
		if ((($_FILES["image"]["type"] == "image/gif")
		|| ($_FILES["image"]["type"] == "image/jpeg")
		|| ($_FILES["image"]["type"] == "image/pjpeg")
		|| ($_FILES["image"]["type"] == "image/png"))
		&& ($_FILES["image"]["size"] < 200000))
			{
			if ($_FILES["image"]["error"] > 0)
				{
					echo "There was an error uploading your screenshot.";
				}
			else
				{
				$im_file_name = 'medusa/' . str_replace("'", "", $_FILES['image']['name']);
				if (file_exists($im_file_name))
				{
					echo "File already exists! Try renaming your image.";
				}
				else
				{				 
				move_uploaded_file($_FILES["image"]["tmp_name"], $im_file_name);				
				$image_attribs = getimagesize($im_file_name); 
				$im_old = imageCreateFromJpeg($im_file_name);
				$th_max_width = 55; 
				$th_max_height = 41; 
				$ratio = ($width > $height) ? $th_max_width/$image_attribs[0] : $th_max_height/$image_attribs[1]; 
				$th_width = $image_attribs[0] * $ratio; 
				$th_height = $image_attribs[1] * $ratio; 
				$im_new = imagecreatetruecolor($th_width,$th_height); 
				imageAntiAlias($im_new,true);
				$th_file_name = 'medusa/thumbs/' . str_replace("'", "", $_FILES['image']['name']); 
				imageCopyResampled($im_new,$im_old,0,0,0,0,$th_width,$th_height, $image_attribs[0], $image_attribs[1]); 
				imageJpeg($im_new,$th_file_name,100); 		
				
				
				echo "Screenshot added successfully!";
				
				$imageurl = "medusa/" . $_FILES["image"]["name"];				
							 				
				mysql_select_db($db, $link);

				$sql = "INSERT INTO medusakills (id, killname, imageurl, thumburl) VALUES ($currentuserid, '$killname', '$imageurl', '$th_file_name')";

				if (!mysql_query($sql,$link))
				{
					die('Error: ' . mysql_error());
				}		
				
				}
			}
		}
		else
		{
			echo "Your screenshot was the wrong file type or too large.";
		}	
	
} else {
	echo "You have to enter a name for the dead Aresden.";
}
?>