<?php

// meta tags for head section

	$config['site']['meta_title'] = $config['site']['title']." - Home Page";

// output content

	// $page_title = "front page: ".$config['site']['title'];
	
	if(isset($article)) {
		
		echo show_article($article, $config);
		
	} elseif(isset($articles)) {
		
		echo show_page_header('Home Page',$config['site']['title']);
		echo show_listing($articles);		
		
	} elseif($config['front']['page_style'] == 'custom') {
		
		echo 'custom front page';
		
	}

/*
	debug_array($_GET);
	debug_array($config);
	
*/

?>