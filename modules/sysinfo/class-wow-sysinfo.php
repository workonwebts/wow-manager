<?php

if ( ! defined( 'ABSPATH' ) ) exit;
if( ! class_exists( 'WoW_Sysinfo' ) ) {
	class WoW_Sysinfo {

		/**
		 * The single instance of WoW_class.
		 * @var 	object
		 * @access  private
		 * @since 	1.0.0
		 */
		private static $_instance = null;
	
	  /**
		 * The domain translation
		 * @var 	string
		 * @access  public
		 * @since 	1.0.0
		 */
		public $textdomain;
	
	  /**
		 * The parent object
		 * @var 	object
		 * @access  public
		 * @since 	1.0.0
		 */
		public $appRef;
	
	  /**
		 * The settings object
		 * @var 	object
		 * @access  public
		 * @since 	1.0.0
		 */
		public $settings;
	
	  /**
		 * The basevars prefix
		 * @var 	string
		 * @access  public
		 * @since 	1.0.0
		 */
		public $basevars;
		public $appdir;
		public $appurl;
	
		public function __construct ( $ref='' , $module='' ) {
	
			if ( ! $ref ) return;
			$this->appRef=$ref;
			if(isset($this->appRef->textdomain)) {
				$this->textdomain=$this->appRef->textdomain;
			} else {
				$this->textdomain=sanitize_file_name(basename(__FILE__,'.php'));
			}
			$this->basevars=$this->appRef->basevars;
			$this->settings=$this->appRef->settings;
			$this->appdir=trailingslashit(WOW_MANAGER_DIR.'modules/'.$module);
			$this->appurl=trailingslashit(WOW_MANAGER_URI.'modules/'.$module);
	
		// Load Settings
			add_action( 'wow_load_settings_sections', array( $this, 'build_sections'),11);
			add_action( 'wow_load_settings_fields', array( $this, 'build_fields'),21);
			add_action( 'wow_load_settings', array( $this, 'build_settings'),31);
				// pagina personale
				add_action( 'admin_menu', array( $this, 'sysinfo_page'), 199);
		}
		
		 // Manager menu
		public function sysinfo_page() {
			// menu item
				add_submenu_page($this->appRef->menu_name, __( 'Sys Info', $this->textdomain ), '<span class="dashicons dashicons-bell"></span>'.__( 'System Info', $this->textdomain ), 'read', 'wow-sysinfo', array( $this, 'sysinfo_callback'));
	   }

		 // Manager page callback
		public function sysinfo_callback() {
			global $wowdb;
			// include page
			include_once( $this->appdir.'sysinfo-view-page.php' );
	   }
	   

		public function build_sections() {
			/*
			$sec[]=array('sysinfo',
				__( 'Informazioni PHP', $this->textdomain ),
				__( 'Visualizza impostazioni PHP.', $this->textdomain )
			);
			*/
			//$this->settings->add_setting_sections($sec);
		}
		
		public function build_fields() {
			$fld[]=array();
			$this->settings->add_section_fields($fld);
		}
		
		public function build_settings() {
			$this->settings->settings_fields();
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
	public static function instance ( $ref , $mod) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $ref , $mod);
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
