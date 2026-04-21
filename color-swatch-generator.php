<?php
/**
 * Plugin Name: Color Swatch Generator
 * Plugin URI: https://e-mot.co.jp
 * Description: Generates color swatch GIF images for product variations with color picker and name search
 * Version: 1.0.0
 * Author: e-mot
 * Author URI: https://e-mot.co.jp
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: color-swatch-generator
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CSG_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CSG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CSG_VERSION', '1.0.0' );

// Include core classes and functions
require_once CSG_PLUGIN_DIR . 'includes/class-gif-generator.php';
require_once CSG_PLUGIN_DIR . 'includes/class-color-database.php';
require_once CSG_PLUGIN_DIR . 'includes/class-admin-page.php';
require_once CSG_PLUGIN_DIR . 'includes/class-ajax-handler.php';

/**
 * Initialize the plugin
 */
function csg_init() {
	// Load plugin text domain
	load_plugin_textdomain( 'color-swatch-generator', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	// Initialize admin page
	if ( is_admin() ) {
		CSG_Admin_Page::init();
	}

	// Initialize AJAX handler
	CSG_AJAX_Handler::init();
}
add_action( 'plugins_loaded', 'csg_init' );

/**
 * Activation hook
 */
function csg_activate() {
	// Create necessary uploads directory if needed
	$upload_dir = wp_upload_dir();
	$color_swatch_dir = $upload_dir['basedir'] . '/color-swatches';
	
	if ( ! file_exists( $color_swatch_dir ) ) {
		wp_mkdir_p( $color_swatch_dir );
	}
}
register_activation_hook( __FILE__, 'csg_activate' );

/**
 * Deactivation hook
 */
function csg_deactivate() {
	// Cleanup if needed
}
register_deactivation_hook( __FILE__, 'csg_deactivate' );
