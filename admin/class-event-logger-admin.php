<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      0.3.0
 *
 * @package    Event_Logger
 * @subpackage Event_Logger/admin
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    Event_Logger
 * @subpackage Event_Logger/admin
 * @author     Tom Bergman <tom@klandestino.se>
 */
class Event_Logger_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.3.0
	 * @access   private
	 * @var      string    $event_logger    The ID of this plugin.
	 */
	private $event_logger;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.3.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.3.0
	 * @var      string    $event_logger       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $event_logger, $version ) {

		$this->event_logger = $event_logger;
		$this->version = $version;

		//add_action( 'admin_init', array($this, 'admin_init') );
		//add_action( 'init', array($this, 'init') );
		//add_action( 'admin_menu', array($this, 'admin_menu') );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add the option settings
		add_action( 'admin_init', array( 'Event_Logger_Option', 'get_instance' ) );

	}

	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @since    0.3.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Event_Logger_Admin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Event_Logger_Admin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->event_logger, plugin_dir_url( __FILE__ ) . 'css/event-logger-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @since    0.3.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Event_Logger_Admin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Event_Logger_Admin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->event_logger, plugin_dir_url( __FILE__ ) . 'js/event-logger-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	* Register the administration menu for this plugin into the WordPress Dashboard menu.
	*
	* @since    0.3.0
	*/
	public function add_plugin_admin_menu() {
	/*
	 * Add a settings page for this plugin to the Settings menu.
	 */
		add_options_page(
			__( 'Event Logger Settings', $this->event_logger ),
			__( 'Event Logger', $this->event_logger ),
			'manage_options',
			$this->event_logger,
			array( $this, 'settings_page' )
			);
	}


	//TODO: Ska vi använda det här?
	function settings_page() {
		?>
		<div class="wrap">
			<form action="options.php" method="POST">
			<?php 
				$plugin = Event_Logger::get_instance();
				$plugin_slug = $plugin->get_plugin_slug();
				settings_fields( 'event_logger_default_section' );
				do_settings_sections( $plugin_slug );
				submit_button( __('Save', $plugin_slug ) );
			?>
		</form>
		</div>
		<?php
		
	}

	// user logs in
	function event_logger_wp_login( $user ) {		

		$current_user = wp_get_current_user();
		$user = get_user_by( "login", $user );
		$user_nicename = urlencode( $user->user_nicename );

		if ( $current_user->ID == 0 ) {
			$user_id = $user->ID;
		} else {
			$user_id = $current_user->ID;
		}

		$log = array(
			'event' => 'login',
			'object_type' => 'user',
			'object_id' => $user->ID,
			'object_name'=> $user_nicename,
			'user_id' => $user_id
		);

		$this->event_logger_write_to_log( $log );

	}

	// user logs out 
	function event_logger_wp_logout() { 
		
		$current_user = wp_get_current_user();
		$current_user_id = $current_user->ID;
		$user_nicename = urlencode($current_user->user_nicename);

		$log = array(
			'event' => 'logout',
			'object_type' => 'user',
			'object_id' => $current_user_id,
			'object_name'=> $user_nicename
			);

		$this->event_logger_write_to_log( $log );

	}

	/**
	 * Log failed login attempt to username that exists
	 */
	function event_logger_wp_authenticate_user( $user, $password ) {

		if ( ! wp_check_password( $password, $user->user_pass, $user->ID ) ) {
			
			// call __() to make translation exist
			__("failed to log in because they entered the wrong password", $this->event_logger );

			$description = "";
			$description .= "HTTP_USER_AGENT: " . $_SERVER["HTTP_USER_AGENT"];
			$description .= "\nHTTP_REFERER: " . $_SERVER["HTTP_REFERER"];
			$description .= "\nREMOTE_ADDR: " . $_SERVER["REMOTE_ADDR"];

			$log = array(
				'event' => 'failed_authentication',
				'object_type' => 'user',
				'object_id' => $user->ID,
				'object_name'=> $user->user_login
				);
			
			$this->event_logger_write_to_log( $log );

		}

		return $user;

	}

	//Depends on define('SAVEQUERIES', true); in wp-config.php
	function sql_logger() {
		global $wpdb;
		
		$event = '';

		foreach($wpdb->queries as $q) {

			$event .= $q[0] . " - ($q[1] s)" . "\n";
		}
		$this->event_logger_write_to_log( $event );
	}

	/**
	* Writes to log 
	*
	* @since    0.3.0
	* @var      array    $args       Arguments to write to log.
	*/
	public static function event_logger_write_to_log( $args ) {

		$defaults = array(
			"event" => '',
			"object_type" => '',
			"object_subtype" => '',
			"object_id" => '',
			"object_name" => '',
			"user_id" => 0,
			"description" => '',
			"option_type" => 'event_logger_options'
		);

		$args = wp_parse_args( $args, $defaults );

		if ( self::event_logger_should_log( $args[ 'event' ], $args[ 'option_type' ] ) ) {
			//Add (utc) time
			$event_time = current_time( "mysql" );

			$event = ' Event: '. $args[ "event" ];
			$object_type = ' object_type: '. $args[ "object_type" ];
			$object_subtype = ' object_subtype: '. $args[ "object_subtype" ];
			$object_id = ' object_id: '. $args[ "object_id" ];
			$object_name = ' object_name: '. $args[ "object_name" ];
			$user_id = ' user_id: '. $args[ "user_id" ];
			$description = ' description: '. $args[ "description" ];
			
			$event_text = $event_time . $event . $object_type . $object_id . $object_name . $user_id . $description . "\n"; 
			
			//Append to file
			//Default value for file path
			$log_file = ABSPATH . 'event_logger_wp.log';
			$settings = get_option( 'event_logger_options' );
			if ( isset( $settings[ 'logfilepath' ] ) && '' != $settings[ 'logfilepath' ] ) {

				$log_file = sanitize_text_field( $settings[ 'logfilepath' ] );

				//Should we log to a specific file? (E.g. ip based)
				if ( false !== strpos( $log_file, '%ip%' ) ) {
					$log_file = str_replace( '%ip%', self::get_the_user_ip(), $log_file );
				}
			}
			file_put_contents( $log_file, $event_text, FILE_APPEND );

			if ( headers_sent() ) {
				self::event_logger_log_to_console( $args );
			}

		}

	}

	/**
	* Writes to browser console log 
	*
	* @since    0.3.2
	* @var      array    $data       Arguments to write to log.
	*/
	public static function event_logger_log_to_console( $data ) {

		if ( is_array( $data ) || is_object( $data ) ) {
			print "<script type='text/javascript'>console.log('PHP: " . json_encode( $data ) . "');</script>";
		} else {
			print "<script type='text/javascript'>console.log('PHP: " . $data . "');</script>";
		}

	}

	// Handle sessions to override logging settings on settings page.
	// Intended for temporary logging of specific events.

	private static function event_logger_should_log( $event_to_log, $option_type = '' ) {

		$settings = get_option( 'event_logger_options' );

		// All logging turned off?
		if ( isset( $settings[ 'stop_all_logging' ] ) && 1 == $settings[ 'stop_all_logging' ] ) {
			return false;
		}
			
		//If custom settings are to be used
		if ( 'event_logger_custom_options' == $option_type ) {
			$settings = get_option( 'event_logger_custom_options' );
		}

		if ( ( isset( $settings[ $event_to_log ] ) && 1 == $settings[ $event_to_log ] )
				|| ( isset( $_SESSION[ 'event_logger_' . $event_to_log ] ) 
				&&  $_SESSION[ 'event_logger_' . $event_to_log ] ) ) {
			return true;
		}

		return false;

	}

	function event_logger_get_action_events() {

		//TODO: Array av godkända val att logga? 
		if ( isset( $_GET[ 'event-logger-action' ] ) ) {
			$action = sanitize_text_field( $_GET[ 'event-logger-action' ] );
			$_SESSION[ 'event_logger_' . $action ] = true;
		}

	}

	function event_logger_register_session() {
		
		if( ! session_id() && current_user_can( 'manage_options' ) ) {
			session_start();
			//session_unset();
		}

	}

	function event_logger_destroy_session() {
		
		if ( isset( $_GET[ 'event-logger-action' ] ) 
			&& 'quit_logging' == $_GET[ 'event-logger-action' ] 
			&& current_user_can( 'manage_options' ) ) {
			session_destroy();
		}

	}

	//End session handling


	// Display User IP in WordPress
	static function get_the_user_ip() {

		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		//check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			//to check ip is pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return apply_filters( 'get_the_user_ip', $ip );

	}

}
