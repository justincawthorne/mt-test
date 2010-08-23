<?php

// meta tags for head section

	$config['site']['meta_title'] = " 404 error - ".$config['site']['title'];
	$config['site']['meta']['description'] = '404 error page for '.$config['site']['title'];
	unset($config['site']['meta']['keywords']);
	unset($config['site']['meta']['author']);
	
	// test adding a link
	
	$config['site']['link'][] = array(
						'rel' 	=>	'home',
						'title' =>	'home page',
						'href'	=>	WW_WEB_ROOT
									);

// prepare main content

	$nf_text = array();

	if(isset($_GET['category_url'])) {
		if(!isset($_GET['category_id'])) {
			$nf_text[] = "<strong>Category:</strong> ".$_GET['category_url']." - not found";
		} else {
			$nf_text[] = "<strong>Category:</strong> <a href=\"".WW_WEB_ROOT.'/'.$_GET['category_url']."/\">".$_GET['category_url']."</a> was found!";
		}
	}

	if(isset($_GET['author_url'])) {
		if(!isset($_GET['author_id'])) {
			$nf_text[] = "<strong>Author:</strong> ".$_GET['author_url']." - not found";
		} else {
			$nf_text[] = "<strong>Author:</strong> <a href=\"".WW_WEB_ROOT.'/author/'.$_GET['author_url']."/\">".$_GET['author_url']."</a> was found!";
		}
	}
	
	if(isset($_GET['tag_url'])) {
		if(!isset($_GET['tag_id'])) {
			$nf_text[] = "<strong>Tag:</strong> ".$_GET['tag_url']." - not found";
		} else {
			$nf_text[] = "<strong>Tag:</strong> <a href=\"".WW_WEB_ROOT.'/tag/'.$_GET['tag_url']."/\">".$_GET['tag_url']."</a> was found!";
		}
	}	

	if(isset($_GET['article_url'])) {
		$article_id = get_article_id();
		if(empty($article_id)) {
			$nf_text[] = "<strong>Article:</strong> ".$_GET['article_url']." - not found";
		} else {
			$nf_text[] = "<strong>Article:</strong> <a href=\"".WW_WEB_ROOT.'/id/'.$article_id."/\">".$_GET['article_url']."</a> was found!";
		}
	}
	

// build content

	echo "
	<h1>404 error</h1>
	<h4>Based on the url you entered we tried to find the following:</h4>
	<ul>";

	foreach($nf_text as $nf) {
		echo "
		<li>".$nf."</li>";
	}
	
	echo "
	</ul>";
?>