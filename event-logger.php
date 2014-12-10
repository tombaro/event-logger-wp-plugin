<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             0.3.0
 * @package           Event_Logger
 *
 * @wordpress-plugin
 * Plugin Name:       Event logger
 * Plugin URI:        http://klandestino.se
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress dashboard.
 * Version:           0.3.0
 * Author:            Tom Bergman
 * Author URI:        http://klandestino.se
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       event-logger
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

//TODO: Move to loader class?
add_action( 'event_logger_log', array( 'Event_Logger_Admin', 'event_logger_write_to_log' ) );
add_action( 'event_logger_output_checkbox', array( 'Event_Logger_Option', 'event_logger_render_output_checkbox' ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-event-logger-activator.php
 */
function activate_event_logger() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-event-logger-activator.php';
	Event_Logger_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-event-logger-deactivator.php
 */
function deactivate_event_logger() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-event-logger-deactivator.php';
	Event_Logger_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_event_logger' );
register_deactivation_hook( __FILE__, 'deactivate_event_logger' );

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-event-logger.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.3.0
 */
function run_event_logger() {

	$plugin = new Event_Logger();
	$plugin->run();

}
run_event_logger();
