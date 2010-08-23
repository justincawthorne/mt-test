<?php
	session_start();

	/*	check author session 
	
	 	the author id and session_id are sent via url params to the iframe
	 	
		using the author id we derive the cookie name - then we can check if the 
		cookie value matches the session id that was sent via the url	 	
	*/
	
	$author_id = (isset($_GET['author_id'])) ? (int)$_GET['author_id'] : '' ;
	$sess_val = (isset($_GET['sess'])) ? htmlspecialchars_decode($_GET['sess']) : '' ;
	
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
	
	$url = $_SERVER["PHP_SELF"].'?sess='.$_GET['sess'].'&author_id='.$_GET['author_id'].'&image_id='.$_GET['image_id'];
	
// now bring in our functions	
	
	include_once(WW_ROOT.'/ww_config/model_functions.php');
	include_once(WW_ROOT.'/ww_config/controller_functions.php');
	include_once(WW_ROOT.'/ww_config/author_controller_functions.php');
	include_once(WW_ROOT.'/ww_config/author_view_functions.php');
	
// get image details

	$image_id = (isset($_GET['image_id'])) ? (int)$_GET['image_id'] : '' ;
	
// process edited image

	$edit_success = 0;
	if(isset($_POST['submit'])) {
		
		$edit = update_image($image_id);
		$edit_success = ($edit == false) ? 0 : 1 ;
		
	}
	
// replace thumbnail

	if(isset($_POST['replace_thumb'])) {
		
		if( (isset($_FILES['new_thumb'])) && (empty($_FILES['new_thumb']['error'])) ) {
			$current = $_POST['current_thumb'];
			$new = $_FILES['new_thumb'];
			$thumb_error = replace_file($current,$new);
			if(is_array($thumb_error)) {
				header('Location: '.$url);
			}
		}
		
	}

