<table class="form-table">
	<tr>
		<th><label for="dfwmt_selected_theme"><?php _e( 'Distraction Free Theme', 'dfwmdt' ); ?></label></th>

		<td>
			<?php echo $microtemplate->t( 'user/fields/user_theme_field', array( 'working_directory' => dirname( $v['plugin_path'] ), 'plugin_url' => plugins_url( '', $v['plugin_path'] ) ) ); ?>
		</td>
	</tr>
</table>