<?php

// get user defined settings and functions
	include_once('bouncer_params.php');

// start sessions if needed
	if (!session_id()) session_start();
	
// set target page for redirects

	$target_page = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$target_page = (substr($target_page,0,7) != "http://") ? "http://".$target_page : $target_page;

// if a login is attempted we automatically clear the logged_in session

	if( (isset($_POST['email'])) && (isset($_POST['pass'])) ) {
		$_SESSION[WW_SESS]['logged_in'] = 0;
	}

// we only bother processing the login if the user hasn't already logged in
// this enables us to include login.php on other pages without affecting
// users already logged in


if(empty($_SESSION[WW_SESS]['logged_in'])) {

	// void applicable variables
		$user_email 	= 0; // posted email value
		$user_pass	 	= 0; // posted password value
		$cookie_key 	= 0; // email validated flag
		$password_check	= 0;
		
	// prepare to login with cookies if cookies are set
	
		if( (isset($_COOKIE[$pre.'_c_key'])) && (isset($_COOKIE[$pre.'_c_user'])) ) {

			$user_email = $_COOKIE[$pre.'_c_user'];
			$cookie_key = $_COOKIE[$pre.'_c_key'];

		}
		
	// otherwise check form has been submitted
	
		if( (!empty($_POST['bounce'])) && ($_POST['bounce'] == md5(constant($pre.'BOUNCE_WEB_ROOT'))) ) { 			

			$user_email = $_POST['email'];
			$user_pass = (isset($_POST['pass'])) ? trim($_POST['pass']) : '' ;

		}

	// if email address has been successfully verified then check password

		if( (!empty($user_email)) && (bouncer_verify_email($user_email) == true) ) { 

			// pull user details from database
			$conn = author_connect();
			$query_login = "
					SELECT 
						".constant($pre.'ID').", ".constant($pre.'EMAIL').", 
						".constant($pre.'PASS').", ".constant($pre.'SUB_EXPIRY').", 
						".constant($pre.'GUEST_FLAG').", ".constant($pre.'GUEST_AREAS').",
						".constant($pre.'LAST_LOGIN')."
					FROM ".constant($pre.'USER_TBL')." 
					WHERE ".constant($pre.'EMAIL')." = '".$conn->real_escape_string($user_email)."'";
			$result_login = $conn->query($query_login);
			$total_login = $result_login->num_rows;
			if($total_login == 1) {
				$user_data = array();
				while($row = $result_login->fetch_assoc()) { 
					$user_data = $row;
				}
				$result_login->close();
				// check that stored database password matches POSTed password
				// or that encrypted password in cookie matches
				$password_check = 0;
				$password_db = $user_data[constant($pre.'PASS')];
				if(!empty($user_pass)) {
					$password_check = (strcmp($user_pass,$password_db) == 0) ? 1 : 0 ;
				} elseif(!empty($cookie_key)) {
					$password_check = (strcmp(md5($cipher.$password_db),$cookie_key) == 0) ? 1 : 0 ;
				}	
				// harsh consequences for wrong password	
				if(empty($password_check)) {
					$bouncer_message['error'] = $bouncer_message['wrong_password'];
					setcookie($pre.'_c_user','', time()-1, "/");
					setcookie($pre.'_c_key','', time()-1, "/");
					// reset POSTed values for added security
					$user_email = 0;
					$user_pass 	= 0;
				}
			} else {
				$bouncer_message['error'] = "More than one use with that email address";
			}
		} elseif(!empty($user_email)) {
			$bouncer_message['error'] = $bouncer_message['wrong_email'];
		}
	
	// once email and password are verified we also check whether subscription expiry dates are used
	
		if(!empty($password_check)) {
		
			$user_expiry = $user_data[constant($pre.'SUB_EXPIRY')];
			$expiry_check = ( ($user_expiry == '0000-00-00 00:00:00') || (empty($user_expiry)) ) ? 0 : 1 ;
			$user_expired = $expiry_check; // empty if user has not expired
	
		// if an expiry date does exist (and the password checks out) we then check whether subscription has actually expired
		
			if( (!empty($expiry_check)) && (!empty($password_check)) ) { 
				$current_date = strtotime(date('Y-m-d H:i:s'));
				$expiry_ts = strtotime($user_expiry);
				if($current_date > $expiry_ts) {
					// subscription has expired - reset cookies
					setcookie($pre.'_c_user','', time()-1, "/");
					setcookie($pre.'_c_key','', time()-1, "/");
					$user_expired = 1; 
				} else {
					// subscription has not expired
					$user_expired = 0; 	
				}
			}
		}
	
	// finally log in user, update database and set sessions...
	// note that a user is still logged in even if subscription has expired
	// as the restrict.php page will block expired users from accessing restricted pages

		if ( (!empty($password_check)) && (empty($user_expired)) ) {

		// set up variables to store in sessions
		
			$login_flag 	= 1; 
			$current_date 	= date('Y-m-d H:i:s');
			$current_ip 	= $_SERVER['REMOTE_ADDR'];
			$agent 			= $_SERVER['HTTP_USER_AGENT'];
			$bounce 		= md5($cipher.$agent);
		
		// set sessions
		
			session_regenerate_id();
			$last_sess = session_id();
			$_SESSION[WW_SESS]['expired'] = $user_expired;
			$_SESSION[WW_SESS]['user_id'] = $user_data[constant($pre.'ID')];
			$_SESSION[WW_SESS]['bounce'] 	= $bounce;
			$_SESSION[WW_SESS]['guest'] 	= $user_data[constant($pre.'GUEST_FLAG')];
			// get last login before we update the database
			$_SESSION[WW_SESS]['last_login'] = $user_data[constant($pre.'LAST_LOGIN')];
			
		// set up guest areas array

			if(!empty($user_data[constant($pre.'GUEST_AREAS')])) {
				$guestareas = array();
				$guestareas = explode(',',$user_data[constant($pre.'GUEST_AREAS')]);
				$_SESSION[WW_SESS]['guest_areas'] = $guestareas;
			} else {
				$_SESSION[WW_SESS]['guest_areas'] = 0;
			}
			
		// update database table
			$conn = author_connect();
			$update_user = "UPDATE ".constant($pre.'USER_TBL')." 
								SET ".constant($pre.'LAST_LOGIN')." = '".$current_date."',
								".constant($pre.'LAST_IP')." = '".$current_ip."',
								".constant($pre.'LAST_SESS')." = '".$last_sess."' 
							WHERE ".constant($pre.'ID')." = ".(int)$user_data[constant($pre.'ID')];
			$conn->query($update_user) or die($conn->error);
			
		// finally set cookies if user wants to be 'remembered'
		
			if(!empty($_POST['remember'])) {
				if(empty($user_expired)) { // if subscription has not expired
					if(!empty($expiry_check)) { 
					// if expiry dates are used then set cookies to expire when subscription expires
						$time = strtotime($user_expiry);
					} else {
					// otherwise set cookie expiry to 30 days by default
						$time = time() + 60*60*24*30; 
					}
				} else {
					// pre-expire cookies if subscription has already expired
					$time = time()-1; 
				}
				// create the cookies	
				$key = md5($cipher.$password_db);
				setcookie($pre.'_c_key', $key, $time, "/");
				setcookie($pre.'_c_user', $user_email, $time, "/");
			}
		
			// set login session and send user back to page
			unset($_POST['email']);
			unset($_POST['pass']);
			$_SESSION[WW_SESS]['logged_in'] = $login_flag;
			header('Location: '.$target_page);
			exit();
			
			// ... or not....
		} else {
			$login_flag = 0; // login_flag is empty if user not found
			if(isset($_SESSION[WW_SESS])) {
				unset($_SESSION[WW_SESS]['logged_in']);
			}
			session_destroy();
			// reset cookies
			setcookie($pre.'_c_key','', time()-1, "/");
			setcookie($pre.'_c_user','', time()-1, "/");
		}
}	
?>