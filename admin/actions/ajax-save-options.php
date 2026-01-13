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
function bstr_save_options() {
	check_ajax_referer( 'ajax-nonce', 'nonce' );
	if ( ! isset( $_POST['options'] ) ) {
		wp_send_json_error( array( 'message' => 'options parameter is missing' ) );
	}
	$options = json_decode( wp_unslash( $_POST['options'] ), true );
	$saved   = BSTR\Options::save( $options );
	wp_send_json_success( array( 'saved' => $saved ) );
	wp_die();
}
add_action( 'wp_ajax_bstr_save_options', 'bstr_save_options' );
