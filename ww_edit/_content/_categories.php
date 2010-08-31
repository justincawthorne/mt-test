<?php

// page title - if undefined the site title is displayed by default

	$page_title = 'Categories';

// process post actions
	
	// insert category

	if( (isset($_POST['add_category'])) && ($_POST['add_category'] == 'add') ) {

		$insert_status = insert_category();
		if(is_int($insert_status)) {
			$reload = $_SERVER["PHP_SELF"].'?page_name=categories&category_id='.$new_category_id;
			header('Location: '.$reload);			
		} else {
			$insert_error = $insert_status;
		}
		
	}
	
	// edit category

	if( (isset($_POST['edit_category'])) && ($_POST['edit_category'] == 'edit') ) {

		$category_id = (int)$_GET['category_id'];
		$update_status = update_category($category_id);
		if($update_status === true) {
			header('Location: '.$url);
		} else {
			$error = $update_status;
		}
		
	}

	// delete category
	
	if( (isset($_POST['confirm_delete_category'])) && ($_POST['confirm_delete_category'] == 'Yes') ) {
		
		$category_id = (int)$_GET['category_id'];
		$delete_status = delete_category($category_id);
		if($delete_status === true) {
			$reload = $_SERVER["PHP_SELF"].'?page_name=categories';
			header('Location: '.$reload);
		} else {
			$error = $delete_status;
		}
	}
	
	// category list
	
	$categories_list = get_categories_admin();

// get main content

	$category_id = (isset($_GET['category_id'])) ? (int)$_GET['category_id'] : 0 ;

	// parent category list for select box

	$parent_select = array();
	foreach($categories_list as $parent) {	
		if(!empty($parent['category_id'])) {
			continue;
		} else {
			$parent_select[$parent['id']] = $parent['title'];
		}
	}





	// show category details if requested
	
	if(!empty($category_id)) {

	// confirm file delete
		
		if( (isset($_GET['action'])) && ($_GET['action'] == 'delete') ) {

			$main_content .= '
				<h2>Are you sure you want to delete this category?</h2>
				<form action="'.$action_url.'" method="post" name="confirm_delete_category_form">
					<input name="confirm_delete_category" type="submit" value="Yes"/>
					<input name="cancel_delete_category" type="submit" value="No" />
				</form>
				<hr />
				';
			
		}


		
		// get category details
		
		$category = get_category_details($category_id);
		$usage = (empty($category['category_id'])) 
			? $categories_list[$category_id]['total'] 
			: $categories_list[$category['category_id']]['child'][$category_id]['total'] ;
		
		// use category details for page header
		
		$left_text = $category['title'];
		$right_text = $category['url'];
	
		// build edit form
		
		$page_content = '
				<h2>Edit category</h2>';
		
		if(!empty($usage)) {
			$page_content .= '		
				<p><a href="'.$_SERVER["PHP_SELF"].'?page_name=articles&amp;category_id='.$category_id.'">
					Check which articles use this category</a>
				</p>';
		} else {
			$page_content .= '
				<p>This category is not currently used by any articles.</p>';
		}
		
		$page_content .= '		
				<form action="'.$_SERVER["PHP_SELF"].'?page_name=categories&amp;category_id='.$category_id.'" 
					method="post" 
					id="edit_category_form">
				<p>
					<label for="title">Title</label>
					<input name="title" id="title" type="text" value="'.$category['title'].'"/>
				</p>
				<p>
					<label for="url">Url (read only)</label>
					<input name="url" id="url" type="text" value="'.$category['url'].'" readonly="readonly"/>
					<span class="note">
						<input name="update_url" id="update_url" value="1" type="checkbox"/>&nbsp;tick here if you want the url-friendly version of the category title updated<br />(note that this may change the permanent url for your article)
					</span>

				</p>
				<p>
					<label for="category_id">Parent category</label>
					<select name="category_id" id="category_id">
						<option value="">(no parent)</option>';
						foreach($parent_select as $option_id => $option) {
							if($option_id ==  $category_id) {
								continue;
							}
							$selected = ($option_id == $category['category_id']) ? ' selected="selected"' : '';
							$page_content .= '
								<option value="'.$option_id.'"'.$selected.'>'.$option.'</option>
							';
						}
		$page_content .= '
					</select>
				</p>
				<p>
					<label for="summary">Summary (optional)</label>
					<textarea name="summary" id="summary" cols="21" rows="3">'.$category['summary'].'</textarea>
					<span class="note">brief optional summary to show on listings page and at the head of the rss feed for this category</span>
				</p>
				<p>
					<label for="description">Description (optional)</label>
					<textarea name="description" id="description" cols="21" rows="3">'.$category['description'].'</textarea>
					<span class="note">longer description only used for podcast/rss feeds</span>
				</p>
				<p>
					<label for="type">Type</label>
					<input name="type" id="type" type="text" value="'.$category['type'].'"/>
					<span class="note">iTunes category for podcasts</span>
				</p>
				<p>
					<input name="edit_category" value="edit" type="submit"/>
				</p>
				</form';
				
	// delete category form	
	
		if(!empty($usage)) {
			
			$main_content .= '
				<hr />
				<h4>delete category</h4>
				
				<form action="'.$action_url.'" method="get" id="delete_category_form">
					<p>
						<span class="note">
						<input type="hidden" name="page_name" value="categories"/>
						<input type="hidden" name="category_id" value="'.$category_id.'"/>
						<input name="action" type="submit" value="delete"/>
						</span>
					</p>
				</form>
				';
			
			}		
				
		} else {
		
			$left_text = 'Categories';
			$right_text = '';
			
			$page_content = '<p>No category selected: choose a category to edit from the right hand menu.</p>';
			
		}


// get aside content
	
	// category edit form

	$add_category_form = ( (isset($insert_error)) && (!empty($insert_error)) ) ? '<p><strong>'.$insert_error.'</strong></p>' : '';

	$add_category_form .= '
				<form action="'.$_SERVER["PHP_SELF"].'?page_name=categories" method="post" id="add_category_form">
				<p>
					<label for="title">Category title</label>
					<input name="title" type="text" size="20"/>
				</p>
				<p>
					<label for="category_id">Parent category</label>
					<select name="category_id">
						<option value="">(no parent)</option>';
				foreach($parent_select as $option_id => $option) {
					$add_category_form .= '
						<option value="'.$option_id.'">'.$option.'</option>
					';
				}
	$add_category_form .= '
					</select>
				</p>
				<p>
					<label for="summary">Category summary (optional)</label>
					<textarea name="summary" cols="21" rows="3"></textarea>
				</p>
				<p>
					<input name="add_category" value="add" type="submit"/>
				</p>
				</form>
	';


// output main content - into $main_content variable

	$main_content = show_page_header($left_text, $right_text);;
	$main_content .= $page_content;


// output aside content - into $aside_content variable

	$aside_content = build_snippet('Categories',$categories_list);
	$aside_content .= build_snippet('Add a Category',$add_category_form);
?>