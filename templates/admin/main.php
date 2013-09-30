<div class="wrap">
	<?php screen_icon(); ?>
	<h2>
		<?php _e( 'Distraction Free Writing mode Themes Configuration', 'dfwmdt' ); ?>
	</h2>

	<div class="info" style="padding-top: 10px;">
		<?php _e( 'From this screen you can control the global settings for this plugin. If you have multiple users on your blog, they can set their own preferences in their', 'dfwmdt' ); ?>
		<a href="<?php echo admin_url('profile.php'); ?>"><?php _e('profile settings', 'dfwmdt'); ?></a>
	</div>

	<form method="post" action="options.php">
		<?php
		settings_fields( 'dfwmdt-group' );
		do_settings_sections( 'dfwmdt' );
		submit_button();
		?>
	</form>
</div>