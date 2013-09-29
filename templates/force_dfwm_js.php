<?php if ( ( $v['current_page'] == 'post-new.php' || ( $v['current_page'] == 'post.php' && $v['current_action'] == "edit" ) ) && ( ( get_option( 'dfwmt_force_distraction_free_mode' ) == 1 ) && ( in_array( $v['current_user_role'], get_option( 'dfwmt_distraction_free_mode_roles' ) ) ) ) ) : ?>
	<script type="text/javascript">
		jQuery(document).ready(function () {
			fullscreen.on();
		});
	</script>
<?php endif; ?>