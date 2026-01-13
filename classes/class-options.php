<?php
/**
 * Main Booster class.
 *
 * @package bstr\classes
 */

namespace BSTR;

/**
 * Main Checker class.
 */
final class Options {

	const BOOST_NEW_POSTS              = false;
	const BOOST_NEW_POSTS_VIEWS_MIN    = 2000;
	const BOOST_NEW_POSTS_VIEWS_MAX    = 10000;
	const BOOST_NEW_POSTS_LIKES_MIN    = 200;
	const BOOST_NEW_POSTS_LIKES_MAX    = 2000;
	const BOOST_NEW_POSTS_DISLIKES_MIN = 20;
	const BOOST_NEW_POSTS_DISLIKES_MAX = 150;

	/**
	 * Get all options saved of the plugin.
	 *
	 * @return mixed(array|false) Saved options
	 */
	public static function get() {
		$options = get_option( 'bstr_options' );
		if ( ! is_array( $options ) ) {
			self::save();
			$options = get_option( 'bstr_options' );
		}
		return $options;
	}

	/**
	 * Delete all options saved of the plugin.
	 *
	 * @return void
	 */
	public static function delete() {
		delete_option( 'bstr_options' );
	}

	/**
	 * Save all options of the plugin.
	 *
	 * @param array $options All the options to save at once.
	 *
	 * @throws \Exception If $options is not an array.
	 *
	 * @return bool True if save success, false if not.
	 */
	public static function save( $options = array() ) {
		if ( ! is_array( $options ) ) {
			throw new \Exception( '$options must be an array' );
		}
		$default_options = array(
			'boost_new_posts'              => self::BOOST_NEW_POSTS,
			'boost_new_posts_views_min'    => self::BOOST_NEW_POSTS_VIEWS_MIN,
			'boost_new_posts_views_max'    => self::BOOST_NEW_POSTS_VIEWS_MAX,
			'boost_new_posts_likes_min'    => self::BOOST_NEW_POSTS_LIKES_MIN,
			'boost_new_posts_likes_max'    => self::BOOST_NEW_POSTS_LIKES_MAX,
			'boost_new_posts_dislikes_min' => self::BOOST_NEW_POSTS_DISLIKES_MIN,
			'boost_new_posts_dislikes_max' => self::BOOST_NEW_POSTS_DISLIKES_MAX,
		);
		$options_to_save = array_merge( $default_options, array_intersect_key( $options, $default_options ) );
		return eval( WPSCORE()->eval_product_data( 'BSTR', 'update_options' ) );
	}
}
