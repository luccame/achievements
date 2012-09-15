<?php
/**
 * User functions
 *
 * @package Achievements
 * @subpackage UserFunctions
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Checks if user is active
 * 
 * @param int $user_id Optional. The user ID to check
 * @return bool True if public, false if not
 * @since 3.0
 */
function dpa_is_user_active( $user_id = 0 ) {
	// Default to current user
	if ( empty( $user_id ) && is_user_logged_in() )
		$user_id = get_current_user_id();

	// No user to check
	if ( empty( $user_id ) )
		return false;

	// Check spam
	if ( dpa_is_user_spammer( $user_id ) )
		return false;

	// Check deleted
	if ( dpa_is_user_deleted( $user_id ) )
		return false;

	// Assume true if not spam or deleted
	return true;
}

/**
 * Checks if the user has been marked as a spammer.
 *
 * @param int|WP_User $user_id int Optional. The ID for the user, or a WP_User object.
 * @return bool True if spammer, False if not.
 * @since 3.0
 */
function dpa_is_user_spammer( $user_id = 0 ) {
	// Default to current user
	if ( empty( $user_id ) && is_user_logged_in() )
		$user_id = get_current_user_id();

	// No user to check
	if ( empty( $user_id ) )
		return false;

	// Assume user is not spam
	$is_spammer = false;

	// Get user data
	if ( is_a( $user_id, 'WP_User' ) )
		$user = $user_id;
	else
		$user = get_userdata( $user_id );

	// No user found
	if ( empty( $user ) ) {
		$is_spammer = false;

	// User found
	} else {

		// Check if spam
		if ( ! empty( $user->spam ) )
			$is_spammer = true;

		if ( 1 == $user->user_status )
			$is_spammer = true;
	}

	return apply_filters( 'dpa_is_user_spammer', (bool) $is_spammer );
}

/**
 * Mark a user's stuff as spam (or just delete it) when the user is marked as spam
 *
 * @param int $user_id Optional. User ID to spam.
 * @since 3.0
 */
function dpa_make_spam_user( $user_id = 0 ) {
	// Bail if no user ID
	if ( empty( $user_id ) )
		return;

	// Bail if user ID is super admin
	if ( is_super_admin( $user_id ) )
		return;

	// @todo Do stuff here
}

/**
 * Mark a user's stuff as ham when the user is marked as not a spammer
 *
 * @param int $user_id Optional. User ID to unspam.
 * @since 3.0
 */
function dpa_make_ham_user( $user_id = 0 ) {
	// Bail if no user ID
	if ( empty( $user_id ) )
		return;

	// Bail if user ID is super admin
	if ( is_super_admin( $user_id ) )
		return;

	// Do stuff here
}

/**
 * Checks if the user has been marked as deleted.
 *
 * @param int|WP_User $user_id int Optional. The ID for the user, or a WP_User object.
 * @return bool True if deleted, False if not.
 * @since 3.0
 */
function dpa_is_user_deleted( $user_id = 0 ) {
	// Default to current user
	if ( empty( $user_id ) && is_user_logged_in() )
		$user_id = get_current_user_id();

	// No user to check
	if ( empty( $user_id ) )
		return false;

	// Assume user is not deleted
	$is_deleted = false;

	// Get user data
	if ( is_a( $user_id, 'WP_User' ) )
		$user = $user_id;
	else
		$user = get_userdata( $user_id );

	// No user found
	if ( empty( $user ) ) {
		$is_deleted = true;

	// User found
	} else {

		// Check if deleted
		if ( ! empty( $user->deleted ) )
			$is_deleted = true;

		if ( 2 == $user->user_status )
			$is_deleted = true;
	}

	return apply_filters( 'dpa_is_user_deleted', (bool) $is_deleted );
}


/**
 * These things happen when a user unlocks an achievement.
 */

/**
 * When an achievement is unlocked, give the points to the user.
 *
 * @param object $achievement_obj The Achievement object to send a notification for.
 * @param int $user_id ID of the user who unlocked the achievement.
 * @param int $progress_id The Progress object's ID.
 * @since 3.0
 */
function dpa_send_points( $achievement_obj, $user_id, $progress_id ) {
	// Let other plugins easily bypass sending points.
	if ( ! apply_filters( 'dpa_maybe_send_points', true, $achievement_obj, $user_id, $progress_id ) )
		return;

	// Get the user's current total points plus the point value for the unlocked achievement
	$points = dpa_get_user_points( $user_id ) + dpa_get_achievement_points( $achievement_obj->ID );
	$points = apply_filters( 'dpa_send_points_value', $points, $achievement_obj, $user_id, $progress_id );

	// Give points to user
	dpa_update_user_points( $points, $user_id );

	// Allow other things to happen after the user's points have been updated
	do_action( 'dpa_send_points', $achievement_obj, $user_id, $progress_id, $points );
}


/**
 * User avatar
 */

/**
 * Output the avatar link of a user
 *
 * @param array $args See dpa_get_user_avatar_link() documentation.
 * @since 3.0
 */
function dpa_user_avatar_link( $args = 0 ) {
	echo dpa_get_user_avatar_link( $args );
}
	/**
	 * Return the avatar link of a user
	 *
	 * @param array $args This function supports these arguments:
	 *  - int $size If we're showing an avatar, set it to this size
	 *  - string $type What type of link to return; either "avatar", "name", or "both".
	 *  - int $user_id The ID for the user.
	 * @return string
	 * @since 3.0
	 */
	function dpa_get_user_avatar_link( $args = array() ) {
		$defaults = array(
			'size'    => 50,
			'type'    => 'both',
			'user_id' => 0,
		);
		$r = dpa_parse_args( $args, $defaults, 'get_user_avatar_link' );
		extract( $r );

		// Default to current user
		if ( empty( $user_id ) && is_user_logged_in() )
			$user_id = get_current_user_id();

		// Assemble some link bits
		$user_link = array();
		$user_url  = get_author_posts_url( $user_id );

		// Get avatar
		if ( 'avatar' == $type || 'both' == $type )
			$user_link[] = sprintf( '<a href="%1$s">%2$s</a>', esc_url( $user_url ), get_avatar( $user_id, $size ) );

		// Get display name
		if ( 'avatar' != $type )
			$user_link[] = sprintf( '<a href="%1$s">%2$s</a>', esc_url( $user_url ), get_the_author_meta( 'display_name', $user_id ) );

		// Piece together the link parts and return
		$user_link = join( '&nbsp;', $user_link );

		return apply_filters( 'dpa_get_user_avatar_link', $user_link, $args );
	}
