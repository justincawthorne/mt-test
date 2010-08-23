<?php

// page title

	$page_title = 'Home Page';


// get required main content

	// list of author's recent articles

	$_GET['author_id'] = $_SESSION[WW_SESS]['user_id'];
	$recent_articles = get_articles_admin();
	
	// text for page header
	
	$left_header = 'Welcome back, '.$_SESSION[WW_SESS]['name'];
	$right_header = '<a href="'.$_SERVER["PHP_SELF"].'?page_name=write">/Write a new article</a>';


// get required aside content

	// article stats for the currently logged in author

	$_GET['author_id'] = $_SESSION[WW_SESS]['user_id'];
	
	$user_article_stats = get_articles_stats(1);
	
	if(!empty($user_article_stats)) {
		$first_user_post = $user_article_stats['P']['first_post'];
		$last_user_post = $user_article_stats['P']['last_post'];		
	}
		
	unset($_GET['author_id']);
	
	// comment stats for the currently logged in author
	
	$user_comment_stats = get_comments_stats($_SESSION[WW_SESS]['user_id']);
	$user_new_comments = get_new_comments($_SESSION[WW_SESS]['user_id']);
	
	// sitewide stats (if author rights allow)

	$all_article_stats = '';
	$all_comment_stats = '';
	if(empty($_SESSION[WW_SESS]['guest'])) {
		
		$all_article_stats = get_articles_stats();
		
		if(!empty($all_article_stats)) {
			$first_site_post = $all_article_stats['P']['first_post'];
			$last_site_post = $all_article_stats['P']['last_post'];
		}
		
		$all_comment_stats = get_comments_stats();
		$all_new_comments = get_new_comments();
	}
	
	// quick links
	
	$quicklinks = '
		<ul>
			<li><a href="'.$_SERVER["PHP_SELF"].'?page_name=comments">Manage Comments</a></li>
			<li><a href="'.$_SERVER["PHP_SELF"].'?page_name=categories">Manage Categories</a></li>
			<li><a href="'.$_SERVER["PHP_SELF"].'?page_name=tags">Manage Tags</a></li>
		</ul>
	';


// output main content

	$main_content = show_page_header($left_header, $right_header);
	$main_content .= "
		<h4>Your most recent articles are listed below.<br />To view all articles posted to the site click on the <strong><a href=\"".$_SERVER["PHP_SELF"]."?page_name=articles\">Articles</a></strong> item in the menu.</h4>";
	
	// output articles list

	$main_content .= build_admin_article_listing($recent_articles);



// output aside content

	// quick links
	
	$aside_content .= build_snippet('Quick Links', $quicklinks);
	
	if(!empty($recent_articles)) {
		
		$aside_content .= 
			'<h4>Your articles</h4>'.
			build_snippet('Your article statistics',$user_article_stats).'
			<div class="snippet">
				<p>Your last article was published on:</p>
				<p class="indent"><em>'.from_mysql_date($last_user_post,'l, j F Y').'</em></p>
				<p>You have published a total of:</p>
				<p class="indent"><strong><em>'.$user_article_stats['P']['total'].' articles</strong> since 
				'.from_mysql_date($first_user_post,'j M Y').'</em></p>
			</div>';
	
		if(!empty($user_comment_stats)) {
			$aside_content .= 
				build_snippet('Your comments statistics', $user_comment_stats);
		}
			
		if(!empty($user_new_comments)) {
			$c_text = (count($user_new_comments) > 1) ? ' new comments</a> have ' : ' new comment</a> has ' ;
			$aside_content .= '
				<div class="snippet">
				<p><a href="'.$_SERVER["PHP_SELF"].'?page_name=comments">'.count($user_new_comments).$c_text.'been posted to your articles since your last login.</div>';
		}	
		
		if(!empty($all_article_stats)) {
			$aside_content .= 
			'<h4>All articles</h4>'.
				build_snippet('Sitewide article statistics', $all_article_stats).'
			<div class="snippet">
				<p>The last article was published on:</p>
				<p class="indent"><em>'.from_mysql_date($last_site_post,'l, j F Y').'</em></p>
				<p>Total published:</p>
				<p class="indent"><strong><em>'.$all_article_stats['P']['total'].' articles</strong> since 
				'.from_mysql_date($first_site_post,'j M Y').'</em></p>
			</div>';
		}
	
		if(!empty($all_comment_stats)) {
			$aside_content .= 
				build_snippet('Sitewide comment statistics', $all_comment_stats);
		}
		
		if(!empty($all_new_comments)) {
			$c_text = (count($all_new_comments) > 1) ? ' new comments</a> have ' : ' new comment</a> has ' ;
			$aside_content .= '
				<div class="snippet">
				<p><a href="'.$_SERVER["PHP_SELF"].'?page_name=comments&amp;new">'.count($all_new_comments).$c_text.'been posted to the site since your last login.</div>';
		}
	}	

?>