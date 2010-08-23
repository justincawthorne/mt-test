<?php

// page title - if undefined the site title is displayed by default

	$page_title = 'Files';


// get main content
	
	// any functions
	
	// any content generation
	
	$left_text = '<a href="'.$_SERVER["PHP_SELF"].'?page_name=files">files</a>: folder';
	$right_text = 'filename';
	$page_header = show_page_header($left_text, $right_text);


// get aside content

	// any functions
	
	$attachment_folders = get_folders(WW_ROOT."/ww_files/attachments/");
	$file_folders = get_folders(WW_ROOT."/ww_files/");
	$exclude_folders = array('_cache','attachments','images');
	$ex_id = '';
	foreach($exclude_folders as $ef) {
		$ex_id = array_search($ef,$file_folders);
		unset($file_folders[$ex_id]);
	}
	//sort($att_folders);
	
	// any content generation


// output main content - into $main_content variable

	$main_content = $page_header;
	
	if(!isset($_GET['folder'])) {
		
	// if no folder selected then summarise all folders
		
		$main_content .= '
		
		<h4>images</h4>
		
			<p><a href="'.$_SERVER["PHP_SELF"].'?page_name=images">Manage your images</a></p>
		
		<hr />
		<h4>attachment folders</h4>';
		if(!empty($attachment_folders)) {
			$main_content .= '<ul>';
			foreach($attachment_folders as $folder) {
				$main_content .= '
				<li>
					<a href="'.$_SERVER["PHP_SELF"].'?page_name=attachments&amp;ext='.$folder.'">'.$folder.'</a>
				</li>';
			}
			$main_content .= '</ul>';
		} else {
			$main_content .= '<p>No attachments have been uploaded</p>';
		}
		
		$main_content .= '
		<hr />
		<h4>custom folders</h4>';
		if(!empty($file_folders)) {
			$main_content .= '<ul>';
			foreach($file_folders as $folder) {
				$main_content .= '
				<li>
					<a href="'.$_SERVER["PHP_SELF"].'?page_name=files&amp;folder='.$folder.'">'.$folder.'</a>
				</li>';
			}
			$main_content .= '</ul>';
		} else {
			$main_content .= '<p>No custom folders have been created</p>';
		}
		
	} else {
		
		if(!isset($_GET['filename'])) {
			
			// if folder selected then show all files within
			
		} else {
			
		// if file selected then show:
		
			// files details
			
			// replace file
			
			// delete file			
			
		}
		
	}
	
	
	
	
	



// output aside content - into $aside_content variable

	$aside_content = '
			<h4>Quick links</h4>
			<ul>
				<li><a href="'.$_SERVER["PHP_SELF"].'?page_name=images">Images</a></li>
				<li><a href="'.$_SERVER["PHP_SELF"].'?page_name=attachments">Attachments</a></li>
			</ul>';
	
	// show other folders
			
	if(!empty($file_folders)) {
		
		$aside_content .= '
		<div class="snippet">
		<h6>Custom folders</h6>
		<ul>';
		
		foreach($file_folders as $folder) {
			$aside_content .= '
			<li>
				<a href="'.$_SERVER["PHP_SELF"].'?page_name=files&amp;folder='.$folder.'">'.$folder.'</a>
			</li>';
		}
		
		$aside_content .= '
		</ul>
		</div>';
	}
	
	// upload a file to selected folder
	
	// create a new folder

?>