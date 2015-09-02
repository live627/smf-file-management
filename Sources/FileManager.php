<?php
/******************************************************************************************
* FileManager.php														*
* The SMF File Manager modification by Marcus "Nas" Forsberg.								*
* This file was last updated in verison 1.0.											*
* This modification, including this file, is copyrighted and may not be redistributed.						*
* Copyright (c) 2009 Marcus Forsberg											*
*******************************************************************************************/
if (!defined('SMF'))
	die('Hacking attempt...');
// The main function. This handles all basic stuff such as loading sub actions and templates.
function FileManager()
{
	global $sourcedir, $context, $smf_important, $imagetypes, $wbfolders, $settings, $txt, $scripturl, $audiofiles;

	// We are on... Which version...?
	$context['fm_curVer'] = '1.0.1';

	// Cool people only 8)
	isAllowedTo('admin_forum');

	// Bots: Keep out.
	$context['robot_no_index'] = true;

	// Now for some templates and languages.
	loadTemplate('FileManager');
	loadLanguage('FileManager');

	// We're gonna need this file no matter what happens. So let's just get it now.
	require_once($sourcedir . '/Subs-FileManager.php');

	// We will use the "filemanager" template layer...
	$context['template_layers'][] = 'filemanager';

	// These files cannot be removed. We don't wanna kill our forum, do we?
	$smf_important = array('Sources', 'Themes', 'avatars', 'attachments', 'Smileys', 'index.php', 'Settings.php', 'Packages', 'Settings_bak.php','cache', 'subscriptions.php', 'SSI.php', 'default', 'filemanager', 'Filemanager.php', 'Subs-FileManager.php', 'FileManager.template.php');
	// Check out my collection of image file types :P
	$imagetypes = array('TIF','TIFF','JPG','PNG','GIF','BMP','tif','jpg','png','gif','bmp','tiff');
	// And off archives, wannabe folders. ;)
	$wbfolders = array('ZIP','TAR','TAR.GZ','GZ','RAR','ALZ','LZH','CAB','JAR','BH','zip','tar','tar.gz','gz','rar','alz','lzh','cab','jar','bh','smf');
	// I've also got a few audio files.
	$audiofiles = array('mp3','mid','wav','MP3','MID','WAV','nas');

	// Available sub actions
	$actions = array(
		'browse' => 'FM_BrowseDirectory',
		'viewfile' => 'FM_ViewFile',		
		'editfile' => 'FM_EditFile',
		'editfile2' => 'FM_EditFile2',		
		'remove' => 'FM_RemoveFileorDir',
		'remove2' => 'FM_RemoveFileorDir2',	
		'image' => 'FM_DisplayImage',		
		'rename' => 'FM_RenameFileorDir',
		'rename2' => 'FM_RenameFileorDir2',		
		'chmod' => 'FM_ChmodFileorDir',
		'chmod2' => 'FM_ChmodFileorDir2',	
		'search' => 'FM_SearchFiles',			
		'upload' => 'FM_UploadFile',	
		'archive' => 'FM_DownloadFile', // Place holder for the old archive function.
		'download' => 'FM_DownloadFile',
		'createdir' => 'FM_CreateDir',	
		'createdir2' => 'FM_CreateDir2',	
		'createfile' => 'FM_CreateFile',	
		'createfile2' => 'FM_CreateFile2',
		'extract' => 'FM_Extract',
		'audio' => 'FM_PlayAudio',			
	);

	// Get the action we are going to use.
	if (isset($_REQUEST['do']) && isset($actions[$_REQUEST['do']]))
		$context['sub_action'] = $_REQUEST['do'];
	else
		$context['sub_action'] = 'browse';

	// Do what we are supposed to do...
	$actions[$context['sub_action']]();	

	// This is displayed at the top.
	$context['fm_location'] = getLocation();

	// Navigation buttons
	$context['nav_buttons'] = array(
		'browse' => array('text' => 'fm_index', 'lang' => true, 'url' => $scripturl . '?action=admin;area=filemanager;dir='.$context['directory']),
		'upload' => array('text' => 'fm_uploadfile', 'lang' => true, 'url' => $scripturl . '?action=admin;area=filemanager;do=upload;dir='.$context['directory']),
		'createdir' => array('text' => 'fm_createdirnow', 'lang' => true, 'url' => $scripturl . '?action=admin;area=filemanager;do=createdir;dir='.$context['directory']),
		'createfile' => array('text' => 'fm_createfilenow', 'lang' => true, 'url' => $scripturl . '?action=admin;area=filemanager;do=createfile;dir='.$context['directory']),
	);

	// For the syntax highlighter. Which languages can we highlight?
	$context['highlightable'] = array('css','php','js','txt','html','sql','rb','pl','shtml');
	$context['highlight_languages'] = array(
		'css' => 'css',
		'php' => 'php',
		'js' => 'javascript',
		'txt' => 'text',
		'html' => 'html',
		'shtml' => 'html',
		'sql' => 'sql',
		'rb' => 'ruby',
		'pl' => 'perl',
	);

	// This checks for the latest version.
	$context['check_version'] = '
		<script language="JavaScript" type="text/javascript" src="http://forentadatorerna.se/filemanager_latestversion.js"></script>
		<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
			function getLatestVersion()
			{
				var latVer, curVer;
				if (typeof(window.latestVersion) != "string")
					return;
				latVer = document.getElementById("latestVersion");
				curVer = document.getElementById("currentVersion");
				setInnerHTML(latVer, window.latestVersion);
				var currentVersion = getInnerHTML(curVer);
				if (currentVersion != window.latestVersion)
				{
					setInnerHTML(curVer, "<span class=\"alert\">" + currentVersion + "<" + "/span>");
					setInnerHTML(latVer, "<span style=\"color: green\">" + latestVersion + "<" + "/span>");
					document.getElementById("newVersion").style.display="block";
				}
			}
			getLatestVersion();
		// ]]></script>';

	// Html headers
	$context['html_headers'] .= '
	<script type="text/javascript" src="' . $settings['default_theme_url'] . '/filemanager/highslide.js"></script>
	<link rel="stylesheet" type="text/css" href="' . $settings['default_theme_url'] . '/filemanager/highslide.css" />
		<script type="text/javascript">
		hs.graphicsDir = \'' . $settings['default_images_url'] . '/filemanager/highslide/\';
		hs.outlineType = \'rounded-white\';
	</script>
	<script src="' . $settings['default_theme_url'] . '/filemanager/codepress/codepress.js" type="text/javascript"></script>';
}

