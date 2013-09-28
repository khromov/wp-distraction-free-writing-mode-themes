<?php
/*
Plugin Name: Distraction Free Writing mode Themes
Plugin URI: http://wordpress.org/extend/plugins/distraction-free-writing-mode-themes/
Description: Provides dark and light themes for for Distraction Free Writing mode. Use one of the beautiful built-in themes or write your own.
Version: 2.2-beta
License: GPL2
Author: khromov
Author URI: http://khromov.wordpress.com
License: GPL2
Text Domain: dfwmdt
Domain Path: /languages/
*/

$main = new DFWMDT();

class DFWMDT {
	public $template;
	const text_domain = "dfwmdt";

	function __construct() {
		/**
		 * Register hooks, languages etc
		 **/
		register_activation_hook( __FILE__, array( 'DFWMDT', 'activate' ) );
		load_plugin_textdomain( self::text_domain, false, basename( dirname( __FILE__ ) ) . '/languages' );

		/**
		 * http://wordpress.org/support/topic/add_editor_style-for-plugin?replies=3
		 */
		add_filter( 'mce_css', array( &$this, 'filter_mce_css' ) );
		add_action( 'admin_print_styles-post.php', array( &$this, 'dfw_terminal_style' ) );
		add_action( 'admin_print_styles-post-new.php', array( &$this, 'dfw_terminal_style' ) );

		/**
		 * Menu stuff
		 **/
		//TODO: Should this be wrapped in is_admin(), check what other user classes can do.
		add_action( 'admin_menu', array( &$this, 'register_admin_menus' ) );
		add_action( 'admin_init', array( &$this, 'register_settings' ) );
		add_action( 'admin_footer', array( &$this, 'force_distraction_free_mode' ) );


		add_action( 'show_user_profile', array( &$this, 'dfwmt_user_theme_selection' ) );
		add_action( 'edit_user_profile', array( &$this, 'dfwmt_user_theme_selection' ) );

		add_action( 'personal_options_update', array( &$this, 'dfwmt_save_user_theme_selection' ) );
		add_action( 'edit_user_profile_update', array( &$this, 'dfwmt_save_user_theme_selection' ) );

		/**
		 * Include libs and dependencies
		 **/
		if ( ! class_exists( 'MicroTemplate' ) && ! class_exists( 'MT' ) )
			include( 'lib/microtemplate.class.php' );

		//Register template directory by getting directory of current file
		$this->template = new MicroTemplate( dirname( __FILE__ ) . '/templates/' );
	}

	function force_distraction_free_mode() {
		global $pagenow;
		if ( ( $pagenow == 'post-new.php' || ( $pagenow == 'post.php' && $_REQUEST['action'] == "edit" ) ) && get_option( 'dfwmt_force_distraction_free_mode' ) == 1 ) {
			?>
			<script type="text/javascript">
				jQuery(document).ready(function () {
					fullscreen.on();
				});
			</script>
		<?php
		}
	}


	function register_admin_menus() {
		add_submenu_page( 'options-general.php', __( "Distraction Free Writing mode Themes Configuration", self::text_domain ), __( "DFWM Themes", self::text_domain ), 'manage_options', 'dfwmdt', array( &$this, 'admin_main' ) );
	}

	function register_settings() {
		register_setting( 'dfwmdt-group', 'dfwmt_selected_theme', array( &$this, 'sanitize_selected_theme' ) );
		register_setting( 'dfwmdt-group', 'dfwmt_force_distraction_free_mode', array( &$this, 'sanitize_distraction_free_mode' ) );
		register_setting( 'dfwmdt-group', 'dfwmt_custom_theme_css', array( &$this, 'sanitize_custom_theme_css' ) );

		add_settings_section( 'dfwmdt-main', __( 'Main configuration', self::text_domain ), array( &$this, 'admin_main_part' ), 'dfwmdt' );

		add_settings_field( 'dfwmt_selected_theme', __( 'Selected theme', self::text_domain ), array( &$this, 'field_selected_theme' ), 'dfwmdt', 'dfwmdt-main' );
		add_settings_field( 'dfwmt_force_distraction_free_mode', __( 'Force Distraction Free Writing mode', self::text_domain ), array( &$this, 'distraction_free_field' ), 'dfwmdt', 'dfwmdt-main' );
		add_settings_field( 'dfwmt_custom_theme_css', __( 'Custom CSS', self::text_domain ), array( &$this, 'field_custom_theme_css' ), 'dfwmdt', 'dfwmdt-main' );
	}

