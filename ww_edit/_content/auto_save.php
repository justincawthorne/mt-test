<?php
	session_start();

	/*	check author session 
	
	 	the author id and session_id are sent via post params to the popup
	 	
		using the author id we derive the cookie name - then we can check if the 
		cookie value matches the session id that was sent via the form	 	
	*/
	
	$author_id = (isset($_POST['author_id'])) ? (int)$_POST['author_id'] : '' ;
	$sess_val = (isset($_POST['author_sess'])) ? htmlspecialchars_decode($_POST['author_sess']) : '' ;
	
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


// posted data

	$article_id 	= (isset($_POST['article_id'])) ? (int)$_POST['article_id'] : 0 ;
	$article_body 	= $_POST['content'];
	$body 			= (function_exists('gzdeflate')) ? gzdeflate($article_body, 5) : $article_body;


// has an article id been set (i.e. new article or existing article)

	$cookie_name = (empty($article_id)) ? 'autosave_new' : 'autosave_'.$article_id ;


// check if the cookie is set - or set one

	if(isset($_COOKIE[$cookie_name])) {
		
		// cookie already set - update autosave
		$conn = author_connect();
		// use cookie value to determine which database row to update
		$edit_id = (int)$_COOKIE[$cookie_name];
		$update = "
				UPDATE edits SET
					body 		= '".$conn->real_escape_string($body)."',
					date_edited	= '".$conn->real_escape_string(date('Y-m-d H:i:s'))."'
				WHERE id = ".$edit_id;	
		$result = $conn->query($update);
		if(!$result) {
			echo '<p>autosave update error: '.$conn->error.'</p>';
		} else {
			// output message
			echo "<p><strong>Autosave:</strong> autosave last updated at ".date('r')."</p>";			
		}

		
	} else {
		
		// cookie NOT set - insert new autosave		
		$conn = author_connect();
		$insert = "
				INSERT INTO edits
				(author_id, article_id, body, date_edited)
				VALUES 
				(
					".(int)$author_id.",
					".(int)$article_id.",
					'".$conn->real_escape_string($body)."',
					'".$conn->real_escape_string(date('Y-m-d H:i:s'))."'
				)";
		// get new edit_id value
		$result = $conn->query($insert);
		if(!$result) {
			echo '<p>autosave update error: '.$conn->error.'</p>';
		} else {
			// set cookie value
			$new_edit_id = $conn->insert_id;
			setcookie($cookie_name, $new_edit_id, time()+60*30, "/"); // 30 minutes
			// output message
			echo "<p><strong>Autosave:</strong> new autosave created at ".date('r')."</p>";
		}
	}
?>