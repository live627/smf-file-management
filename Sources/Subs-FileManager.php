<?php
/******************************************************************************************
* Subs-FileManager.php													*
* The SMF File Manager modification by Marcus "Nas" Forsberg.								*
* This file was last updated in verison 1.0.											*
* This modification, including this file, is copyrighted and may not be redistributed.						*
* Copyright (c) 2009 Marcus Forsberg											*
*******************************************************************************************/

if (!defined('SMF'))
	die('Hacking attempt...');

// This function counts all files inside the given directory
function countFiles($directory,$seperator = ',')
{
	global $txt;

	$filecount = 0;
	$foldercount = 0;

    $dirhandler = opendir($directory);

    // Read and count each file
    while (false !== ($file = readdir($dirhandler))) {

        if ($file != '.' && $file != '..')
		{
			$file = realpath($directory . '/' . $file);

			// We want to seperate the count of files and folders.
			if(filetype($file) == 'dir')
				$foldercount++;
			else
				$filecount++;
		}
    }

    // Tidy up
    closedir($dirhandler);

	$foldersufix = $foldercount == 1 ? $txt['fm_folder'] : $txt['fm_folders'];
	$filesufix = $filecount == 1 ? $txt['fm_file'] : $txt['fm_files'];

	// Put the stuff together.
	$files = $filecount == 0 ? '' : $filecount . ' ' . $filesufix;
	$folders = $foldercount == 0 ? '' : $foldercount . ' ' . $foldersufix;
	$seperator = $foldercount == 0 || $filecount == 0 ? '' : $seperator . ' ';
	$return = $folders == 0 && $files == 0 ? $txt['fm_empty'] : $files . '' . $seperator . '' . $folders;

    // /me throws the count at JBlaze
    return $return;
}

// Convert bytes to kilobytes
function bytesHR($bytes)
{
	global $txt;

	$result = round($bytes / 1024);
	$output = $result . 'kb';

	if($result > 1024)
	{
		$result = round($result / 1024);
		$output = $result . 'mb';
	}
	elseif($result > 1024)
	{
		$result = round($result / 1024);
		$output = $result . 'gb';
	}

    return $output;
}