// Browse the given directory.
function FM_BrowseDirectory()
{
	global $context, $txt, $settings, $boarddir, $smf_important, $modSettings, $boardurl, $sourcedir, $audiofiles;

	// Wait.. We do have a directory, don't we? If not, blame JBlaze.
	$context['directory'] = !empty($_REQUEST['dir']) && is_dir($_REQUEST['dir']) ? $_REQUEST['dir'] : $boarddir;
	// Make sure they aren't trying to view a directory below the smf root o.O
	$context['directory'] = strlen($context['directory']) < strlen($boarddir) ? $boarddir : $context['directory'];

	// List all files in the directory.
	$context['files'] = listFiles($context['directory']);
	$context['files'] = $context['files'];

	// Create the page index
	$context['page_index'] = filePageIndex($context['files'],$context['directory']);
	$context['files'] = $context['page_index']['files'];

	// The image displaying what type of file it is
	$context['typeimagepath'] = $settings['default_images_url']. '/filemanager/filetypes/';

	$context['sub_template'] = 'browse';
	$context['page_title'] = $txt['fm_pagetitle'] . ' - ' . $txt['fm_browsefiles'] . ' ' . checkDirName($context['directory']);
}
// View a certain file without any options to edit it.
function FM_ViewFile()
{
	global $context, $txt, $settings, $smf_important;

	// No veiwing without a file...
	if(!empty($_REQUEST['file']) && file_exists($_REQUEST['file']))
	$context['file'] = $_REQUEST['file'];
	else
	fatal_lang_error('fm_file_noexist',0);

	// Open the file
	$context['file_content'] = htmlentities(openFile($context['file']));

	// Some file data
	$context['file_data'] = fileData($context['file']);

	// The parent directory
	$context['directory'] = $context['file_data']['data']['dirname'];

	$context['sub_template'] = 'view';
	$context['page_title'] = $txt['fm_pagetitle'] . ' - ' . $txt['fm_viewfile'] . ' ' . basename($context['file']);
}
// Edit a file.
function FM_EditFile()
{
	global $context, $txt, $settings, $smf_important;

	// No editing without a file...
	if(!empty($_REQUEST['file']) && file_exists($_REQUEST['file']))
	$context['file'] = $_REQUEST['file'];
	else
	fatal_lang_error('fm_file_noexist',0);

	// Some file data
	$context['file_data'] = fileData($context['file']);

	// If this is a newly created file, we'll want to show a nice notice.
	if(isset($_GET['new']))
		$context['edit_message'] = sprintf($txt['fm_createfile_complete'],basename($context['file']));

	if(in_array(basename($context['file']),$smf_important) || in_array(basename($context['file_data']['data']['dirname']),$smf_important))
	$context['edit_message'] = $txt['fm_edit_warning'];

	// Open the file
	$context['file_content'] = htmlentities(openFile($context['file']));

	// The parent directory
	$context['directory'] = $context['file_data']['data']['dirname'];
	$context['sub_template'] = 'edit';
	$context['page_title'] = $txt['fm_pagetitle'] . ' - ' . $txt['fm_editfile'] . ' ' . basename($context['file']);
}
// Save edits to a file
function FM_EditFile2()
{
	global $context, $txt, $settings, $scripturl, $smf_important;

	// Make sure they aren't trying to edit a file that does not exist :o
	if(!empty($_REQUEST['file']) && file_exists($_REQUEST['file']))
	$context['file'] = $_REQUEST['file'];
	else
	redirectexit('action=admin;area=filemanager');

	// Some file data
	$context['file_data'] = fileData($context['file']);

	// Make sure we've got something to write. If not, display an error message.
	if(empty($_POST['filecontent']))
	{
		$context['edit_message'] = $txt['fm_file_empty'];

		// Open the file with its old content.
		$context['file_content'] = openFile($context['file']);
	}
	else
	{
		// Save the edits
		writeToFile($context['file'],$_POST['filecontent']);

		// For the backlink
		$context['file_data'] = fileData($context['file']);

		// Completed message
		$context['edit_message'] = sprintf($txt['fm_file_complete'],$scripturl. '?action=admin;area=filemanager;do=browse;dir=' . urlEncode($context['file_data']['data']['dirname']));

		// We'll want to display the updated file content instead of the old content.
		$context['file_content'] = $_POST['filecontent'];
	}

	// The parent directory
	$context['directory'] = $context['file_data']['data']['dirname'];

	$context['sub_template'] = 'edit';
	$context['page_title'] = $txt['fm_pagetitle'] . ' - ' . $txt['fm_editfile'] . ' ' . basename($context['file']);
}

