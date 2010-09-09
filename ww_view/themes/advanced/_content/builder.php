<?php
	
	$config['site']['doctype'] = 'html5';
	
	$head_content = '';
	show_head($head_content, $config);

?>
<body>

	<div id="page_wrapper">
	
	<?php echo insert_header($config['site']['title'],$config['site']['subtitle']); ?> 
	
	<div id="content_wrapper">
	
		<h1>my builder file!</h1>
		<?php
		echo insert_main_content($body_content['main']);
		?>
		
	</div>
	
	</div>
<div id="footer">this is the footer</div>	
</body>
</html>