// Get all files in the specified directory
function listFiles($directory) 
{
	global $context, $txt, $smf_important, $scripturl, $boarddir, $settings, $txt, $imagetypes, $wbfolders, $audiofiles;

	$files = array(
	);
	$directories = array(
	);	

	$dirhandler = dir(realpath($directory));
			
	while ($file = $dirhandler->read()) 
	{
	
	if ($file != '.' && $file != '..')
	{
			$fullpath = realpath($directory . '/' . $file);

			// this is a directory
			if(filetype($fullpath) == 'dir')
			{
				$directories[$file] = array(
					'size' => countFiles($fullpath),
					'icon' => icon('','dir'),				
					'link' => $scripturl. '?action=admin;area=filemanager;do=browse;dir=' . urlEncode($fullpath),
					'view' => '<a href="' . $scripturl . '?action=admin;area=filemanager;do=browse;dir=' . urlEncode($fullpath) . '" title="' . $txt['fm_view'] . '"><img src="' . $settings['default_images_url'] . '/filemanager/view.png" alt="' . $txt['fm_view'] . '" /></a>',
					'data' => pathinfo($fullpath),
					'lastmodified' => timeformat(filemtime($fullpath)),
					'filepermissions' => '<a href="' . $scripturl . '?action=admin;area=filemanager;do=chmod;file=' . urlEncode($fullpath) . '" title="' . $txt['fm_chmod'] . '">' . substr(sprintf('%o', fileperms($fullpath)), -4) . '</a>',
					'remove' => !in_array($file,$smf_important) && !in_array(basename($directory),$smf_important) ? '<a href="' . $scripturl . '?action=admin;area=filemanager;do=remove;file=' . urlEncode($fullpath) . '" title="' . $txt['fm_remove'] . '"><img src="' . $settings['default_images_url'] . '/filemanager/remove.png" alt="' . $txt['fm_remove'] . '" /></a>' : '',
					'rename' => !in_array($file,$smf_important) && !in_array(basename($directory),$smf_important) ? '<a href="' . $scripturl . '?action=admin;area=filemanager;do=rename;file=' . urlEncode($fullpath) . '" title="' . $txt['fm_rename'] . '"><img src="' . $settings['default_images_url'] . '/filemanager/rename.png" alt="' . $txt['fm_remove'] . '" /></a>' : '',
					'chmod' => '<a href="' . $scripturl . '?action=admin;area=filemanager;do=chmod;file=' . urlEncode($fullpath) . '" title="' . $txt['fm_chmod'] . '"><img src="' . $settings['default_images_url'] . '/filemanager/chmod.png" alt="' . $txt['fm_chmod'] . '" /></a>',						
								
				);
			}

			// This is some sort of file
			else
			{
				// common stuff
				$files[$file] = array(
					'data' => pathinfo($fullpath),
					'lastmodified' => timeformat(filemtime($fullpath)),
					'filepermissions' => '<a href="' . $scripturl . '?action=admin;area=filemanager;do=chmod;file=' . urlEncode($fullpath) . '" title="' . $txt['fm_chmod'] . '">' . substr(sprintf('%o', fileperms($fullpath)), -4) . '</a>',
					'remove' => !in_array($file,$smf_important) && !in_array(basename($directory),$smf_important) ? '<a href="' . $scripturl . '?action=admin;area=filemanager;do=remove;file=' . urlEncode($fullpath) . '" title="' . $txt['fm_remove'] . '"><img src="' . $settings['default_images_url'] . '/filemanager/remove.png" alt="' . $txt['fm_remove'] . '" /></a>' : '',
					'rename' => !in_array($file,$smf_important) && !in_array(basename($directory),$smf_important) ? '<a href="' . $scripturl . '?action=admin;area=filemanager;do=rename;file=' . urlEncode($fullpath) . '" title="' . $txt['fm_rename'] . '"><img src="' . $settings['default_images_url'] . '/filemanager/rename.png" alt="' . $txt['fm_remove'] . '" /></a>' : '',
					'chmod' => '<a href="' . $scripturl . '?action=admin;area=filemanager;do=chmod;file=' . urlEncode($fullpath) . '" title="' . $txt['fm_chmod'] . '"><img src="' . $settings['default_images_url'] . '/filemanager/chmod.png" alt="' . $txt['fm_chmod'] . '" /></a>',	
					'download' => '<a href="' . $scripturl . '?action=admin;area=filemanager;do=download;file=' . urlEncode($fullpath) . '" title="' . $txt['fm_download'] . '"><img src="' . $settings['default_images_url'] . '/filemanager/download.png" alt="' . $txt['fm_download'] . '" /></a>',												
				);

				if(in_array($files[$file]['data']['extension'],$imagetypes))
				{
					$files[$file] = array_merge($files[$file], array(
						'img' => imageData($fullpath), // We need the image size to figure out if we should use highslide for it or if its too small.
						'size' => bytesHR(filesize($fullpath)),
						'icon' => prepareImage($fullpath),												
						'link' => $scripturl. '?action=admin;area=filemanager;do=image;file=' . urlEncode($fullpath),
						'view' => '<a href="' . $scripturl . '?action=admin;area=filemanager;do=image;file=' . urlEncode($fullpath) . '" title="' . $txt['fm_view'] . '"><img src="' . $settings['default_images_url'] . '/filemanager/view.png" alt="' . $txt['fm_view'] . '" /></a>',
					));
				}
				elseif(in_array($files[$file]['data']['extension'],$wbfolders))
				{
					$files[$file] = array_merge($files[$file], array(
						'size' => bytesHR(filesize($fullpath)),
						'icon' => icon($files[$file]['data']['extension'],'package'),									
						'link' => $scripturl. '?action=admin;area=filemanager;do=download;file=' . urlEncode($fullpath) . '',
						'view' => '<a href="' . $scripturl . '?action=admin;area=filemanager;do=download;file=' . urlEncode($fullpath) . '" title="' . $txt['fm_view'] . '"><img src="' . $settings['default_images_url'] . '/filemanager/view.png" alt="' . $txt['fm_view'] . '" /></a>',
					));
				}
				elseif(in_array($files[$file]['data']['extension'],$audiofiles))
				{
					$files[$file] = array_merge($files[$file], array(
						'size' => bytesHR(filesize($fullpath)),
						'icon' => icon($files[$file]['data']['extension'],'audio'),									
						'link' => $scripturl. '?action=admin;area=filemanager;do=audio;file=' . urlEncode($fullpath) . '',
						'view' => '<a href="' . $scripturl . '?action=admin;area=filemanager;do=audio;file=' . urlEncode($fullpath) . '" title="' . $txt['fm_view'] . '"><img src="' . $settings['default_images_url'] . '/filemanager/view.png" alt="' . $txt['fm_view'] . '" /></a>',
					));
				}						
				else
				{
					$files[$file] = array_merge($files[$file], array(
						'size' => bytesHR(filesize($fullpath)),
						'icon' => icon($files[$file]['data']['extension'],'file'),			
						'link' => $scripturl. '?action=admin;area=filemanager;do=viewfile;file=' . urlEncode($fullpath),
						'view' => '<a href="' . $scripturl . '?action=admin;area=filemanager;do=viewfile;file=' . urlEncode($fullpath) . '" title="' . $txt['fm_view'] . '"><img src="' . $settings['default_images_url'] . '/filemanager/view.png" alt="' . $txt['fm_view'] . '" /></a>',					
						'edit' => '<a href="' . $scripturl . '?action=admin;area=filemanager;do=editfile;file=' . urlEncode($fullpath) . '" title="' . $txt['fm_edit'] . '"><img src="' . $settings['default_images_url'] . '/filemanager/edit.png" alt="' . $txt['fm_edit'] . '" /></a>',		
					));			
				}
			}
		}
    }

	// Tidy up
	@closedir($dirhandler);

	$files_order = array_keys($files);
	natcasesort($files_order);
	$directories_order = array_keys($directories);
	natcasesort($directories_order);

	$data = array(
		'files' => $files,
		'files_order' => $files_order,
		'directories' => $directories,
		'directories_order' => $directories_order,
	);

    // Merry christmas...
    return $data;
}

