<?php

// page title - if undefined the site title is displayed by default

	$page_title = 'links';

	function build_links_listing($links) {
		if(empty($links)) {
			return;
		}
		$looped_cat = '';
		$ignore_cats = array('site_rss','site_menu','site_head');
		$html = '
		<ul class="links_listing">';
		foreach($links as $cat => $links_array) {
			if($cat != $looped_cat) {
				if(!in_array($cat, $ignore_cats)) {
					$html .= '
						<li class="link_category">
							<div>
								'.$cat.'
							</div>
						</li>';
				}
			}
			foreach($links_array as $link) {
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
			}
		$looped_cat = $cat;
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
	
	$menu_links = array();
	$rss_links 	= array();
	$head_links = array();
	$other_links = array();
	$link_categories = array();
	
	foreach($links as $cat => $data) {
		switch($cat) {
			
			case 'site_head':
			$head_links[$cat] = $data;
			break;
			
			case 'site_menu':
			$menu_links[$cat] = $data;
			break;
			
			case 'site_rss':
			$rss_links[$cat] = $data;
			break;
			
			default:
			$other_links[$cat] = $data;
			if(!empty($cat)) {
				$link_categories[] = $cat;
			}
			break;
		}
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
		<p>Add links to your menu, additional rss feeds, &lt;link&gt; tags for the head section and other links for a blogroll.</p>';

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
	
	$main_content .= '
		<div id="tab_menu">
		<p>Any links entered in this section will automatically be used for your nav menu (should you choose to use one)</p>';
	if(!empty($menu_links)) {
		$main_content .= build_links_listing($menu_links);
	} else {
		$main_content .= '
		<p>No links set in this category</p>';
	}
	$main_content .= '</div>';
	
	// rss links
	
	$main_content .= '
	<div id="tab_rss">
	<p>Any links entered in this section will appear in the list of rss feeds for your site (this is primarily designed for situations where you want to provide site feeds via a third-party such as Feedburner. Remember, you can set an alternate url for your main rss feed in the settings/admin section (feed url).</p>';
	if(!empty($rss_links)) {
		$main_content .= build_links_listing($rss_links);
	} else {
		$main_content .= '
		<p>No links set in this category</p>';
	}
	$main_content .= '</div>';
	
	// head links
	
	$main_content .= '
	<div id="tab_head">
	<p>This section enables you to set up additional &lt;link&gt; items for the head section of your site. This should not be used for stylesheets or favicons (unless they are located off-site) - however, links can be placed here for alternate rss feeds, for instance, since only the main rss feed is included in the head by default.</p>';
	if(!empty($head_links)) {
		$main_content .= build_links_listing($head_links);
	} else {
		$main_content .= '
		<p>No links set in this category</p>';
	}
	$main_content .= '</div>';
	
	// other links
	
	$main_content .= '
	<div id="tab_other">
	<p>This section is for any other links you wish to feature on your site - in a blogroll aside snippet, for instance.</p>';
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
		$edit_link['attributes'] = '';
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
			<textarea name="url" cols="16" rows="3">'.$edit_link['url'].'</textarea></p>
		
		<p><label for="summary">Description:</label> 
			<textarea name="summary" cols="16" rows="3">'.$edit_link['summary'].'</textarea></p>
		
		<p><label for="attributes">Attributes:</label> 
			<textarea name="attributes" cols="16" rows="3">'.$edit_link['attributes'].'</textarea></p>
		
		<p><label for="category">Select Category:</label> 
			<select name="category">';
			$default_categories = array('site_menu','site_rss','site_head');
			foreach($default_categories as $d_cat) {
				$selected = ($d_cat == $edit_link['category']) ? ' selected="selected"' : '' ;
				$aside_content .= '
				<option value="'.$d_cat.'"'.$selected.'>'.ucwords(str_replace('_',' ',$d_cat)).'</option>';
			}
			if(!empty($link_categories)) {
			foreach($link_categories as $l_cat) {
				$selected = ($l_cat == $edit_link['category']) ? ' selected="selected"' : '' ;
				$aside_content .= '
				<option value="'.$l_cat.'"'.$selected.'>'.$l_cat.'</option>';
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