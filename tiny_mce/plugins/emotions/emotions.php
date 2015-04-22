<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="height: 100%;">
<head>
	<title>{#emotions_dlg.title}</title>
	<script type="text/javascript" src="../../tiny_mce_popup.js"></script>
	<script type="text/javascript" src="js/emotions.js"></script>
</head>
<body style="display: none; background-color: #2A2C2B; height: 100%; width: 100%; margin: 0;">
	<div align="center">
		<div class="title" style="color: white;">{#emotions_dlg.title}:<br /><br /></div>
<?php
   $dir = 'img/';
	
   // open specified directory
   $dirHandle = opendir($dir);
   $count = -1;
   $returnstr = "";
   while ($file = readdir($dirHandle)) {
      // if not a subdirectory and if filename contains the string '.jpg' 
      if(!is_dir($file)) {
         // update count and string of files to be returned
         $count++;
         $returnstr .= '&f'.$count.'='.$file;
		 echo "<a style=\"display: inline-block; margin-right: 10px; margin-bottom: 10px;\" href=\"javascript:EmotionsDialog.insert('$file','$file');\"><img src=\"img/$file\" alt=\"$file\" title=\"$file\" /></a>";		
		 
      }
   } 
   closedir($dirHandle);
?>
		
	</div>
</body>
</html>
