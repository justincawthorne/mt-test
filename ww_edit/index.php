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

// increase session lifetime

	ini_set('session.gc_maxlifetime', 120*60); // 120*60 = 2 hours
	
// change session storage location to bypass garbage collector
	
	$sessdir = ini_get('session.save_path')."/ww_sess";
	if (!is_dir($sessdir)) { 
		mkdir($sessdir, 0777); 
	}
	ini_set('session.save_path', $sessdir);
	
// now bring in our functions	
	
	include_once('../ww_config/model_functions.php');
	include_once('../ww_config/controller_functions.php');
	include_once('../ww_config/view_functions.php');
	include_once('../ww_config/author_controller_functions.php');
	include_once('../ww_config/author_view_functions.php');


// authentication process

	// ensure we have the login params

	if(!defined('WW_SESS')) {
		include_once('bouncer_admin/_scripts/bouncer_params.php');
	}

	// add option for pass-through authentication

	$pta = false; // set to true to enable pta or false to disable

	if($pta == true) {
		include_once('bouncer_admin/pta.php');
	}

	// restrict access to page

	require_once('bouncer_admin/restrict.php');

	// ... and bounce people right out if they happen to get here without logging in
	
	if(!isset($_SESSION[WW_SESS])) {
		header('Location:'.WW_WEB_ROOT);
		exit();
	}

	
// now create the author details session

	create_author_session();

	/*	reference:
	
		$_SESSION[WW_SESS]['logged_in'] = 1/0 	(should be 1 obviously)
		$_SESSION[WW_SESS]['expired'] 	= 1/0	(should be 0)
		$_SESSION[WW_SESS]['user_id']
		$_SESSION[WW_SESS]['guest']		= 1/0	(if 0 then user has full access)
		$_SESSION[WW_SESS]['last_login']
		$_SESSION[WW_SESS]['name']
		$_SESSION[WW_SESS]['email']
		$_SESSION[WW_SESS]['level'] 	= author/editor/contributor
	*/
	

// define the theme and select the page to display
	
	$theme = 'desktop';
	
	$allowed_pages = array(	'front',
							'write',
							'articles',
							'comments',
							'categories',
							'tags',
							'authors',
							'links',
							'files',
							'images',
							'attachments',
							'settings',
							);
	
	$page_name = (isset($_GET['page_name'])) ? $_GET['page_name'] : 'front' ;
	$page_name = (in_array($page_name,$allowed_pages)) ? $page_name : 'front' ;
	
	$content_partial = WW_ROOT.'/ww_edit/_content/_'.$page_name.'.php';


// now buffer main content into a variable
		
	    ob_start();
		
		$main_content = '';						// initialize content variables
		$aside_content = '';
		include $content_partial;				// get content
		echo build_admin_main($main_content);	// build into html structure
		echo build_admin_aside($aside_content);		
	    $page_content = ob_get_contents();		//load into $page_content variable

	    ob_end_clean();							// flush buffer 


// output the page	

	// show head section
	
	show_admin_head($config['site']['title'], $page_title, $theme);

	// show titlebar and menu
	
	show_admin_page_header($config['site']['title']);
	
	// show main content
	
	echo $page_content;
	
	// show page footer
	
	show_admin_page_footer();

// and we're done :)	
?>