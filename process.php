<?php
/*************************************************************************************
* process.php														*
* The SMF File Manager modification by Marcus "Nas" Forsberg.							*
* This file was last updated in verison 1.0.1										*
* This modification, including this file, is copyrighted and may not be redistributed.					*
* Copyright (c) 2009 Marcus Forsberg										*
**************************************************************************************/
	// We'll need SSI.php
	if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	{
		require_once(dirname(__FILE__) . '/SSI.php');		
		global $context, $boarddir, $settings, $txt, $sourcedir;
		
		// Security++
		isAllowedTo('admin_forum');
		checkSession('post');
		loadLanguage('FileManager');
		require_once($sourcedir . '/Subs-FileManager.php');
		// The path to which we want to upload the file.
		$path = $_POST['dir'].'/';
		$path = $path . basename($_FILES['file']['name']);
		// By default the result is set to 0; fail.
		$result = 0;
		$message = sprintf($txt['fm_uploaderror'],$txt['fm_uploaderror_'.$_FILES['file']['error']]);		
		// Upload the file. If it works, set the result to 1; succes.
		if(@move_uploaded_file($_FILES['file']['tmp_name'], $path))		{
			$result = 1;					
			// Also give them a nice message.
			$message = sprintf($txt['fm_fileuploaded'],$_FILES['file']['name']) . ' <strong>'.checkDirName($_POST['dir']).'</strong>!';
		}		
		// Give the server some breath.
		sleep(1);
	}
	elseif (!defined('SMF'))
	{
		$result = 0;
		$message = $txt['fm_nossi'];
	}
		// Add the close link to the message
		$message = $message. ' <a href="javascript:closeResult();"><img src="'.$settings['default_images_url'].'/filemanager/close'.$result.'.png" alt="'.$txt['fm_uploadclosenotice'].'" border="0" /></a>';
		// Call the javascript finishing the process.
		echo'
		<html>
		<head>
			<script language="javascript" type="text/javascript">
				window.top.window.finishUploadProcess('.$result.', \''.$message.'\');
			</script>
		</head>
		<body></body>
		</html>';		
?>   