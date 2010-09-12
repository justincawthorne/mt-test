<?php

// meta tags for head section - slightly different ones for a search results page
	
	$page_title_text 	= array();
	$page_header_text 	= array();
	$list_title_text 	= array();

	if(!empty($_GET['search'])) {	
		
		$config['site']['meta_title'] = 'Search results for &quot;'.$_GET['search'].'&quot; - '.$config['site']['title'];
		$config['site']['meta']['description'] = 'Search results for '.$_GET['search'].' on '.$config['site']['meta_title'];
		$config['site']['meta']['keywords'] = $_GET['search'];
		
		$page_title_text[] = 'containing the term &quot;'.$_GET['search'].'&quot;';
		$page_header_text[] = 'search: '.$_GET['search'];
		
	} else {
		
		// what are we looking for?
		
		// category
		if( (isset($_GET['category_id'])) && (isset($_GET['category_url'])) ) {
			$page_title_text[] = "filed in ".get_category_title($_GET['category_id']);
			
			// slightly more complex for categories in case of parent/child categories

			$category_details = get_category_details($_GET['category_id']);
			
			$parent_category = (!empty($category_details['parent_title'])) 
				? '<a href="'.WW_WEB_ROOT.'/'.$category_details['parent_url'].'">'.$category_details['parent_title'].'</a> &gt; ' 
				: '' ;
			
			$page_header_text[] = (!empty($parent_category)) ? $parent_category.$category_details['title'] : $category_details['title'] ;
			
		}
		
		// author
		if( (isset($_GET['author_id'])) && (isset($_GET['author_url'])) ) {
			$page_title_text[] = "by ".get_author_name($_GET['author_id']);
			$page_header_text[] = 'author: '.get_author_name($_GET['author_id']);
		}
		
		// tag
		if( (isset($_GET['tag_id'])) && (isset($_GET['tag_url'])) ) {
			$page_title_text[] = "tagged ".get_tag_title($_GET['tag_id']);
			$page_header_text[] = 'tag: '.get_tag_title($_GET['tag_id']);
		}
		
		// date
		if(isset($_GET['year'])) {
			if(isset($_GET['day'])) {
				$query_ts = strtotime($_GET['year'].'-'.$_GET['month'].'-'.$_GET['day']);
				$page_title_text[] = "posted on ".date('d M Y',$query_ts);
				$page_header_text[] = 'date: '.date('d M Y',$query_ts);
			} elseif(isset($_GET['month'])) {
				$query_ts = strtotime($_GET['year'].'-'.$_GET['month']);
				$page_title_text[] = "posted during ".date('F Y',$query_ts);
				$page_header_text[] = 'month: '.date('F Y',$query_ts);
			} else {
				$page_title_text[] = "posted during ".$_GET['year'];
				$page_header_text[] = 'year: '.$_GET['year'];
			}
		}
		
		// meta tag
		$meta_title_text = implode(', ',$page_title_text);
		$config['site']['meta_title'] = 'Articles '.strip_tags($meta_title_text).' - '.$config['site']['title'];
		
	}
	
	// total results string
	
	$page_header 	= implode(', ',$page_header_text);
	$page_title 	= 'Showing articles '.implode(', ',$page_title_text);
	
	$total			= (!empty($articles[0]['total_found'])) ? $articles[0]['total_found'] : '0' ;
	$total_articles = $total.' articles found';
	
	// feed listing
	
	if(isset($_GET['feed_listing'])) {
		$config['site']['meta_title'] = 'RSS Feeds for '.$config['site']['title'];
		$page_title = 'RSS Feeds';
		$sub_header = "RSS";
		$total_articles = '';			
	}

	echo show_page_header($page_header,$total_articles);
	
	// any sub categories to list
	
	if(isset($category_details['child'])) {
		echo '<p class="subcategories"><strong>Sub-categories:</strong> ';
		$delimiter = '';
		foreach($category_details['child'] as $child) {
			echo $delimiter.' <a href="'.WW_WEB_ROOT.'/'.$child['url'].'">'.$child['title'].'</a>';
			$delimiter = ',';
		}
		echo '</p>';
	}

	
	$disqus = $config['connections']['disqus_shortname'];
	
	echo show_listing($articles, $page_title, $disqus);
	
	if($total > $config['layout']['per_page']) {
		echo show_listing_nav($articles[0]['total_pages'], $config['layout']['per_page']);
	} 
?>