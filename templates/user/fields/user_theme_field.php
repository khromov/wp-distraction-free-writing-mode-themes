<?php
	$current_user_theme_option = get_user_option( 'dfwmt_selected_theme' );
	$dir = $v['working_directory'] . '/css/*';
?>
<select name="dfwmt_selected_theme">

	<!-- Add not selected option for users that haven't set their preference -->
	<option value="none"<?php echo ($current_user_theme_option === false || $current_user_theme_option == 'none') ? ' selected' : ''; ?>><?php _e('- Admin default -', 'dfwmdt'); ?></option>

	<!-- Add WordPress default theme option -->
	<option value="default" <?php echo ($current_user_theme_option === 'default') ? ' selected' : ''; ?>> <?php _e('- WordPress default -', 'dfwmdt'); ?></option>

	<?php
	foreach ( glob( $dir ) as $file ) {
		if ( filetype( $file ) == 'dir' ) {
			?>
			<option value="<?php echo basename( $file ) ?>"<?php echo get_user_option( 'dfwmt_selected_theme' ) == basename( $file ) ? ' selected' : '' ?>> <?php echo ucfirst( basename( $file ) ) ?></option>
		<?php
		}
	}
	?>
</select>