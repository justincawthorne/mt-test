<?php

// page title - if undefined the site title is displayed by default

	$page_title = 'Attachments';

	$url = current_url();

// insert a new attachment

// edit an attachment

// replace an attachment

// delete an attachment
	

// process our params and get main content

	if(isset($_GET['attachment_id'])) {
		
		// a single attachment
		
		$attachment_id = (int)$_GET['attachment_id'];
		$attachment = get_attachment_full($attachment_id);
		
		// file usage
		
		$usage = attachment_usage($attachment_id);

		// default to list if requested attachment not found

		if(empty($attachment)) {
			$attachments = get_attachments();	
		}

	} elseif(isset($_GET['filename'])) {
		
		// this is a rogue file
		$rogue_path = WW_ROOT.'/ww_files/attachments/'.$_GET['ext'].'/'.$_GET['filename'];
		if(file_exists($rogue_path)) {
			$attachment = array();
			$attachment['title'] 	= $_GET['filename'];
			$attachment['filename'] = $_GET['filename'];
			$attachment['ext'] 		= $_GET['ext'];
			$attachment['size'] 	= filesize($rogue_path);
			$attachment['date_uploaded'] = filemtime($rogue_path);
		}
		
	} 
	
	// if no single attachment is found/requested we return a list
	
	if( (!isset($attachment)) || (empty($attachment)) ) {
		
		$attachments = get_attachments();
		
		// check for rogues and orphans
		
		if(isset($_GET['ext'])) {
			$orphans = get_attachment_orphans($_GET['ext']);
		} else {
			$rogue = get_files(WW_ROOT.'/ww_files/attachments/');
			if(!empty($rogue)) {
				foreach($rogue as $name){
					$orphans['files'][] = $name['filename'];
				}
			}
		}
	}
	
	
	
	
	// any content generation
	
	$attachment_folders = get_folders(WW_ROOT."/ww_files/attachments");
	// files link
	$left_text = '<a href="'.$_SERVER["PHP_SELF"].'?page_name=files">files</a>: ';
	// attachments link
	$left_text .= '<a href="'.$_SERVER["PHP_SELF"].'?page_name=attachments">attachments</a>';
	// extension / optional link
	if(isset($attachments)) {
		$left_text .= (isset($_GET['ext'])) ? ': '.$_GET['ext'] : '' ;
	} else {
		$left_text .= ': <a href="'.$_SERVER["PHP_SELF"].'?page_name=attachments&amp;ext='.$attachment['ext'].'">'.$attachment['ext'].'</a>';
	}
	$right_text = (isset($attachment)) ? $attachment['filename'] : 'listing' ;
	$page_header = show_page_header($left_text, $right_text);


// get aside content

	// any functions
	
	// any content generation


