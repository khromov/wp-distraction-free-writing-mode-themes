<?php if ( ( $v['current_page'] == 'post-new.php' || ( $v['current_page'] == 'post.php' && $v['current_action'] == "edit" ) ) && get_option( 'dfwmt_force_distraction_free_mode' ) == 1 ) : ?>
<script type="text/javascript">
	jQuery(document).ready(function () {
		fullscreen.on();
	});
</script>
<?php endif; ?>