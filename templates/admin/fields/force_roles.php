<?php
global $wp_roles;
$roles = $wp_roles->get_names();
$force_roles = get_option( 'dfwmt_distraction_free_mode_roles' );
if ( ! is_array( $force_roles ) ) $force_roles = array();

foreach ( $roles as $role => $name ): ?>
	<input type="checkbox" <?php if ( in_array( $role, $force_roles ) ) echo "checked"; ?>  name="dfwmt_distraction_free_mode_roles[]" value="<?php echo $role; ?>" /> <?php echo translate_user_role( $name ); ?>
	<br />
<?php endforeach; ?>