// output main content - into $main_content variable

	$main_content = $page_header;
	
	if(isset($orphans)) {
		if(isset($orphans['files'])) {
			$current_folder = (isset($_GET['ext'])) ? '/ww_files/attachments/'.$_GET['ext'] : '/ww_files/attachments/' ;
			$link_path = $_SERVER["PHP_SELF"].'?page_name=attachments&amp;ext='.$_GET['ext'].'&amp;filename=';
			$main_content .= 
			'<h4>rogue files</h4>
			<p>The following rogue files were found in the <em>'.$current_folder.'</em> folder (you can either destroy these without mercy or add them to the database - click on the filename to take action):
				<ul>';
			foreach($orphans['files'] as $orphan) {
				$main_content .= '
				<li>
					<a href="'.$_SERVER["PHP_SELF"].'?page_name=attachments&amp;ext='.$_GET['ext'].'&amp;filename='.$orphan.'">
						'.$orphan.'
					</a>
				</li>';
			}
			$main_content .= '
			</ul>
			';
		}
		if(isset($orphans['db'])) {
			$main_content .= 
			'<h4>orphaned database entries</h4>
			<p>There are orphaned entries in the database (too sad!). They are highlighted below - you should either delete these pitiful entries or re-upload the files.</p>';
		}
	}
	
	if(isset($attachments)) {
		$main_content .= build_file_listing($attachments);
	} else {
		
		// main attachment details
		
		$mime_edit = (!empty($attachment['mime'])) ? ' readonly="readonly"' : '' ;
		$main_content .= '
			<h4>file details</h4>
			
			<p>This file was uploaded by '.$attachment['author_name'].' on '.from_mysql_date($attachment['date_uploaded']).' &#124; 
			<a href="'.$attachment['link'].'">download</a></p>
			<form id="attachment_details" method="post" action="'.$url.'">
				<p>
					<label for="title">Title</label>
					<input type="text" name="title" value="'.$attachment['title'].'"/>
				</p>
				<p>
					<label for="summary">Description</label>
					<textarea name="summary">'.$attachment['summary'].'</textarea>
				</p>
				<p>
					<label for="filename">Filename</label>
					<input type="text" name="filename" value="'.$attachment['filename'].'" readonly="readonly"/>
				</p>
				<p>
					<label for="mime">Mime type</label>
					<input type="text" name="mime" value="'.$attachment['mime'].'"'.$mime_edit.'/>
				</p>
				<p>
					<label for="size">Size (bytes)</label>
					<input type="text" name="size" value="'.$attachment['size'].'" readonly="readonly"/>
				</p>
				<p>
					<label for="downloads">Downloads</label>
					<input type="text" name="downloads" value="'.$attachment['downloads'].'" readonly="readonly"/>
				</p>
				<p>
					<span class="note">
					<input type="submit" name="update" value="update details" />
					</span>
				</p>
			</form>';
			
		// attachment usage
		
		$main_content .= '	
			<hr />
			<h4>usage</h4>';
			$usage_total = count($usage);
			$article_link = $_SERVER["PHP_SELF"].'?page_name=write&amp;article_id=';
			switch($usage_total) {
				
				case 0;
				$main_content .= '
				<p>This file isn\'t currently attached to any articles</p>';
				break;
				
				case 1;
				$main_content .= '
				<p>This file is attached to one article: <a href="'.$article_link.$usage[0]['article_id'].'">'.$usage[0]['title'].'</a></p>';
				break;
				
				default;
				$main_content .= '
				<p>This file is attached to '.$usage_total.' articles:</p>
					<ul>';
					foreach($usage as $used) {
						$main_content .= '
						<li><a href="'.$article_link.$used['article_id'].'">'.$used['title'].'</a></li>';
					}
				$main_content .= '
					</ul>';
				break;
			}

			
		$main_content .= '
			<h4>replace file</h4>';
			
		$main_content .= '
			<h4>delete file</h4>';
	}
	
	// if file selected then show:
	
		// file details
		
		// file usages (i.e. which articles is it attached to)
		
		// replace file
		
		// delete file

// output aside content - into $aside_content variable

	$aside_content = '
			<h4>Quick links</h4>
			<ul>
				<li><a href="'.$_SERVER["PHP_SELF"].'?page_name=images">Images</a></li>
				<li><a href="'.$_SERVER["PHP_SELF"].'?page_name=attachments">Attachments</a></li>
			</ul>';

	// attachment subfolders
	
	if(!empty($attachment_folders)) {
		
		$aside_content .= '
		<div class="snippet">
		<h6>Attachment folders</h6>
		<ul>';
		
		foreach($attachment_folders as $folder) {
			$aside_content .= '
			<li>
				<a href="'.$_SERVER["PHP_SELF"].'?page_name=attachments&amp;ext='.$folder.'">'.$folder.'</a>
			</li>';
		}
		
		$aside_content .= '
		</ul>
		</div>';
	}	
	
	// upload an attachment
	
	$aside_content .= '
		<div class="snippet">
		<h6>Upload new attachment</h6>';
	
	if(isset($upload_success)) {
		$aside_content .= '<p>'.$upload_success.'</p>';
	}
	
	$aside_content .= '
		<form action="'.$url.'" method="post" enctype="multipart/form-data" id="attachment_form">
		
			<p><label for="attachment_file">Select file</label>
				<input name="attachment_file" type="file" size="12" id="attachment_file"/></p>
			
			<p><label for="title">Image title (optional)</label> 
				<input name="title" type="text" name="title" size="24"/></p>
			
			<p><label for="summary">Description (optional)</label> 
				<textarea name="summary"></textarea></p>
			<p>			
				<input type="submit" name="upload_image" value="upload" /></p>
		</form>
	</div>';
?>