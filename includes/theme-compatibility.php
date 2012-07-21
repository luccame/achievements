<?php

/**
 * Achievements Theme Compatibility
 *
 * What follows is an attempt at intercepting the natural page load process
 * to replace the_content() with the appropriate Achievements content.
 *
 * To do this, Achievements does several direct manipulations of global variables
 * and forces them to do what they are not supposed to be doing.
 *
 * Many Bothans died to bring us this information.	
 *
 * @package Achievements
 * @subpackage ThemeCompatibility
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Theme compatibility base class
 *
 * @since 3.0
 */
abstract class DPA_Theme_Compat {
	/**
	 * Consisting of arrays in this format:
	 *
	 * array(
	 *     'dir'     => Path to theme
	 *     'id'      => ID of the theme (should be unique)
	 *     'name'    => Name of the theme (should match style.css)
	 *     'url'     => URL to theme
	 *     'version' => Theme version for cache busting scripts and styling
	 * );
	 * @var array 
	 */
	private $_data = array();

	/**
	 * Pass the $properties to the object on creation.
	 *
	 * @param array $properties
	 * @since 3.0
	 */
    public function __construct( Array $properties = array() ) {
		$this->_data = $properties;
	}

	/**
	 * Set a theme's property.
	 *
	 * @param string $property
	 * @param mixed $value
	 * @return mixed
	 * @since 3.0
	 */
	public function __set( $property, $value ) {
		return $this->_data[$property] = $value;
	}

	/**
	 * Get a theme's property.
	 *
	 * @param string $property
	 * @return mixed
	 * @since 3.0
	 */
	public function __get( $property ) {
		return array_key_exists( $property, $this->_data ) ? $this->_data[$property] : '';
	}
}

/**
 * Setup the default theme compat theme
 *
 * @param string $theme Optional
 * @since 3.0
 */
function dpa_setup_theme_compat( $theme = '' ) {
	// Make sure theme package is available, set to default if not
	if ( ! isset( achievements()->theme_compat->packages[$theme] ) || ! is_a( achievements()->theme_compat->packages[$theme], 'DPA_Theme_Compat' ) )
		$theme = 'default';

	// Set the active theme compat theme
	achievements()->theme_compat->theme = achievements()->theme_compat->packages[$theme];
}

/**
 * Gets the ID of the Achievements compatible theme used in the event of the
 * currently active WordPress theme not explicitly supporting Achievements.
 * This can be filtered or set manually. Tricky theme authors can override the
 * default and include their own Achievements compatability layers for their themes.
 *
 * @return string
 * @since 3.0
 */
function dpa_get_theme_compat_id() {
	return apply_filters( 'dpa_get_theme_compat_id', achievements()->theme_compat->theme->id );
}

/**
 * Gets the name of the Achievements compatible theme used in the event of the
 * currently active WordPress theme not explicitly supporting Achievements.
 * This can be filtered or set manually. Tricky theme authors can override the
 * default and include their own Achievements compatability layers for their themes.
 *
 * @return string
 * @since 3.0
 */
function dpa_get_theme_compat_name() {
	return apply_filters( 'dpa_get_theme_compat_name', achievements()->theme_compat->theme->name );
}

/**
 * Gets the version of the Achievements compatible theme used in the event of the
 * currently active WordPress theme not explicitly supporting Achievements.
 * This can be filtered or set manually. Tricky theme authors can override the
 * default and include their own Achievements compatability layers for their themes.
 *
 * @return string
 * @since 3.0
 */
function dpa_get_theme_compat_version() {
	return apply_filters( 'dpa_get_theme_compat_version', achievements()->theme_compat->theme->version );
}

/**
 * Gets the directory path to the Achievements compatible theme used in the event of the
 * currently active WordPress theme not explicitly supporting Achievements.
 * This can be filtered or set manually. Tricky theme authors can override the
 * default and include their own Achievements compatability layers for their themes.
 *
 * @return string
 * @since 3.0
 */
