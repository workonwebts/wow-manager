<?php

if ( ! defined( 'ABSPATH' ) ) exit;
if( ! class_exists( 'WoW_Settings' ) ) {
class WoW_Settings {

	/**
	 * The single instance of WoW_Class.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The main plugin object.
	 * @var 	object
	 * @access  public
	 * @since 	1.0.0
	 */
	public $appRef = null;

	/**
	 * Prefix for plugin settings.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $base = '';

	/**
	 * Available settings for plugin.
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = array();
	public $sections = array();
	public $opt_title;
	public $opt_menu;
	public $textdomain;

	public function __construct ( $ref, $opt_title="Pagina Impostazioni", $opt_menu="Impostazioni" ) {
		$this->appRef = $ref;

		$this->base = $this->appRef->basevars;
		
		$this->textdomain = $this->appRef->textdomain;
		
		$this->opt_title = $opt_title;
		$this->opt_menu = $opt_menu;

		// Initialise settings
		add_action( 'init', array( $this, 'init_settings' ), 11 );

		// Register plugin settings
		add_action( 'admin_init' , array( $this, 'register_settings' ), 11 );

		// Add settings page to menu
		add_action( 'admin_menu' , array( $this, 'add_menu_item' ), 15 );
		// add script & css
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_settings_enqueue_assets' ), 19, 1 );

		// Add settings link to plugins page
		add_filter( 'plugin_action_links_' . plugin_basename( $this->appRef->file ) , array( $this, 'add_settings_link' ) );
	}

	/**
	 * Initialise settings
	 * @return void
	 */
	public function init_settings () {
		do_action( 'wow_load_settings_sections'); // utilizzare add_action('wow_load_settings_sections',nomefunzione o istanza->add__,8,1);
		do_action( 'wow_load_settings_fields'); // utilizzare add_action('wow_load_settings_fields',nomefunzione o istanza->add__,9,1);
		do_action( 'wow_load_settings'); // utilizzare add_action('wow_load_settings',nomefunzione o istanza->add__,10,1);
//		$this->settings = $this->settings_fields();
	}

	/**
	 * Add settings page to admin menu
	 * @return void
	 */
	public function add_menu_item () {
		$page = add_options_page( $this->opt_title , $this->opt_menu , 'manage_options' , $this->appRef->_token . '_settings' ,  array( $this, 'settings_output' ) );
		add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );	
	}
	// add script
	public function admin_settings_enqueue_assets() {

		do_action('wow_load_settings_assets');

    	wp_register_script( $this->appRef->_token . '-settings-js', $this->appRef->assets_url . 'js/settings' . $this->appRef->script_suffix . '.js', array( 'jquery' ), '1.0.0' );
    	wp_enqueue_script( $this->appRef->_token . '-settings-js' );
	}
	// add styles
	public function settings_output() {
		$this->settings_page();
	}

	/**
	 * Load settings JS & CSS
	 * @return void
	 */
	public function settings_assets () {

		// We're including the farbtastic script & styles here because they're needed for the colour picker
		// If you're not including a colour picker field then you can leave these calls out as well as the farbtastic dependency for the wpt-admin-js script below
/*
		wp_register_style( $this->appRef->_token . '-farbtastic', esc_url( $this->appRef->assets_url ) . 'farbtastic/farbtastic.css', array(), $this->appRef->version );
		wp_register_script( $this->appRef->_token . '-farbtastic', esc_url( $this->appRef->assets_url ) . 'farbtastic/farbtastic.js', array('jquery'), $this->appRef->version );
		
		wp_enqueue_style( $this->appRef->_token . '-farbtastic' );
    	wp_enqueue_script( $this->appRef->_token . '-farbtastic' );
*/
    	// We're including the WP media scripts here because they're needed for the image upload field
    	// If you're not including an image upload then you can leave this function call out
    	wp_enqueue_media();
	}

	/**
	 * Add settings link to plugin list table
	 * @param  array $links Existing links
	 * @return array 		Modified links
	 */
	public function add_settings_link ( $links ) {
		$settings_link = '<a href="options-general.php?page=' . $this->appRef->_token . '_settings">' . __( 'Impostazioni', $this->textdomain ) . '</a>';
  		array_push( $links, $settings_link );
  		return $links;
	}
	
	/**
	 * Add settings section
	 * @param  string $name Section ID
	 * @param  string $title Section Title
	 * @param  string $desc Section Description
	 * @return none
	 */
	public function add_setting_section($name,$title,$desc) {
		$this->sections[$name]=array('title'=>$title, 'description'=>$desc, 'fields'=>array());
	}
	
	/**
	 * Add settings sections
	 * @return none
	 */
	public function add_setting_sections($secs) {
		foreach($secs as $sec) {
			$this->add_setting_section($sec[0], $sec[1], $sec[2]);
		}
	}

	/**
	 * Add field to section
	 * @param  string $sec Section ID
	 * @param  array $field Field to add
	 * @return none
	 */
	public function add_section_field($sec,$field) {
		$this->sections[$sec]['fields'][]=$field;
	}

	/**
	 * Add fields to section
	 * @return none
	 */
	public function add_section_fields($flds) {
		foreach($flds as $fld) {
			if (count($fld)>0) {
				$this->add_section_field($fld[0], $fld[1]);
			}
		}
	}


	/**
	 * Build settings fields
	 * @return array Fields to be displayed on settings page
	 */
	public function settings_fields () {
//		$settings = apply_filters( $this->appRef->_token . '_settings_fields', $settings );
		if (! empty($this->sections)) {
			$this->settings=$this->sections;
			$settings = apply_filters( $this->appRef->_token . '_settings_fields', $this->sections );
		}
		return $settings;
	}
	
	public function get_fieldnames_of_section($sec) {
		$flds=$this->sections[$sec]['fields'];
		$aname=array();
		foreach($flds as $fld) {
			$aname[]=$fld['id'];
		}
		return $aname;
	}

	/**
	 * Register plugin settings
	 * @return void
	 */
	public function register_settings () {
		if ( is_array( $this->settings ) ) {

			// Check posted/selected tab
			$current_section = '';
			if ( isset( $_POST['tab'] ) && $_POST['tab'] ) {
				$current_section = $_POST['tab'];
			} else {
				if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
					$current_section = $_GET['tab'];
				}
			}

			foreach ( $this->settings as $section => $data ) {

				if ( $current_section && $current_section != $section ) continue;

				// Add section to page
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), $this->appRef->_token . '_settings' );

				foreach ( $data['fields'] as $field ) {

					// Validation callback for field
					$validation = '';
					if ( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}
					// Display callback for field
					$display = array( $this->appRef->admin, 'display_field' );
					if ( isset( $field['display'] ) ) {
						$display = array( $this->appRef, $field['display'] );
					}
					// abilita/disabilita opzione
					// Add class on disabled
					$disabled = '';
					$disabled_class = '';
					if ( isset( $field['disabled'] ) && $field['disabled']!='no' ) {
						if (is_array($field['disabled'])) {
							$myFunc=$field['disabled'][0];
							if( isset($field['disabled'][1])) {
								if (is_array($field['disabled'][1]) ) {
									$myParam=$field['disabled'][1];
								} else {
									$myParam=array($field['disabled'][1]);
								}
							} else {
								$myParam=array();
							}
						} else {
							$myFunc=$field['disabled'];
							$myParam=array();
						}
						if (function_exists($myFunc)) {
							if(call_user_func_array($myFunc,$myParam)=='yes') {
								$disabled = ' disabled="disabled"';
								$disabled_class = 'class_disable';
							}
						} else {
							$disabled = ' disabled="disabled"';
							$disabled_class = 'class_disable';
						}
					}

					// Register field
					$option_name = $this->base . $field['id'];
					register_setting( $this->appRef->_token . '_settings', $option_name, $validation );

					// Add field to page
					add_settings_field( $field['id'], $field['label'], $display, $this->appRef->_token . '_settings', $section, array( 'field' => $field, 'prefix' => $this->base, 'class' => $disabled_class ) );
				}
 
				if ( ! $current_section ) break;
			}
		}
	}

	public function settings_section ( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html;
	}

	/**
	 * Load settings page content
	 * @return void
	 */
	public function settings_page () {

		// Build page HTML
		$html = '<div class="wrap" id="' . $this->appRef->_token . '_settings">' . "\n";
			$html .= '<h2>' . $this->opt_title . __( ' Settings' , $this->textdomain ) . '</h2>' . "\n";

			$tab = '';
			if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
				$tab .= $_GET['tab'];
			}

			// Show page tabs
			if ( is_array( $this->settings ) && 1 < count( $this->settings ) ) {

				$html .= '<h2 class="nav-tab-wrapper">' . "\n";

				$c = 0;
				foreach ( $this->settings as $section => $data ) {

					// Set tab class
					$class = 'nav-tab';
					if ( ! isset( $_GET['tab'] ) ) {
						if ( 0 == $c ) {
							$class .= ' nav-tab-active';
						}
					} else {
						if ( isset( $_GET['tab'] ) && $section == $_GET['tab'] ) {
							$class .= ' nav-tab-active';
						}
					}

					// Set tab link
					$tab_link = add_query_arg( array( 'tab' => $section ) );
					if ( isset( $_GET['settings-updated'] ) ) {
						$tab_link = remove_query_arg( 'settings-updated', $tab_link );
					}

					// Output tab
					$html .= '<a href="' . $tab_link . '" class="' . esc_attr( $class ) . '">' . esc_html( $data['title'] ) . '</a>' . "\n";

					++$c;
				}

				$html .= '</h2>' . "\n";
			}

			$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

				// Get settings fields
				ob_start();
				settings_fields( $this->appRef->_token . '_settings' );
				do_settings_sections( $this->appRef->_token . '_settings' );
				$html .= ob_get_clean();

				$html .= '<p class="submit">' . "\n";
					$html .= '<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '" />' . "\n";
					$html .= get_submit_button();
//					$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings' , $this->textdomain ) ) . '" />' . "\n";
				$html .= '</p>' . "\n";
			$html .= '</form>' . "\n";
		$html .= '</div>' . "\n";

		echo $html;
	}

	/**
	 * Main WoW_Class Instance
	 *
	 * Ensures only one instance of WoW_Class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WoW_BookStore()
	 * @return Main WoW_Class instance
	 */
	public static function instance ( $ref, $opt_title, $opt_menu ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $ref, $opt_title, $opt_menu );
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->appRef->version );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->appRef->version );
	} // End __wakeup()

}
}
