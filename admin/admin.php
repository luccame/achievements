<?php
/**
 * Main Achievements Admin Class
 *
 * @package Achievements
 * @subpackage Admin
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'DPA_Admin' ) ) :
/**
 * Loads Achievements admin area thing
 *
 * @since 3.0
 */
class DPA_Admin {
	// Paths

	/**
	 * Path to the Achievements admin directory
	 */
	public $admin_dir = '';

	/**
	 * URL to the Achievements admin directory
	 */
	public $admin_url = '';

	/**
	 * URL to the Achievements admin css directory
	 */
	public $css_url = '';

	/**
	 * URL to the Achievements admin image directory
	 */
	public $images_url = '';

	/**
	 * URL to the Achievements admin javascript directory
	 */
	public $javascript_url = '';


	/**
	 * The main Achievements admin loader
	 *
	 * @since 3.0
	 */
	public function __construct() {
		$this->setup_globals();
		$this->includes();
		$this->setup_actions();
	}

	/**
	 * Set up the admin hooks, actions and filters
	 *
	 * @since 3.0
	 */
	private function setup_actions() {
		// General Actions

		// Add menu item to settings menu
		add_action( 'dpa_admin_menu',              array( $this, 'admin_menus'             ) );

		// Add some general styling to the admin area
		//add_action( 'dpa_admin_head',              array( $this, 'admin_head'              ) );

		// Add settings
		add_action( 'dpa_register_admin_settings', array( $this, 'register_admin_settings' ) );

		// Add menu item to settings menu
		//add_action( 'dpa_activation',              array( $this, 'new_install'             ) );


		// Dependencies

		// Allow plugins to modify these actions
		do_action_ref_array( 'dpa_admin_loaded', array( &$this ) );
	}

	/**
	 * Include required files
	 *
	 * @since 3.0
	 */
	private function includes() {
		require( $this->admin_dir . 'admin-functions.php'   );
		require( $this->admin_dir . 'supported-plugins.php' );  // Supported plugins screen
	}

	/**
	 * Set admin globals
	 *
	 * @since 3.0
	 */
	private function setup_globals() {
		$this->admin_dir      = trailingslashit( achievements()->plugin_dir . 'admin'  ); // Admin path
		$this->admin_url      = trailingslashit( achievements()->plugin_url . 'admin'  ); // Admin URL
		$this->css_url        = trailingslashit( $this->admin_url           . 'css'    ); // Admin CSS URL
		$this->images_url     = trailingslashit( $this->admin_url           . 'images' ); // Admin images URL
		$this->javascript_url = trailingslashit( $this->admin_url           . 'js'     ); // Admin javascript URL
	}

	/**
	 * Add wp-admin menus
	 *
	 * @since 3.0
	 */
	public function admin_menus() {
		// "Supported Plugins" menu
		$hook = add_submenu_page(
			'edit.php?post_type=achievement',
			__( 'Achievements &mdash; Supported Plugins', 'dpa' ),
			__( 'Supported Plugins', 'dpa' ),
			achievements()->minimum_capability,
			'achievements-plugins',
			'dpa_supported_plugins'
		);

		// Hook into early actions to register custom CSS and JS
		add_action( "admin_print_styles-$hook",  array( $this, 'enqueue_styles'     ) );
		add_action( "admin_print_scripts-$hook", array( $this, 'enqueue_scripts'    ) );

		// Hook into early actions to register contextual help and screen options
		add_action( "load-$hook",                array( $this, 'screen_options'     ) );

		// Add custom field to the edit user screen
		add_action( 'edit_user_profile',         array( $this, 'add_profile_fields' ) );
	}

	/**
	 * Hook into early actions to register contextual help and screen options
	 *
	 * @since 3.0
	 */
	public function screen_options() {
		// Only load up styles if we're on an Achievements admin screen
		if ( ! DPA_Admin::is_admin_screen() )
			return;

		// "Supported Plugins" screen
		if ( 'achievements-plugins' == $_GET['page'] )
			dpa_supported_plugins_on_load();
	}

	/**
	 * Enqueue CSS for our custom admin screens
	 *
	 * @since 3.0
	 */
	public function enqueue_styles() {
		// Only load up styles if we're on an Achievements admin screen
		if ( ! DPA_Admin::is_admin_screen() )
			return;

		// "Supported Plugins" screen
		if ( 'achievements-plugins' == $_GET['page'] )
			wp_enqueue_style( 'dpa_admin_css', trailingslashit( $this->css_url ) . 'supportedplugins.css', array(), '20120722' );
	}

	/**
	 * Enqueue JS for our custom admin screens
	 *
	 * @since 3.0
	 */
	public function enqueue_scripts() {
		// Only load up scripts if we're on an Achievements admin screen
		if ( ! DPA_Admin::is_admin_screen() )
			return;

		// "Supported Plugins" screen
		if ( 'achievements-plugins' == $_GET['page'] ) {
			wp_enqueue_script( 'dpa_socialite',   trailingslashit( $this->javascript_url ) . 'socialite-min.js',          array(), '20120722', true );
			wp_enqueue_script( 'tablesorter_js',  trailingslashit( $this->javascript_url ) . 'jquery-tablesorter-min.js', array( 'jquery' ), '20120722', true );
			wp_enqueue_script( 'dpa_sp_admin_js', trailingslashit( $this->javascript_url ) . 'supportedplugins-min.js',   array( 'jquery', 'dpa_socialite', 'dashboard', 'postbox' ), '20120722', true );

			// Add thickbox for the 'not installed' links on the List view
			add_thickbox();
		}
	}

	/**
	 * Register the settings
	 *
	 * @since 3.0
	 */
	public function register_admin_settings() {
		// Only do stuff if we're on an Achievements admin screen
		if ( ! DPA_Admin::is_admin_screen() )
			return;

		// Fire an action for Achievements plugins to register their custom settings
		do_action( 'dpa_register_admin_settings' );
	}

	/**
	 * Is the current screen part of Achievements? e.g. a post type screen.
	 *
	 * @return bool True if this is an Achievements admin screen
	 * @since 3.0
	 */
	public static function is_admin_screen() {
		$result = false;

		if ( ! empty( $_GET['post_type'] ) && 'achievement' == $_GET['post_type'] )
			$result = true;

		return true;
	}

}
endif; // class_exists check

/**
 * Set up Achievements' Admin
 *
 * @since 3.0
 */
function dpa_admin() {
	achievements()->admin = new DPA_Admin();
}