// Ask them if they really want to remove the file.
function FM_RemoveFileorDir()
{
	global $context, $txt, $settings, $scripturl, $smf_important, $boarddir;

	// Make sure they aren't trying to remove a file that does not exist :o
	if(!empty($_REQUEST['file']) && file_exists($_REQUEST['file']))
	$context['file'] = $_REQUEST['file'];
	else
	redirectexit('action=admin;area=filemanager');

	// For the backlinks
	$context['file_data'] = fileData($context['file']);

	if(in_array(basename($context['file']),$smf_important) || in_array(basename($context['file_data']['data']['dirname']),$smf_important))
	{
		$context['remove_message'] = sprintf(filetype($context['file']) == 'dir' ? $txt['fm_remove_none_dir'] : $txt['fm_remove_none'],$scripturl.'?action=admin;area=filemanager;do=browse;dir=' . urlEncode($context['file_data']['data']['dirname']));
	}
	else
		$context['remove_message'] = sprintf(filetype($context['file']) == 'dir' ? $txt['fm_remove_sure_dir'] : $txt['fm_remove_sure'],str_replace($boarddir,'',$context['file'])).'<br/><br/><a href="' . $scripturl. '?action=admin;area=filemanager;do=remove2;file='.$context['file'] . '">' . $txt['fm_yes'] . '</a> | <a href="' . $scripturl. '?action=admin;area=filemanager;do=browse;dir=' . urlEncode($context['file_data']['data']['dirname']) . '">' . $txt['fm_no'] . '</a>';

	$context['sub_template'] = 'remove';

	// The parent directory
	$context['directory'] = $context['file_data']['data']['dirname'];
	$context['page_title'] = $txt['fm_pagetitle'] . ' - ' . $txt['fm_removefile'] . ' ' . basename($context['file']);
}

