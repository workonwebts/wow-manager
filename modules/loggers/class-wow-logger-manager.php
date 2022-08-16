<?php

/**
 *
 * This class defines all code necessary to log actions.
 *
 * @since      1.0.0
 * @package    WoW_BookStore
 * @subpackage WoW_BookStore/includes
 * @author     Andrea Starz
 */

if ( ! defined( 'ABSPATH' ) ) exit;
require_once("class-wow-logger.php");
if( ! class_exists( 'WoW_Logger_Manager' ) ) {
	class WoW_Logger_Manager extends WoW_Logger{

		/**
		 * The single instance of WoW_BookStore_Settings.
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
		public $logs;
		public $_prefix;
		
		public function __construct($_pref='wowlog',$ref='' , $module='') {
			if (! $ref) return null;
			$this->appRef=$ref;
			if(isset($this->appRef->textdomain)) {
				$this->textdomain=$this->appRef->textdomain;
			} else {
				$this->textdomain=sanitize_file_name(basename(__FILE__,'.php'));
			}
			$this->_prefix=$_pref;
			$this->logs=array();
			$this->basevars=$this->appRef->basevars;
			$this->settings=$this->appRef->settings;
			$this->appdir=trailingslashit(WOW_MANAGER_DIR.'modules/'.$module);
			$this->appurl=trailingslashit(WOW_MANAGER_URI.'modules/'.$module);

			add_action( 'admin_menu', array( $this, 'logger_pages'), 99);
		}
		
		public function add_log($log_name, $log_desc) {
			$this->logs[$log_name]=new WoW_Logger($this->_prefix, $log_name, $log_desc, $this);
		}
		public function get_log($log_name) {
			return $this->logs[$log_name];
		}
		
		public function logger_pages() {
			add_submenu_page($this->appRef->menu_name, __( 'Visualizza Log Operazioni', $this->textdomain ) , '<span class="dashicons dashicons-clock"></span>'. __('Visualizza Log',$this->textdomain), 'manage_options', 'wow-actions-logger', array( $this, 'view_page_logs'),80);
		}
		
		 // logs page callback
		public function view_page_logs() {
			global $wowdb;
			// include page
			include_once( $this->appdir.'logger-view-page.php' );
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
		public static function instance ($_pref='wowlog',$ref='', $mod) {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self( $_pref,$ref, $mod);
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
	
	function WoW_Logger() {
		return "logger";
		return WoW_Logger_Manager::instance();
	}
}

?>