// get image details
	
	$image = get_image($image_id);
	
	$image_src = '
				<img src="'.$image['src'].'" 
					alt="'.$image['alt'].'" 
					title="'.$image['title'].'" 
					width="'.$image['width'].'" height="'.$image['height'].'"/>';
	$thumb_src = '
				<img src="'.$image['thumb_src'].'" 
					class="image_thumb"
					alt="(thumbnail) '.$image['alt'].'" 
					title="'.$image['title'].'" 
					width="'.$image['thumb_width'].'" height="'.$image['thumb_height'].'"/>';
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<title>Image Details</title>
		<meta http-equiv="Content-type" content="text/html;charset=iso-8859-1"/>

	<script type="text/javascript" src="<?php echo WW_WEB_ROOT; ?>/ww_edit/_js/jquery.js"></script>
	<script type="text/javascript" src="<?php echo WW_WEB_ROOT; ?>/ww_edit/_js/jquery-ui.js"></script>
	<script type="text/javascript">
	
		// image tabs
	
		$(document).ready(function(){  	
			$(function() {
				$("#image_tabs").tabs();
			});
		});
	
		// full size image popup
		
		$(function(){
		    $('img.image_thumb').click(function(){
				var img = new Image();
				img.src = this.src.replace('/thumbs','');
				w = img.width + 20;
				h = img.height + 20;
				window.open(img.src,'image','width='+w+',height='+h+',location=no,scrollbars=no,toolbar=no,status=no,titlebar=no');
		        return false;
		    });
		});
		
		// refresh image browser
		
		$(function(){
		    $('a.edit_success').click(function(){
				window.opener.location.href = window.opener.location.href;
				if (window.opener.progressWindow) {
					window.opener.progressWindow.close()
				}
				window.close();
		    });
		});
		
		// select image div

		$(function(){
		    $('div.image_caption').click(function(){
			   if (document.selection) 
			   {
			      var range = document.body.createTextRange();
			      range.moveToElementText(this);
			      range.select();
			   }
			   else if (window.getSelection) 
			   {
			      var range = document.createRange();
			      range.selectNode(this);
			      window.getSelection().addRange(range);
			   }

		    });
		});


	
	</script>
	<style type="text/css">

		/* resets */
		
		ul, ol, li {
			margin: 0;
			padding: 0;
			list-style:none;
		}
		h1,h2,h3,h4,h5,h6, 
		form,fieldset,input,select,textarea,p,blockquote {
			margin: 0;
			padding: 0;
		}
		
		/* basics */
		body{
			font-family: Arial, Helvetica, sans-serif;
			font-size: 100%;
			background-color: #f4f4f4;
		}
		h1 {
			font-size: 1em;
			line-height: 1.5em;
		}
		h2 {
			font-size: 1em;
			line-height: 2em;
			color: #666;
		}
		p, ul, ol {
			font-size: 0.75em;  /* 13px */
			line-height: 1.5em;
			margin: 0.75em 0;
		}
		a {
			text-decoration: none;
			color: #660000;
		}
		a:hover {
			color: #990000;
		}
		
		/* page specific styles */
		
		div.image_tab {
			overflow: auto;
			background-color: #fff;
			border: solid 1px #ccc;
			border-top: none;
			padding: 4px 8px;
		}
		
		div.image_caption {
			border: solid 1px #ccc;
			font-size: 0.75em;
			padding: 8px;
			width: auto;
		}
		div.image_caption img {
			clear: both;
			border: solid 1px #333;
		}
		div.image_caption div.title {
			font-weight: bold;
			margin: 4px auto;
		}
		div.image_caption div.caption {
			clear: both;
			margin: 4px auto;
		}
		img.image_thumb {
			border: solid 1px #ccc;
			padding: 4px;
		}
		.left {
			margin-right: 8px;
			padding-top: 2px;
			margin-bottom: 2px;
			float: left;
		}
		.right {
			margin-left: 8px;
			padding-top: 2px;
			margin-bottom: 2px;
			float: right;
		}
		.center {
			margin: 10px auto;
			text-align: center;
			overflow: auto;
		}
		form {
			width: 240px;
			float: left;
			border: solid 1px #eee;
			padding: 4px;
		}
		form label {
			color: #999;
			display: block;
		}
		form input[type="text"]{
			background-color: #f4f4f4;
			border: solid 1px #ccc;
			height: 16px;
			padding: 2px;
		}
		form textarea{
			background-color: #f4f4f4;
			border: solid 1px #ccc;
			padding: 2px;
		}
		form textarea:focus,
		form input:focus{
			background-color: #fff;
			border: solid 1px #666;
		}
		#image_form input[type="submit"]{
			float: right;
		}
		
		/* ------------- tabs styles ----------------*/
		
		ul.ui-tabs-nav {
			border-bottom: solid 1px #ccc;
			padding: 2px 12px;
			font-size: 100%;
			margin-bottom: 0;
		}
		ul.ui-tabs-nav li.ui-state-default {
			display: inline-block;
			/*width: 128px;*/
			font-size: 0.875em;
			font-weight: bold;
		}
		ul.ui-tabs-nav li.ui-state-default a {
			display: inline-block;
			border: solid 1px #ccc;
			border-bottom: none;
			background-color: #f4f4f4;
			margin-bottom: -4px;
			-moz-border-radius: 1px 18px 0px 0px;
			-moz-box-shadow: inset 1px -1px 12px #ddd;
		}
		ul.ui-tabs-nav li.ui-state-default a:hover {
			background-color: #fff;
		}
		ul.ui-tabs-nav li.ui-tabs-selected a,
		ul.ui-tabs-nav li.ui-tabs-selected a:hover {
			border: solid 1px #ddd;
			border-bottom: solid 1px #fbfbfb;
			background-color: #fff;
			outline: none;
			color: #666;
			-moz-box-shadow: none;
		}
		.ui-tabs-panel {
			display: block;
		}
		.ui-tabs-hide {
			display: none;
		}
		#image_tabs ul.ui-tabs-nav li.ui-state-default a {
			padding: 2px 8px;
			width: auto;	
		}	
	</style>
</head>
<body>
<h1>image detail</h1>
<?php

	if(!empty($edit_success)) {
		echo '
		<h2>
			<a href="javascript:void(0);" class="edit_success">
				Your changes have been saved - click here to close this window and refresh the image browser
			</a>
		</h2>
		';
	}

