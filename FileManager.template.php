<?php
// FileManager 1.0;

// Information about the current directory.
function template_filemanager_above()
{
	global $context, $boarddir, $scripturl, $txt;
	
	if(!empty($context['fm_location']))
	{
		echo'
			<span class="upperframe"><span></span></span>
			<div class="roundframe">
				<table width="100%">
					<tr>
						<td width="80%" valign="top">
							', !empty($context['fm_location']['directory']) ? $context['fm_location']['directory'] : '', '<br/>
							', !empty($context['page_index']['index']) ? $context['page_index']['index'] : '', '
						</td><td valign="top">
							<br />
							<form action="', $scripturl, '?action=admin;area=filemanager;do=search;dir=', $context['directory'], '" method="post">
								<input type="text" name="search" value="', $txt['fm_search_desc'], '" />
								<input type="submit" value="', $txt['fm_search'], '" class="button_submit" />
							</form>';

	echo'
						</td>
					</tr>
				</table><br/>';

	// This will only be visible if they're using an outdated version.
	echo'
		<p id="newVersion" class="errorbox" style="display: none;">
			', sprintf($txt['fm_outdated'],$context['fm_curVer']), '
		</p>';

	echo'
			</div>
			<span class="lowerframe"><span></span></span>';

	// Navigation
	echo template_button_strip($context['nav_buttons']),'<br/><br/>';

	// Version check
	echo $context['check_version'];

	}
}

function template_filemanager_below()
{

	if(empty($_REQUEST['do']) || $_REQUEST['do'] == 'browse')
	echo'
	<div class="windowbg"><span class="botslice"><span></span></span></div>';

	echo'
	<span class="smalltext">
		', modcopyright(), '
	</span>';

}

// Browsing a directory.
function template_browse()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt, $boardurl, $imagetypes;
	
	// Just start off the table
	echo'
		<div class="tborder topic_table" id="messageindex">
			<table class="table_grid" cellspacing="0">
				<thead>
					<tr>
						<th scope="col" class="smalltext first_th" width="5%">' , $txt['fm_filetype'], '</th>
						<th scope="col" class="smalltext">' , $txt['fm_filename'], '</th>	
						<th scope="col" class="smalltext">' , $txt['fm_filesize'], '</th>
						<th scope="col" class="smalltext">' , $txt['fm_filepermissions'], '</th>					
						<th scope="col" class="smalltext">' , $txt['fm_lastedit'], '</th>					
						<th scope="col" class="smalltext last_th">' , $txt['fm_actions'], '</th>
					</tr>
				</thead>		
				<tbody>';

		// Go to SMF Root (If we aren't there already)
		if(!empty($context['fm_location']['root']))
		echo'
					<tr cellspacing="5">
						<td class="approvebg" style="text-align: center; margin: 25px;">
							<img src="', $context['typeimagepath'], 'parent.png" alt="." />
						</td>
						<td class="approvebg" colspan="5" style="padding: 7px;">', $context['fm_location']['root'], '</td>
					</tr>';

		// Go to parent directory
		if(!empty($context['fm_location']['parent']))
		echo'
					<tr cellspacing="5">
						<td class="approvebg" style="text-align: center; margin: 25px;">
							<img src="', $context['typeimagepath'], 'parent.png" alt="." />
						</td>
						<td class="approvebg" colspan="5" style="padding: 7px;">', $context['fm_location']['parent'], '</td>
					</tr>';
				
		// Make sure we've got something to show them...			
		if(empty($context['files']['directories']) && empty($context['files']['files']))
		{
			echo'
					<tr>
						<td class="windowbg" style="text-align: center;" colspan="6">', $txt['fm_nofiles'], '</td>
					</tr>';
		} 
	
		else 
		{
			// Display all folders in this directory
			foreach($context['files']['directories_order'] AS $dirname)
			{
				$file = $context['files']['directories'][$dirname];
		
				echo'
					<tr>
						<td class="windowbg2" style="text-align: center;">
							<img src="', $file['icon'], '" alt="" />
						</td>
						<td class="windowbg2">
							<a href="', $file['link'], '">', $file['data']['basename'], '</a>
						</td>
						<td class="windowbg2">
							', $file['size'], '
						</td>
						<td class="windowbg2" style="text-align: center;">
							', $file['filepermissions'], '
						</td>						
						<td class="windowbg2">
							', $file['lastmodified'], '
						</td>
						<td class="windowbg2">
							' , !empty($file['view']) ? $file['view'] : '', '
							' , !empty($file['edit']) ? $file['edit'] : '', '		
							' , !empty($file['remove']) ? $file['remove'] : '', '		
							' , !empty($file['rename']) ? $file['rename'] : '', '
							' , !empty($file['chmod']) ? $file['chmod'] : '', '									
						</td>
					</tr>';
			}
			
			// Display all files in this directory
			foreach($context['files']['files_order'] AS $filename)
			{
				$file = $context['files']['files'][$filename];

				echo'
					<tr>
						<td class="windowbg" style="text-align: center;">
						', in_array($file['data']['extension'],$imagetypes) && $file['img']['size']['0'] > 100 ? '<a href="' . $file['icon'] . '" onclick="return hs.expand(this)">' : '', '<img src="', $file['icon'], '" alt="', !empty($file['data']['extension']) ? $file['data']['extension'] : '', '" ', in_array($file['data']['extension'],$imagetypes) ? 'style="max-width: 50px; max-height: 50px;"' : '', '/>', !empty($file['data']['extension']) ? '<br /><span class="smalltext">' . $file['data']['extension'] . '</span>' : '', '', in_array($file['data']['extension'],$imagetypes) && $file['img']['size']['0'] > 100 ? '</a>' : '', '
						</td>
						<td class="windowbg">
							<a href="', $file['link'], '">', $file['data']['basename'], '</a>
						</td>
						<td class="windowbg">
							', $file['size'], '
						</td>
						<td class="windowbg" style="text-align: center;">
							', $file['filepermissions'], '
						</td>						
						<td class="windowbg">
							', $file['lastmodified'], '
						</td>
						<td class="windowbg">
							' , !empty($file['view']) ? $file['view'] : '', '
							' , !empty($file['edit']) ? $file['edit'] : '', '		
							' , !empty($file['remove']) ? $file['remove'] : '', '		
							' , !empty($file['rename']) ? $file['rename'] : '', '		
							' , !empty($file['chmod']) ? $file['chmod'] : '', '	
							' , !empty($file['download']) ? $file['download'] : '', '									
						</td>
					</tr>';
			}
		}
	
	// Close the table
	echo'			</tbody>
			</table>
		</div>';
}

