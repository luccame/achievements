<?php
/**
 * Base interface and class for adding support for your plugin to Achievements.
 *
 * @package Achievements
 * @subpackage CoreClasses
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * The objects that you store in achievements()->extends->your_plugin needs to
 * implement this interface. Please note the return types in the phpdoc.
 *
 * @since 3.0
 */
interface DPA_Extension_Interface {
	/**
	 * Returns details of actions from this plugin that Achievements can use.
	 *
	 * Note that you still have to add these into the dpa_event taxonomy yourself.
	 *
	 * @return array e.g. array[0] = array( 'action' => 'publish_post', name => 'Publish blog post', 'description' => 'This event occurs when a blog post has been published. Reward authors for publishing frequently.' );
	 * @since 3.0
	 */
	public function get_actions();

	/**
	 * Returns array of key/value pairs for each contributor to this plugin (user name, gravatar URL, profile URL).
	 *
	 * @return array e.g. array[0] = array( 'username' => 'Paul Gibbs', 'gravatar' => 'http://www.gravatar.com/avatar/3bc9ab796299d67ce83dceb9554f75df', 'profile' => 'http://profiles.wordpress.org/DJPaul' )
	 * @since 3.0
	 */
	public function get_contributors();

	/**
	 * Plugin description
	 *
	 * @return string
	 * @since 3.0
	 */
	public function get_description();

	/**
	 * URL to plugin image.
	 *
	 * SHOULD be local (e.g. on user's own site, rather than linking to your own site).
	 *
	 * @return string
	 * @since 3.0
	 */
	public function get_image_url();

	/**
	 * Plugin name
	 *
	 * @return string
	 * @since 3.0
	 */
	public function get_name();

	/**
	 * URL to a news RSS feed for this plugin. This may be the author's own website.
	 *
	 * @return string
	 * @since 3.0
	 */
	public function get_rss_url();

	/**
	 * Plugin slug
	 *
	 * A unique string representing your plugin. This is used for keying indexes
	 * and is also output on elements' class property in the wp-admin screens.
	 *
	 * @return string
	 * @since 3.0
	 */
	public function get_slug();

	/**
	 * URL to your plugin on WordPress.org
	 *
	 * @return string
	 * @since 3.0
	 */
	public function get_wporg_url();
}

/**
 * Adding support to Achievements for your plugin can be a little complicated if
 * the action is a built-in WordPress action for a post types. This abstract
 * class gives you a starting point to more easily add support for such plugins.
 *
 * @since 3.0
 */
abstract class DPA_CPT_Extension implements DPA_Extension_Interface {
	/**
	 * For actions that are in WordPress core and handle post types, update the
	 * user ID from the logged in user to the post author's ID (e.g. for draft
	 * posts which are then published by another user).
	 *
	 * In your class you need to add_filter this method to 'dpa_handle_event_user_id'.
	 * In your implementation you need to check that $event_name matches the
	 * name of the action that your plugin implements.
	 *
	 * This method assumes that $func_args[0] is the Post object.
	 *
	 * @param int    $user_id    Logged in user's ID
	 * @param string $event_name Name of the event
	 * @param array  $func_args  Arguments that were passed to the action
	 * @return int New user ID
	 * @since 3.0
	 */
	public function modify_user_id_for_post( $user_id, $event_name, $func_args ) {
		$post = $func_args[0];

		if ( ! empty( $post->post_author ) )
			return (int) $post->post_author;
		else
			return $user_id;
	}
}