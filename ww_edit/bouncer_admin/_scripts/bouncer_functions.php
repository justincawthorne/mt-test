<?php
/**
 * verify_email
 * 
 * takes the posted email address and securely
 * checks it against emails currently stored in the database
 * returns true or false
 *
 * @param 	string 	$email
 * @return 	bool 	1/0
 */
	
function bouncer_verify_email($email) {
	
	global $pre;
	
	if(empty($email)) {
		return false;
	}
	
	$conn = author_connect();

	// grab existing list of email addresses
	$query = "SELECT ".constant($pre.'EMAIL')." as emails
				FROM ".constant($pre.'USER_TBL');
	$result = $conn->query($query);
	$data = array();
	// create array of existing email addresses
	while($row = $result->fetch_assoc()) { 
		$data[] = strtolower($row['emails']);
	}
	$result->close();
	// check posted email address exists - if not bounce user immediately
	$email = strtolower($email);
	if(in_array($email, $data)) {
		return true;
	} else {
		// reset cookies
		setcookie($pre.'_c_user','', time()-1, "/");
		setcookie($pre.'_c_key','', time()-1, "/");
		// generate error message
		return false;
	}
}


/**
 * send_password
 * 
 * takes a user's email address and sends them their password
 * for password reminder service
 *
 * @param 	string	$email
 * @return 	string	$message - error/success message
 */

	function send_password($email) {
		global $dbase_conn;
		global $use_mysqli;
		// grab user from database
		$query_rsEmailUser = "SELECT ".AD_EMAIL_FLD.", ".AD_PASS_FLD." FROM ".AD_USER_TBL." WHERE ".AD_EMAIL_FLD." = '$email'";
		if($use_mysqli == false) {
			$rsEmailUser = @mysql_query($query_rsEmailUser, $dbase_conn) or die();
			$row_rsEmailUser = mysql_fetch_assoc($rsEmailUser);
			$totalRows_rsEmailUser = mysql_num_rows($rsEmailUser);
			mysql_free_result($rsEmailUser); 
		} elseif($use_mysqli == true) {
			$rsEmailUser = @$dbase_conn->query($query_rsEmailUser) or die();
			$row_rsEmailUser = mysqli_fetch_assoc($rsEmailUser);
			$totalRows_rsEmailUser = mysqli_num_rows($rsEmailUser);
			mysqli_free_result($rsEmailUser); 
		}
		switch ($totalRows_rsEmailUser) {
			case 0:		// no users are found
				$message = "We didn't find a user with the email address ".$email;
				break;
			case 1: 	// exactly one user is found
				$user_email = $row_rsEmailUser[AD_EMAIL_FLD];
				$subject = AD_SITE_NAME." login details";
				$pass = $row_rsEmailUser[AD_PASS_FLD];
	    		$message = "Your password for the ".AD_SITE_NAME." website is:<br/><br/>".$pass;
	    		$headers = 	"From: ".AD_ADMIN_EMAIL."\n".
							"X-Mailer: PHP/" . phpversion() . "\n" .
							"Content-Type: text/html; charset=utf-8\n" .
							"Content-Transfer-Encoding: 8bit\n\n";
	    		if(mail($user_email, $subject, $message, $headers, "-f".AD_ADMIN_EMAIL."")) { 
	    			$message = "Your login details have been emailed to: ".$user_email.".";
					} else {
					$message = "There was a problem sending the email.";
				}
				break;
				
			default:	// more than one user is found
				$message = "There seems to be more than one user with your email address. 
							For security reasons we have not sent a password.";
				break;
		}
		return $message;
	}


	function change_password($user_id, $newpass) {
		global $dbase_conn;
		global $use_mysqli;
		// build queries
			$update_email = "	UPDATE ".AD_USER_TBL." 
								SET ".AD_PASS_FLD." = '".$newpass."' 
								WHERE ".AD_ID_FLD." = ".$user_id;
			$confirm_email = "	SELECT ".AD_EMAIL_FLD.",".AD_PASS_FLD." 
								FROM ".AD_USER_TBL." 
								WHERE ".AD_ID_FLD." = ".$user_id;
		// update database table
			if($use_mysqli == false) {
				// change pass
				@mysql_query($update_email, $dbase_conn) or die();
				// get new details
				$rsConfirmPass = @mysql_query($confirm_email, $dbase_conn) or die();
				$row_rsConfirmPass = mysql_fetch_assoc($rsConfirmPass);
				$totalRows_rsConfirmPass = mysql_num_rows($rsConfirmPass);
			} elseif($use_mysqli == true) {
				// change pass
				@$dbase_conn->query($update_email) or die();
				// get new details
				$rsConfirmPass = @$dbase_conn->query($confirm_email, $dbase_conn) or die();
				$row_rsConfirmPass = mysqli_fetch_assoc($rsConfirmPass);
				$totalRows_rsConfirmPass = mysqli_num_rows($rsConfirmPass);				
			}

		// email new password to user
			$user_email = $row_rsConfirmPass[AD_EMAIL_FLD];
			$subject = AD_SITE_NAME." - password changed";
			$pass = $row_rsConfirmPass[AD_PASS_FLD];
			$message = "Your password for the ".AD_SITE_NAME." website has been changed to:<br><br>".$pass;
			$headers = 	"From: ".AD_ADMIN_EMAIL."\n".
						"X-Mailer: PHP/" . phpversion() . "\n" .
						"Content-Type: text/html; charset=utf-8\n" .
						"Content-Transfer-Encoding: 8bit\n\n";
			if(mail($user_email, $subject, $message, $headers, "-f".AD_ADMIN_EMAIL."")) {
				$loginmessage = "Your password has been changed. You will need to login again.";
			} else {
			// message your password has been changed
				$loginmessage = "Your password has been changed. You will need to login again. 
				Unfortunately we were unable to send a confirmation email.";
			}
			unset($_SESSION[WW_SESS]['logged_in']);
			return $loginmessage;
	}
	