// Viewing a file.
function template_view()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt, $boarddir, $smf_important;

	
	echo'
		<table width="100%">
			<tr>
				<td width="20%" valign="top">
					<table class="table_grid" cellspacing="0">
						<thead>
							<tr>
								<th scope="col" class="smalltext first_th">', $txt['fm_fileinfo'], '</th>
								<th scope="col" class="smalltext">&nbsp;</th>
								<th scope="col" class="smalltext last_th">&nbsp;</th>
							</tr>
						</thead>
							<tr>
								<td class="windowbg headerpadding smalltext" style="text-align: left;" colspan="3">
									', !empty($context['file']) ? '<strong>'. $txt['fm_filename']. ':</strong> '. basename($context['file']). '<br/>' : '','
									', !empty($context['file_data']['data']['extension']) ? '<strong>'.$txt['fm_filetype'].':</strong>  '.$context['file_data']['data']['extension'].'<br />' : '','												
									', !empty($context['file_data']['filesize']) ? '<strong>'. $txt['fm_filesize']. ':</strong>  '. $context['file_data']['filesize']. '<br />' : '','	
									', !empty($context['file_data']['filepermissions']) ? '<strong>'. $txt['fm_filepermissions']. ':</strong>  '. $context['file_data']['filepermissions']. '<br />' : '','
									', !empty($context['file_data']['data']['dirname']) ? '<strong>'.$txt['fm_filedir'].':</strong>  '.str_replace($boarddir,$txt['fm_rootfolder'],$context['file_data']['data']['dirname']).'<br />' : '', '
									', !empty($context['file_data']['lastmodified']) ? '<strong>'. $txt['fm_lastedit']. ':</strong>  '. $context['file_data']['lastmodified']. '<br />' : '','
									', !empty($context['file']) && in_array(basename($context['file']),$smf_important) ? '<i>'.$txt['fm_smfimportant'].'</i>' : '', '												
								</td>
							</tr>					
					</table>
					<br/>
					<table class="table_grid" cellspacing="0">
						<thead>
							<tr>
								<th scope="col" class="smalltext first_th" width="100%">', $txt['fm_fileactions'], '</th>
								<th scope="col" class="smalltext">&nbsp;</th>
								<th scope="col" class="smalltext last_th">&nbsp;</th>
							</tr>
						</thead>
							<tr>
								<td class="windowbg headerpadding smalltext" colspan="3">
									', !empty($context['file_data']['view']) ? $context['file_data']['view'] : '', ' 											
									', !empty($context['file_data']['edit']) ? $context['file_data']['edit'] : '', ' 												
									', !empty($context['file_data']['remove']) ? $context['file_data']['remove'] : '', ' 	
									', !empty($context['file_data']['rename']) ? $context['file_data']['rename'] : '', '	
									', !empty($context['file_data']['chmod']) ? $context['file_data']['chmod'] : '', '	
									', !empty($context['file_data']['download']) ? $context['file_data']['download'] : '', '											
								</td>
							</tr>					
					</table>	
					<br/>								
								<h3 class="catbg"><span class="left"></span><a href="', $scripturl, '?action=admin;area=filemanager;do=browse;dir=', !empty($context['file_data']['data']['dirname']) ? urlEncode($context['file_data']['data']['dirname']) : '', '">', $txt['back'], '</a></h4>
									
				</td>
				<td width="80%" valign="top">
					<h4 class="catbg"><span class="left"></span>
					<span class="align_left">', $txt['fm_viewfile'], ' ', !empty($context['file']) ? basename($context['file']) : '', '</span></h4>
					<div class="windowbg2" style="text-align: center;">
						<span class="topslice"><span></span></span>

							<input type="button" value="', $txt['fm_codepress_toggle'], '" onClick="viewfile.toggleEditor()"/>
							<input type="button" value="', $txt['fm_codepress_toggle_lines'], '" onClick="viewfile.toggleLineNumbers()"/>
							<textarea rows="60" cols="120" name="filecontent" id="viewfile" class="codepress ', in_array($context['file_data']['data']['extension'],$context['highlightable']) ? $context['highlight_languages'][$context['file_data']['data']['extension']] : 'text', ' linenumbers-on readonly-on">', !empty($context['file_content']) ? $context['file_content'] : '', '</textarea>
						<span class="botslice"><span></span></span>
					</div>
				</td>
			</tr>
		</table>';
	
}

