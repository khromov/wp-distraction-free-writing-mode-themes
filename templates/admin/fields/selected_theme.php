	<input type="radio" name="dfwmt_selected_theme" value="default"<?=get_option('dfwmt_selected_theme')=='default' ? ' checked' : ''?>/> Default WP theme
	<br />
<?php
	$dir = $v['working_directory']. '/css/*';
	foreach(glob($dir) as $file) 
	{
		if(filetype($file)=='dir')
		{
		 	?>
		 		<input type="radio" name="dfwmt_selected_theme" value="<?=basename($file)?>"<?=get_option('dfwmt_selected_theme')==basename($file) ? ' checked' : ''?>/> <?=ucfirst(basename($file))?> - <a href="<?=$v['plugin_url'] .'/css/'. basename($file) .'/preview.png'?>" target="_blank" onclick="return df_popup(this.href)">Preview</a>
		 		<br />
		 	<?php
		}
	}
?>
	<input type="radio" name="dfwmt_selected_theme" value="custom"<?=get_option('dfwmt_selected_theme')=='custom' ? ' checked' : ''?>/> Custom - Enter CSS below