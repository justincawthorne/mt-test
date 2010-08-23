<?php
/**
 * -----------------------------------------------------------------------------
 * ROOT/PATH FUNCTIONS
 * -----------------------------------------------------------------------------
 */
 
/**
 * end_slash
 * 
 * adds a trailing slash to pathname if needed
 * 
 * @param	string	$path	path to be checked
 * @return	string	$path	amended path
 */

	function end_slash($path) { 
		$len = strlen($path);
		if($len > 0 ) {
			$path = ($path[($len-1)] != '/') ? $path.'/' : $path;
		}
		return $path;
	}

/**
 * clean_end_slash
 * 
 * remove trailing slash from pathname if needed
 * 
 * @param	string	$path	path to be checked
 * @return	string	$path	amended path
 */

	function clean_end_slash($path) { 
		$len = strlen($path);
		if($len > 0 ) {
			$path = ($path[($len-1)] == '/') ? substr($path,0,($len-1)) : $path;
		}
		return $path;
	}

/**
 * start_slash
 * 
 * add a starting slash to pathname if needed
 * 
 * @param	string	$path	path to be checked
 * @return	string	$path	amended path
 */

	function start_slash($path) { 
		$len = strlen($path);
		if($len > 0 ) {
			$path = ($path[0] != '/') ? '/'.$path : $path;
		}
		return $path;
	}

/**
 * clean_start_slash
 * 
 * remove a starting slash to pathname if needed
 * 
 * @param	string	$path	path to be checked
 * @return	string	$path	amended path
 */

	function clean_start_slash($path) { 
		$path = ($path[0] == '/') ? substr($path,1) : $path;
		return $path;
	}

/**
 * clean_slashes
 * 
 * strips double slashes from path names
 * 
 * @param	string	$path	path to be checked
 * @return	string	$path	amended path
 */

	function clean_slashes($path) { 
		$path = str_replace('//','/',$path);
		return $path;
	}
	
/**
 * get_server_path
 * 
 * returns the server path to the bouncer folder
 * this function is designed to return a value for the BOUNCE_ROOT constant
 * however, to make it portable the line used to strip the _scripts folder name
 * has been made optional
 * 
 * @param 	string	$file_path - usually provided by __FILE__
 * @param 	bool	$strip_folder	set to true/on by default, simply strips the
 * 									_scripts foldername, but can be disabled
 */

	function get_server_path($file_path, $strip_folder = 1) {
		$this_path 		= dirname($file_path);					// strip filename from provided path
		$this_dir 		= (!empty($strip_folder)) ? basename($this_path) : "" ;		// get the current directory name
		$server_path 	= str_replace($this_dir,'',$this_path);	// remove $this_dir from $this_path
		$server_path 	= clean_end_slash($server_path);		// clean path name
		return $server_path;
	}
	
/**
 * get_url_path
 * 
 * this returns the url to the bouncer folder, used for linking to css files
 * and providing other url links, such as to password reminder service 
 * 
 * it works by stripping the server path (BOUNCE_ROOT) from the document root
 * and then appending the resulting path to the host url
 * in most cases this shoud provide an accurate URL to the bouncer folder
 */

	function get_url_path($file_path) {
		$this_host = (substr($_SERVER['HTTP_HOST'],0,7) != "http://") 
			? 'http://'.$_SERVER['HTTP_HOST'] 
			: $_SERVER['HTTP_HOST'] ;
		$root_suffix = get_root_suffix($file_path);
		$url_path = $this_host.$root_suffix;
		return $url_path;
	}
	
	function get_root_suffix($file_path) {
		$doc_root = $_SERVER['DOCUMENT_ROOT'];
		$server_path = get_server_path($file_path); // this will be the same as BOUNCE_ROOT
		if ((strpos($server_path, $doc_root)) !== false) {
			$root_suffix = str_replace($doc_root,'',$server_path);
		} elseif(!empty($_SERVER['SCRIPT_FILENAME'])) {
			$server_path = get_server_path($_SERVER['SCRIPT_FILENAME']);
			$root_suffix = str_replace($doc_root,'',$server_path);
		} else {
			$root_suffix = '';
		}
		$root_suffix = start_slash($root_suffix);
		return $root_suffix;
	}

/**
 * -----------------------------------------------------------------------------
 * ROOT DEFINITIONS
 * -----------------------------------------------------------------------------
 */


// get the server path to this file - use the __FILE__ constant to ensure the path remains, well, constant

	$ww_root = (!empty($set_ww_root)) ? clean_end_slash($set_ww_root) : get_server_path(__FILE__) ;
	
	define('WW_ROOT',$ww_root);
	
	// check we got the root correct

	if(WW_ROOT."/ww_config/root_functions.php" != __FILE__) {
		echo "WARNING: Root configuration error:<br/>";
		echo "set_ww_root needs to be manually configured in ww_root.php<br/>";
		exit();
	}

	
// now calculate the correct URL for this blog

	/*
		root suffix is used later on for redirecting urls
		it is essentially the section of the URL path that follows the domain name
	*/
	
	$ww_web_root = (!empty($set_bounce_html_root)) ? clean_end_slash($set_ww_web_root) : get_url_path(__FILE__);	

	if(!empty($site['redirect_url'])) {
		$ww_web_root = clean_end_slash($site['redirect_url']);
		$ww_real_web_root = get_url_path(__FILE__);
		$host = (substr($_SERVER['HTTP_HOST'],0,7) != "http://") ? "http://".$_SERVER['HTTP_HOST'] : $_SERVER['HTTP_HOST'] ;
		$root_suffix = str_replace($host,'',$site['redirect_url']);
		$root_suffix = start_slash($root_suffix);
	} else {
		$ww_web_root = $ww_web_root;
		$ww_real_web_root = $ww_web_root;
		$root_suffix = get_root_suffix(__FILE__);
	}
	
	define('WW_ROOT_SUFFIX',$root_suffix);
	define('WW_WEB_ROOT',$ww_web_root);
	define('WW_REAL_WEB_ROOT',$ww_real_web_root);

/**
 * -----------------------------------------------------------------------------
 * DEBUGGING
 * -----------------------------------------------------------------------------
 */
 
 	// set debug_mode at the end of model_functions.php to 1 to display debugging info

	if(!empty($debug_mode)) {
		echo constant('WW_ROOT').' - <b>WW_ROOT</b><br/>';
		echo constant('WW_WEB_ROOT').' - <b>WW_WEB_ROOT</b><br/>';
		echo constant('WW_REAL_WEB_ROOT').' - <b>WW_REAL_WEB_ROOT</b><br/>';
		echo constant('WW_ROOT_SUFFIX').' - <b>WW_ROOT_SUFFIX</b><br/>';
		foreach($_SERVER as $key => $value) {
			echo "<b>".$key."</b>: ".$value."<br>";
		}
	}	
	
?>