// Editing a file.
function template_edit()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt, $boarddir, $smf_important;
	
	echo'
		<table width="100%">
			<tr>
				<td width="20%" valign="top">
					<table class="table_grid" cellspacing="0">
						<thead>
							<tr>
								<th scope="col" class="smalltext first_th">', $txt['fm_fileinfo'], '</th>
								<th scope="col" class="smalltext">&nbsp;</th>
								<th scope="col" class="smalltext last_th">&nbsp;</th>
							</tr>
						</thead>
							<tr>
								<td class="windowbg headerpadding smalltext" style="text-align: left;" colspan="3">
									', !empty($context['file']) ? '<strong>'. $txt['fm_filename']. ':</strong> '. basename($context['file']). '<br/>' : '','
									', !empty($context['file_data']['data']['extension']) ? '<strong>'.$txt['fm_filetype'].':</strong>  '.$context['file_data']['data']['extension'].'<br />' : '','												
									', !empty($context['file_data']['filesize']) ? '<strong>'. $txt['fm_filesize']. ':</strong>  '. $context['file_data']['filesize']. '<br />' : '','	
									', !empty($context['file_data']['filepermissions']) ? '<strong>'. $txt['fm_filepermissions']. ':</strong>  '. $context['file_data']['filepermissions']. '<br />' : '','
									', !empty($context['file_data']['data']['dirname']) ? '<strong>'.$txt['fm_filedir'].':</strong>  '.str_replace($boarddir,$txt['fm_rootfolder'],$context['file_data']['data']['dirname']).'<br />' : '', '
									', !empty($context['file_data']['lastmodified']) ? '<strong>'. $txt['fm_lastedit']. ':</strong>  '. $context['file_data']['lastmodified']. '<br />' : '','
									', !empty($context['file']) && in_array(basename($context['file']),$smf_important) ? '<i>'.$txt['fm_smfimportant'].'</i>' : '', '												
								</td>
							</tr>					
					</table>
					<br/>
					<table class="table_grid" cellspacing="0">
						<thead>
							<tr>
								<th scope="col" class="smalltext first_th" width="100%">', $txt['fm_fileactions'], '</th>
								<th scope="col" class="smalltext">&nbsp;</th>
								<th scope="col" class="smalltext last_th">&nbsp;</th>
							</tr>
						</thead>
							<tr>
								<td class="windowbg headerpadding smalltext" colspan="3">
									', !empty($context['file_data']['view']) ? $context['file_data']['view'] : '', ' 											
									', !empty($context['file_data']['edit']) ? $context['file_data']['edit'] : '', ' 												
									', !empty($context['file_data']['remove']) ? $context['file_data']['remove'] : '', ' 	
									', !empty($context['file_data']['rename']) ? $context['file_data']['rename'] : '', '	
									', !empty($context['file_data']['chmod']) ? $context['file_data']['chmod'] : '', '
									', !empty($context['file_data']['download']) ? $context['file_data']['download'] : '', '														
								</td>
							</tr>					
					</table>	
					<br/>								
					<h3 class="catbg"><span class="left"></span><a href="', $scripturl, '?action=admin;area=filemanager;do=browse;dir=', !empty($context['file_data']['data']['dirname']) ? urlEncode($context['file_data']['data']['dirname']) : '', '">', $txt['back'], '</a></h4>									
				</td>
				<td width="80%" valign="top">
					<h4 class="catbg"><span class="left"></span>
					<span class="align_left">', $txt['fm_editfile'], ' ', basename($context['file']), '</span></h4>
					<div class="windowbg2" style="text-align: center;">
					<span class="topslice"><span></span></span>
						', empty($context['edit_message']) ? '<strong>' . $txt['fm_editinfo'] . '</strong><br/>'  : '', '

						', !empty($context['edit_message']) ? $context['edit_message'].'<br/><br/>' : '<br/>' ,'


						<form action="', $scripturl, '?action=admin;area=filemanager;do=editfile2;file=', $context['file'], '" method="post">
							<input type="button" value="', $txt['fm_codepress_toggle'], '" onClick="editfile.toggleEditor()"/>
							<input type="button" value="', $txt['fm_codepress_toggle_lines'], '" onClick="editfile.toggleLineNumbers()"/>
							<input type="button" value="', $txt['fm_codepress_toggle_auto'], '" onClick="editfile.toggleAutoComplete()"/>	
							<textarea rows="60" cols="120" name="filecontent" id="editfile" class="codepress ', in_array($context['file_data']['data']['extension'],$context['highlightable']) ? $context['highlight_languages'][$context['file_data']['data']['extension']] : 'text', ' linenumbers-on">', $context['file_content'], '</textarea><br /><br />
							<input type="submit" value="', $txt['fm_savefile'], '" onClick="editfile.toggleEditor()" />
						</form>';


					echo'
					<span class="botslice"><span></span></span>
					</div>
				</td>
			</tr>
		</table>';
}

