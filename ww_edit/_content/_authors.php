<?php

// page title - if undefined the site title is displayed by default

	$page_title = 'Authors';


// get main content
	
	// any functions
	
	$author_id = (isset($_GET['author_id'])) ? (int)$_GET['author_id'] : $_SESSION[WW_SESS]['user_id'] ;
	$author = get_author($author_id);
	
	// any content generation
	
	$left_text = $author['name'];
	$right_text = $author['level'];
	$page_header = show_page_header($left_text, $right_text);
	
	$edit_author_form = '
		<form action="'.$_SERVER["PHP_SELF"].'?page=authors" method="post" enctype="multipart/form-data" name="edit_author_form">

				<p>
					<label for="author_name">Author Name</label>
					<input name="author_name" type="text" id="author_name" value="Justin"/>
				</p>
					
				<p>
					<label for="author_email">Email Address</label>
					<input name="author_email" type="text" id="author_email" size="50" value="edpriceishungry@gmail.com"/>
				</p>
				
				<p>
					<label for="author_summary">Author Summary</label>
					<textarea name="author_summary" id="author_summary" cols="56" rows="3"></textarea></p>
					
				<p>
					<label for="author_bio">Biography</label>
					<textarea name="author_bio" id="author_bio" cols="56" rows="13"></textarea>
				</p>
				
				<p>
					<label for="author_image">Image</label>
					<input name="author_image" type="file" id="author_image" />
					<span class="note">Any existing image for this author will be deleted if a new one is uploaded.</span>
				</p>
				
				<p>
					<label for="email_is_public">Display Email?</label>
					<select name="email_is_public" id="email_is_public">
						<option value="1">Yes</option>
						<option value="0">No</option>
					</select>
				</p>
		
		
				<p><label for="author_level">Access Level</label>
					<select name="author_level" id="access_level">
						<option value="contributor">Contributor</option>
						<option value="editor">Editor</option>
						<option value="author">Author</option>
					</select>
				</p>
					
				<p>
					<label for="block_author">Block Author</label>
					<select name="block_author" id="block_author">
						<option value="1">Author Is Blocked</option>
						<option value="0">Author Not Blocked</option>
					</select>
					<span class="note">Blocking an author sets their subscription expiry date to the current time
				 - when the author tries to log in they will get a message saying their subscription has expired
				 and won\'t be able to access the admin site</span>
				</p>
					
				<p>
					<label for="last_login">Last Logged In</label>
					<input name="last_login" type="text" id="last_login" value="2010-07-17 20:09:16" readonly/>
				</p>
				<p>
					<label for="last_ip">Last IP</label>
					<input name="last_ip" type="text" id="last_ip" value="203.206.40.48" readonly/>
				</p>
				
				<p><label for="new_pass">New Password</label>
					<input name="new_pass" type="password" id="new_pass"/>
				<br />
					<label for="confirm_new_pass">Confirm</label>
					<input name="confirm_new_pass" type="password" id="confirm_new_pass" size="20"/>
				</p>
				
				<p>
					<input type="submit" name="edit_author" value="Update Author" />
				</p>
			
			</form>
	';


// get aside content

	// any functions
	
	$author_list 	= get_authors_admin();
	
	// any content generation
	
	$add_author_form = '
			<form action="'.$_SERVER["PHP_SELF"].'?page=authors" method="post" id="add_author_form">
		
				<p><label for="author_name">Author name:</label> 
					<input name="author_name" id="author_name" size="20" type="text"/>
				</p>
			
				
				<p><label for="author_email">Email address:</label> 
					<input name="author_email" id="author_email" size="20" type="text"/>
				</p>
				
				<p><label for="author_level">Access Level:</label>
					<select name="author_level" id="access_level">
						<option value="contributor">Contributor</option>
						<option value="editor">Editor</option>
						<option value="author">Author</option>
					</select>
				</p>
	
			<input name="add_author" value="Add Author" type="submit"/>
			</form>
			';
			
		$author_levels_text = '
			<ul>
				<li>
					<strong>Author</strong><br />
					access to all areas
				</li>
				<li>
					<strong>Editor</strong><br />
					access to write/edit all articles (no access to settings)
				</li>
				<li>
					<strong>Author</strong><br />
					access to write/edit own articles only (no access to settings)
				</li>
			</ul>
		';	


// output main content - into $main_content variable

	$main_content = $page_header;
	$main_content .= $edit_author_form;


// output aside content - into $aside_content variable

	$aside_content = build_snippet('Authors',$author_list);
	$aside_content .= build_snippet('Add an author',$add_author_form);
	$aside_content .= build_snippet('Access levels',$author_levels_text);

?>