/**
 * html_header
 * 
 * generates an html header section for bouncer pages
 * optional parameter gives a title to the page
 *
 * @param string $page_name
 * @return $header
 */

	function html_header($page_name = "Bouncer page") {
		
		global $pre;
		
		$header = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
		 "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
			<head>
			<title>'.$page_name.' for '.constant($pre.'SITE_NAME').'</title>
			<link href="'.constant($pre.'BOUNCE_WEB_ROOT').'/_css/bouncer.css" rel="stylesheet" type="text/css" />';
		if(stripos($_SERVER['HTTP_USER_AGENT'],"iphone")!== false) {
			$header .= '
			<meta name="viewport" content="width=device-width" />';
		}
		$header .= ' 	
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		</head>
		<body>
		';
		return $header;
	}


/**
 * html_title
 * 
 * generates title bar - checks for logo
 *
 * 
 * @param	string	$header_image	name of header image file (in _css folder - e.g. 'header.jpg'))
 * @return $title - html for titlebar
 */

	function html_title($header_image = 0) {
		$title = "
		<div id=\"header\">";
		if( (!empty($header_image)) && (file_exists(AD_BOUNCE_ROOT.'/_css/'.$header_image)) ) {
			$title .= "
			<a href=\"".constant($pre.'BOUNCE_WEB_ROOT')."\">
			<img src=\"".constant($pre.'BOUNCE_WEB_ROOT')."/_css/".$header_image."\" alt=\"site logo\" />
			</a>\n";
		} else {
			$title .= "
			<h1>".AD_SITE_NAME."</h1>\n";
		}
		$title .=
		"</div>\n\n";
		return $title;
	}

/**
 * html_footer
 * 
 * generates an html footer section for bouncer pages
 *
 * @return $footer
 */

	function html_footer() {
		$footer = '
		<div id="footer">
			<p>Security provided by Bouncer :: an <a href="http://www.evilchicken.biz/">evil
		    chicken</a> production</p>
		</div>
		</body>
		</html>
		';
		return $footer;
	}