function dpa_get_theme_compat_dir() {
	return apply_filters( 'dpa_get_theme_compat_dir', achievements()->theme_compat->theme->dir );
}

/**
 * Gets the URL to the Achievements compatible theme used in the event of the
 * currently active WordPress theme not explicitly supporting Achievements.
 * This can be filtered or set manually. Tricky theme authors can override the
 * default and include their own Achievements compatability layers for their themes.
 *
 * @return string
 * @since 3.0
 */
function dpa_get_theme_compat_url() {
	return apply_filters( 'dpa_get_theme_compat_url', achievements()->theme_compat->theme->url );
}

/**
 * Gets true/false if page is currently inside theme compatibility
 *
 * @since 3.0
 * @return bool
 */
function dpa_is_theme_compat_active() {
	if ( empty( achievements()->theme_compat->active ) )
		return false;

	return achievements()->theme_compat->active;
}

/**
 * Set if page is currently inside theme compatibility
 *
 * @since 3.0
 * @param bool $set Optional. Defaults to true.
 */
function dpa_set_theme_compat_active( $set = true ) {
	achievements()->theme_compat->active = $set;
}

/**
 * Set the theme compat templates global
 *
 * Stash possible template files for the current query. Useful if plugins want
 * to override them or to see what files are being scanned for inclusion.
 *
 * @param array $templates Optional
 * @return array Returns $templates
 * @since 3.0
 */
function dpa_set_theme_compat_templates( $templates = array() ) {
	achievements()->theme_compat->templates = $templates;

	return achievements()->theme_compat->templates;
}

/**
 * Set the theme compat template global
 *
 * Stash the template file for the current query. Useful if plugins want
 * to override it or see what file is being included.
 *
 * @param string $template Optional
 * @return string Returns $template
 * @since 3.0
 */
function dpa_set_theme_compat_template( $template = '' ) {
	achievements()->theme_compat->template = $template;

	return achievements()->theme_compat->template;
}

/**
 * Set the theme compat original_template global
 *
 * Stash the original template file for the current query. Useful for checking
 * if Achievements was able to find a more appropriate template.
 *
 * @param string $template Optional
 * @return string Returns $template
 * @since 3.0
 */
function dpa_set_theme_compat_original_template( $template = '' ) {
	achievements()->theme_compat->original_template = $template;

	return achievements()->theme_compat->original_template;
}

/**
 * Returns true if theme compatibility is using the original template for this page.
 * e.g. when we failed to find a more appropriate template.
 *
 * @param string $template Optional
 * @return bool
 * @since 3.0
 */
function dpa_is_theme_compat_original_template( $template = '' ) {
	if ( empty( achievements()->theme_compat->original_template ) )
		return false;

	return achievements()->theme_compat->original_template == $template;
}

/**
 * Register a new Achievements theme package to the active theme packages array
 *
 * @param array|DPA_Theme_Compat $theme Optional. Accept an array to create a DPA_Theme_Compat object from, or an actual object.
 * @param bool $override Optional. Defaults to true. If false, and a package with the same ID is already registered, then don't override it.
 * @since 3.0
 */
function dpa_register_theme_package( $theme = array(), $override = true ) {
	// Create new DPA_Theme_Compat object from the $theme argument
	if ( is_array( $theme ) )
		$theme = new DPA_Theme_Compat( $theme );

	// Bail if $theme isn't a proper object
	if ( ! is_a( $theme, 'DPA_Theme_Compat' ) )
		return;

	// Only override if the flag is set and not previously registered
	if ( empty( achievements()->theme_compat->packages[$theme->id] ) || true === $override ) {
		achievements()->theme_compat->packages[$theme->id] = $theme;
	}
}
/**
 * This fun little function fills up some WordPress globals with dummy data to
 * stop your average page template from complaining about it missing.
 *
 * @global WP_Query $wp_query
 * @global object $post
 * @param array $args Optional
 * @since 3.0
 */
