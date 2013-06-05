<?php
	header("Content-type: text/css");
	
	//Include wp-load.php
	include('../../../../wp-load.php');
	
	echo get_option('dfwmt_custom_theme_css');