// Actually remove the file/dir. 
function FM_RemoveFileorDir2()
{
	global $context, $txt, $settings, $scripturl, $smf_important;

	// Make sure they aren't trying to remove a file that does not exist :o
	if(!empty($_REQUEST['file']) && file_exists($_REQUEST['file']))
	$context['file'] = $_REQUEST['file'];
	else
	redirectexit('action=admin;area=filemanager');

	// For the backlinks
	$context['file_data'] = fileData($context['file']);

	if(in_array(basename($context['file']),$smf_important) || in_array(basename($context['file_data']['data']['dirname']),$smf_important))
		$context['remove_message'] = sprintf($txt['fm_remove_none'],$scripturl.'?action=admin;area=filemanager;do=browse;dir=' . urlEncode($context['file_data']['data']['dirname']));
	else
	{
		// Find out if it's a directory or a file
		if(filetype($context['file']) == 'dir')
		{
			// Remove it
			if(deleteDir($context['file']))
				$context['remove_message'] = sprintf($txt['fm_remove_complete'],$scripturl. '?action=admin;area=filemanager;do=browse;dir=' . urlEncode($context['file_data']['data']['dirname']));
			else
				$context['remove_message'] = sprintf($txt['fm_remove_failed'],$scripturl. '?action=admin;area=filemanager;do=browse;dir=' . urlEncode($context['file_data']['data']['dirname']));
		}
		else
		{
			// Remove it
			if(unlink($context['file']))
				$context['remove_message'] = sprintf($txt['fm_remove_complete'],$scripturl. '?action=admin;area=filemanager;do=browse;dir=' . urlEncode($context['file_data']['data']['dirname']));
			else
				$context['remove_message'] = sprintf($txt['fm_remove_failed'],$scripturl. '?action=admin;area=filemanager;do=browse;dir=' . urlEncode($context['file_data']['data']['dirname']));
		}
	}

	// The parent directory
	$context['directory'] = $context['file_data']['data']['dirname'];

	$context['sub_template'] = 'remove';	
	$context['page_title'] = $txt['fm_pagetitle'] . ' - ' . $txt['fm_removefile'] . ' ' . basename($context['file']);
}