// Removing a file.
function template_remove()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt;
	
		echo'
		<h4 class="catbg"><span class="left"></span>
			<span class="align_left">
				', $txt['fm_removefile'], ' ', basename($context['file']), '
			</span>
		</h4>
		<div class="windowbg2 headerpadding" style="text-align: center;">
			<span class="topslice"><span></span></span>
				', $context['remove_message'], '
			<span class="botslice"><span></span></span>
		</div>';
}

// Displaying an image.
function template_image()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt, $boarddir, $smf_important;
	
	echo'
		<table width="100%">
			<tr>
				<td width="20%" valign="top">
					<table class="table_grid" cellspacing="0">
						<thead>
							<tr>
								<th scope="col" class="smalltext first_th">', $txt['fm_imageinfo'], '</th>
								<th scope="col" class="smalltext">&nbsp;</th>
								<th scope="col" class="smalltext last_th">&nbsp;</th>
							</tr>
						</thead>
							<tr>
								<td class="windowbg headerpadding smalltext" style="text-align: left;" colspan="3">
									<strong>', $txt['fm_filename'], ':</strong> ', basename($context['file']), '<br/>											
												
									<strong>', $txt['fm_imagewidth'], ':</strong>  ', $context['image_data']['size']['0'], 'px<br />

									<strong>', $txt['fm_imageheight'], ':</strong>  ', $context['image_data']['size']['1'], 'px<br />		
												
									', !empty($context['image_data']['data']['extension']) ? '<strong>'.$txt['fm_filetype'].':</strong>  '.sprintf($txt['fm_image'],$context['image_data']['data']['extension']).'<br />' : '','														
												
									<strong>', $txt['fm_imagesize'], ':</strong>  ', $context['image_data']['filesize'], '<br />
													
									<strong>', $txt['fm_filepermissions'], ':</strong>  ', $context['image_data']['filepermissions'], '<br />
												
									', !empty($context['image_data']['data']['dirname']) ? '<strong>'.$txt['fm_imagedir'].':</strong>  '.str_replace($boarddir,$txt['fm_rootfolder'],$context['image_data']['data']['dirname']).'<br />' : '', '
												
									<strong>', $txt['fm_lastedit'], ':</strong>  ', $context['image_data']['lastmodified'], '<br />
												
									', in_array(basename($context['file']),$smf_important) ? '<i>'.$txt['fm_smfimportant'].'</i>' : '', '											
								</td>
							</tr>					
					</table>
					<br/>
					<table class="table_grid" cellspacing="0">
						<thead>
							<tr>
								<th scope="col" class="smalltext first_th" width="100%">', $txt['fm_fileactions'], '</th>
								<th scope="col" class="smalltext">&nbsp;</th>
								<th scope="col" class="smalltext last_th">&nbsp;</th>
							</tr>
						</thead>
							<tr>
								<td class="windowbg headerpadding smalltext" colspan="3">
									', !empty($context['image_data']['view']) ? $context['image_data']['view'] : '', ' 											
									', !empty($context['image_data']['edit']) ? $context['image_data']['edit'] : '', ' 												
									', !empty($context['image_data']['remove']) ? $context['image_data']['remove'] : '', ' 	
									', !empty($context['image_data']['rename']) ? $context['image_data']['rename'] : '', '	
									', !empty($context['image_data']['chmod']) ? $context['image_data']['chmod'] : '', '
									', !empty($context['image_data']['download']) ? $context['image_data']['download'] : '', '																								
								</td>
							</tr>					
					</table>	
					<br/>								
					<h3 class="catbg"><span class="left"></span><a href="', $scripturl, '?action=admin;area=filemanager;do=browse;dir=', !empty($context['image_data']['data']['dirname']) ? urlEncode($context['image_data']['data']['dirname']) : '', '">', $txt['back'], '</a></h4>									
				</td>
					<td width="80%" valign="top">
					<h4 class="catbg"><span class="left"></span>
					<span class="align_left">', $txt['fm_viewfile'], ' ', !empty($context['file']) ? basename($context['file']) : '', '</span></h4>
						<div class="windowbg2" style="text-align: center;">
							<span class="topslice"><span></span></span>
							 ', $context['image_data']['size']['0'] > 700 ? '<a class="highslide" href="' . $context['image'] . '" onclick="return hs.expand(this)" title="' . basename($context['file']) . '">' : '', ' <img src="', $context['image'], '" alt="', basename($context['file']), '" style="max-width: 700px;" /></a><br />
							', $context['image_data']['size']['0'] > 700 ? '<i class="smalltext">' . $txt['fm_fullsize'] . '</i> <br />' : '', '
					<span class="botslice"><span></span></span>
					</div>
				</td>
			</tr>
		</table>';
	
}

