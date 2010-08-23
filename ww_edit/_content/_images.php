<?php

// page title - if undefined the site title is displayed by default

	$page_title = 'Images';

	$url = current_url();
	$action_url = htmlspecialchars($url);

	// upload image
	
	if( (isset($_POST['upload_image'])) && ($_POST['upload_image'] == 'upload') ) {
		
		$upload_success = insert_image();
		if(is_int($upload_success)) {
			// redirect to new image page
		}
		
	}
	
	// replace thumbnail

	if(isset($_POST['replace_thumb'])) {
		
		if( (isset($_FILES['new_thumb'])) && (empty($_FILES['new_thumb']['error'])) ) {
			$current = $_POST['current_thumb'];
			$new = $_FILES['new_thumb'];
			$thumb_error = replace_file($current,$new);
			if(is_array($thumb_error)) {
				header('Location: '.$url);
			}
		}
		
	}

	// replace thumbnail

	if(isset($_POST['replace_image'])) {
		
		if( (isset($_FILES['new_image'])) && (empty($_FILES['new_image']['error'])) ) {
			$current = $_POST['current_image'];
			$current_w = (int)$_POST['current_w'];
			$new = $_FILES['new_image'];
			$image_error = replace_image($current, $new, $current_w);
			if(is_array($image_error)) {
				header('Location: '.current_url());
			}
		}
		
	}
	
	// delete image
	
	if(isset($_POST['confirm_delete'])) {
		
		$image_delete = delete_image($_POST['filename']);
		if(!empty($image_delete)) {
			header('Location: '.WW_WEB_ROOT.'/ww_edit/index.php?page_name=images');
		}
	}
	

	
// get main content
	
	// any functions
	

	
	// any content generation
	
	// get image details / list of images
	
	$image_id = (isset($_GET['image_id'])) ? (int)$_GET['image_id'] : 0 ;
	
	if(!empty($image_id)) {
		
		$image = get_image($_GET['image_id']);
		
		// file usage
		
		$usage = file_usage($image['filename']);
		
	} else {
		
		$images = get_images(16);
				
	}
	
	$left_text = '<a href="'.$_SERVER["PHP_SELF"].'?page_name=files">files</a>';
	$left_text .= (isset($image)) ? ': <a href="'.$_SERVER["PHP_SELF"].'?page_name=images">images</a>' : ': images' ;
	$right_text = (isset($image)) ? $image['filename'] : 'listing' ;
	$page_header = show_page_header($left_text, $right_text);


