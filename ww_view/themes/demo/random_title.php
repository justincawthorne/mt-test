<?php
header("Content-type: text/css");
	
	/*
		because this file has a content type of css we can't stick it in the _includes folder otherwise it
		renders the entire site as 'css', instead we stick another file 'random_title_var.php' in the _includes folder
		to reference this and insert it as a css file within the header
	*/	
	
	// set path to theme folder

	include_once('../../../ww_config/model_functions.php');

	$path = WW_ROOT.'/ww_view/themes/demo/_img/random/';
	$file_path = WW_REAL_WEB_ROOT.'/ww_view/themes/demo/_img/random/';

	$folder = opendir($path);
	$images = array();
	
    while (false !== ($file = readdir($folder))) {
        if (!is_dir($file)) {
		$images[] = $file;
		}
    }

	$random = array_rand($images);
	$image = $images[$random];
	$image = $file_path.$image;

echo "

#header_panel {
	background-image: url('".$image."');
	background-repeat: no-repeat;
	background-position: center top;
}";