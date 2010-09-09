<?php

// meta tags for head section - slightly different ones for a search results page
	
	$title_text = array();
	$sub_header_text = array();

	if(!empty($_GET['search'])) {	
		
		$config['site']['meta_title'] = 'Search results for &quot;'.$_GET['search'].'&quot; - '.$config['site']['title'];
		$config['site']['meta']['description'] = 'Search results for '.$_GET['search'].' on '.$config['site']['meta_title'];
		$config['site']['meta']['keywords'] = $_GET['search'];
		
		$title_text[] = 'containing the term &quot;'.$_GET['search'].'&quot;';
		$sub_header_text[] = 'search: '.$_GET['search'];
		
	} else {
		
		// what are we looking for?
		
		// category
		if( (isset($_GET['category_id'])) && (isset($_GET['category_url'])) ) {
			$title_text[] = "filed in ".get_category_title($_GET['category_id']);
			$sub_header_text[] = get_category_title($_GET['category_id']);
		}
		
		// author
		if( (isset($_GET['author_id'])) && (isset($_GET['author_url'])) ) {
			$title_text[] = "by ".get_author_name($_GET['author_id']);
			$sub_header_text[] = 'author: '.get_author_name($_GET['author_id']);
		}
		
		// tag
		if( (isset($_GET['tag_id'])) && (isset($_GET['tag_url'])) ) {
			$title_text[] = "tagged ".get_tag_title($_GET['tag_id']);
			$sub_header_text[] = 'tag: '.get_tag_title($_GET['tag_id']);
		}
		
		// date
		if(isset($_GET['year'])) {
			if(isset($_GET['day'])) {
				$query_ts = strtotime($_GET['year'].'-'.$_GET['month'].'-'.$_GET['day']);
				$title_text[] = "posted on ".date('d M Y',$query_ts);
				$sub_header_text[] = 'date: '.date('d M Y',$query_ts);
			} elseif(isset($_GET['month'])) {
				$query_ts = strtotime($_GET['year'].'-'.$_GET['month']);
				$title_text[] = "posted during ".date('F Y',$query_ts);
				$sub_header_text[] = 'month: '.date('F Y',$query_ts);
			} else {
				$title_text[] = "posted during ".$_GET['year'];
				$sub_header_text[] = 'year: '.$_GET['year'];
			}
		}
		
		// meta tag
		
		$config['site']['meta_title'] = 'Articles '.implode(', ',$title_text).' - '.$config['site']['title'];
		
	}
	
	// total results string
	
	$sub_header 	= implode(', ',$sub_header_text);
	$page_title 	= 'Showing articles '.implode(', ',$title_text);
	
	$total			= (!empty($articles[0]['total_found'])) ? $articles[0]['total_found'] : '0' ;
	$total_articles = $total.' articles found';
	
	// feed listing
	
	if(isset($_GET['feed_listing'])) {
		$config['site']['meta_title'] = 'RSS Feeds for '.$config['site']['title'];
		$page_title = 'RSS Feeds';
		$sub_header = "RSS";
		$total_articles = '';			
	}

	echo show_page_header($sub_header,$total_articles);
	
	$disqus = $config['connections']['disqus_shortname'];
	
	echo show_listing($articles, $page_title, $disqus);
	
	if($total > $config['layout']['per_page']) {
		echo show_listing_nav($articles[0]['total_pages'], $config['layout']['per_page']);
	} 
?>