// Display an image.
function FM_DisplayImage()
{
	global $context, $txt, $settings, $smf_important;

	// No veiwing without a file...
	if(!empty($_REQUEST['file']) && file_exists($_REQUEST['file']))
	$context['file'] = $_REQUEST['file'];
	else
	fatal_lang_error('fm_file_noexist',0);

	// We need to prepare the image for displaying :o
	$context['image'] = prepareImage($context['file']);

	// Get some image data
	$context['image_data'] = imageData($context['file']);

	// The parent directory
	$context['directory'] = $context['image_data']['data']['dirname'];

	$context['sub_template'] = 'image';
	$context['page_title'] = $txt['fm_pagetitle'] . ' - ' . $txt['fm_viewimage'] . ' ' . basename($context['file']);
}
// CHMOD a file or directory.
function FM_ChmodFileorDir()
{
	global $context, $txt, $settings, $smf_important;

	// No chmodding without a file...
	if(!empty($_REQUEST['file']) && file_exists($_REQUEST['file']))
	$context['file'] = $_REQUEST['file'];
	else
	fatal_lang_error('fm_file_noexist',0);

	// For the backlinks
	$context['file_data'] = fileData($context['file']);
	
	$context['chmod_message'] = sprintf($txt['fm_chmodinfo'],basename($context['file']));

	$context['show_form'] = 1;	

	// The parent directory
	$context['directory'] = $context['file_data']['data']['dirname'];

	$context['sub_template'] = 'chmod';
	$context['page_title'] = $txt['fm_pagetitle'] . ' - ' . $txt['fm_chmodfile'] . ' ' . $context['file_data']['data']['basename'];
}
// Save edits to a file
function FM_ChmodFileorDir2()
{
	global $context, $txt, $settings, $scripturl, $smf_important;

	// No chmodding without a file...
	if(!empty($_REQUEST['file']) && file_exists($_REQUEST['file']))
	$context['file'] = $_REQUEST['file'];
	else
	redirectexit('action=admin;area=filemanager');

	// For the backlinks
	$context['file_data'] = fileData($context['file']);

	// What if they left the field empty? :o
	if(empty($_REQUEST['value']))
	{
		$context['chmod_message'] = $txt['fm_chmod_empty'];
		
		// Show the form
		$context['show_form'] = 1;
	}
	else
	{
		$value = octdec( (int) $_REQUEST['value'] );

		if(chmod($context['file'],$value))
			$context['chmod_message'] = sprintf($txt['fm_chmod_complete'],$scripturl. '?action=admin;area=filemanager;do=browse;dir=' . urlEncode($context['file_data']['data']['dirname']));
		else
			$context['chmod_message'] = sprintf($txt['fm_chmod_failed'],$scripturl. '?action=admin;area=filemanager;do=browse;dir=' . urlEncode($context['file_data']['data']['dirname']));

		// Hide the form
		$context['show_form'] = 0;
	}
	// The parent directory
	$context['directory'] = $context['file_data']['data']['dirname'];

	$context['sub_template'] = 'chmod';
	$context['page_title'] = $txt['fm_pagetitle'] . ' - ' . $txt['fm_chmodfile'] . ' ' . $context['file_data']['data']['basename'];
}
// Rename a file or directory.
function FM_RenameFileorDir()
{
	global $context, $txt, $settings, $smf_important;

	// No renaming without a file...
	if(!empty($_REQUEST['file']) && file_exists($_REQUEST['file']))
	$context['file'] = $_REQUEST['file'];
	else
	fatal_lang_error('fm_file_noexist',0);

	// For the backlinks
	$context['file_data'] = fileData($context['file']);

	if(in_array(basename($context['file']),$smf_important) || in_array(basename($context['file_data']['data']['dirname']),$smf_important))
	{
		$context['rename_message'] = sprintf(filetype($context['file']) == 'dir' ? $txt['fm_rename_none_dir'] : $txt['fm_rename_none'],$scripturl.'?action=admin;area=filemanager;do=browse;dir=' . urlEncode($context['file_data']['data']['dirname']));
		$context['show_form'] = 0;
	}
	else
	{
		$context['rename_message'] = sprintf($txt['fm_renameinfo'],basename($context['file']));
		$context['show_form'] = 1;

		// We need to find the name without any extension.
		$context['filedata'] = pathinfo($context['file']);
	}

	// The parent directory
	$context['directory'] = $context['file_data']['data']['dirname'];

	$context['sub_template'] = 'rename';
	$context['page_title'] = $txt['fm_pagetitle'] . ' - ' . $txt['fm_renamefile'] . ' ' . $context['filedata']['basename'];
}