/**
 * mini_login
 * 
 * generates html for the embedded login form
 *
 * @return $mini-login - generated html
 */

	function mini_login() {
		
		global $bouncer_message;
		
		$target = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$target = (substr($target,0,7) != "http://") ? "http://".$target : $target;
		
		// html follows
		if(isset($bouncer_message['error'])) {
			$mini_login =  "
			<p>".$bouncer_message['error']."</p>";
		}
		$mini_login .= 
		'
		<form id="bouncer_miniloginform" method="post" action="'.$target_page.'">
			
			<p>
				<label for="email">Email address:</label>
				<input name="email" type="text" id="email" class="email" />
			</p>
			<p>
				<label for="pass">Password:</label>
				<input name="pass" id="pass" type="password" class="pass" />
			</p>
			<p><label for="remember">Remember Me:</label>
				<input name="remember" class="remember" type="checkbox" id="remember" />
				<input type="hidden" name="bounce" value="'.md5(constant($pre.'BOUNCE_WEB_ROOT')).'" />
			</p>
			<p>
				<input type="submit" name="Submit" value="Login" />
			</p>
			<p>
				<a href="'.constant($pre.'BOUNCE_WEB_ROOT').'/pass_form.php?action=reminder">Forgotten your password? Click here.</a>
			</p>
			
		</form>';
		return $mini_login;
	}

/**
 * login_form
 * 
 * generates complete login form page
 *
 * @return $login_form - complete html code
 */
	
	function login_form() {
		
		global $bouncer_message;
		global $bouncer_page;
		// global $target_page;
		global $pre;
		
		$target = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$target = (substr($target,0,7) != "http://") ? "http://".$target : $target;

		// put together html
		$login_form = html_header("Login Page");
		$login_form .= "
		<div class=\"page_clear\"></div>
		<div id=\"page\">
		".html_title()."
			<div id=\"content\">";
			if(isset($bouncer_message['error'])) {
				$login_form .=  "
				<div id=\"login_message\">".$bouncer_message['error']."</div>";
			}	
			if(!empty($bouncer_message['general'])) {
				$login_form .=  "
				<div id=\"general_message\">".$bouncer_message['general']."</div>";
			}	
			$login_form .=  '			
				<h2>Please enter your email address and password to login</h2>
				
				<form id="bouncer_loginform" method="post" action="'.$target.'">
				
				<p><label for="email">Email address:</label><br/>
					<input name="email" type="email" id="email" /></p>
				
				<p><label for="pass">Password:</label><br/>
					<input name="pass" id="pass" type="password" /></p>
				
				<p><label for="remember">Remember Me:</label>
					<input name="remember" type="checkbox" id="remember" /></p>
				
				<p><input type="hidden" name="bounce" value="'.md5(constant($pre.'BOUNCE_WEB_ROOT')).'" />
					<input type="submit" name="login" value="Login" /></p>
				</form>
				
				<h2>
				<a href="'.$bouncer_page['password_email'].'">Forgotten your password?</a>
				</h2>
		
			</div>
		</div>
		';
		$login_form .= html_footer();
		return $login_form;
	}
	

/**
 * logout
 * 
 * function logs user out and clears all sessions and cookies
 * see restrict.php for usage instructions
 *
 */
	
	function logout() {
		global $pre;
		if (!session_id()) session_start();
		unset($_SESSION[WW_SESS]['logged_in']);
		unset($_SESSION[WW_SESS]['guest']);
		unset($_SESSION[WW_SESS]['guest_areas']);
		unset($_SESSION[WW_SESS]['user_id']);
		session_regenerate_id();
		session_destroy();
		setcookie($pre."c_user",'', time(), "/");
		setcookie($pre."c_key",'', time(), "/");
		header('Location: '.WW_REAL_WEB_ROOT.'/admin/');
		exit;
	}
?>