<?php

// reminder or change
	
	if (isset($_GET['action'])) {
		$action = (get_magic_quotes_gpc()) ? $_GET['action'] : addslashes($_GET['action']);
	}
	
	$allowed_actions = array('reminder','change');
	
	// set target page for redirects
	$target = (!empty($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING'] ;
	
	$loginmessage = "";
	
	switch ($action) {
			
		case "reminder":
			include_once('_scripts/bouncer_params.php');
			include_once('_scripts/bouncer_functions.php');
			// process reminder
			if(!empty($_POST['p_email'])) {
				$p_email = (get_magic_quotes_gpc()) ? $_POST['p_email'] : addslashes($_POST['p_email']);
				$target = $_POST['target'];
				$loginmessage = send_password($p_email); // close if totalrows
			}
			// text variables
			$pass_title = "Password Reminder Service";
			$pass_intro = 
			"<p>To return to the previous page <a href=\"".$target."\">click here</a></p>
			 <p>Enter the email address you registered with and your password will be sent to you.</p>";
			// show reminder form
			$pass_form = "
				<form id=\"emailreminder\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."\">
					
					<p><label for=\"p_email\">Email address:</label></p>
					<p><input name=\"p_email\" type=\"text\" id=\"p_email\" /></p>
					<p>
						<input type=\"hidden\" name=\"target\" value=\"".$target."\" />
						<input type=\"submit\" name=\"submit\" value=\"Send Password\" />
					</p>
				</form>
			";
		break;
			
		case "change";
			include('restrict.php');
			// process change
			if(!empty($_POST['change'])) {
								
				if (isset($_POST['currentpass'])) {
					$currentpass = (get_magic_quotes_gpc()) ? $_POST['currentpass'] : addslashes($_POST['currentpass']);
				} else {
					$error = "You must enter your current password";
				}
				if (isset($_POST['newpass'])) {
					$newpass = (get_magic_quotes_gpc()) ? $_POST['newpass'] : addslashes($_POST['newpass']);
				} else {
					$error = "You must enter your new password";
				}
				if (isset($_POST['newpass2'])) {
					$newpass2 = (get_magic_quotes_gpc()) ? $_POST['newpass2'] : addslashes($_POST['newpass2']);
				} else {
					$error = "You didn't re-enter your new password";
				}
				// check new password matches in both fields
				if($newpass != $newpass2) {
					$error = "Your new password doesn't match. Please try again.";
				}
				if( (strlen($newpass) > 10) || (strlen($newpass) < 6)) {
					$error = "Your password must be between 6 and 10 characters long.";
				}
				if(empty($error)) {
					// check existing password in database
					$query_rsCheckPass = "SELECT ".PASS_FLD." FROM ".USER_TBL." WHERE ".ID_FLD." = ".$_SESSION['user_id'];
					if($use_mysqli == false) {
						$rsCheckPass = @mysql_query($query_rsCheckPass, $dbase_conn) or die();
						$row_rsCheckPass = mysql_fetch_assoc($rsCheckPass);
						$totalRows_rsCheckPass = mysql_num_rows($rsCheckPass);
					} elseif($use_mysqli == true) {
						$rsCheckPass = @$dbase_conn->query($query_rsCheckPass) or die();
						$row_rsCheckPass = mysqli_fetch_assoc($rsCheckPass);
						$totalRows_rsCheckPass = mysqli_num_rows($rsCheckPass);	
					}

					// does posted password match current password
					if($currentpass != $row_rsCheckPass[PASS_FLD]) {
						$error = "Your current password was entered incorrectly.";
					}
					// if there are no users, or more than one, then return an error
					if($totalRows_rsCheckPass != 1) {
						$error = "Your current password was entered incorrectly.";
					}
				}
				if(empty($error)) {
					$loginmessage = change_password($_SESSION['user_id'],$newpass);
				} else {
					$loginmessage = $error;
				}
			}
			// text variables
			$pass_title = "Change Password Service";
			$pass_intro = "
			<p>Please complete this form to change your password. Once your password has been changed successfully you will need to log in again.</p>
			<p><strong>NOTE: Your new password must be between 6 - 10 characters.</strong></p>";
			$pass_form = "
			<form name=\"changepass\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."\">
				<p><label for=\"currentpass\">Current Password:</label><br/>
					<input name=\"currentpass\" type=\"password\" id=\"currentpass\" class=\"loginpass\"></p>
				<p><label for=\"newpass\">New Password:</label><br/>
					<input name=\"newpass\" id=\"newpass\" type=\"password\" class=\"loginpass\"></p>
				<p><label for=\"newpass2\">Re-enter New Password:</label><br/>
					<input name=\"newpass2\" type=\"password\" id=\"newpass2\" class=\"loginpass\"></p>
				<p><input type=\"submit\" class=\"button\" name=\"change\" value=\"Change\"></p>
				</form>
			";
		break;	
		
		default:
			echo "not allowed";
		break;
	}
	
// show html page framework

	echo html_header($pass_title);

	echo
	"<div id=\"page\">".
	
	html_title().
	
		"<div id=\"content\">
		
		<h1>".$pass_title."</h1>
		
		<p class=\"loginmessage\">".$loginmessage."</p>

		".$pass_intro.
		
			$pass_form.

		"<p>If you experience any problems with this form please email ".AD_ADMIN_EMAIL."</p>

		</div>
	</div>".

	html_footer();

?>