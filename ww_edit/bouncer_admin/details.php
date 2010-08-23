<?php 
// to restrict guest access to this page set the $guest_access variable to match 
// settings stored in the guest_areas field in the database
	$guest_access = 'details'; 
	include('restrict.php'); 

	echo html_header("Detailed test page");

	echo
	"<div id=\"page\">".
	
	html_title(); 
?>
	<div id="content">
	
	<h1>Bouncer - Detailed test page</h1>
	<p>This page lists all test parameters for bouncer.</p>
	<p><a href="?logout">Click here to logout</a></p>
	<p><a href="test.php?embedded">Click here for embedded login test page</a></p>
	<p><a href="test.php">Click here for conventional login test page</a></p>

	<h2>Guest status</h2>
	<?php	if(empty($_SESSION[AD_SESS]['guest'])) {
				echo "<p><b>you are logged in with:</b> full admin rights</p>";
			} else {
				echo '<p><b>you are logged in with:</b> guest rights</p>';
				echo '<p><b>you have access to these guest areas</b>: '.implode(',',$_SESSION[AD_SESS]['guest_areas']).'</p>';
			}
	?>

	<h2>Guest access</h2>
	<?php 	if(!empty($guest_access)) {
				echo "<p>This page will allow only guests with guest_areas set to: <b>".$guest_access."</b> (as well as anyone with full admin rights)</p>";
			} else {
				echo "<p>Guest access has not been set for this page. No guests will be allowed.</p>";
			} 
	?>

	<h2>Test mode</h2>
	<?php 	if(empty($test_mode)) { 
				echo "<p>Test mode is <b>OFF</b></p>";
			} else {
				echo "<p>Test mode is <b>ON</b></p>";
			}
	?>

	<h2>Your details:</h2>
	<?php	if( (empty($_SESSION[AD_SESS]['guest'])) || (in_array($guest_access,$_SESSION[AD_SESS]['guest_areas'])) ) {
				echo "<p><b>session id</b>: ".session_id()."</p>";
				echo "<p><b>your user id</b>: ".$_SESSION[AD_SESS]['user_id']."</p>";
				echo "<p><b>your current IP</b>: ".$_SERVER['REMOTE_ADDR']."</p>";
				echo "<p><b>your user agent</b>: ".$_SERVER['HTTP_USER_AGENT']."</p>";
				
				// check cookies
				if(isset($_COOKIE['ad_c_user'])) {
					echo "<p>email cookie has been set: ".$_COOKIE['c_user']."</p>";
				} else {
					echo "<p>email cookie NOT set</p>";
				}
				if(isset($_COOKIE['ad_c_key'])) {
					echo "<p>passkey cookie has been set</p>";
				} else {
					echo "<p>passkey cookie NOT set</p>";
				}
			
				echo "<h2>all sessions</h2>";
				foreach($_SESSION[AD_SESS] as $key_name => $key_value) {
					echo "<p><b>".$key_name.":</b> ". $key_value."</p>";
				}
				echo "<h2>all cookies</h2>";
				foreach($_COOKIE as $key_name => $key_value) {
					echo "<p><b>".$key_name.":</b> ". $key_value."</p>";
				}
			
			echo "<h2>all user data from database</h2>";
			mysql_select_db($dbase_name, $dbase_conn);
			$query_rsUser = "SELECT * FROM ".AD_USER_TBL." WHERE ".AD_ID_FLD." = ".$_SESSION[AD_SESS]['user_id'];
			$rsUser = @mysql_query($query_rsUser, $dbase_conn) or die(mysql_error());
			$row_rsUser = mysql_fetch_assoc($rsUser);
			$totalRows_rsUser = mysql_num_rows($rsUser);
			foreach($row_rsUser as $key_name => $key_value) {
					echo "<p><b>".$key_name.":</b> ". $key_value."</p>";
				}
			}
	?>

	<h2>Guest Areas test</h2>
	<?php 
		if(empty($_SESSION[AD_SESS]['guest_areas'])) {
			echo "<p>No guest areas set for this user</p>";
		} else {
		
			// simply change $guest_access to test different guest areas
			$guest_access = "details";
			echo (in_array($guest_access,$_SESSION[AD_SESS]['guest_areas'])) ? "<p>you can access <b>".$guest_access."</b></p>" : "<p>no access to <b>".$guest_access."</b></p>"; 
			
			$guest_access = "test";
			echo (in_array($guest_access,$_SESSION[AD_SESS]['guest_areas'])) ? "<p>you can access <b>".$guest_access."</b></p>" : "<p>no access to <b>".$guest_access."</b></p>"; 
		}
	?>
	
	<h2>bouncer_params.php vars</h2>
	<?php	if(empty($_SESSION[AD_SESS]['guest'])) {	
				echo "";
				echo "<p><b>BOUNCE_ROOT:</b>&nbsp;".constant('BOUNCE_ROOT')."</p>";
				echo "<p><b>BOUNCE_HTML_ROOT:</b>&nbsp;".constant('BOUNCE_HTML_ROOT')."</p>";
			} else {
				echo "<p>bouncer_params.php vars only visible to full admin members.</p>";
			}
	?>	
		
	<h2>server vars</h2>
	<?php	if(empty($_SESSION[AD_SESS]['guest'])) {	
				// loop server variables
				echo "";
				foreach($_SERVER as $key_name => $key_value) {
					echo "<p><b>".$key_name.":</b> ". $key_value."</p>";
				}
			} else {
				echo "<p>Server variables only visible to full admin members.</p>";
			}
			
?>
	</div>
</div>
</div>
<?php echo html_footer(); ?>