// Save edits to a file
function FM_RenameFileorDir2()
{
	global $context, $txt, $settings, $scripturl, $smf_important;

	// No renaming without a file...
	if(!empty($_REQUEST['file']) && file_exists($_REQUEST['file']))
	$context['file'] = $_REQUEST['file'];
	else
	redirectexit('action=admin;area=filemanager');

	// For the backlinks
	$context['file_data'] = fileData($context['file']);
	if(in_array(basename($context['file']),$smf_important) || in_array(basename($context['file_data']['data']['dirname']),$smf_important))
	{
		$context['rename_message'] = sprintf(filetype($context['file']) == 'dir' ? $txt['fm_rename_none_dir'] : $txt['fm_rename_none'],$scripturl.'?action=admin;area=filemanager;do=browse;dir=' . urlEncode($context['file_data']['data']['dirname']));
		$context['show_form'] = 0;
	}
	else
	{	
		// We need to find the name without any extension, and the files original path.
		$context['filedata'] = pathinfo($context['file']);

		// What if they left the name empty? :o
		if(empty($_REQUEST['filename']))
		{
			$context['rename_message'] = $txt['fm_rename_empty'];

			// Show the form
			$context['show_form'] = 1;
		}
		else
		{
			// Do we have any extension? :O
			$extension = !empty($context['filedata']['extension'])  ? '.' . $context['filedata']['extension'] : '';

			if(rename($context['file'],$context['filedata']['dirname'] . '/' . $_REQUEST['filename']))
				$context['rename_message'] = sprintf($txt['fm_rename_complete'],$scripturl. '?action=admin;area=filemanager;do=browse;dir=' . urlEncode($context['file_data']['data']['dirname']));
			else
				$context['rename_message'] = sprintf($txt['fm_rename_failed'],$scripturl. '?action=admin;area=filemanager;do=browse;dir=' . urlEncode($context['file_data']['data']['dirname']));	

			// Hide the form
			$context['show_form'] = 0;
		}
	}

	// The parent directory
	$context['directory'] = $context['file_data']['data']['dirname'];

	$context['sub_template'] = 'rename';
	$context['page_title'] = $txt['fm_pagetitle'] . ' - ' . $txt['fm_renamefile'] . ' ' . $context['filedata']['basename'];
}

// Search for files.
function FM_SearchFiles()
{
	global $context, $txt, $settings, $scripturl, $smf_important, $imagetypes, $wbfolders, $boarddir;

	// Have they entered anything to search for?
	if(empty($_REQUEST['search']))
	{
		$context['search_message'] = $txt['fm_search_empty'];

		$context['page_title'] = $txt['fm_pagetitle'] . ' - ' . $txt['fm_search'];

		// Nothing to show.
		$context['showresults'] = 0;
	}
	else
	{
		// Where are we supposed to be searching for this?
		$searchdir = !empty($_REQUEST['dir']) && is_dir($_REQUEST['dir']) ? $_REQUEST['dir'] : $boarddir;

		// Make sure they aren't trying to search a directory below the smf root o.O
		$searchdir = strlen($searchdir) < strlen($boarddir) ? $boarddir : $searchdir;

		// Now search the directory. :D
		$context['results'] = searchDir($_REQUEST['search'],$searchdir);

		$context['search_message'] = sprintf($txt['fm_search_results'],$_REQUEST['search']);	

		// Show results
		$context['showresults'] = 1;
		
		$context['page_title'] = $txt['fm_pagetitle'] . ' - ' . $txt['fm_searchdir']. ' ' . checkDirName($searchdir);
	}

	// The parent directory
	$context['directory'] = $_REQUEST['dir'];

	$context['sub_template'] = 'search';
}