// Get the data of a file
function fileData($file) 
{
	global $smf_important, $txt, $settings, $scripturl;

	$fullpath = realpath($file);

	$data = array(
		'data' => pathinfo($fullpath),
		'lastmodified' => timeformat(filemtime($fullpath)),
		'filesize' => bytesHR(filesize($fullpath)),
		'filepermissions' => substr(sprintf('%o', fileperms($fullpath)), -3),		
		'remove' => !in_array(basename($fullpath),$smf_important) ? '<a href="' . $scripturl . '?action=admin;area=filemanager;do=remove;file=' . urlEncode($fullpath) . '" title="' . $txt['fm_remove'] . '"><img src="' . $settings['default_images_url'] . '/filemanager/remove.png" alt="' . $txt['fm_remove'] . '" /></a>' : '',
		'edit' => '<a href="' . $scripturl . '?action=admin;area=filemanager;do=editfile;file=' . urlEncode($fullpath) . '" title="' . $txt['fm_edit'] . '"><img src="' . $settings['default_images_url'] . '/filemanager/edit.png" alt="' . $txt['fm_edit'] . '" /></a>',		
		'rename' => !in_array(basename($fullpath),$smf_important) ? '<a href="' . $scripturl . '?action=admin;area=filemanager;do=rename;file=' . urlEncode($fullpath) . '" title="' . $txt['fm_rename'] . '"><img src="' . $settings['default_images_url'] . '/filemanager/rename.png" alt="' . $txt['fm_rename'] . '" /></a>' : '',
		'view' => '<a href="' . $scripturl . '?action=admin;area=filemanager;do=viewfile;file=' . urlEncode($fullpath) . '" title="' . $txt['fm_view'] . '"><img src="' . $settings['default_images_url'] . '/filemanager/view.png" alt="' . $txt['fm_view'] . '" /></a>',	
		'chmod' => '<a href="' . $scripturl . '?action=admin;area=filemanager;do=chmod;file=' . urlEncode($fullpath) . '" title="' . $txt['fm_chmod'] . '"><img src="' . $settings['default_images_url'] . '/filemanager/chmod.png" alt="' . $txt['fm_chmod'] . '" /></a>',						
		'download' => '<a href="' . $scripturl . '?action=admin;area=filemanager;do=download;file=' . urlEncode($fullpath) . '" title="' . $txt['fm_download'] . '"><img src="' . $settings['default_images_url'] . '/filemanager/download.png" alt="' . $txt['fm_download'] . '" /></a>',
		);

	return $data;
}