?>

	<div id="image_tabs">
		<ul>
			<li><a href="#tab_left">left</a></li>
			<li><a href="#tab_center">center</a></li>
			<li><a href="#tab_right">right</a></li>
			<li><a href="#tab_thumbnail">thumbnail</a></li>
			<li><a href="#tab_edit">edit</a></li>
		</ul>
		
		<div id="tab_left" class="image_tab">
		
			<h2>image left</h2>
			<div class="image_caption left">
				<?php echo $image_src; ?>
				<div class="title" style="width:<?php echo $image['width']; ?>px;"><?php echo $image['title']; ?></div>
				<div class="caption" style="width:<?php echo $image['width']; ?>px;"><?php echo $image['caption']; ?></div>
			</div>
		
		</div>
		
		<div id="tab_center" class="image_tab">
		
			<h2>image center</h2>
			<div class="image_caption center">
				<?php echo $image_src; ?>
				<div class="title" style="width:<?php echo $image['width']; ?>px;"><?php echo $image['title']; ?></div>
				<div class="caption" style="width:<?php echo $image['width']; ?>px;"><?php echo $image['caption']; ?></div>
			</div>
			
		</div>
		
		<div id="tab_right" class="image_tab">
		
			<h2>image right</h2>
			<div class="image_caption right">
				<?php echo $image_src; ?>
				<div class="title" style="width:<?php echo $image['width']; ?>px;"><?php echo $image['title']; ?></div>
				<div class="caption" style="width:<?php echo $image['width']; ?>px;"><?php echo $image['caption']; ?></div>
			</div>
		
		</div>
		
		<div id="tab_thumbnail" class="image_tab">
		
			<h2>image thumb</h2>
			<?php echo $thumb_src; ?>
			<p>click on the thumbnail to see the full image</p>
			<p>thumbnail size: <?php echo $image['thumb_width'].' x '.$image['thumb_height']; ?></p>
			<form action="<?php echo $url; ?>" method="post" enctype="multipart/form-data" id="thumb_replace_form">
				<?php
					if( (isset($thumb_error)) && (!empty($thumb_error)) ) {
						echo '<p>'.$thumb_error.'</p>';
					}
				?>
				<p>
				<label for="new_thumb">Replace thumbnail:</label>
					<input name="new_thumb" type="file" size="12" id="new_thumb"/>
					<input name="current_thumb" type="hidden" value="<?php echo WW_ROOT.'/ww_files/images/thumbs/'.$image['filename']; ?>"/>
				</p>
				<p>
					<input type="submit" name="replace_thumb" value="replace" />
				</p>
			</form>
		
		</div>
		
		<div id="tab_edit" class="image_tab">
		
			<h2>image edit</h2>
			<form action="<?php echo $url; ?>" method="post" enctype="multipart/form-data" id="image_edit_form">
		
			<p>
			<label for="title">Image title:</label> 
				<input name="title" type="text" value="<?php echo $image['title'];?>" size="28" />
			</p>
			<p>
			<label for="alt">Alt text:</label> 
				<input name="alt" type="text" value="<?php echo $image['alt'];?>" size="28" />
			</p>
			<p>
			<label for="caption">Image caption:</label> 
				<textarea name="caption" cols="24" rows="5"><?php echo $image['caption'];?></textarea>
			</p>
			<p>
			<label for="credit">Image credit:</label> 
				<textarea name="credit" cols="24" rows="2"><?php echo $image['credit'];?></textarea>
			</p>
			<p>	
				<input name="filename" type="hidden" value="<?php echo $image['filename'];?>" />
				<input type="submit" name="submit" class="button" value="Save Changes" />
			</p>
			</form>
			
			<div class="image_caption right">
				<?php echo $image_src; ?>
				<div class="title" style="width:<?php echo $image['width']; ?>px;">
					<?php echo $image['filename']; ?>
				</div>
				<div class="caption" style="width:<?php echo $image['width']; ?>px;">
					<?php echo $image['width'].' x '.$image['height']; ?> &#124;
					Uploaded: <?php echo date('d M Y H:i',strtotime($image['date_uploaded'])); ?>
				</div>
			</div>
				
		</div>
		
	</div>


</body>
</html>