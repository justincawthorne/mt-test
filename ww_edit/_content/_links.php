<?php

// page title - if undefined the site title is displayed by default

	$page_title = 'links';

	function build_links_listing($links) {
		if(empty($links)) {
			return;
		}
		$category = '';
		$html = '
		<ul class="links_listing">';
		foreach($links as $link) {
			if($link['category'] != $category) {
			$html .= '
				<li>
					<div class="link_category">
						'.$link['category'].'
					</div>
				</li>';
			}
			$html .= '
				<li>
					<div class="link_title">
						<a href="'.$_SERVER["PHP_SELF"].'?page_name=links&amp;link_id='.$link['id'].'&amp;action=edit">
						'.$link['title'].'</a>
					</div>
					
					<div class="link_url">
						<a href="'.$link['url'].'">
						'.$link['url'].'</a>
					</div>
					
					<div class="link_summary">
						'.$link['summary'].'
					</div>';
			if(!empty($link['attributes'])) {
				$html .= '	
					<div class="link_attributes">
						'.$link['attributes'].'
					</div>';
			}
				$html .= '	
					<div class="link_delete">
						<a href="'.$_SERVER["PHP_SELF"].'?page_name=files&amp;action=delete&amp;link_id='.$link['id'].'">
						delete</a>
					</div>
					
				</li>
			';
			$category = $link['category'];
		}
		$html .= '</ul>';
		return $html;
	}

// process post actions
	
	// insert a link

	if( (isset($_POST['insert_link'])) && ($_POST['insert_link'] == 'add') ) {

		$insert_status = insert_link();
		if(is_int($insert_status)) {
			header('Location: '.$_SERVER["PHP_SELF"].'?page_name=links&link_id='.$insert_status);
		} else {
			$error = $insert_status;
		}
		
	}
	
	// update a link
	
	if( (isset($_POST['update_link'])) && ($_POST['update_link'] == 'update') ) {

		$link_id = (int)$_GET['link_id'];
		$update_status = update_link($link_id);
		if($update_status === true) {
			header('Location: '.$_SERVER["PHP_SELF"].'?page_name=links&link_id='.$link_id);
		} else {
			$error = $update_status;
		}
		
	}
	
	// delete a link

	if( (isset($_POST['confirm_delete_link'])) && ($_POST['confirm_delete_link'] == 'Yes') ) {
		
		$link_id = (int)$_GET['link_id'];
		$delete_status = delete_link($link_id);
		if($delete_status === true) {
			header('Location: '.$_SERVER["PHP_SELF"].'?page_name=links');
		} else {
			$error = $delete_status;
		}
				
	}

	if( (isset($_POST['delete_link'])) && ($_POST['delete_link'] == 'delete') ) {
		
	}

// get main content
	
	// get all links always
	
	$links = get_links();
	$menu_links = (isset($links['site_menu'])) ? $links['site_menu'] : '' ;
	$rss_links = (isset($links['site_rss'])) ? $links['site_rss'] : '' ;
	$head_links = (isset($links['site_head'])) ? $links['site_head'] : '' ;
	
	// for convenience we'll create another array for all the non-default links categories
	
	$strip_links = $links;
	unset($strip_links['site_menu']);
	unset($strip_links['site_rss']);
	unset($strip_links['site_head']);
	foreach($strip_links as $sl_cat => $sl) {
		$other_links = $sl;
	}
	
	// is a link id sent?
	
	$link_id = (isset($_GET['link_id'])) ? (int)$_GET['link_id'] : 0 ;
	
	$link =  (!empty($link_id)) ? get_link($link_id) : '' ;
	
	$left_text = 'Links';
	$right_text = 'right_header';
	$page_header = show_page_header($left_text, $right_text);


