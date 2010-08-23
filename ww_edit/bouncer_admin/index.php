<?php
include_once('_scripts/bouncer_params.php');

echo html_header("Bouncer index page");

echo
	"<div id=\"page\">".
	
	html_title(); 
?>
		<div id="content">
		<h1>Welcome to Bouncer</h1>
		<h2>You can test your bouncer installation with the following pages:</h2>
		<ul>
		<li><strong><a href="test.php?embedded">Embedded login</a></strong>: This page tests the 'mini login' option. Only a section of the page is restricted. User will have to log in to view the restricted content.</li>
		<li><strong><a href="test.php">Conventional login</a></strong>: The entire page is restricted. User will be required to log in before viewing the page.</li>
		</ul>
		</div>
	</div>
<?php echo html_footer(); ?>