// Renaming a file
function template_rename()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt;
	

	echo'
		<h4 class="catbg"><span class="left"></span>
			<span class="align_left">
				', $txt['fm_renamefile'], ' ', $context['filedata']['basename'], '
			</span>
		</h4>
		<div class="windowbg2 headerpadding" style="text-align: center;">
			<span class="topslice"><span></span></span>
				', $context['rename_message'], '<br/><br/>';
				
				if($context['show_form'])
				echo'
					<form action="', $scripturl, '?action=admin;area=filemanager;do=rename2;file=', $context['file'], '" method="post">
						<input type="text" name="filename" value="', $context['filedata']['basename'], '" /><br /><br />
						<input type="submit" value="', $txt['fm_savenewname'], '" />
					</form>';

		echo'
			<span class="botslice"><span></span></span>
		</div>';
}

// Chmoding a file
function template_chmod()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt;

	echo'
		<h4 class="catbg"><span class="left"></span>
			<span class="align_left">
				', $txt['fm_chmodfile'], ' ', $context['file_data']['data']['basename'], '
			</span>
		</h4>
		<div class="windowbg2 headerpadding" style="text-align: center;">
			<span class="topslice"><span></span></span>
				', $context['chmod_message'], '<br/><br/>';
				
				if($context['show_form'])
				echo'
					<form action="', $scripturl, '?action=admin;area=filemanager;do=chmod2;file=', $context['file'], '" method="post">
						<input type="text" name="value" value="', $context['file_data']['filepermissions'], '" /><br /><br />
						<input type="submit" value="', $txt['fm_savechmod'], '" />
					</form>';
		echo'
			<span class="botslice"><span></span></span>
		</div>';
}

