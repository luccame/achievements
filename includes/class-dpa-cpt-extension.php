<?php
/**
 * Base class for adding support for your plugins to Achievements that will
 * implement post type actions.
 *
 * To add support for your plugin to Achievements, you need to create a new
 * class derived from either {@link DPA_Extension} or {@link DPA_CPT_Extension}.
 *
 * @package Achievements
 * @see {@link DPA_Extension}
 * @subpackage CoreClasses
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Adding support to Achievements for your plugin can be a little complicated if
 * the event is a built-in WordPress action for a post types. This abstract
 * class gives you a starting point to more easily add support for such plugins.
 * Otherwise, use {@link DPA_Extension}.
 *
 * A note about WordPress post type actions. For example, the "publish_post" action happens when
 * the user publishes a new post. Simple, but from that action it's not efficient to find out if
 * the post has been previously published -- maybe someone's just corrected a typo. We use an
 * alternative post transition action to get around this, e.g. "draft_to_publish".
 *
 * The problem with draft_to_publish is that it is too generic (doesn't specify post type) and
 * we will probably have multiple extensions that all want to use this action. We use the dpa_event
 * taxonomy to find out which actions we need to listen to for the active achievements and each
 * term must have a unique slug.
 *
 * To avoid that problem, we return fake action(s) from DPA_Extension::get_actions() and return
 * the real action(s) from DPA_CPT_Extension::get_generic_cpt_actions(). In DPA_CPT_Extension::event_name()
 * we listen for the real action(s) and if the post type belongs to this extension's plugin, we'll
 * swap the generic action name for our fake action name.
 *
 * Simples.
 *
 * @since 3.0
 */
abstract class DPA_CPT_Extension extends DPA_Extension {
	/**
	 * Constructor
	 *
	 * @since 3.0
	 */
	public function __construct() {
		add_filter( 'dpa_filter_events',     array( get_class( $this ), 'get_generic_cpt_actions' ), 1,  1 );
		add_filter( 'dpa_handle_event_name', array( get_class( $this ), 'event_name'              ), 10, 2 );
	}

	/**
	 * Add generic post type actions to the list of events that Achievements will listen for.
	 *
	 * Be sure to array_merge the new events that you are adding to the $events argument.
	 *
	 * @param array $events
	 * @return array
 	 * @since 3.0
 	 */
 	abstract public function get_generic_cpt_actions( $events );

 	/**
	 * Filters the event name which is currently being processed.
	 *
	 * Returning a value different from $event_name will make Achievements think that
	 * it was the action that was originally triggered.
	 *
	 * @param string $name Action name
	 * @param array $func_args Optional; action's arguments, from func_get_args().
	 * @return string|bool Action name or false to skip any further processing
	 * @since 3.0
	 */
	abstract function event_name( $event_name, $func_args );

	/**
	 * For actions that are in WordPress core and handle post types, update the
	 * user ID from the logged in user to the post author's ID (e.g. for draft
	 * posts which are then published by another user).
	 *
	 * In your implementation you must check that $event_name matches the name of the
	 * action that your plugin implements.
	 *
	 * This method assumes that $func_args[0] is the Post object.
	 *
	 * @param int    $user_id    Logged in user's ID
	 * @param string $event_name Name of the event
	 * @param array  $func_args  Arguments that were passed to the action
	 * @return int New user ID
	 * @since 3.0
	 */
	public function get_post_author( $user_id, $event_name, $func_args ) {
		$post = $func_args[0];

		if ( ! empty( $post->post_author ) )
			return (int) $post->post_author;
		else
			return $user_id;
	}
}