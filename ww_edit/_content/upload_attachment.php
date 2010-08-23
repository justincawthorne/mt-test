<?php
	session_start();

	/*	check author session 
	
	 	the author id and session_id are sent via post params to the popup
	 	
		using the author id we derive the cookie name - then we can check if the 
		cookie value matches the session id that was sent via the form	 	
	*/
	
	$author_id = (isset($_POST['current_author_id'])) ? (int)$_POST['current_author_id'] : '' ;
	$sess_val = (isset($_POST['current_author_sess'])) ? htmlspecialchars_decode($_POST['current_author_sess']) : '' ;
	
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
	
// now bring in our functions	
	
	include_once(WW_ROOT.'/ww_config/model_functions.php');
	include_once(WW_ROOT.'/ww_config/author_controller_functions.php');
	include_once(WW_ROOT.'/ww_config/author_view_functions.php');
		
// process file upload

	if(isset($_FILES['attachment_file'])) {
	
		if($_FILES['attachment_file']['size'] > 0) {
			
			if(empty($_FILES['attachment_file']['error'])) {
				
				$_POST['author_id'] = $author_id;
				$_POST['title'] 	= $_FILES['attachment_file']['name'];
				$_POST['summary'] 	= '';
				$upload_status = insert_attachment();
				
			} else {
				
				$upload_status = '<p>File upload error: '.$_FILES['attachment_file']['error'].'</p>';	
					
			}	
			
		} else {
			
			$upload_status = '<p>No file (or empty file) uploaded</p>';
			
		}
		
	} else {
		
		$upload_status = '<p>Attachment file not set</p>';
		
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
	<script type="text/javascript" src="<?php echo WW_WEB_ROOT; ?>/ww_edit/_js/jquery.js"></script>
	</head>
<body>
<?php
// report on status of upload - successful if an integer (the new database id) is sent
	
	if(isset($upload_status)) { 
		
		if(is_int($upload_status)) {
			
			echo '<p>Upload was successful - you can now close this window</p>';
			?>
			<script type="text/javascript">
				// get selected value
				var select_val = <?=$upload_status ?>;
				var select_text = '<?=$_FILES['attachment_file']['name'] ?>';
				// add item as a checkbox
				var new_attachment = '<li><label for="attachments[' + select_val + ']" class="checked" >';
				new_attachment += '<input type="checkbox" name="attachments[' + select_val + ']" ';
				new_attachment += 'id="attachments[' + select_val + ']" value="' + select_val + '" checked="checked"/>';
				new_attachment += select_text + '</label></li>';
				// add new file to attachments list
				// alert(new_attachment);
				window.opener.$('ul#attachments').append(new_attachment);
				window.opener.$('input[name=no_attachments]').hide();
			    // clear upload form values
			    window.opener.$('input[name=attachment_file]').val('');
				// close window
				window.close(); 
			</script>
			<?php

		} else {

			echo '<p>'.$upload_status.'</p>';

		}
	} 
?>
</body>
</html>