// Searching
function template_search()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt;
	
	echo'
		<h4 class="catbg"><span class="left"></span>
			<span class="align_left">
				', $txt['fm_search'], '
			</span>
		</h4>
		<div class="windowbg2 headerpadding" style="text-align: center;">
			<span class="topslice"><span></span></span>
				', $context['search_message'], '<br/><br/>';
				
				if($context['showresults'])
				{
					if(empty($context['results']['directories']) && empty($context['results']['files']))
					echo'<i class="smalltext">', $txt['fm_search_noresults'], '</i>';
					else
					{
						foreach($context['results']['directories'] AS $result)
							echo $result['display'], '<br/>';
						foreach($context['results']['files'] AS $result)
							echo $result['display'], '<br/>';
					}
				}
				
				
			echo'
				<br/><br/>
				<a href="', $scripturl, '?action=admin;area=filemanager;do=browse;dir=', $context['directory'], '">', $txt['back'], '</a>	
			<span class="botslice"><span></span></span>
		</div>';
}

// Upload a file
function template_upload()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt, $boardurl;
	
	echo'
		<h4 class="catbg"><span class="left"></span>
			<span class="align_left">
				', $txt['fm_uploadfiletodir'], ': ', checkDirName($context['directory']), '
			</span>
		</h4>
			<div class="windowbg2 headerpadding" style="text-align: center;">
			<span class="topslice"><span></span></span>
				<p id="loading"><img src="', $settings['default_images_url'], '/filemanager/loading.gif" alt="', $txt['fm_uploading'], '" /><br/></p>
				<p id="result"></p>
					<form action="', $boardurl, '/process.php" method="post" enctype="multipart/form-data" target="UploadProcess" onsubmit="initializeUploadProcess();">
						', $txt['fm_filetoupload'], ': <input name="file" type="file" />
						<input type="hidden" name="dir" value="', $context['directory'], '"/>
						<input type="submit" value="', $txt['fm_upload'], '" />
						<input type="hidden" name="sc" value="', $context['session_id'], '" />
					</form>
					<iframe id="UploadProcess" name="UploadProcess" src="#" style="display: none;"></iframe>
					<br /><br />
					<a href="', $scripturl, '?action=admin;area=filemanager;do=browse;dir=' . urlEncode($context['directory']) . '">', $txt['back'], '</a>
			<span class="botslice"><span></span></span>
		</div>';
}

// Create a new directory
function template_createdir()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt;

	echo'
		<h4 class="catbg"><span class="left"></span>
			<span class="align_left">
				', $txt['fm_createdir'], ' ', checkdirname($context['directory']), '
			</span>
		</h4>
		<div class="windowbg2 headerpadding" style="text-align: center;">
			<span class="topslice"><span></span></span>
				', $context['create_message'], '<br/><br/>';
				
				if($context['show_form'])
				echo'
					<form action="', $scripturl, '?action=admin;area=filemanager;do=createdir2;dir=', $context['directory'], '" method="post">
						<input type="text" name="name" /><br /><br />
						<input type="submit" value="', $txt['fm_createdirnow'], '" />
					</form>';
		echo'
			<span class="botslice"><span></span></span>
		</div>';
}