// output main content - into $main_content variable

	$main_content = $page_header;
	
	$main_content .= '
		<p>Add links to your menu, additional rss feeds, &lt;link&gt; tags for the head section (not yet implemented) and other links for a blogroll.</p>';

	// show errors
	
		if( (isset($error)) && (!empty($error)) ) {
			$main_content .= '
			<p><strong>'.$error.'</strong></p>';
		}
	
	// start tabs
		
	$main_content .= '
			<div id="links_tabs">
			<ul>
				<li><a href="#tab_menu">menu</a></li>
				<li><a href="#tab_rss">rss</a></li>
				<li><a href="#tab_head">head</a></li>
				<li><a href="#tab_other">other</a></li>
			</ul>';
	
	// menu links
	
	$main_content .= '<div id="tab_menu">';
	if(!empty($menu_links)) {
		$main_content .= build_links_listing($menu_links);
	} else {
		$main_content .= '
		<p>No links set in this category</p>';
	}
	$main_content .= '</div>';
	
	// rss links
	
	$main_content .= '<div id="tab_rss">';
	if(!empty($rss_links)) {
		$main_content .= build_links_listing($rss_links);
	} else {
		$main_content .= '
		<p>No links set in this category</p>';
	}
	$main_content .= '</div>';
	
	// head links
	
	$main_content .= '<div id="tab_head">';
	if(!empty($head_links)) {
		$main_content .= build_links_listing($head_links);
	} else {
		$main_content .= '
		<p>No links set in this category</p>';
	}
	$main_content .= '</div>';
	
	// other links
	
	$main_content .= '<div id="tab_other">';
	if(!empty($other_links)) {
		$main_content .= build_links_listing($other_links);
	} else {
		$main_content .= '
		<p>No links set in this category</p>';
	}
	$main_content .= '</div>';
		
	// close tabs
	
	$main_content .= '</div>';

// output aside content - into $aside_content variable
	if( (isset($_GET['action'])) && ($_GET['action'] == 'edit') ) {
		
		$form_title = 'Edit this link';
		$edit_link = $link;
		$edit_link['link_id'] = $link_id;
		
	} else {
		
		$form_title = 'Add a new link';
		$edit_link['id'] = 0;
		$edit_link['title'] = '';
		$edit_link['url'] = '';
		$edit_link['summary'] = '';
		$edit_link['category'] = '';
		
	}
	
	$aside_content = '
	<div class="snippet">
		<h6>'.$form_title.'</h6>
		
		<form action="'.$action_url.'" method="post" id="edit_link_form">
	
		<p><label for="title">Link title:</label> 
			<input name="title" type="text" size="20" value="'.$edit_link['title'].'"/>
		</p>
	
		<p><label for="url">Link URL:</label> 
			<textarea name="url" cols="16" rows="4">'.$edit_link['url'].'</textarea></p>
		
		<p><label for="summary">Description:</label> 
			<textarea name="summary" cols="16" rows="4">'.$edit_link['summary'].'</textarea></p>
		
		<p><label for="category">Select Category:</label> 
			<select name="category">
				<option value="site_menu">Site Menu</option>
				<option value="site_rss">Site RSS</option>
				<option value="site_head">Site Head</option>';
			if(!empty($link_categories)) {
			foreach($link_categories as $cat) {
				$selected = ($cat['category'] == $edit_link['category']) ? ' selected="selected"' : '' ;
				$aside_content .= '
				<option value="'.$cat['category'].'"'.$selected.'>'.$cat['category'].'</option>';
			}
			}
		$aside_content .= '
			</select></p>	
		
		<p>OR</p>
		
		<p><label for="new_category">Type New Category:</label> 
			<input name="new_category" type="text" size="20"/></p>';
			
		if (!empty($edit_link['link_id'])) {
			$aside_content .= '
			<input name="id" type="hidden" value="'.$edit_link['link_id'].'"/>
			<input type="submit" name="update_link" value="update"/>
			<input type="submit" name="delete_link" value="delete"/>';
		} else {
			$aside_content .= '
			<input type="submit" name="insert_link" value="add">';
		}

	$aside_content .= "
		</form>
	</div>";
?>