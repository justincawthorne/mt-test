<?php 
// to restrict guest access to this page set the $guest_access variable to match 
// settings stored in the guest_areas field in the database
// $guest_access = 'pr'; 

	$embedded_login = (isset($_GET['embedded'])) ? 1 : 0 ;
	$login_type = (isset($_GET['embedded'])) ? "Embedded" : "Conventional" ;
	include('restrict.php');

	echo html_header($login_type.' login test page'); ?>

<div id="page">
	<?php echo html_title(); ?>
	
	<div id="content">
	<h1>Bouncer - <?php echo $login_type; ?> login test page</h1>
	
		<p>Unrestricted content - viewable by all users</p>
		<?php 
			if(!empty($hidecontent)) {
				//echo $mini_login;
				echo mini_login();
			} else {
			// this is the restricted content
			?>
			<p><b>This is the restricted content.</b></p>
			<!--<p><a href="_scripts/logout.php">Click here to logout</a></p>-->
			<p><a href="?logout">Click here to logout</a></p>
			<p><a href="test.php">Click here for conventional login test page</a></p>
			<p><a href="test.php?embedded">Click here for embedded login test page</a></p>
			<p><a href="details.php">Click here for detailed test page</a></p>
		<?php } ?>
	
		<p>Unrestricted content - viewable by all users</p>
		
	</div>
</div>
<?php echo html_footer(); ?>
