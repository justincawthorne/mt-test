<?php
	/*
		bounces any wicked directory browsers back to the blog home page
		alternatively you can enter any $home url you like
	*/
	
	include_once('model_functions.php');	
	$home = WW_WEB_ROOT;
	
	// $home = 'www.google.com'; // 
	
	header('Location: http://'.$home);
?>