<?php
/**
 * Public AJAX handlers for frontend voting.
 *
 * @package bstr\public
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bstr_handle_frontend_vote' ) ) {
	/**
	 * Handle frontend vote submission.
	 *
	 * @return void
	 */
	function bstr_handle_frontend_vote() {
		check_ajax_referer( 'bstr_vote_nonce', 'nonce' );

		$post_id   = isset( $_POST['post_id'] ) ? (int) $_POST['post_id'] : 0;
		$vote_type = isset( $_POST['vote_type'] ) ? sanitize_text_field( $_POST['vote_type'] ) : '';

		if ( $post_id <= 0 ) {
			bstr_log_vote_error( 'Invalid post ID provided.' );
			wp_send_json_error( array( 'message' => 'Invalid post ID' ) );
		}

		if ( ! in_array( $vote_type, array( 'like', 'dislike' ), true ) ) {
			bstr_log_vote_error( 'Invalid vote type provided.' );
			wp_send_json_error( array( 'message' => 'Invalid vote type' ) );
		}

		$post = get_post( $post_id );
		if ( ! $post ) {
			bstr_log_vote_error( 'Post lookup failed for voting.' );
			wp_send_json_error( array( 'message' => 'Invalid post' ) );
		}

		$supported_types = function_exists( 'bstr_get_supported_post_types' )
			? bstr_get_supported_post_types()
			: array( 'post', 'model' );
		if ( ! in_array( $post->post_type, $supported_types, true ) ) {
			bstr_log_vote_error( 'Unsupported post type for voting.' );
			wp_send_json_error( array( 'message' => 'Invalid post' ) );
		}

		if ( 'like' === $vote_type ) {
			bstr_increment_likes( $post_id );
		} else {
			bstr_increment_dislikes( $post_id );
		}

		wp_send_json_success(
			array(
				'likes'          => bstr_get_likes_count( $post_id ),
				'dislikes'       => bstr_get_dislikes_count( $post_id ),
				'rating_percent' => bstr_get_rating_percent( $post_id ),
			)
		);
	}
}
add_action( 'wp_ajax_bstr_frontend_vote', 'bstr_handle_frontend_vote' );
add_action( 'wp_ajax_nopriv_bstr_frontend_vote', 'bstr_handle_frontend_vote' );

if ( ! function_exists( 'bstr_log_vote_error' ) ) {
	/**
	 * Log frontend voting errors for troubleshooting.
	 *
	 * @param string $message Error message.
	 *
	 * @return void
	 */
	function bstr_log_vote_error( $message ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( '[TMW-BOOST] ' . $message ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}
	}
}

if ( ! function_exists( 'bstr_localize_frontend_script' ) ) {
	/**
	 * Localize voting data for frontend scripts.
	 *
	 * @return void
	 */
	function bstr_localize_frontend_script() {
		if ( is_singular( bstr_get_supported_post_types() ) ) {
			wp_localize_script(
				'jquery',
				'bstr_voting',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'post_id'  => get_the_ID(),
					'nonce'    => wp_create_nonce( 'bstr_vote_nonce' ),
				)
			);
		}
	}
}
add_action( 'wp_enqueue_scripts', 'bstr_localize_frontend_script', 100 );
