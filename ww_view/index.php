<?php
/*
	Copyright (C) 2010  Justin Cawthorne
	
	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>
*/

	error_reporting(E_ALL | E_STRICT);
	ini_set('display_errors', 1);

include_once('../ww_config/model_functions.php');
include_once('../ww_config/controller_functions.php');
include_once('../ww_config/view_functions.php');

	if (!session_id()) session_start();
	
// set theme and check for includes

	if(empty($config)) {
		echo 'it appears this is a new installation of Wicked Words<br />
		... well done you!<br />
		now go to the <a href="create.php">create page</a> to create your database tables...';
		exit();
	}
	
// process url

	$request = process_request();
	
	$config['site']['theme'] = (isset($_SESSION['theme'])) ? $_SESSION['theme'] : $config['site']['theme'] ;

// check for includes

	$includes = get_includes($config['site']['theme']);
	if(!empty($includes)) {
		foreach($includes as $inc){
			include_once($inc);
		}
	}
	
// create a 'meta title' from the main site title (used for browser titlebar and <title> tag

// redirect as needed

	if(isset($_GET['page_name'])) {

	// rss feed - redirect to xml page
		
		if($_GET['page_name'] == 'feed') {

			include(WW_ROOT.'/ww_view/rss-xml.php');
			exit();
			
		}
		
	// front page
		
		if($_GET['page_name'] == 'front') {

			if(!empty($config['front']['article_id'])) {
				
				// this is a specific article, selected in the edit room
				
				$article = get_article($config['front']['article_id']);
				
			} else {
		
				// determine what content goes on front page, and which list style
		
				switch($config['front']['page_style']) {
					
					case 'custom':
					break;
					
					case 'latest_post':
					$article = get_article();
					break;
					
					case 'latest_month':
					
					// get latest article to check dates
					$config_per_page = $config['layout']['per_page'];
					$config['layout']['per_page'] = 1;
					$latest_article = get_articles($config['layout']);
					
					// configure date parameters
					$latest_ts = strtotime($latest_article[0]['date_uploaded']);
					$_GET['month'] = date('m',$latest_ts);
					$_GET['year'] = date('Y',$latest_ts);
					
					// use front list style setting
					$config['layout']['per_page'] = $config_per_page;
					$config['layout']['list_style'] = $config['front']['list_style'];
					$articles = get_articles($config['layout']);
					break;
					
					case 'per_page':
					default:
					$config['layout']['list_style'] = $config['front']['list_style'];
					$articles = get_articles($config['layout']);
					break;
				}

			}
			
		}
	
	
	// 404 error
		
		if($_GET['page_name'] == '404') {
			
			// no content
			// we set up the meta tags and parse the request string on the partial page
			
		} 
	
	
	// single article
		
		if($_GET['page_name'] == 'article') {
			
			$article = get_article($_GET['article_id']);
	
		}
	
	
	// multiple articles - either a search or listings request
		
		if($_GET['page_name'] == 'listing') {
	
			if(isset($_GET['feed_listing'])) {
				
				// for a list of all available feeds we'll borrow the listing page
				$articles = format_feeds_list();
				
			} elseif(!empty($_GET['search'])) {
				
				// run a search
				$articles = search_articles($_GET['search'],$config['layout']);
				
			} else {
				
				// standard article listing
				$articles =  get_articles($config['layout']);
			}
		
		}


	// allowed pages array - redirect to 404 if any other page name is attempted
		
		$allowed_pages = array('feed','front','404','article','listing');
		
		$_GET['page_name'] = (in_array($_GET['page_name'],$allowed_pages)) ? $_GET['page_name'] : '404' ;


	// get content partial - checking for theme versions as well
	
		$theme_content_folder = WW_ROOT.'/ww_view/themes'.$config['site']['theme'].'/_content';
		
		$content_partial = (file_exists($theme_content_folder.'/_'.$_GET['page_name'].'.php')) 
			? $theme_content_folder.'/_'.$_GET['page_name'].'.php'
			: WW_ROOT.'/ww_view/_content/_'.$_GET['page_name'].'.php';

// at this point we could look for a custom 'index' file in the theme folder	
	
	/*	reference:
	$body_content['header'] = '';
	$body_content['nav'] = '';
	$body_content['main'] = '';
	$body_content['aside'] = '';
	$body_content['footer'] = '';
	*/
	
	// header content - below is just for example
	
		// $body_content['header'] = '<div id="header">yeah, header</div>';
		
	// nav content - below is just for example
	
		// $body_content['nav'] = '<div id="nav">yeah, nav content</div>';
	
	// now buffer main content and insert into a variable

	    ob_start();
		include $content_partial;
	    $body_content['main'] = ob_get_contents();
	    ob_end_clean();	
	    
	// get aside content

		// default aside content
		
		include_once('../ww_view/_content/_aside.php');
		
		// additional theme specific asides?
		
		if(file_exists($theme_content_folder.'/_aside.php')) {
			include_once($theme_content_folder.'/_aside.php');
		}
	
		$body_content['aside'] = insert_aside($aside_content);
	
	// footer content - below is just for example
		
		// $body_content['footer'] = '<div id="footer">test footer</div>';

	// output head section
	
		$head_content = '';
		show_head($head_content, $config);
	
	// output body section
	
		show_body($body_content, $config);
		
	} // end checking for $_GET['page_name']
?>