// output main content

	$main_content = $page_header;

	// if a single image is selected then show it
	
	if( (isset($image)) && (!empty($image)) ) {
		
		// confirm file delete
		
		if( (isset($_POST['action'])) && ($_POST['action'] == 'delete') ) {

			$main_content .= '
				<h2>Are you sure you want to delete this image?</h2>
				<form action="'.$url.'" method="post" name="confirm_delete_image_form">
					<input name="filename" type="hidden" value="'.$image['filename'].'"/>
					<input name="confirm_delete_image" type="submit" value="Yes"/>
					<input name="cancel_delete_image" type="button" value="No" onclick="javascript:history.back();"/>
				</form>
				';
			
		}
		
		// jump options:
		
		$main_content .= '
		<p><strong> Jump to:</strong>
			<a href="#preview">preview image</a> &#124;
			<a href="#replace_thumbnail">replace thumbnail image</a> &#124;
			<a href="#replace_image">replace image</a> &#124;
			<a href="#delete_image">delete image</a>
		</p>';
		
		// show image details
		$preview_style = ($image['width'] < 280) 
			? 'width:'.$image['width'].'px; height:auto; position: absolute; top:140px; right:240px;'
			: 'width:680px; height:auto; overflow: auto;' ;
		
		$mime_edit = (!empty($image['mime'])) ? ' readonly="readonly"' : '' ;
		$ext_edit = (!empty($image['ext'])) ? ' readonly="readonly"' : '' ;
		$main_content .= '
			<hr />
			<h4>image details</h4>

			<p>This image was uploaded by '.$image['author_name'].' on '.from_mysql_date($image['date_uploaded']).'</p>
			<p>Form items with dotted borders are non-editable</p>
			<form id="image_details" method="post" action="'.$action_url.'">

				<p>
					<label for="title">Title</label>
					<input type="text" name="title" id="title" value="'.$image['title'].'"/>
				</p>
				<p>
					<label for="filename">Filename</label>
					<input type="text" name="filename" id="filename" value="'.$image['filename'].'" readonly="readonly"/>
				</p>
				<p>
					<label for="alt">Alt text</label>
					<input type="text" name="alt" id="alt" value="'.$image['alt'].'"/>
				</p>
				<p>
					<label for="caption">Caption</label>
					<textarea name="caption" id="caption" rows="3" cols="18">'.$image['caption'].'</textarea>
				</p>
				<p>
					<label for="credit">Credit</label>
					<input type="text" name="credit" id="credit" value="'.$image['credit'].'"/>
				</p>
				<p>
					<label for="ext">Extension</label>
					<input type="text" name="ext" id="ext" value="'.$image['ext'].'"'.$ext_edit.'/>
				</p>
				<p>
					<label for="mime">Mime type</label>
					<input type="text" name="mime" id="mime" value="'.$image['mime'].'"'.$mime_edit.'/>
				</p>
				<p>
					<label for="size">Size (bytes)</label>
					<input type="text" name="size" id="size" value="'.$image['size'].'" readonly="readonly"/>
				</p>
				<p>
					<label for="width">Width (pixels)</label>
					<input type="text" name="width" id="width" value="'.$image['width'].'" readonly="readonly"/>
				</p>
				<p>
					<label for="height">Height (pixels)</label>
					<input type="text" name="height" id="height" value="'.$image['height'].'" readonly="readonly"/>
				</p>
				<p>
					<span class="note">
					<input type="submit" name="update" value="update details" />
					</span>
				</p>
			</form>
			
			<div class="image_preview" style="'.$preview_style.'">
				<a id="preview"></a>
				<img style="float: right;" alt="'.$image['alt'].'" title="'.$image['title'].'" src="'.$image['thumb_src'].'"/>
				<img style="float: left;" alt="'.$image['alt'].'" title="'.$image['title'].'" src="'.$image['src'].'"/>
			</div>
			';		
		
		$main_content .= '	
			<hr />
			<h4><a id="usage"></a>usage</h4>';
			
			if(empty($usage)) {
				$main_content .= '
				<p>This image isn\'t currently used in any articles.</p>';
			}
		
		$main_content .= '
			<hr />
			
			<h4><a id="replace_image"></a>replace image</h4>
			
			<form action="'.$action_url.'" method="post" enctype="multipart/form-data" id="image_replace_form">
				<p>
					<label for="new_image">Image:</label>
					<input name="new_image" id="new_image" type="file" size="12" id="new_image"/>
				</p>
				<p>
					<label for="image_width">Image width:</label>
					<input name="image_width" id="image_width" type="text" style="width:40px;" value="'.$image['width'].'"/> px
				</p>
				<p>
					<span class="note">
					<input name="current_image" type="hidden" value="'.$image['filename'].'"/>
					<input type="submit" name="replace_image" value="replace" />
					</span>
				</p>
			</form>	
			
			<hr />
				
			<h4><a id="replace_thumbnail"></a>replace thumbnail</h4>
			
			<form action="'.$action_url.'" method="post" enctype="multipart/form-data" id="thumb_replace_form">
				<p>
					<label for="new_thumb">Thumbnail:</label>
					<input name="new_thumb" id="new_thumb" type="file" id="new_thumb"/>
				</p>
				<p>
					<label for="thumb_width">Thumb width:</label>
					<input name="thumb_width" id="thumb_width" type="text" style="width:40px;" value="'.$config['files']['thumb_width'].'"/> px
				</p>
				<p>
					<span class="note">
					<input name="current_thumb" type="hidden" value="'.$image['filename'].'"/>
					<input type="submit" name="replace_thumb" value="replace" />
					</span>
				</p>
			</form>

			';
			
		$main_content .= '
			<hr />
			<h4><a id="delete_image"></a>delete image</h4>';
		
		if(!empty($usage)) {
			
			$main_content .= '
			<p>WARNING: you should delete the image from the articles that are currently using it prior to deletion.</p>';
		}
		
		$main_content .= '
			<form action="'.$action_url.'" method="post" id="delete_image_form">
				<p>
					<span class="note">
					<input name="action" type="submit" value="delete"/>
					</span>
				</p>
			</form>
			';
			
	} else {
		
		// also need to check for rogues and orphans

		// if no file selected then show all images in a grid
	
		foreach($images as $img) {
			
			$main_content .= '
			<div class="gallery_item">
				<div class="thumb">
					<a href="'.$url.'&amp;image_id='.$img['id'].'">
					<img src="http://dev.wickedwords.org/ww_files/images/thumbs/'.$img['filename'].'"/>
					</a>
				</div>
				<div class="caption">
					<span class="img_title">'.$img['title'].'</span>
					<span class="img_size">'.$img['width'].' x '.$img['height'].'</span>
				</div>
			</div>';
		}
		
	}


// output aside content - into $aside_content variable

	$aside_content = '<h4>Quick links</h4>';
	$aside_content .= '<p><a href="'.$_SERVER["PHP_SELF"].'?page_name=images">Images</a></p>';
	$aside_content .= '<p><a href="'.$_SERVER["PHP_SELF"].'?page_name=attachments">Attachments</a></p>';
	$aside_content .= '<p><a href="'.$_SERVER["PHP_SELF"].'?page_name=files">Files</a></p>';

	// upload an image	
	
	$aside_content .= '
	<div class="snippet">
		<h6>Upload new image</h6>';
	
	if(isset($upload_success)) {
		$aside_content .= '<p>'.$upload_success.'</p>';
	}
	
	$aside_content .= '
	
		<form action="'.$action_url.'" method="post" enctype="multipart/form-data" id="upload_image_form">
		
		<p><label for="image_file">Select image</label>
			<input name="image_file" type="file" size="12" id="image_file"/></p>
			
		<p><label for="thumb_file">Thumbnail (optional)</label>
			<input name="thumb_file" type="file" size="12" id="thumb_file"/></p>
		
		<p><label for="title">Image title (optional)</label> 
			<input name="title" type="text" size="24"/></p>
		
		<p><label for="alt">Alt text (optional)</label> 
			<input name="alt" type="text" size="24"/></p>
			
		<p><label for="caption">Caption (optional)</label> 
			<input name="caption" type="text" size="24"/></p>
			
		<p><label for="credit">Credit (optional)</label> 
			<input name="credit" type="text" size="24"/></p>
			
		<p><label for="width">New width (px) (optional)</label> 
			<input name="width" type="text" size="3"/>
		</p>
		
		<p><input type="submit" name="upload_image" value="upload" /></p>
	</form>
	</div>';
	


?>