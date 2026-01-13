<?php
/**
 * Plugin class.
 *
 * @package bstr\classes
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * BSTR Plugin Singleton Class
 */
final class BSTR {
	/**
	 * The instance of the BSTR plugin
	 *
	 * @var      instanceof BSTR $instance
	 * @static
	 */
	private static $instance;

	/**
	 * The config of the BSTR plugin
	 *
	 * @var      array $config
	 * @static
	 */
	private static $config;

	/**
	 * __clone method
	 *
	 * @return   void
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Do not clone or wake up this class', 'bstr_lang' ), '1.0' );
	}

	/**
	 * __wakeup method
	 *
	 * @return   void
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Do not clone or wake up this class', 'bstr_lang' ), '1.0' );
	}

	/**
	 * Instance method
	 *
	 * @return   self::$instance
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof BSTR ) ) {
			self::$instance = new BSTR();
			self::$instance->load_textdomain();
			// Load config file.
			require_once BSTR_DIR . 'config.php';
			if ( is_admin() ) {
				self::$instance->load_admin_filters();
				self::$instance->load_admin_hooks();
				self::$instance->auto_load_php_files( 'admin' );
				self::$instance->admin_init();
			}
			self::$instance->load_boost_actions();
			self::$instance->public_init();
		}
		return self::$instance;
	}

	/**
	 * Add js and css files, tabs, pages, php files in admin mode.
	 *
	 * @return void.
	 */
	public function load_admin_filters() {
		// add js and css files, tabs, pages, php files.
		add_filter( 'WPSCORE-scripts', array( $this, 'add_admin_scripts' ) );
		add_filter( 'WPSCORE-tabs', array( $this, 'add_admin_navigation' ) );
		add_filter( 'WPSCORE-pages', array( $this, 'add_admin_navigation' ) );
	}

	/**
	 * Add admin js and css scripts. This is a WPSCORE-scripts filter callback function.
	 *
	 * @param array $scripts List of all WPS CORE CSS / JS to load.
	 *
	 * @return array $scripts List of all WPS CORE + plugin CSS / JS to load.
	 */
	public function add_admin_scripts( $scripts ) {
		if ( isset( self::$config['scripts'] ) ) {
			if ( isset( self::$config['scripts']['js'] ) ) {
				$scripts += (array) self::$config['scripts']['js'];
			}
			if ( isset( self::$config['scripts']['css'] ) ) {
				$scripts += (array) self::$config['scripts']['css'];
			}
		}
		return $scripts;
	}

	/**
	 * Add plugin admin navigation tab. This is a WPSCORE-tabs and WPSCORE-pages filters callback function.
	 *
	 * @param array $nav List of all WPS CORE navigation tabs to add.
	 *
	 * @return array $nav List of all WPS CORE + plugin navigation tabs to add.
	 */
	public function add_admin_navigation( $nav ) {
		if ( isset( self::$config['nav'] ) ) {
			$nav += (array) self::$config['nav'];
		}
		return $nav;
	}

	/**
	 * Add admin actions.
	 *
	 * @return void.
	 */
	public function load_boost_actions() {
		eval( WPSCORE()->eval_product_data( 'BSTR', 'load_admin_actions' ) );
		add_action( 'wp_insert_post', array( $this, 'action_boost_new_posts' ), 10, 3 );
	}

	/**
	 * Action - Boost posts on wp_insert_post hook.
	 *
	 * @param int            $post_id  The id of the post to boost on insertion.
	 * @param WP_Post object $post     The WP_Post object to boost.
	 * @param bool           $update   Is the current post updated.
	 *
	 * @return bool False if post boosted, false if not.
	 */
	public function action_boost_new_posts( $post_id, $post, $update ) {
		$bstr_options = BSTR\Options::get();
		// Allow boosting for both default posts (videos) and model post types.
		$supported_types = function_exists( 'bstr_get_supported_post_types' )
			? bstr_get_supported_post_types()
			: array( 'post', 'model' );
		if ( ! $post || ! in_array( $post->post_type, $supported_types, true ) ) {
			return false;
		}
		if ( $update ) {
			return false;
		}
		if ( false === $bstr_options['boost_new_posts'] ) {
			return false;
		}
		BSTR\Booster::boost_data( $post_id, 'post_views_count', $bstr_options['boost_new_posts_views_min'], $bstr_options['boost_new_posts_views_max'] );
		BSTR\Booster::boost_data( $post_id, 'likes_count', $bstr_options['boost_new_posts_likes_min'], $bstr_options['boost_new_posts_likes_max'] );
		BSTR\Booster::boost_data( $post_id, 'dislikes_count', $bstr_options['boost_new_posts_dislikes_min'], $bstr_options['boost_new_posts_dislikes_max'] );
		return true;
	}

