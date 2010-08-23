<?php

/*
	create a new link item to go into the header
	
	if we put the link into the $config array as below it will automatically be picked up by
	the insert_links() function (within show_head() )
	
	this code could be added to any of the _content partial files, but by sticking it in its own php file
	and then putting it in the _includes folder it is automatically picked up at the top of the main index
	file - this enables us to insert the 'random_title.php' file as a stylesheet within the site header
*/

	$config['site']['link'][] = array(
						'rel' 	=> 'stylesheet',
						'type' 	=>	'text/css',
						'href'	=>	WW_REAL_WEB_ROOT.'/ww_view/themes'.$config['site']['theme'].'/random_title.php'		
							);

?>