// Upload a file
function FM_UploadFile()
{
	global $context, $txt, $settings, $scripturl, $smf_important, $boarddir;

	// Wait.. We do have a directory, don't we? If not, blame Nas.
	$context['directory'] = !empty($_REQUEST['dir']) && is_dir($_REQUEST['dir']) ? $_REQUEST['dir'] : $boarddir;
	// Make sure they aren't trying to view a directory below the smf root o.O
	$context['directory'] = strlen($context['directory']) < strlen($boarddir) ? $boarddir : $context['directory'];

	$context['page_title'] = $txt['fm_pagetitle'] . ' - ' . $txt['fm_uploadfiletodir']. ': ' . checkDirName($context['directory']);
	$context['sub_template'] = 'upload';

	$context['html_headers'] .= '
	<script type="text/javascript" src="' . $settings['default_theme_url'] . '/filemanager/upload.js"></script>
	<link rel="stylesheet" type="text/css" href="' . $settings['default_theme_url'] . '/filemanager/upload.css" />';
}

// Download a file
function FM_DownloadFile()
{
	global $context, $txt, $settings, $scripturl, $smf_important;

	// Do we have a file?
	if(empty($_REQUEST['file']) && !file_exists($_REQUEST['file']))
	{
		redirectexit('action=admin;area=filemanager');
		return false;
	}
	else
	{
		// Required for IE
		if(ini_get('zlib.output_compression'))
		{
			ini_set('zlib.output_compression', 'Off');
		}	

		$mime = 'application/force-download';
		header('Pragma: public'); 
		header('Expires: 0');	// No cache
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control: private',false);
		header('Content-Type: '.$mime);
		header('Content-Disposition: attachment; filename="'.basename($_REQUEST['file']).'"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: '.filesize($_REQUEST['file']));	// Provide file size
		readfile($_REQUEST['file']);		// Push it out	
	
		exit();
	}

	// The parent directory
	$context['directory'] = $context['file_data']['data']['dirname'];
}

// Create a new directory
function FM_CreateDir()
{
	global $context, $txt, $settings, $smf_important, $boarddir;

	// Wait.. We do have a directory, don't we? If not, blame Arantor.
	$context['directory'] = !empty($_REQUEST['dir']) && is_dir($_REQUEST['dir']) ? $_REQUEST['dir'] : $boarddir;
	// Make sure they aren't trying to view a directory below the smf root o.O
	$context['directory'] = strlen($context['directory']) < strlen($boarddir) ? $boarddir : $context['directory'];

	$context['create_message'] = sprintf($txt['fm_createdirinfo'],basename($context['directory']));
	$context['show_form'] = 1;

	$context['sub_template'] = 'createdir';
	$context['page_title'] = $txt['fm_pagetitle'] . ' - ' . $txt['fm_createdir'] . ' ' . checkdirname($context['directory']);
}

// Actually create a dir
function FM_CreateDir2()
{
	global $context, $txt, $settings, $scripturl, $smf_important, $boarddir;
	
	// Wait.. We do have a directory, don't we? If not, blame Arantor.
	$context['directory'] = !empty($_REQUEST['dir']) && is_dir($_REQUEST['dir']) ? $_REQUEST['dir'] : $boarddir;
	// Make sure they aren't trying to view a directory below the smf root o.O
	$context['directory'] = strlen($context['directory']) < strlen($boarddir) ? $boarddir : $context['directory'];

		// What if they left the field empty? :o
		if(empty($_REQUEST['name']))
		{
			$context['create_message'] = $txt['fm_createdir_empty'];

			// Show the form
			$context['show_form'] = 1;
		}
		else
		{
			if(mkdir($context['directory'].'/'.$_REQUEST['name']))
				$context['create_message'] = sprintf($txt['fm_createdir_complete'],$scripturl. '?action=admin;area=filemanager;do=browse;dir=' . urlEncode($context['directory']));
			else
				$context['create_message'] = sprintf($txt['fm_createdir_failed'],$scripturl. '?action=admin;area=filemanager;do=browse;dir=' . urlEncode($context['directory']));

			// Hide the form
			$context['show_form'] = 0;
		}

	$context['sub_template'] = 'createdir';
	$context['page_title'] = $txt['fm_pagetitle'] . ' - ' . $txt['fm_createdir'] . ' ' . checkdirname($context['directory']);
}