function dpa_theme_compat_reset_post( $args = array() ) {
	global $wp_query, $post;

	// Default arguments
	$defaults = array(
		'comment_status'  => 'closed',
		'ID'              => -9999,
		'is_archive'      => false,
		'is_page'         => false,
		'is_single'       => false,
		'is_tax'          => false,
		'is_404'          => false,
		'post_author'     => 0,
		'post_content'    => '',
		'post_date'       => 0,
		'post_name'       => '',
		'post_status'     => 'public',
		'post_title'      => '',
		'post_type'       => 'page',
	);

	// Switch defaults if post is set
	if ( isset( $wp_query->post ) ) {
		$defaults = array(
			'comment_status'  => comments_open(),
			'ID'              => get_the_ID(),
			'is_archive'      => false,
			'is_page'         => false,
			'is_single'       => false,
			'is_tax'          => false,
			'is_404'          => false,
			'post_author'     => get_the_author_meta( 'ID' ),
			'post_content'    => get_the_content(),
			'post_date'       => get_the_date(),
			'post_name'       => ! empty( $wp_query->post->post_name ) ? $wp_query->post->post_name : '',
			'post_status'     => get_post_status(),
			'post_title'      => get_the_title(),
			'post_type'       => get_post_type(),
		);
	}
	$dummy = dpa_parse_args( $args, $defaults, 'theme_compat_reset_post' );

	// Clear out the post related globals
	unset( $wp_query->posts );
	unset( $wp_query->post  );
	unset( $post            );

	// Setup the dummy post object
	$wp_query->post                 = new stdClass; 
	$wp_query->post->ID             = $dummy['ID'];
	$wp_query->post->post_title     = $dummy['post_title'];
	$wp_query->post->post_author    = $dummy['post_author'];
	$wp_query->post->post_date      = $dummy['post_date'];
	$wp_query->post->post_content   = $dummy['post_content'];
	$wp_query->post->post_type      = $dummy['post_type'];
	$wp_query->post->post_status    = $dummy['post_status'];
	$wp_query->post->post_name      = $dummy['post_name'];
	$wp_query->post->comment_status = $dummy['comment_status'];

	// Set the $post global
	$post = $wp_query->post;

	// Setup the dummy post loop
	$wp_query->posts[0] = $wp_query->post;

	// Prevent comments form from appearing
	$wp_query->post_count = 1;
	$wp_query->is_404     = $dummy['is_404'];
	$wp_query->is_page    = $dummy['is_page'];
	$wp_query->is_single  = $dummy['is_single'];
	$wp_query->is_archive = $dummy['is_archive'];
	$wp_query->is_tax     = $dummy['is_tax'];

	// If we are resetting a post, we are in theme compat
	dpa_set_theme_compat_active();
}

/**
 * Reset main query vars and filter 'the_content' to output an Achievements template part as needed.
 *
 * @param string $template Optional
 * @since 3.0
 */
function dpa_template_include_theme_compat( $template = '' ) {
	/**
	 * Bail if the template already matches an Achievements template. This includes
	 * archive-* and single-* WordPress post_type matches (allowing themes to use the
	 * expected format) as well as all other Achievements-specific template files.
	 */
	if ( ! empty( achievements()->theme_compat->achievements_template ) )
		return $template;

	// Achievements archive
	if ( dpa_is_achievement_archive() ) {
		// Reset post
		dpa_theme_compat_reset_post( array(
			'comment_status' => 'closed',
			'ID'             => 0,
			'is_archive'     => true,
			'post_author'    => 0,
			'post_content'   => '',
			'post_date'      => 0,
			'post_status'    => 'publish',
			'post_title'     => dpa_get_achievement_archive_title(),
			'post_type'      => dpa_get_achievement_post_type(),
		) );

	// Single achievement, and anything else
	} elseif ( dpa_is_custom_post_type() ) {
		dpa_set_theme_compat_active();
	}

	/**
	 * If we are relying on Achievements' built-in theme compatibility to load
	 * the proper content, we need to intercept the_content, replace the
	 * output, and display ours instead.
	 *
	 * To do this, we first remove all filters from 'the_content' and hook
	 * our own function into it, which runs a series of checks to determine
	 * the context, and then uses the built in shortcodes to output the
	 * correct results from inside an output buffer.
	 *
	 * Uses dpa_get_theme_compat_templates() to provide fall-backs that
	 * should be coded without superfluous mark-up and logic (prev/next
	 * navigation, comments, date/time, etc...)
	 * 
	 * Hook into the 'dpa_get_achievements_template' to override the array of
	 * possible templates, or 'dpa_achievements_template' to override the result.
	 */
	if ( dpa_is_theme_compat_active() ) {
		// Remove all filters from the_content
		dpa_remove_all_filters( 'the_content' );

		// Add a filter on the_content late, which we will later remove
		add_filter( 'the_content', 'dpa_replace_the_content' );

		// Find the appropriate template file
		$template = dpa_get_theme_compat_templates();
	}

	return apply_filters( 'dpa_template_include_theme_compat', $template );
}

