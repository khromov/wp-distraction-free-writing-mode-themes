<?php
/* TODO: Test shorttag disabled functionality
Plugin Name: Distraction Free Writing mode Themes
Plugin URI: http://wordpress.org/extend/plugins/distraction-free-writing-mode-themes/
Description: Provides dark and light themes for for Distraction Free Writing mode. Use one of the beautiful built-in themes or write your own.
Version: 2.2-alpha
License: GPL2
Author: khromov
Author URI: http://khromov.wordpress.com
License: GPL2
*/

$main = new DFWMDT();

class DFWMDT {
	var $template;

	function __construct() {
		/**
		 * Register hooks, languages etc
		 **/
		register_activation_hook( __FILE__, array( 'DFWMDT', 'activate' ) );
		load_plugin_textdomain( 'distraction-free-writing-mode-themes', false, basename( dirname( __FILE__ ) ) . '/languages' );

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
		if ( $pagenow == 'post-new.php' && get_option( 'dfwmt_force_zen_mode' ) == 1 ) {
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
		add_submenu_page( 'options-general.php', __( "Distraction Free Writing mode Themes Configuration" ), __( "DFWM Themes" ), 'manage_options', 'dfwmdt', array( &$this, 'admin_main' ) );
	}

	function register_settings() {
		register_setting( 'dfwmdt-group', 'dfwmt_selected_theme', array( &$this, 'sanitize_selected_theme' ) );
		register_setting( 'dfwmdt-group', 'dfwmt_force_zen_mode', array( &$this, 'sanitize_force_mode' ) );
		register_setting( 'dfwmdt-group', 'dfwmt_custom_theme_css', array( &$this, 'sanitize_custom_theme_css' ) );

		add_settings_section( 'dfwmdt-main', __( 'Main configuration' ), array( &$this, 'admin_main_part' ), 'dfwmdt' );

		add_settings_field( 'dfwmt_force_zen_mode', __( 'Force Zen mode' ), array( &$this, 'zen_mode_fied' ), 'dfwmdt', 'dfwmdt-main' );
		add_settings_field( 'dfwmt_selected_theme', __( 'Selected theme' ), array( &$this, 'field_selected_theme' ), 'dfwmdt', 'dfwmdt-main' );
		add_settings_field( 'dfwmt_custom_theme_css', __( 'Custom CSS' ), array( &$this, 'field_custom_theme_css' ), 'dfwmdt', 'dfwmdt-main' );
	}

	function admin_main() {
		echo $this->template->t( 'admin/js-popup' );
		echo $this->template->t( 'admin/main' );
		echo $this->template->t( 'admin/footer' );
	}

	function zen_mode_fied() {
		echo $this->template->t( 'admin/fields/zen_field' );
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
			die( __( 'DFWM Themes: Incorrect or malicious form data received.' ) );
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
	function sanitize_force_mode( $value = false ) {
		if ( $value ) {
			return true;
		}
		return false;
	}

	/** End sanitization **/

	function admin_main_part() {
	}

	/** Add custom CSS to MCE and wordpress post page **/
	function filter_mce_css( $mce_css ) {
		$df_theme = get_option( 'dfwmt_selected_theme' );
		if ( $df_theme == 'custom' ) {
			$mce_css .= ', ' . plugins_url( 'css/custom.css.php', __FILE__ );
		}
		else if ( $df_theme != 'default' ) {
			$mce_css .= ', ' . plugins_url( 'css/' . $df_theme . '/style.css', __FILE__ );
		}

		return $mce_css;
	}

	function dfw_terminal_style() {
		$df_theme = get_option( 'dfwmt_selected_theme' );
		if ( $df_theme == 'custom' ) {
			wp_enqueue_style( 'fullscreen-style', plugins_url( 'css/custom.css.php', __FILE__ ) );
		}
		else if ( $df_theme != 'default' ) {
			wp_enqueue_style( 'fullscreen-style', plugins_url( 'css/' . $df_theme . '/style.css', __FILE__ ) );
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
}