<?php
/**
 * Plugin config file.
 *
 * @package bstr\main
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Navigation config
 */
self::$config['nav'] = array(
	'200'          => array(
		'slug'     => 'bstr-boost-data',
		'callback' => 'bstr_boost_data_page',
		'title'    => 'Booster',
	),
	'bstr-options' => array(
		'slug' => 'bstr-options',
	),
);

/**
 * JS config
 */
self::$config['scripts']['js'] = array(
	// pages.
	'BSTR_boost-data.js' => array(
		'in_pages'  => array( 'bstr-boost-data' ),
		'path'      => 'admin/pages/page-boost-data/assets/page-boost-data.min.js',
		'require'   => array( 'WPSCORE_vue.js' ),
		'version'   => BSTR_VERSION,
		'in_footer' => true,
		'localize'  => array(
			'ajax' => true,
			'i18n' => bstr_localize(),
		),
	),
);

/**
 * Function to parse ./localize.json file to an array of localized strings.
 *
 * @return array Localized strings.
 */
function bstr_localize() {
	$localize = array();

	// Parse localize.php file.
	$localize_file = wp_normalize_path( BSTR_DIR . 'localize.php' );
	if ( file_exists( $localize_file ) ) {
		$localize = include_once $localize_file;
	}

	return $localize;
}

/**
 *  CSS config.
 */
self::$config['scripts']['css'] = array(
	// assets.
	'BSTR_admin.css'      => array(
		'in_pages' => array( 'bstr-boost-data' ),
		'path'     => 'admin/assets/css/admin.css',
		'require'  => array(),
		'version'  => BSTR_VERSION,
		'media'    => 'all',
	),
	'BSTR_boost-data.css' => array(
		'in_pages' => array( 'bstr-boost-data' ),
		'path'     => 'admin/pages/page-boost-data/assets/page-boost-data.min.css',
		'require'  => array(),
		'version'  => BSTR_VERSION,
		'media'    => 'all',
	),
);
