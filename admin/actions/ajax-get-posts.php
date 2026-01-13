<?php
/**
 * Ajax function to get posts that haven't been checked yet.
 *
 * @package bstr\admin\actions
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Update options of a given camsite_id.
 *
 * @return void
 */
function bstr_get_posts() {
	check_ajax_referer( 'ajax-nonce', 'nonce' );
	if ( ! isset( $_POST['posts_per_page'] ) ) {
		wp_send_json_error( array( 'message' => 'posts_per_page parameter is missing' ) );
	}
	if ( ! isset( $_POST['offset'] ) ) {
		wp_send_json_error( array( 'message' => 'offset parameter is missing' ) );
	}
	$posts_per_page = intval( $_POST['posts_per_page'] );
	$offset         = intval( $_POST['offset'] );

	// Base args.
	// Use the supported post types list so the admin booster includes models too.
	$supported_post_types = function_exists( 'bstr_get_supported_post_types' )
		? bstr_get_supported_post_types()
		: array( 'post', 'model' );

	$args = array(
		'fields'         => 'ids',
		'post_status'    => 'publish',
		'post_type'      => $supported_post_types,
		'posts_per_page' => $posts_per_page,
		'offset'         => $offset,
		'orderby'        => 'date',
		'order'          => 'ASC',
	);

	$query = new WP_Query( $args );
	wp_send_json_success(
		array(
			'found_posts' => intval( $query->found_posts ),
			'posts_ids'   => $query->posts,
		)
	);
	wp_die();
}
add_action( 'wp_ajax_bstr_get_posts', 'bstr_get_posts' );