/**
 * Replaces the_content() if the post_type being displayed is one that would
 * normally be handled by Achievements, but proper single page templates do not
 * exist in the currently active theme.
 *
 * @param string $content Optional
 * @return type
 * @since 3.0
 */
function dpa_replace_the_content( $content = '' ) {
	// Bail if shortcodes are unset somehow
	if ( ! is_a( achievements()->shortcodes, 'DPA_Shortcodes' ) )
		return $content;

	$new_content = '';

	/**
	 * Use shortcode API to display template parts because they are
	 * already output buffered and ready to fit inside the_content.
	 */

	// Achievement archive
	if ( dpa_is_achievement_archive() ) {

		// Page exists where this archive should be
		$page = dpa_get_page_by_path( dpa_get_root_slug() );
		if ( ! empty( $page ) ) {

			// Restore previously unset filters
			dpa_restore_all_filters( 'the_content' );

			// Remove 'dpa_replace_the_content' filter to prevent infinite loops
			remove_filter( 'the_content', 'dpa_replace_the_content' );

			// Start output buffer
			ob_start();

			// Grab the content of this page
			$new_content = apply_filters( 'the_content', $page->post_content );

			// Clean up the buffer
			ob_end_clean();

			// Add 'dpa_replace_the_content' filter back
			add_filter( 'the_content', 'dpa_replace_the_content' );

		// No page so show the archive
		} else {
			$new_content = achievements()->shortcodes->display_achievement_index();
		}

	// Single achievement post
	} else {
		// Check the post_type
		switch ( get_post_type() ) {

			// Single Forum
			case dpa_get_achievement_post_type() :
				$new_content = achievements()->shortcodes->display_achievement( array( 'id' => get_the_ID() ) );
				break;
		}
	}

	// Juggle the content around and try to prevent unsightly comments
	if ( ! empty( $new_content ) && $new_content != $content ) {

		// Set the content to be the new content
		$content = apply_filters( 'dpa_replace_the_content', $new_content, $content );
		unset( $new_content );

		/**
		 * Supplemental hack to prevent stubborn comments_template() output.
		 *
		 * Note: If a theme uses custom code to output comments, it's possible all of this is a waste of time.
		 * Note: If you need to keep these globals around for any special reason, we've provided a failsafe
		 *       hook to bypass this. You can put in your plugin or theme below:
		 *
		 *       apply_filters( 'dpa_spill_the_beans', '__return_true' );
		 *
		 * @see comments_template() For why we're doing this :)
		 */
		if ( ! apply_filters( 'dpa_spill_the_beans', false ) ) {
			// Empty globals that aren't being used in this loop anymore
			$GLOBALS['withcomments'] = false;
			$GLOBALS['post']         = false;

			// Reset the post data when the next sidebar is fired
			add_action( 'get_sidebar', 'dpa_theme_compat_reset_post_data' );
			add_action( 'get_footer',  'dpa_theme_compat_reset_post_data' );
		}
	}

	// Return possibly hi-jacked content
	return $content;
}

/**
 * Resets the post data after the content has displayed
 *
 * @since 3.0
 */