// Create a new file
function template_createfile()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt;

	echo'
		<h4 class="catbg"><span class="left"></span>
			<span class="align_left">
				', $txt['fm_createfile'], ' ', checkdirname($context['directory']), '
			</span>
		</h4>
		<div class="windowbg2 headerpadding" style="text-align: center;">
			<span class="topslice"><span></span></span>
				', $context['create_message'], '<br/><br/>';
				
				if($context['show_form'])
				echo'
					<form action="', $scripturl, '?action=admin;area=filemanager;do=createfile2;dir=', $context['directory'], '" method="post">
						<input type="text" name="name" /><br /><br />
						<input type="submit" value="', $txt['fm_createfilenow'], '" />
					</form>';
		echo'
			<span class="botslice"><span></span></span>
		</div>';
}

// Play an audio file.
function template_audio()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt, $boarddir, $smf_important;
	
	echo'
		<table width="100%">
			<tr>
				<td width="20%" valign="top">
					<table class="table_grid" cellspacing="0">
						<thead>
							<tr>
								<th scope="col" class="smalltext first_th">', $txt['fm_audioinfo'], '</th>
								<th scope="col" class="smalltext">&nbsp;</th>
								<th scope="col" class="smalltext last_th">&nbsp;</th>
							</tr>
						</thead>
							<tr>
								<td class="windowbg headerpadding smalltext" style="text-align: left;" colspan="3">
									<strong>', $txt['fm_filename'], ':</strong> ', basename($context['file']), '<br/>											
																	
									', !empty($context['audio_data']['data']['extension']) ? '<strong>'.$txt['fm_filetype'].':</strong>  '.$context['audio_data']['data']['extension'].'<br />' : '','														
												
									<strong>', $txt['fm_filesize'], ':</strong>  ', $context['audio_data']['filesize'], '<br />
													
									<strong>', $txt['fm_filepermissions'], ':</strong>  ', $context['audio_data']['filepermissions'], '<br />
												
									', !empty($context['audio_data']['data']['dirname']) ? '<strong>'.$txt['fm_filedir'].':</strong>  '.str_replace($boarddir,$txt['fm_rootfolder'],$context['audio_data']['data']['dirname']).'<br />' : '', '
												
									<strong>', $txt['fm_lastedit'], ':</strong>  ', $context['audio_data']['lastmodified'], '<br />
												
									', in_array(basename($context['file']),$smf_important) ? '<i>'.$txt['fm_smfimportant'].'</i>' : '', '											
								</td>
							</tr>					
					</table>
					<br/>
					<table class="table_grid" cellspacing="0">
						<thead>
							<tr>
								<th scope="col" class="smalltext first_th" width="100%">', $txt['fm_fileactions'], '</th>
								<th scope="col" class="smalltext">&nbsp;</th>
								<th scope="col" class="smalltext last_th">&nbsp;</th>
							</tr>
						</thead>
							<tr>
								<td class="windowbg headerpadding smalltext" colspan="3">
									', !empty($context['audio_data']['view']) ? $context['audio_data']['view'] : '', ' 											
									', !empty($context['audio_data']['edit']) ? $context['audio_data']['edit'] : '', ' 												
									', !empty($context['audio_data']['remove']) ? $context['audio_data']['remove'] : '', ' 	
									', !empty($context['audio_data']['rename']) ? $context['audio_data']['rename'] : '', '	
									', !empty($context['audio_data']['chmod']) ? $context['audio_data']['chmod'] : '', '
									', !empty($context['audio_data']['download']) ? $context['audio_data']['download'] : '', '																								
								</td>
							</tr>					
					</table>	
					<br/>								
					<h3 class="catbg"><span class="left"></span><a href="', $scripturl, '?action=admin;area=filemanager;do=browse;dir=', !empty($context['audio_data']['data']['dirname']) ? urlEncode($context['audio_data']['data']['dirname']) : '', '">', $txt['back'], '</a></h4>
										
				</td>
					<td width="80%" valign="top">
					<h4 class="catbg"><span class="left"></span>
					<span class="align_left">', $txt['fm_viewfile'], ' ', !empty($context['file']) ? basename($context['file']) : '', '</span></h4>
						<div class="windowbg2" style="text-align: center;">
							<span class="topslice"><span></span></span>
								<embed src="', $context['audio'], '" />
							<span class="botslice"><span></span></span>
						</div>
					</td>
				</tr>
			</table>';
	
}

?>