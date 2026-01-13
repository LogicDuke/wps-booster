<?php
/**
 * Theme integration helpers for WPS Booster.
 *
 * @package bstr\includes
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'bstr_get_likes_count' ) ) {
	/**
	 * Get the current likes count for a post (WPS-Booster compatible).
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return int Likes count.
	 */
	function bstr_get_likes_count( $post_id ) {
		$count = get_post_meta( $post_id, 'likes_count', true );
		return is_numeric( $count ) ? (int) $count : 0;
	}
}

if ( ! function_exists( 'bstr_get_dislikes_count' ) ) {
	/**
	 * Get the current dislikes count for a post (WPS-Booster compatible).
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return int Dislikes count.
	 */
	function bstr_get_dislikes_count( $post_id ) {
		$count = get_post_meta( $post_id, 'dislikes_count', true );
		return is_numeric( $count ) ? (int) $count : 0;
	}
}

if ( ! function_exists( 'bstr_get_views_count' ) ) {
	/**
	 * Get the current views count for a post (WPS-Booster compatible).
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return int Views count.
	 */
	function bstr_get_views_count( $post_id ) {
		$count = get_post_meta( $post_id, 'post_views_count', true );
		return is_numeric( $count ) ? (int) $count : 0;
	}
}

if ( ! function_exists( 'bstr_increment_likes' ) ) {
	/**
	 * Increment likes count by 1 (for frontend voting).
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return int Updated likes count.
	 */
	function bstr_increment_likes( $post_id ) {
		$current = bstr_get_likes_count( $post_id );
		update_post_meta( $post_id, 'likes_count', $current + 1 );
		return $current + 1;
	}
}

if ( ! function_exists( 'bstr_increment_dislikes' ) ) {
	/**
	 * Increment dislikes count by 1 (for frontend voting).
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return int Updated dislikes count.
	 */
	function bstr_increment_dislikes( $post_id ) {
		$current = bstr_get_dislikes_count( $post_id );
		update_post_meta( $post_id, 'dislikes_count', $current + 1 );
		return $current + 1;
	}
}

if ( ! function_exists( 'bstr_get_rating_percent' ) ) {
	/**
	 * Calculate rating percentage from likes/dislikes.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return float Rating percentage.
	 */
	function bstr_get_rating_percent( $post_id ) {
		$likes    = bstr_get_likes_count( $post_id );
		$dislikes = bstr_get_dislikes_count( $post_id );
		$total    = $likes + $dislikes;

		if ( 0 === $total ) {
			return 0;
		}

		return round( ( $likes / $total ) * 100, 1 );
	}
}

if ( ! function_exists( 'bstr_get_supported_post_types' ) ) {
	/**
	 * Return the list of supported post types for booster actions.
	 *
	 * @return array Supported post types.
	 */
	function bstr_get_supported_post_types() {
		$default_types = array( 'post', 'model' );

		/**
		 * Filter the post types that WPS Booster supports for boosting and voting.
		 *
		 * @param array $default_types Default supported post types.
		 */
		return apply_filters( 'bstr_supported_post_types', $default_types );
	}
}
