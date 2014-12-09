<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       http://example.com
 * @since      0.3.1
 *
 * @package    Event_Logger
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( ! is_user_logged_in() ) {
	wp_die( 'You must be logged in to run this script.' );
}

if ( ! current_user_can( 'install_plugins' ) ) {
	wp_die( 'You do not have permission to run this script.' );
}

// Default options to delete
if ( false != get_option( 'event_logger_options' ) ) {
	delete_option( 'event_logger_options' );
}

// Custom options to delete
if ( false != get_option( 'event_logger_custom_options' ) ) {
	delete_option( 'event_logger_custom_options' );
}