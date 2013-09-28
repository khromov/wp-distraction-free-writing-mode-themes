<select name="dfwmt_selected_theme">
	<?php
	$dir = $v['working_directory'] . '/css/*';
	foreach ( glob( $dir ) as $file ) {
		if ( filetype( $file ) == 'dir' ) {
			?>
			<option value="<?php echo basename( $file ) ?>"<?php echo get_user_option( 'dfwmt_selected_theme' ) == basename( $file ) ? 'selected' : '' ?>/> <?php echo ucfirst( basename( $file ) ) ?></option>
		<?php
		}
	}
	?>
</select>