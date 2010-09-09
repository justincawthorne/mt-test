<?php
/*
	refer to ww_view/_content/aside.php to see all default snippets 
*/

	include_once(WW_ROOT.'/ww_view/_content/_aside.php');

	/* custom asides for this theme are automatically imported */
	

// add a new custom aside, using the build_snippet function for easy formatting

	$custom_html = "<p>This is a custom aside I've just added now</p>";
	
	$aside_snippet['custom_aside'] = build_snippet('Custom Sample',$custom_html);


// recreate $aside_content array - remove some defaults, add our custom ones

	$aside_content['upper'] = array(
									$aside_snippet['search'],
									$aside_snippet['list_test'],
									$aside_snippet['date_today'],
									$aside_snippet['custom_aside']
									);
	$aside_content['inner']	= array(
									$aside_snippet['latest_articles'],
									$aside_snippet['popular_articles'],
									$aside_snippet['authors_list'],
									$aside_snippet['categories_list'],
									$aside_snippet['tags_list'],
									$aside_snippet['months_list'],
									$aside_snippet['feeds']
									);
	$aside_content['outer'] = array();
	$aside_content['lower']	= array();


// finally use the insert_aside function to put our $aside_content array into the correct html structure
	
	echo insert_aside($aside_content);
	
// to define additional snippets:-

	/*
		$aside_snippet['custom_aside'] = build_snippet('Custom Sample',$custom_html);
		
			'custom_aside'		this is the unique name for your snippet
			'Custom Sample'		this is the snippet title that will appear on the page
			$custom_html		this is the snippet data - can be html or an array (which would be rendered as a list)
		
			you don't have to use the build snippet function - this just provides a wrapper
			for your snippet and converts arrays to lists - you can manually code the html
			if you prefer
	*/

// to add your new snippet to an existing aside_content array:-

	/*
		$aside_content['upper'][] = $aside_snippet['custom_aside'];
		
			here we're adding the snippet we created above to the aside_upper div
	*/
	
// to recreate the aside_content array (i.e. ensure default tags don't appear)

	/*
		$aside_content['upper'] = array ();  
								
			we reset the $aside_content['upper'] array first
		
		$aside_content['upper'][] = $aside_snippet['custom_aside'];
		$aside_content['upper'][] = $aside_snippet['search']
		
			then add the snippets to the array as before
	*/
?>