function dpa_theme_compat_reset_post_data() {
	static $ran = false;

	// Bail if this already ran
	if ( true === $ran )
		return;

	// Reset the post data to whatever our global post is
	wp_reset_postdata();

	// Prevent this from firing again
	remove_action( 'get_sidebar', 'dpa_theme_compat_reset_post_data' );
	remove_action( 'get_footer',  'dpa_theme_compat_reset_post_data' );

	// Set this to true so it does not run again
	$ran = true;
}


/**
 * Helpers
 */

/**
 * Remove the canonical redirect to allow pretty pagination
 *
 * @global unknown $wp_rewrite
 * @param string $redirect_url Redirect url
 * @return bool|string False if it's a topic/forum and their first page, otherwise the redirect url.
 * @since 3.0
 */
function dpa_redirect_canonical( $redirect_url ) {
	global $wp_rewrite;

	// Canonical is for the beautiful
	if ( $wp_rewrite->using_permalinks() ) {

		// If viewing beyond page 1 of several
		if ( 1 < dpa_get_paged() ) {

			// On a single achievement
			if ( dpa_is_single_achievement() ) {
				$redirect_url = false;

			// ...and any single anything else...
			// @todo - Find a more accurate way to disable paged canonicals for paged shortcode usage within other posts.
			} elseif ( is_page() || is_singular() ) {
				$redirect_url = false;
			}
		}
	}

	return $redirect_url;
}

/** Filters *******************************************************************/

/**
 * Removes all filters from a WordPress filter, and stashes them in achievements()
 * in the event they need to be restored later.
 *
 * @global array $merged_filters
 * @global WP_Filter $wp_filter
 * @param string $tag
 * @param int $priority Optional
 * @since 3.0
 */
function dpa_remove_all_filters( $tag, $priority = 0 ) {
	global $merged_filters, $wp_filter;

	// Filters exist
	if ( isset( $wp_filter[$tag] ) ) {

		// Filters exist in this priority
		if ( ! empty( $priority ) && isset( $wp_filter[$tag][$priority] ) ) {

			// Store filters in a backup
			achievements()->filters->wp_filter[$tag][$priority] = $wp_filter[$tag][$priority];

			// Unset the filters
			unset( $wp_filter[$tag][$priority] );

		// Priority is empty
		} else {

			// Store filters in a backup
			achievements()->filters->wp_filter[$tag] = $wp_filter[$tag];

			// Unset the filters
			unset( $wp_filter[$tag] );
		}
	}

	// Check merged filters
	if ( isset( $merged_filters[$tag] ) ) {

		// Store filters in a backup
		achievements()->filters->merged_filters[$tag] = $merged_filters[$tag];

		// Unset the filters
		unset( $merged_filters[$tag] );
	}
}

/**
 * Restores filters from achievements() that were removed using dpa_remove_all_filters()
 *
 * @global array $merged_filters
 * @global WP_Filter $wp_filter
 * @param string $tag
 * @param int $priority
 * @since 3.0
 */
function dpa_restore_all_filters( $tag, $priority = false ) {
	global $merged_filters, $wp_filter;

	// Filters exist
	if ( isset( achievements()->filters->wp_filter[$tag] ) ) {

		// Filters exist in this priority
		if ( ! empty( $priority ) && isset( achievements()->filters->wp_filter[$tag][$priority] ) ) {

			// Restore filter
			$wp_filter[$tag][$priority] = achievements()->filters->wp_filter[$tag][$priority];

			// Clear out our stash
			unset( achievements()->filters->wp_filter[$tag][$priority] );

		// Priority is empty
		} else {

			// Restore filter
			$wp_filter[$tag] = achievements()->filters->wp_filter[$tag];

			// Clear out our stash
			unset( achievements()->filters->wp_filter[$tag] );
		}
	}

	// Check merged filters
	if ( isset( $achievements()->filters->merged_filters[$tag] ) ) {

		// Restore filter
		$merged_filters[$tag] = achievements()->filters->merged_filters[$tag];

		// Clear out our stash
		unset( achievements()->filters->merged_filters[$tag] );
	}
}
