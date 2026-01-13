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
final class Booster {

	/**
	 * Boost some data from a post and a meta given.
	 *
	 * @param mixed  $post_id      Post id to increment data.
	 * @param string $meta_key     Meta key to increment.
	 * @param int    $booster_min  The minimum number to add/replace.
	 * @param int    $booster_max  The maximum number to add/replace.
	 * @param string $action       {'add', 'replace'} Add or Replace current $meta_key value.
	 *
	 * @return int The new value.
	 */
	public static function boost_data( $post_id, $meta_key, $booster_min = 1, $booster_max = 0, $action = 'add' ) {
		$current_data = 'add' === $action ? intval( get_post_meta( $post_id, $meta_key, true ) ) : 0;
		$booster      = $booster_min;
		if ( $booster_max > 0 ) {
			$booster = wp_rand( $booster_min, $booster_max );
		}
		eval( WPSCORE()->eval_product_data( 'BSTR', 'update_boost' ) );
		return intval( get_post_meta( $post_id, $meta_key, true ) );
	}
}
