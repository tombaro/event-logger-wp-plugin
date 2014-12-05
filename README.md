=== Plugin Name ===
Contributors: toronja
Donate link: http://klandestino.se
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

Just nu måste en trycka in en massa kod i functions.php för att registrera nya loggningsfunktioner. Ett exempel för att logga när någon loggar ut skulle kunna se ut såhär:


//functions.php

add_action( 'admin_init', 'register_custom_settings' );

function register_custom_settings() {
	add_settings_field(
		'event_logger_logout_option',
		'Log out',
		'event_logger_render_logout_output_html',
		'event-logger',
		'event_logger_custom_section',
		array(
			'option_array_name' => 'event_logger_custom_options',
			'option_key' => 'logout')
		);

	register_setting(
		'event_logger_default_section',
		'event_logger_custom_options',
		'event_logger_validate_input'
		);
}

function event_logger_render_logout_output_html( $args ) {
	$plugin = Event_Logger_Option::get_instance();
	$plugin->event_logger_render_output_checkbox( $args );
	//do_action( 'event_logger_output_checkbox', $options );
}

function event_logger_validate_input( $input ) {
	$plugin = Event_Logger_Option::get_instance();
	return $plugin->event_logger_validate_input( $input );
}

add_action( "wp_logout", "event_logger_wp_logout" );

// user logs out
function event_logger_wp_logout() {
	$settings = get_option( 'event_logger_custom_options' );

	if ( 1 == $settings[ 'logout' ] ) {
		$current_user = wp_get_current_user();
		$current_user_id = $current_user->ID;
		$user_nicename = urlencode($current_user->user_nicename);
		
		$log = [
				'event' => 'Logged out',
				'object_type' => 'user',
				'object_id' => $current_user_id,
				'object_name'=> $user_nicename
			];

		do_action( 'event_logger_log', $log );
	}
}




== Frequently Asked Questions ==

= A question that someone might have =

An answer to that question.

= What about foo bar? =

Answer to foo bar dilemma.

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

= 0.1 =
Initial commits, provides logging for login to predefiend log file

== Arbitrary section ==

You may provide arbitrary sections, in the same format as the ones above.  This may be of use for extremely complicated
plugins where more information needs to be conveyed that doesn't fit into the categories of "description" or
"installation."  Arbitrary sections will be shown below the built-in sections outlined above.

== A brief Markdown Example ==

Ordered list:

1. Some feature
1. Another feature
1. Something else about the plugin

Unordered list:

* something
* something else
* third thing

Here's a link to [WordPress](http://wordpress.org/ "Your favorite software") and one to [Markdown's Syntax Documentation][markdown syntax].
Titles are optional, naturally.

[markdown syntax]: http://daringfireball.net/projects/markdown/syntax
            "Markdown is what the parser uses to process much of the readme file"

Markdown uses email style notation for blockquotes and I've been told:
> Asterisks for *emphasis*. Double it up  for **strong**.

`<?php code(); // goes in backticks ?>`