// Get the data of an image
function imageData($file) 
{
	global $smf_important, $txt, $settings, $scripturl;

	$fullpath = realpath($file);

	$data = array(
		'data' => pathinfo($file),	
		'size' => getimagesize($file),
		'filepermissions' => substr(sprintf('%o', fileperms($fullpath)), -4),	
		'lastmodified' => timeformat(filemtime($file)),
		'filesize' => bytesHR(filesize($file)),
		'remove' => !in_array(basename($file),$smf_important) ? '<a href="' . $scripturl . '?action=admin;area=filemanager;do=remove;file=' . urlEncode($file) . '" title="' . $txt['fm_remove'] . '"><img src="' . $settings['default_images_url'] . '/filemanager/remove.png" alt="' . $txt['fm_remove'] . '" /></a>' : '',
		'rename' => !in_array(basename($file),$smf_important) ? '<a href="' . $scripturl . '?action=admin;area=filemanager;do=rename;file=' . urlEncode($file) . '" title="' . $txt['fm_rename'] . '"><img src="' . $settings['default_images_url'] . '/filemanager/rename.png" alt="' . $txt['fm_rename'] . '" /></a>' : '',	
		'chmod' => '<a href="' . $scripturl . '?action=admin;area=filemanager;do=chmod;file=' . urlEncode($fullpath) . '" title="' . $txt['fm_chmod'] . '"><img src="' . $settings['default_images_url'] . '/filemanager/chmod.png" alt="' . $txt['fm_chmod'] . '" /></a>',						
		'download' => '<a href="' . $scripturl . '?action=admin;area=filemanager;do=download;file=' . urlEncode($fullpath) . '" title="' . $txt['fm_download'] . '"><img src="' . $settings['default_images_url'] . '/filemanager/download.png" alt="' . $txt['fm_download'] . '" /></a>',
		);

	return $data;
}

// Open a file for reading
function openFile($file) 
{
	global $txt;

	$file_open = fopen($file, 'r') or fatal_lang_error('fm_file_couldnotopen_read',0);
	$file_content = filesize($file) != 0 ? fread($file_open, filesize($file)) : '';
	fclose($file_open);

	return $file_content;

}

// Save edits to a file
function writeToFile($file,$content) 
{
	global $txt;

	$file_open = fopen($file, 'w') or fatal_lang_error('fm_file_couldnotopen_write',0);
	fwrite($file_open, $content);
	fclose($file_open);
}

// Empty a dir.
function emptyDir($dir)
{
	$dirhandler = opendir($dir);

		while ($file = @readdir($dirhandler))
		{
			if ($file != '.' && $file != '..')
			{
				$fullpath = realpath($dir . '/' . $file);

				if(filetype($fullpath) == 'dir')
				{
					deleteDir($fullpath);

				}
				else
				{
					@unlink($fullpath);
				}
			}
		}
		
		@closedir($dirhandler);

		return true;
}

