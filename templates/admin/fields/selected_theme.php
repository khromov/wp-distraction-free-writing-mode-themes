	<input type="radio" name="dfwmt_selected_theme" value="default"<?php echo get_option('dfwmt_selected_theme')=='default' ? ' checked' : ''?>/> <?php _e('Default WP theme','dfwmdt');?>
	<br />
<?php
	$dir = $v['working_directory']. '/css/*';
	foreach(glob($dir) as $file) 
	{
		if(filetype($file)=='dir')
		{
		 	?>
		 		<input type="radio" name="dfwmt_selected_theme" value="<?php echo basename($file)?>"<?php echo get_option('dfwmt_selected_theme')==basename($file) ? ' checked' : ''?>/> <?php echo ucfirst(basename($file))?> - <a href="<?php echo $v['plugin_url'] .'/css/'. basename($file) .'/preview.png'?>" target="_blank" onclick="return df_popup(this.href)"><?php _e('Preview','dfwmdt');?></a>
		 		<br />
		 	<?php
		}
	}
?>
	<input type="radio" name="dfwmt_selected_theme" value="custom"<?php echo get_option('dfwmt_selected_theme')=='custom' ? ' checked' : ''?>/> <?php _e('Custom - Enter CSS below','dfwmdt');?>