	/**
	 * Auto-loader for PHP files
	 *
	 * @param string{'admin','public'} $dir Directory where to find PHP files to load.
	 *
	 * @return void
	 */
	public function auto_load_php_files( $dir ) {
		$dirs = (array) ( BSTR_DIR . $dir . '/' );
		foreach ( (array) $dirs as $dir ) {
			$files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir ) );
			if ( ! empty( $files ) ) {
				foreach ( $files as $file ) {
					// exlude dirs.
					if ( $file->isDir() ) {
						continue; }
					// exlude index.php.
					if ( $file->getPathname() === 'index.php' ) {
						continue; }
					// exlude files !== .php.
					if ( substr( $file->getPathname(), -4 ) !== '.php' ) {
						continue; }
					// exlude files from -x suffixed directories.
					if ( substr( $file->getPath(), -2 ) === '-x' ) {
						continue; }
					// exlude -x suffixed files.
					if ( substr( $file->getPathname(), -6 ) === '-x.php' ) {
						continue; }
					// else require file.
					require $file->getPathname();
				}
			}
		}
	}

	/**
	 * Registering plugin activation / deactivation / uninstall hooks.
	 *
	 * @return void
	 */
	public function load_admin_hooks() {
		register_activation_hook( BSTR_FILE, array( __CLASS__, 'activation' ) );
		register_deactivation_hook( BSTR_FILE, array( __CLASS__, 'deactivation' ) );
		register_uninstall_hook( BSTR_FILE, array( __CLASS__, 'uninstall' ) );
	}

	/**
	 * Stuff to do on plugin activation. This is a register_activation_hook callback function.
	 *
	 * @static
	 *
	 * @return void
	 */
	public static function activation() {
		WPSCORE()->update_client_signature();
		WPSCORE()->init( true );
	}

	/**
	 * Stuff to do on plugin deactivation. This is a register_deactivation_hook callback function.
	 *
	 * @static
	 *
	 * @return void
	 */
	public static function deactivation() {
		WPSCORE()->update_client_signature();
		WPSCORE()->init( true );
	}

	/**
	 * Stuff to do on plugin deactivation. This is a register_deactivation_hook callback function.
	 *
	 * @static
	 *
	 * @return void
	 */
	public static function uninstall() {
		WPSCORE()->update_client_signature();
		WPSCORE()->init( true );
	}

	/**
	 * Text domain function
	 *
	 * @return false by default
	 */
	/**
	 * Load textdomain method.
	 *
	 * @return bool True when textdomain is successfully loaded, false if not.
	 */
	public function load_textdomain() {
		$lang = ( current( explode( '_', get_locale() ) ) );
		if ( 'zh' === $lang ) {
			$lang = 'zh-TW';
		}
		$textdomain = 'bstr_lang';
		$mofile     = BSTR_DIR . "languages/{$textdomain}_{$lang}.mo";
		return load_textdomain( $textdomain, $mofile );
	}


	/**
	 * Load public filters.
	 *
	 * @return   void
	 */
	public function load_public_filters() {
		add_filter( 'WPSCORE-public_dirs', array( $this, 'add_public_dirs' ) );
	}

	/**
	 * Add public php files to require.
	 *
	 * @param array $public_dirs Array of public directories.
	 *
	 * @return array $public_dirs Array of public directories with the current plugin ones.
	 */
	public function add_public_dirs( $public_dirs ) {
		$public_dirs[] = plugin_dir_path( __FILE__ ) . 'public/';
		return $public_dirs;
	}

	/**
	 * Stuff to do on admin init.
	 *
	 * @access private
	 *
	 * @return void
	 */
	private function admin_init() {}

	/**
	 * Stuff to do on public init.
	 *
	 * @access private
	 *
	 * @return void
	 */
	private function public_init() {}
}
