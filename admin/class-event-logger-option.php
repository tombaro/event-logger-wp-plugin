<?php

/**
 * @package     Event_Logger
 * @subpackage  Event_Logger_Admin
 * @author      Tom Bergman <tom@klandestino.se
 * @license     GPL-2.0+
 * @link        http://klandestino.se
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Event_Logger_Option {
	/**
	 * Instance of this class.
	 *
	 * @since    0.3.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.3.0
	 * @access   private
	 * @var      string    $event_logger    The ID of this plugin.
	 */
	private $event_logger;

	/**
	 * Initialize the plugin by registrating settings
	 *
	 * @since     0.3.0
	 */
	private function __construct() {

		// Get $plugin_slug from public plugin class.
		$plugin = Event_Logger::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();
		$this->event_logger = $plugin->get_event_logger();

		// Register Settings
		$this->register_settings();
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     0.3.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register fields
	 *
	 * @since     0.3.0
	 */
	public function register_settings() {

		//Check if options exists in database
		if( false == get_option( 'event_logger_options' ) ) {
			add_option( 'event_logger_options', apply_filters( 'event_logger_default_input_options', $this->event_logger_default_input_options() ) );
		}

		//TODO: Gör samma sak med custom options. Behöver det fixas nåt så att det går att 
		// hooka in i defaultsen där?
	  
		add_settings_section(
			// ID used to identify this section and with which to register options
			'event_logger_default_section', 
			// Title to be displayed on the administration page
			'Event Logger Default Section',  
			// Callback used to render the description of the section
			array( $this, 'event_logger_display_default_section' ),
			// Page on which to add this section of options
			$this->plugin_slug
			);

		//LOG FILE PATH
		add_settings_field(
			// ID used to identify the field throughout the theme
			'event_logger_logfile_option',
			// The label to the left of the option interface element
			__( 'Path to log file', $this->event_logger ),
			// The name of the function responsible for rendering the option interface
			array( $this, 'event_logger_render_output_textbox' ),
			// The page on which this option will be displayed
			$this->plugin_slug,
			// The name of the section to which this field belongs
			'event_logger_default_section'
			// Options to callback
			//array( 'setting_name' => 'login_option' )
		);

		register_setting(
			// The settings group name. Must exist prior to the register_setting call.
			'event_logger_default_section',
			// The name of an option to sanitize and save.
			'event_logger_options',
			// The callback function for sanitization and validation
			array( $this, 'event_logger_validate_input' )
			);

		//LOGIN
		add_settings_field(
			'event_logger_login_option',
			__( 'Log in', $this->event_logger ),
			array( $this, 'event_logger_render_output_checkbox' ),
			$this->plugin_slug,
			'event_logger_default_section',
			array(
				'option_array_name' => 'event_logger_options',
				'option_key' => 'login' )
			);

		//LOGOUT
		add_settings_field(
			'event_logger_logout_option',
			__( 'Log out', $this->event_logger ),
			array( $this, 'event_logger_render_output_checkbox' ),
			$this->plugin_slug,
			'event_logger_default_section',
			array(
				'option_array_name' => 'event_logger_options',
				'option_key' => 'logout' )
			);

		//FAILED AUTHENTICATION
		add_settings_field(
			'event_logger_failed_authentication_option',
			__( 'Failed authentication', $this->event_logger ),
			array( $this, 'event_logger_render_output_checkbox' ),
			$this->plugin_slug,
			'event_logger_default_section',
			array(
				'option_array_name' => 'event_logger_options',
				'option_key' => 'failed_authentication' )
			);



		//CUSTOM SECTION

		//Check if options exists in database
		if( false == get_option( 'event_logger_custom_options' ) ) {
			add_option( 'event_logger_custom_options', apply_filters( 'event_logger_custom_input_options', $this->event_logger_custom_input_options() ) );
		}

		add_settings_section(
			'event_logger_custom_section',
			__( 'Event Logger custom section', $this->event_logger ),  
			array( $this, 'event_logger_display_custom_section' ),
			$this->plugin_slug
			);

		register_setting(
			'event_logger_default_section',
			'event_logger_custom_options',
			array( $this, 'event_logger_validate_input' )
			);

			//Hämta "externa" options med prefix "..." och sätt in de i en egen section
			//Men har vi all info vi behöver? Validering beroende på typ, spara
	}

	/**
	 * This function generate the HTML input element for
	 * an event logger checkbox option and shows its value.
	 *
	 * @since 0.3.0
	 */
	public function event_logger_render_output_checkbox( $args ) {
		$settings = get_option( $args[ 'option_array_name' ] );
		$option_key = $args[ 'option_key' ];
		$option_value = ( isset( $settings[ $option_key ] ) ? $settings[ $option_key ] : 0 );

		$html = "<input type='checkbox' id='" . $option_key . "' name='" . $args[ 'option_array_name' ] . "[" . $option_key . "]' value='1' " . checked( '1', $option_value, false ) . " />";
		//TODO this? $html .= "<label for='login'> "  . $settings[0] . "</label>";

		echo $html;

	}

	function event_logger_render_output_textbox( $options ) {
		$settings = (array) get_option( 'event_logger_options' );
		$log_path = esc_attr( $settings[ 'logfilepath' ] );
		echo "<input type='text' id='logfilepath' name='event_logger_options[logfilepath]' value='" . $log_path . "' />";
	
	}

	/**
	 * This function provides a simple description for the Event Logger Options page.
	 * This function is being passed as a parameter in the add_settings_section function.
	 *
	 * @since 0.3.0
	 */
	public function event_logger_display_default_section() {
		//TODO: Flytta till event-logger-display yadayada
		//require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/event-logger-admin-display.php';
		echo "<p>This is the default section with common log options</p>";
	}

	public function event_logger_display_custom_section() {
		//TODO: Flytta till event-logger-display yadayada
		//require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/event-logger-admin-display.php';
		echo "<p>This is the Custom section</p>";
	}

	/**
	* Validation callback for the options.
	*
	* @param  $input  The value user inputed
	*
	* @return         The validated value(s).
	*
	* @since 0.3.0
	*/
	public function event_logger_validate_input( $input ) {
		$output = array();

		if ( isset( $input ) ) {
			foreach ( $input as $key => $value ) {
				if( isset( $input[ $key ] ) ) {
					$output[ $key ] = strip_tags( stripslashes( $input[ $key ] ) );
				}
			}

			//TODO: Generaliserat felmeddelande	
		}

		return apply_filters( 'event_logger_validate_input', $output, $input );
	}

	/**
	 * Provides default values for the Default Options.
	 */
	function event_logger_default_input_options() {
		
		$defaults = array(
			'login'	=>	'',
			'logfilepath' => ABSPATH . 'event_logger_wp.log',
			'logout' => '',
			'failed_authentication' => ''
		);
		
		return apply_filters( 'event_logger_default_input_options', $defaults );
		
	}

	/**
	 * Provides default values for the Custom Options.
	 */
	function event_logger_custom_input_options() {
		
		$defaults = array(
			'custom'	=>	''
			//'show_content'		=>	'',
			//'show_footer'		=>	'',
		);
		
		return apply_filters( 'event_logger_custom_input_options', $defaults );
		
	}

}