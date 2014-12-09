=== Plugin Name ===
Contributors: toronja
Tags: log, logging, events
Requires at least: 3.0.1
Tested up to: 4.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin aims at provide some simple and customizable wordpress log options, primary direct to file. 

== Description ==


== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `event-logger.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress


*To add a custom logging feature.*
Below is an example code to add logging for saving a post in Wordpress. Please note the comments in the code for some specific parameter names that has to be set properly.

`
//functions.php

	add_action( 'admin_init', 'add_custom_settings' );

	function add_custom_settings() {
		add_settings_field(
			'event_logger_save_post_option',
			'Save post',
			'event_logger_render_output_html',
			'event-logger',
			'event_logger_custom_section',
			array(
				'option_array_name' => 'event_logger_custom_options',
				'option_key' => 'save_post') //Must match the "event" param in the array sent to loggin function
			);
	}

	//Renders the checkbox in admin settings for the Event Logger plugin
	function event_logger_render_output_html( $args ) {
		$plugin = Event_Logger_Option::get_instance();
		$plugin->event_logger_render_output_checkbox( $args );
	}

	// log when user saves posts						 
	add_action( "save_post", "event_logger_save_post" );

	function event_logger_save_post( $post_id ) {

		if ( false == wp_is_post_revision( $post_id ) ) {

			// not a revision
			// it should also not be of type auto draft
			$post = get_post( $post_id );
			if ( "auto-draft" != $post->post_status ) {

				$log = array(
					'event' => 'save_post', //This must match the "option_key" in add_custom_settings above
					'object_type' => 'post',
					'object_id' => $post_id,
					'object_name'=> $post->post_name,
					'user_id' => $post->post_author,
					'option_type' => 'event_logger_custom_options' //Always set to this value, to separate from defult Event Logger options
					);

				do_action( 'event_logger_log', $log );

			}
		}

	}
`



== Frequently Asked Questions ==

= A question that someone might have =

An answer to that question.


== Changelog ==

= 0.3.0 =
Enable custom logging options to be added from code.

= 0.1.0 =
Initial commits, provides logging for login to predefiend log file
