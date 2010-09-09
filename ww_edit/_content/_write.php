<?php

	$cookie_name = 'author'.(int)$_SESSION[WW_SESS]['user_id'];
	
	setcookie($cookie_name, session_id(), 0, "/");

// page title - if undefined the site title is displayed by default

	$page_title = 'Write';

// get main content
	
	// any functions
	
	// any content generation
	
		if( (isset($_POST['publish'])) || (isset($_POST['draft'])) ){
			
			$article_data = validate_article_post_data();
			
			$left_text = 'validating post';
			$right_text = 'posting error';
			
			// received inserted or updated flag
			
			if(isset($article_data['action'])) {
			
				$left_text = '';
				$right_text = 'article '.$article_data['action'];
				
			}
			
		} else {
			
			$article_data = get_article_form_data();

			// header status text
			
			if(!isset($_GET['article_id'])) {
				
				$left_text = 'new article';
				$right_text = 'no comments';
				
			} else {
				
				$right_text = ($article_data['comment_count'] == 1) 
					? $article_data['comment_count'].' comment' 
					:  $article_data['comment_count'].' comments';
				
			}			
		}			
	
	// get text for article status
	
		$status_list = define_article_status();
		
		$status_text = $status_list[$article_data['status']];
		
		if($article_data['status'] == 'P') {
			$status_text = (strtotime($article_data['date_uploaded']) > time()) ? 'postdated' : 'published' ;
		}
	
		$left_text = (empty($left_text)) ? $status_text.' article' : $left_text ;

	
	$page_header = show_page_header($left_text, $right_text);
	


// get aside content

	// any functions
	
	// any content generation


// output main content - into $main_content variable

	$main_content = $page_header;

	if(isset($article_data['action'])) {
		
		$main_content .= '
			<h2>Your article has been '.$article_data['action'].'</h2>
			<p><strong>Title:</strong> '.stripslashes($article_data['title']).'</p>';

			$main_content .= '
			<p>
				<a href="'.$_SERVER["PHP_SELF"].'?page_name=write&amp;article_id='.$article_data['id'].'">Edit your article</a>
			</p>';		
		
		if($status_text == 'postdated') {
			
			$main_content .= '
			<p>This article is postdated and will be available on the site from '.from_mysql_date($article_data['date_uploaded'],'d m Y \a\t H:i').'</p>';
			
		} elseif( ($article_data['status'] == 'P') || ($article_data['status'] == 'A') ){
			
			$main_content .= '	
			<p>
				<a href="'.WW_WEB_ROOT.'/id/'.$article_data['id'].'/">Review your article</a>
			</p>';
						
		} else {
			
			$main_content .= '	
			<p>This article is currently in '.$status_text.' status and is not visible from the main site.</p>';
			
		}

		
	} else {
		
		$main_content .= build_write_form($article_data, $config);
				
	}

// output aside content - into $aside_content variable

	$aside_content = '<iframe style="width: 200px;border: none;" 
						id="image_browser_frame" 
						height="1600"
						frameborder="1" 
						scrolling="auto"
						src="_content/image_browser.php?sess='.htmlspecialchars(session_id()).'&amp;author_id='.(int)$_SESSION[WW_SESS]['user_id'].'">
					</iframe>';

?>