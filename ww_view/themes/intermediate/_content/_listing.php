<?php

// generate text for meta tags, page title and headers

	/* this is a cut down version of the default code in ww_view/_content/_listing.php */
	
	$title_text = array();
	$sub_header_text = array();

	if(!empty($_GET['search'])) {	
		
		$config['site']['meta_title'] = 'Search results for &quot;'.$_GET['search'].'&quot; - '.$config['site']['title'];
		$config['site']['meta']['description'] = 'Search results for '.$_GET['search'].' on '.$config['site']['meta_title'];
		$config['site']['meta']['keywords'] = $_GET['search'];
		
		$title_text[] = 'containing the term &quot;'.$_GET['search'].'&quot;';
		
	} else {
		
		// what are we looking for?
		
		// category
		if( (isset($_GET['category_id'])) && (isset($_GET['category_url'])) ) {
			$title_text[] = "category: ".get_category_title($_GET['category_id']);
		}
		
		// author
		if( (isset($_GET['author_id'])) && (isset($_GET['author_url'])) ) {
			$title_text[] = "author: ".get_author_name($_GET['author_id']);
		}
		
		// tag
		if( (isset($_GET['tag_id'])) && (isset($_GET['tag_url'])) ) {
			$title_text[] = "tagged ".get_tag_title($_GET['tag_id']);
		}
		
		// date
		if(isset($_GET['year'])) {
			if(isset($_GET['day'])) {
				$query_ts = strtotime($_GET['year'].'-'.$_GET['month'].'-'.$_GET['day']);
				$title_text[] = "date: ".date('d M Y',$query_ts);
			} elseif(isset($_GET['month'])) {
				$query_ts = strtotime($_GET['year'].'-'.$_GET['month']);
				$title_text[] = "month: ".date('F Y',$query_ts);
			} else {
				$title_text[] = "year: ".$_GET['year'];
			}
		}
		
		// meta tag
		
		$config['site']['meta_title'] = 'Articles '.implode(', ',$title_text).' - '.$config['site']['title'];
		
	}
	
// now we compile the page title text and grab the total articles variable
	
	$header_text 	= implode(' &#124; ',$title_text);
	$page_title 	= 'Params - '.implode(' &#124; ',$title_text);
	
	$total			= (!empty($articles[0]['total_found'])) ? $articles[0]['total_found'] : '0' ;
	$total_articles = 'found: '.$total;
	
// header text and relevant code for rss feed listing
	
	if(isset($_GET['feed_listing'])) {
		$config['site']['meta_title'] = 'RSS Feeds for '.$config['site']['title'];
		$page_title = 'RSS Feeds';
		$sub_header = "RSS";
		$total_articles = '';			
	}

// use the inbuilt function to generate the page header

	echo show_page_header($header_text,$total_articles);

// add our own h1 tag
	
	echo '<h1>Custom listing page</h1>';
	
// add page navigation at the top of the page as well as the bottom

	if($total > $config['layout']['per_page']) {
		echo show_listing_nav($articles[0]['total_pages'], $config['layout']['per_page']);
	}
	
// create our own listing
	
	if(empty($articles)) {
		
		echo '<h2>No results...</h2>';
		
	} else {
		echo '
		<div id="listing_wrapper">';
		
		foreach($articles as $list) {
			echo '
			<div class="listing">
				<h2>'.$list['title'].'</h2>
				<p><strong>Extract:</strong> '.$list['summary'].'</p>
				<p class="footer">written by '.$list['author_name'].' on '.from_mysql_date($list['date_uploaded']).' &#124; <a href="'.$list['link'].'">read more ...</a></p>
			</div>';
		}
		
		echo '
		</div>';
				
	}

// show nav at the bottom of the page as well
	
	if($total > $config['layout']['per_page']) {
		echo show_listing_nav($articles[0]['total_pages'], $config['layout']['per_page']);
	} 
?>