	function admin_main() {
		echo $this->template->t( 'admin/js-popup' );
		echo $this->template->t( 'admin/main' );
		echo $this->template->t( 'admin/footer' );
	}

	function distraction_free_field() {
		?>
		<input type="checkbox" name="dfwmt_force_distraction_free_mode" value="1" <?php checked( get_option( 'dfwmt_force_distraction_free_mode' ), 1 ); ?> />
		<label><?php _e( 'Yes', self::text_domain ); ?></label>
	<?php
	}

	function field_selected_theme() {
		echo $this->template->t( 'admin/fields/selected_theme', array( 'working_directory' => dirname( __FILE__ ), 'plugin_url' => plugins_url( '', __FILE__ ) ) );
	}

	function field_custom_theme_css() {
		echo $this->template->t( 'admin/fields/custom_theme_css' );
	}

	/** Sanitization **/
	function sanitize_selected_theme( $in ) {
		//Perhaps not correct validator
		if ( strlen( $in ) == 0 ) {
			return "";
		}
		else if ( preg_match( "/^[a-zA-Z0-9_\-]+$/", $in ) ) {
			return $in;
		}
		else {
			die( __( 'DFWM Themes: Incorrect or malicious form data received.', self::text_domain ) );
		}
		//return sanitize_title($in);
	}

	function sanitize_custom_theme_css( $in ) {
		//TODO: find decent CSS sanitize function
		return $in;
	}

	/**
	 * @param bool $value
	 * Default value false
	 *
	 * @return bool
	 */
	function sanitize_distraction_free_mode( $value = false ) {
		if ( $value ) {
			return true;
		}
		return false;
	}

	/** End sanitization **/

	function admin_main_part() {
	}

	/**
	 * Which theme will be loaded?
	 * If user already has a theme use it!
	 * @return mixed|void
	 */
	function dfw_current_theme() {
		$user_theme    = get_user_option( 'dfwmt_selected_theme' );
		$general_theme = get_option( 'dfwmt_selected_theme' );

		if ( ! empty( $user_theme ) ) {
			return $user_theme;
		}
		return $general_theme;
	}


	/** Add custom CSS to MCE and wordpress post page **/
	function filter_mce_css( $mce_css ) {
		if ( $this->dfw_current_theme() == 'custom' ) {
			$mce_css .= ', ' . plugins_url( 'css/custom.css.php', __FILE__ );
		}
		else if ( $this->dfw_current_theme() != 'default' ) {
			$mce_css .= ', ' . plugins_url( 'css/' . $this->dfw_current_theme() . '/style.css', __FILE__ );
		}

		return $mce_css;
	}


	function dfw_terminal_style() {

		if ( $this->dfw_current_theme() == 'custom' ) {
			wp_enqueue_style( 'fullscreen-style', plugins_url( 'css/custom.css.php', __FILE__ ) );
		}
		else if ( $this->dfw_current_theme() != 'default' ) {
			wp_enqueue_style( 'fullscreen-style', plugins_url( 'css/' . $this->dfw_current_theme() . '/style.css', __FILE__ ) );
		}
	}

	/**
	 * Plugin activation function
	 **/
	static function activate() {
		add_option( 'dfwmt_selected_theme', 'monokai' );

		//Needed because WP hasn't called __construct on activation yet.
		$template = new MicroTemplate( dirname( __FILE__ ) . '/templates/' );
		add_option( 'dfwmt_custom_theme_css', $template->t( 'admin/css/default-custom-css' ) );
	}

	/**
	 * Adding custom field to users profile page
	 *
	 * @param $user
	 */
	function dfwmt_user_theme_selection( $user ) {
		?>
		<table class="form-table">

			<tr>
				<th><label for="dfwmt_selected_theme"><?php _e( 'Distraction Free Theme', self::text_domain ); ?></label></th>

				<td>
					<?php echo $this->template->t( 'admin/fields/user_theme_field', array( 'working_directory' => dirname( __FILE__ ), 'plugin_url' => plugins_url( '', __FILE__ ) ) ); ?>
				</td>
			</tr>

		</table>

	<?php
	}

	function dfwmt_save_user_theme_selection( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) )
			return false;

		update_user_meta( $user_id, 'dfwmt_selected_theme', $_POST['dfwmt_selected_theme'] );
	}

}
