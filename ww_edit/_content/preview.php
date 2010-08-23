<?php
	session_start();

	/*	check author session 
	
	 	the author id and session_id are sent via post params to the popup
	 	
		using the author id we derive the cookie name - then we can check if the 
		cookie value matches the session id that was sent via the form	 	
	*/
	
	$author_id = (isset($_POST['current_author_id'])) ? (int)$_POST['current_author_id'] : '' ;
	$sess_val = (isset($_POST['current_author_sess'])) ? htmlspecialchars_decode($_POST['current_author_sess']) : '' ;
	
	// exit if params not sent
		
	$params_check = ($author_id.$sess_val);

	if(empty($params_check)) {
	
		exit();			
	}

	$cookie_name 	= (!empty($author_id)) ? 'author'.$author_id : '' ;
	$cookie_val 	= (!empty($cookie_name)) ? $_COOKIE[$cookie_name] : '' ;

	// exit if cookie value is empty or session/cookie values don't match

	if( (empty($cookie_val)) || ($cookie_val != $sess_val) ) {
	
		exit();			
	}
	
	/*
		if we've gotten this far we're good to go on :)
	*/
	
// get root constants

	if(!defined('WW_ROOT')) {
		include_once('../../ww_config/model_functions.php');
	}
	
// now bring in our functions	
	
	include_once(WW_ROOT.'/ww_config/model_functions.php');
	include_once(WW_ROOT.'/ww_config/controller_functions.php');
	include_once(WW_ROOT.'/ww_config/view_functions.php');	

// get article data
	
	// debug_array($_POST);
	
	$article = stripslashes_deep($_POST);
	
	$article['id'] = $_POST['article_id'];

	
	// get author name
	$article['author_name'] = get_author_name($article['author_id']);
	$article['author_url'] 	= 'temp';
	
	// get category name
	$article['category_title'] 	= get_category_title($article['category_id']);
	$article['category_url'] 	= 'temp';
	
	// comments and other fields
	$article['comments_hide'] 	= 1;
	$article['comments_disable']= 1;
	$article['total_pages'] 	= 1;
	$article['pages'] 			= 1;
	$article['tags'] 			=  '' ;
	$article['attachments'] 	=  '' ;
	$article['comments'] 		=  '' ;
		
	// page name
	
	$_GET['page_name'] = 'article';
	
// get content partial - checking for theme versions as well
	
		$theme_content_folder = WW_ROOT.'/ww_view/themes'.$config['site']['theme'].'/_content';
		
		$content_partial = (file_exists($theme_content_folder.'/_article.php')) 
			? $theme_content_folder.'/_article.php'
			: WW_ROOT.'/ww_view/_content/_article.php';
			
	// now buffer main content into a variable

	    ob_start();
		include $content_partial;
	    $body_content['main'] = ob_get_contents();
	    ob_end_clean();	
	    
	// get aside content

		include_once(WW_ROOT.'/ww_view/_content/_aside.php');
		
		// additional theme specific asides?
		
		if(file_exists($theme_content_folder.'/_aside.php')) {
			include_once($theme_content_folder.'/_aside.php');
		}
	
		$body_content['aside'] = insert_aside($aside_content);

	// output head section
	
		$head_content = '';
		show_head($head_content, $config['site']);
	
	// output body section
	
		show_body($body_content, $config);
?>