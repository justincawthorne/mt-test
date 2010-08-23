<?php

// page title - if undefined the site title is displayed by default

	$page_title = 'Tags';

	$tag_id = (isset($_GET['tag_id'])) ? (int)$_GET['tag_id'] : 0 ;

// get main content

	if(isset($_POST['add_tag'])) {
		
		$tag_id = insert_tag();
	}	
		
	// any functions
	
	if(!empty($tag_id)) {
		
		$tag = get_tag_details($tag_id);
		
		$left_text = $tag['title'];
		$right_text = $tag['url'];
	
	// edit tag
		
		if(isset($_POST['edit_tag'])) {
			update_tag($tag_id);
			$reload = $_SERVER["PHP_SELF"].'?page_name=tags&tag_id='.$tag_id;
			header('Location: '.$reload);
		}
	
	// delete tag
		
		if(isset($_POST['delete_tag'])) {
			
			delete_tag($tag_id);
			$reload = $_SERVER["PHP_SELF"].'?page_name=tags';
			header('Location: '.$reload);
		}
		
	// show form
		
		$page_content = '
				<h2>Edit tag</h2>
				
				<p><a href="'.$_SERVER["PHP_SELF"].'?page_name=articles&amp;tag_id='.$tag_id.'">
					Check which articles use this tag</a>
				</p>
				
				<form action="'.$_SERVER["PHP_SELF"].'?page_name=tags&amp;tag_id='.$tag_id.'" method="post" id="edit_tag_form">
				<p>
					<label for="title">Title</label>
					<input name="title" type="text" value="'.$tag['title'].'"/>
				</p>
				<p>
					<label for="url">Url (read only)</label>
					<input name="url" type="text" value="'.$tag['url'].'" readonly/>
					<span class="note">this will be updated automatically if you change the tag title</span>
				</p>
				<p>
					<label for="summary">Summary (optional)</label>
					<textarea name="summary" cols="21" rows="3">'.$tag['summary'].'</textarea>
				</p>
				<p>
					<input name="edit_tag" value="Edit tag" type="submit">
				</p>
				<p>or</p>
				<p>
					<input name="delete_tag" value="Delete tag" class="delete_button" type="submit">
				</p>
				</form>
		';
					
	} else {
	
		$left_text = 'Tags';
		$right_text = '';
		
		$page_content = '<p>No tag selected: choose a tag to edit from the right hand menu.</p>';
		
	}
	
	// any content generation
	
	$page_header = show_page_header($left_text, $right_text);
	



// get aside content

	// any functions
	

	
	// any content generation
	
	$tags_list 		= get_tags_admin();
	
	$add_tag_form = '
				<form action="'.$_SERVER["PHP_SELF"].'?page_name=tags" method="post" id="add_tag_form">
				<p>
					<label for="title">Tag title</label>
					<input name="title" type="text" size="20"/>
				</p>
				<p>
					<label for="summary">Tag summary (optional)</label>
					<textarea name="summary" cols="21" rows="3"></textarea>
				</p>
				<p>
					<input name="add_tag" value="Add tag" type="submit">
				</p>
				</form>
	';


// output main content - into $main_content variable

	$main_content = $page_header;
	$main_content .= $page_content;


// output aside content - into $aside_content variable

	$aside_content = build_snippet('Tags',$tags_list);
	$aside_content .= build_snippet('Add a Tag',$add_tag_form);
?>