<?php
/**
 * Plugin Name: WPS Booster
 * Plugin URI: https://www.wp-script.com/plugins/wps-booster/
 * Description: Boost views and rating on your new site to make it bigger
 * Author: WP-Script
 * Author URI: https://www.wp-script.com
 * Version: 2.4.0
 * Text Domain: bstr_lang
 * Domain Path: /languages
 * Requires PHP: 7.2
 *
 * @package bstr\main
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

define( 'BSTR_VERSION', '2.4.0' );
define( 'BSTR_DIR', plugin_dir_path( __FILE__ ) );
define( 'BSTR_URL', plugin_dir_url( __FILE__ ) );
define( 'BSTR_FILE', __FILE__ );

require_once BSTR_DIR . 'tgmpa/class-tgm-plugin-activation.php';
require_once BSTR_DIR . 'tgmpa/config.php';
require_once 'vendor/autoload.php';

/**
 * Create the plugin instance in a function and call it.
 *
 * @return BSTR::instance();
 */
if ( ! function_exists( 'bstr' ) ) {
	/**
	 * Run the plugin.
	 */
	function bstr() {
		if ( class_exists( BSTR::class ) ) {
			return BSTR::instance();
		}
	}
	bstr();
}

add_action( 'init', 'bstr_check_plugin_status' );
/**
 * Init hook callback to run the BSTR only if it is connected.
 */
function bstr_check_plugin_status() {
	if ( 'connected' === WPSCORE()->get_product_status( 'BSTR' ) ) {
		BSTR();
	}
}