// Rmove a dir
function deleteDir($directory)
{
	if (emptyDir($directory))
	{
		if (@rmdir($directory))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

// This simply checks if we are in the SMF root directory.
function checkDirName($directory)
{
	global $txt, $boarddir;

	return $directory != $boarddir ? basename($directory) : $txt['fm_rootfolder'];
}

// Work out where we are, how many files there are in here, and get the parent directory. (WIP)
function getlocation()
{
	global $context, $settings, $txt, $boarddir, $scripturl;
	
	if(!empty($context['directory']))
	{
		$directory = checkDirName($context['directory']);
		$parent = checkDirName(dirname($context['directory']));
		
		$output = array(
			'directory' => $txt['fm_currentdir'] . ': ' . $directory . '. '. sprintf($txt['fm_thereare'],countFiles($context['directory'],' &amp;')) . '<br/>',
			'parent' => $context['directory'] != $boarddir ? '<a href="' . $scripturl . '?action=admin;area=filemanager;do=browse;dir=' . dirname($context['directory']) . '">' . $txt['fm_gotoparent'] . '</a> (' . $parent . ')' : '',
			'root' => $context['directory'] != $boarddir && $parent != $txt['fm_rootfolder'] ? '<a href="' . $scripturl . '?action=admin;area=filemanager;do=browse">' . $txt['fm_gotoroot'] . '</a>' : '',
		);

		return $output;
	}

	else
		return false;
}

// Though we usually don't add copyrights: This is only for admins, so some creds to the authors won't hurt anyone, will it? :P
function modcopyright()
{
	echo'&copy; SMF File Management Tool by Marcus Forsberg';
}

// Generate a page index
function filePageIndex($files,$directory)
{
	global $scripturl, $modSettings, $txt;

	$url = $scripturl . '?action=admin;area=filemanager;do=browse;dir=' . $directory;
	$start = !empty($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;	
	$count = count($files);
	$maxfiles = $modSettings['defaultMaxTopics'];
	$files = array_slice($files, $start, $maxfiles);

	$pageindex = array(
		'index' => '<strong>' . $txt['pages'] . ':</strong> ' . constructPageIndex($url, $start, $count, $maxfiles),
		'files' => $files,
	);

	return $pageindex;
}

// Prepare and image for display
function prepareImage($image)
{
	global $scripturl, $modSettings, $txt, $boarddir, $boardurl;

	$imageurl = str_replace($boarddir,$boardurl . '/',$image);
	$imageurl = str_replace('\\','/',$imageurl);

	return $imageurl;
}

// Search through a dir.
function searchDir($string,$dir)
{
	global $scripturl, $txt, $settings, $imagetypes, $wbfolders;
	
	$dirhandler = opendir($dir);

	$files = array();
	$driectories = array();

		while ($file = @readdir($dirhandler))
		{
			if ($file != '.' && $file != '..')
			{
				$fullpath = realpath($dir . '/' . $file);
				
				$data = pathinfo($fullpath);

				if(stripos($fullpath,$string) !== false)
				{
					if(filetype($fullpath) == 'dir')
					$driectories[$file] = array(
						'display' => '<a href="' . $scripturl . '?action=admin;area=filemanager;do=browse;dir=' . urlEncode($fullpath) . '" title="' . $txt['fm_view'] . '">' . basename($fullpath) . '</a>',
					);
					elseif(in_array($data['extension'],$imagetypes))
					$files[$file] = array(
						'display' => '<a href="' . $scripturl . '?action=admin;area=filemanager;do=image;file=' . urlEncode($fullpath) . '" title="' . $txt['fm_view'] . '">' . basename($fullpath) . '</a>',
					);	
					elseif(in_array($data['extension'],$wbfolders))
					$files[$file] = array(
						'display' => '<a href="' . $scripturl . '?action=admin;area=filemanager;do=download;file=' . urlEncode($fullpath) . '" title="' . $txt['fm_view'] . '">' . basename($fullpath) . '</a>',
					);					
					else
					$files[$file] = array(
						'display' => '<a href="' . $scripturl . '?action=admin;area=filemanager;do=viewfile;file=' . urlEncode($fullpath) . '" title="' . $txt['fm_view'] . '">' . basename($fullpath) . '</a>',
					);
				}
			}
		}

		@closedir($dirhandler);

		$results = array(
			'files' => $files,
			'directories' => $driectories,
		);

    // Return the results.
    return $results;
}

// Create a file
function createFile($file)
{
	global $txt;

	$handle = fopen($file, 'w') or fatal_lang_error('fm_createfile_error',0);
	fclose($handle);
	return true;
}

/*************************************************
* --- This isn't finished nor tested. WIP for the next version. ---  *
**************************************************/
function extractFiles($file,$data)
{
	global $txt;

	if($data['extension'] == 'zip')
	{
		if (class_exists('ZipArchive'))
		{

			$zip = new ZipArchive;

			if ($zip->open($file) === TRUE) 
			{
    				$zip->extractTo($data['dirname'] . '/' . $data['basename']);
   				$zip->close();
				return true;
			}
			else
			return false;
		}
		else
		{
			fatal_lang_error('fm_zipnotinstalled',0);
		}
	}

	elseif($data['extension'] == 'rar')
	{
		if (class_exists('extract'))
		{
			$rar = rar_open($file.$data['dirname']);
			$list = rar_list($rar);

			foreach($list as $file)
			{
    				$entry = rar_entry_get($rar, $file);
   				$entry->extract('.');
			}
				rar_close($rar);
		}
		else
		{
			fatal_lang_error('fm_rarnotinstalled',0);
		}
	}
}


// The icon image for a file.
function icon($extension, $fallback = 'file')
{
	global $txt, $settings;

	$fallback_image = $settings['default_images_url'] . '/filemanager/filetypes/' .$fallback . '.png';

	if(empty($extension))
	return $fallback_image;
	else
	{
		$filetypes = array(
			'ttf' => 'font', 'gdf' => 'font', 'htaccess' => 'code', 'htpasswd' => 'code',
			'php' => 'php', 'php~' => 'php', 'asp' => 'text', 'shtml' => 'text',
			'rb' => 'ruby', 'pl' => 'text', 'pdf' => 'pdf', 'txt' => 'text',
			'bas' => 'text', 'c' => 'c', 'h' => 'text', 'htm' => 'html',
			'html' => 'html', 'css' => 'css', 'pas' => 'text', 'js' => 'script',
			'xml' => 'xml', 'sql' => 'sql', 'mp3' => 'audio', 'ram' => 'audio',
			'ra' => 'audio', 'au' => 'audio', 'wav' => 'audio', 'm3u' => 'audio',
			'aiff' => 'audio', 'aifc' => 'audio', 'mid' => 'audio', 'rmi' => 'audio',
			'aif' => 'audio', 'snd' => 'audio', 'avi' => 'video', 'movie' => 'video',
			'mpeg' => 'video', 'mp2' => 'video', 'mpg' => 'video', 'mov' => 'video',
			'mpe' => 'video', 'mpv2' => 'video', 'mpa' => 'video', 'lsf' => 'video',
			'lsx' => 'video', 'asf' => 'video', 'asr' => 'video', 'asx' => 'video',
			'qt' => 'video', 'm2v' => 'video', 'rar' => 'package', 'gz' => 'package',
			'tar' => 'package', 'zip' => 'package', 'bz2' => 'package', 'bin' => 'package',
			'lzh' => 'package', 'cab' => 'package', 'lha' => 'package', '7z' => 'package',
			'tgz' => 'package', 'z' => 'package', 'exe' => 'application', 'doc' => 'text', 
			'dot' => 'text', 'odt' => 'text', 'xlm' => 'text', 'xls' => 'excel',
			'ods' => 'text', 'ppt' => 'text', 'pps' => 'text', 'cab' => 'text',
			'odp' => 'text', 'pub' => 'text', 'wri' => 'text', 'odc' => 'text',
			'odb' => 'text', 'jpg' => 'image', 'gif' => 'image', 'png' => 'image',
			'bmp' => 'image', 'jpeg' => 'image', 'jpe' => 'image', 'jfif' => 'image',
			'svg' => 'image', 'tif' => 'image', 'tiff' => 'image', 'ico' => 'image',
			'cod' => 'image', 'ief' => 'image', 'ras' => 'image', 'cmx' => 'image',
			'pnm' => 'image', 'pbm' => 'image', 'pgm' => 'image', 'ppm' => 'image',
			'rgb' => 'image', 'xbm' => 'image', 'xpm' => 'image', 'xwd' => 'image',
			'flv' => 'flash', 'swf' => 'flash', 'psd' => 'photoshop', 'ai' => 'illustrator',
			'list' => 'list','log' => 'list', 'tif' => 'image'
		);

		if(isset($filetypes[strtolower($extension)]))
		{
			if(file_exists($settings['default_theme_dir'] . '/images/filemanager/filetypes/' . $filetypes[strtolower($extension)] . '.png'))
				return $settings['default_images_url'] . '/filemanager/filetypes/' . $filetypes[strtolower($extension)] . '.png';
			else
				return $fallback_image;
		}
		else
			return $fallback_image;
	}
}



?>