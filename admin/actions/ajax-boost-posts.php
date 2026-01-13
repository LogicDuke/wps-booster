<?php
/**
 * Ajax function to boost posts in the DB.
 *
 * @package bstr\admin\actions
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Boost a chunk of posts given posts ids, metakeys to boost with min and max.
 *
 * @return void
 */
function bstr_boost_posts() {
	check_ajax_referer( 'ajax-nonce', 'nonce' );
	$required_fields = array( 'posts_ids', 'metakey', 'boost_action', 'min', 'max' );
	foreach ( $required_fields as $field ) {
		if ( ! isset( $_POST[ $field ] ) ) {
			wp_send_json_error( array( 'message' => $field . ' parameter is missing' ) );
		}
	}
	try {
		foreach ( (array) $_POST['posts_ids'] as $post_id ) {
			$metakey = $_POST['metakey'];
			$min     = intval( $_POST['min'] );
			$max     = intval( $_POST['max'] );
			$action  = $_POST['boost_action'];
			BSTR\Booster::boost_data( $post_id, $metakey, $min, $max, $action );
		}
		wp_send_json_success();
	} catch ( \Exception $exception ) {
		wp_send_json_error( array( 'message' => $exception->getMessage() ) );
	}
	wp_die();
}
add_action( 'wp_ajax_bstr_boost_posts', 'bstr_boost_posts' );
