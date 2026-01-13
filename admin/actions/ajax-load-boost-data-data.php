<?php
/**
 * Ajax function to load data on check links page.
 *
 * @package bstr\admin\actions
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Returns an array with data needed on check links page.
 *
 * @return void
 */
function bstr_load_boost_data_data() {
	check_ajax_referer( 'ajax-nonce', 'nonce' );
	$data = array(
		'saved_options' => BSTR\Options::get(),
	);
	wp_send_json_success( json_decode( wp_json_encode( $data ) ) );
	wp_die();
}
add_action( 'wp_ajax_bstr_load_boost_data_data', 'bstr_load_boost_data_data' );