// Create a new file
function FM_CreateFile()
{
	global $context, $txt, $settings, $smf_important, $boarddir;

	// Wait.. We do have a directory, don't we? If not, blame mashby.
	$context['directory'] = !empty($_REQUEST['dir']) && is_dir($_REQUEST['dir']) ? $_REQUEST['dir'] : $boarddir;
	// Make sure they aren't trying to view a directory below the smf root o.O
	$context['directory'] = strlen($context['directory']) < strlen($boarddir) ? $boarddir : $context['directory'];

	$context['create_message'] = sprintf($txt['fm_createfileinfo'],basename($context['directory']));
	$context['show_form'] = 1;

	$context['sub_template'] = 'createfile';
	$context['page_title'] = $txt['fm_pagetitle'] . ' - ' . $txt['fm_createfile'] . ' ' . checkdirname($context['directory']);
}

// Actually create a file
function FM_CreateFile2()
{
	global $context, $txt, $settings, $scripturl, $smf_important, $boarddir;

	// Wait.. We do have a directory, don't we? If not, blame mashby.
	$context['directory'] = !empty($_REQUEST['dir']) && is_dir($_REQUEST['dir']) ? $_REQUEST['dir'] : $boarddir;
	// Make sure they aren't trying to view a directory below the smf root o.O
	$context['directory'] = strlen($context['directory']) < strlen($boarddir) ? $boarddir : $context['directory'];

		// What if they left the field empty? :o
		if(empty($_REQUEST['name']))
		{
			$context['create_message'] = $txt['fm_createfile_empty'];

			// Show the form
			$context['show_form'] = 1;
		}
		else
		{
			if(createfile($context['directory'].'/'.$_REQUEST['name']))
				redirectexit('action=admin;area=filemanager;do=editfile;file=' . urlEncode($context['directory'].'/'.$_REQUEST['name']) . ';new');
			else
				$context['create_message'] = sprintf($txt['fm_createfile_failed'],$scripturl. '?action=admin;area=filemanager;do=browse;dir=' . urlEncode($context['directory']));

			// Hide the form
			$context['show_form'] = 0;
		}

	$context['sub_template'] = 'createfile';
	$context['page_title'] = $txt['fm_pagetitle'] . ' - ' . $txt['fm_createfile'] . ' ' . checkdirname($context['directory']);
}

/*************************************************
* --- This isn't finished nor tested. WIP for the next version. ---  *
**************************************************/
function FM_Extract()
{
	global $context, $txt, $settings, $smf_important, $boarddir;

	// Wait.. We do have a directory, don't we? If not, blame Norv.
	$context['file'] = !empty($_REQUEST['file']) && is_dir($_REQUEST['file']) ? $_REQUEST['file'] : $boarddir;
	// Make sure they aren't trying to view a directory below the smf root o.O
	$context['file'] = strlen($context['file']) < strlen($boarddir) ? $boarddir : $context['file'];	

	$context['file_data'] = fileData($context['file']);

	if(extractFiles($context['file'],$context['file_data']))
		redirectexit('action=admin;area=filemanager;do=browse;dir=' . $context['file_data']['dirname']);
	else
		fatal_lang_error('fm_extract_failed',0);
}
// For audio files.
function FM_PlayAudio()
{
	global $context, $txt, $settings, $smf_important;

	// No veiwing without a file...
	if(!empty($_REQUEST['file']) && file_exists($_REQUEST['file']))
	$context['file'] = $_REQUEST['file'];
	else
	fatal_lang_error('fm_file_noexist',0);

	$context['audio'] = prepareImage($context['file']);

	// Get some data
	$context['audio_data'] = imageData($context['file']);

	// The parent directory
	$context['directory'] = $context['audio_data']['data']['dirname'];

	$context['sub_template'] = 'audio';
	$context['page_title'] = $txt['fm_pagetitle'] . ' - ' . $txt['fm_playaudio'] . ' ' . basename($context['file']);
}
?>