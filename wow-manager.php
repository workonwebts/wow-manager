<?php
/*
Plugin Name: WoW Manager
Plugin URI: http://www.work-on-web.it/
Description: Gestione Funzioni di sincronizzazione e importazione di prodotti, categorie, clienti e ordini da WoW-Commerce a WordPress/Woocommerce
Author: Andrea Starz
Version: 2.5.0
Author URI: http://www.work-on-web.it/
Text Domain: wow-manager
License:     GPL-3.0+
License URI: http://www.gnu.org/licenses/gpl-3.0.txt
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // disable direct access
}
 // Set the version number of the plugin.
define( 'WOW_MANAGER_VERSION', '2.3.1' );
 // Set the slug of the plugin.
define( 'WOW_MANAGER_SLUG', basename( dirname( __FILE__ ) ) );
define( 'WOWBOOKSTORE_SLUG', basename( dirname( __FILE__ ) ) );
 // Set constant path to the plugin directory.
define( 'WOW_MANAGER_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
 // Set constant path to the plugin URI.
define( 'WOW_MANAGER_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
 // Set the version number of the plugin.
define( 'WOW_MANAGER_IMPORT_VERSION', '1.5.0' );
 // Set the slug of the plugin.
define( 'WOW_MANAGER_IMPORT_SLUG', basename( dirname( __FILE__ ) ) );
 // Set constant path to the plugin directory.
define( 'WOW_MANAGER_IMPORT_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
 // Set constant path to the plugin URI.
define( 'WOW_MANAGER_IMPORT_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
// set constant name to textdomain
define( 'WOW_MANAGER_TEXT', 'wow-manager');
define( 'WOW_MANAGER_TITLE', 'WoW Manager');

require_once WOW_MANAGER_DIR . 'includes/functions.php';
require_once WOW_MANAGER_DIR . 'includes/functions-gzipfiles.php';
require_once WOW_MANAGER_DIR . 'includes/functions-settings-base.php';
require_once WOW_MANAGER_DIR . 'includes/functions-settings-conn.php';
// install & activate
require_once WOW_MANAGER_DIR . 'includes/install/class-wow-plugin-activator.php';
require_once WOW_MANAGER_DIR . 'includes/install/class-wow-plugin-deactivator.php';
require_once WOW_MANAGER_DIR . 'includes/install/class-wow-plugin-installer.php';
require_once WOW_MANAGER_DIR . 'includes/install/class-wow-plugin-updates.php';
require_once( WOW_MANAGER_DIR . 'includes/lib/class-wow-settings.php' );
// modules
/*
require_once( WOW_MANAGER_DIR . 'modules/maintenance/class-wow-maintenance.php' );
require_once( WOW_MANAGER_DIR . 'modules/support/class-wow-support.php' );
require_once( WOW_MANAGER_DIR . 'modules/loggers/class-wow-logger-manager.php' );
require_once( WOW_MANAGER_DIR . 'modules/woo-utilities/class-wow-wooutilities.php' );
require_once( WOW_MANAGER_DIR . 'modules/sysinfo/class-wow-sysinfo.php' );
*/
define( 'WOW_MANAGER_BASEVARS', 'wowbs_');
define( 'WOW_MANAGER_TOKEN', sanitize_func(sanitize_file_name(basename(__FILE__,'.php'))));
register_activation_hook( __FILE__, 'activate_WoW_Manager' );
register_deactivation_hook( __FILE__, 'deactivate_WoW_Manager' );

if ( ! class_exists( 'WoW_Manager' ) ) {
	/**
	 * Main plugin class
	 */
	final class WoW_Manager {
		/**
		 * The single instance of WoW_BookStore.
		 * @var 	object
		 * @access  private
		 * @since 	1.0.0
		 */
		private static $_instance = null;
	
		/**
		 * The main plugin file.
		 * @var     string
		 * @access  public
		 * @since   1.0.0
		 */
		public $file;
	
		/**
		 * The main plugin directory.
		 * @var     string
		 * @access  public
		 * @since   1.0.0
		 */
		public $dir;
	
		/**
		 * The plugin assets directory.
		 * @var     string
		 * @access  public
		 * @since   1.0.0
		 */
		public $assets_dir;
	
		/**
		 * The plugin assets URL.
		 * @var     string
		 * @access  public
		 * @since   1.0.0
		 */
		public $assets_url;
	
		/**
		 * Suffix for Javascripts.
		 * @var     string
		 * @access  public
		 * @since   1.0.0
		 */
		public $script_suffix;
	
		/**
		 * Settings class object
		 * @var     object
		 * @access  public
		 * @since   1.0.0
		 */
		public $settings = null;
	
		/**
		 * The version number.
		 * @var     string
		 * @access  public
		 * @since   1.0.0
		 */
		public $version;
	
		/**
		 * The token.
		 * @var     string
		 * @access  public
		 * @since   1.0.0
		 */
		public $_token;

		public $basevars = WOW_MANAGER_BASEVARS;
		public $textdomain;
		public $app_name;
		public $menu_name;
		public $menu_title;
		public $option_conn;
		public $option_cats;
		public $option_role;
		public $option_gpsa;
		public $option_imgs;
		public $option_maint;
		public $options=array();
		public $reparti;
		public $taxonomies;
		public $livelli;
		public $roles=array();
		public $noValidRole=array('administrator','shop_manager');
		public $isWoocommerce=false;
		public $tabs=array();
		public $active_tab='';
		public $loggers;
		public $attributes;
		public $custom_taxonomies;
		public $modules;

		 // Constructor
		public function __construct ( $file = '', $version = '1.0.0' ) {

			if ( ! $file ) return;
			$this->version = $version;
		// Load plugin environment variables
			$this->file = $file;
			$this->dir = dirname( $this->file );
			$this->app_name=sanitize_file_name(basename($file,'.php')); // oppure nome fisso eg. 'wow-bookstore'
			$this->textdomain=(defined( 'WOW_MANAGER_TEXT')? WOW_MANAGER_TEXT: sanitize_file_name(basename($file,'.php'))); // oppure nome fisso eg. 'wow-bookstore'
			$this->menu_name=$this->app_name;
			$this->menu_title=(defined( 'WOW_MANAGER_TITLE')? WOW_MANAGER_TITLE: ucwords(sanitize_file_name(basename($file,'.php')),'-')); // oppure nome fisso eg. 'wow-bookstore'
			$this->_token=WOW_MANAGER_TOKEN;
			$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
			$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );
			$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			$this->loggers=array();
			$this->attributes=array();
			$this->modules=array();
			$this->isWoocommerce=WoW_is_woocommerce_activated();
			if ( is_null( $this->settings ) ) {
				$this->settings = WoW_Settings::instance( $this, __( 'Impostazioni ', $this->textdomain ).$this->menu_title, __( $this->menu_title, $this->textdomain ) );
			}
			/*
			$this->modules['maintenance']=WoW_Maintenance::instance( $this);
			$this->modules['support']=WoW_Support::instance( $this);
			$this->modules['loggers']=Wow_Logger_Manager::instance($this->_token, $this);
			$this->modules['wooutils']=Wow_WooUtilities::instance($this);
			$this->modules['sysinfo']=Wow_Sysinfo::instance($this);
			$this->loggers=$this->modules['loggers'];
			*/
			$this->init_hook();
			$this->init_modules();
		}
		
		public function init_hook() {
		// plugin actions
			add_action( 'plugins_loaded', array( $this, 'lang' ), 50);// Internationalize the text strings used.
			add_action( 'plugins_loaded', array( $this, 'constants' ), 60);// Define Constats
			add_action( 'plugins_loaded', array( $this, 'includes' ), 70);// Include necessary files
			add_action( 'plugins_loaded', array( $this, 'WoW_load_config' ), 99);// carica configurazione
			add_action( 'admin_menu', array( $this, 'wow_manager_pages'), 9);
	        add_action( 'admin_init', array( $this, 'options_init' ), 10);
		// Load frontend JS & CSS
			add_action( 'wp_enqueue_scripts', array( $this, 'normalize_styles' ), 1 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 19 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 19 );
		// Load admin JS & CSS
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 10, 1 );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10, 1 );
		// Load Settings
			add_action( 'wow_load_settings_sections', array( $this, 'build_sections'),10);
			add_action( 'wow_load_settings_fields', array( $this, 'build_fields'),20);
			add_action( 'wow_load_settings', array( $this, 'build_settings'),30);
		// Personalizzazioni
			add_action( 'woocommerce_after_register_taxonomy', array( $this, 'modifica_taxonomy'), 89); // modifica tassonomie
		}
		
		// load modules
		public function init_modules() {
			$path=WOW_MANAGER_DIR.'modules/';
			if (file_exists($path)) {
				$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::KEY_AS_FILENAME | FilesystemIterator::CURRENT_AS_FILEINFO), RecursiveIteratorIterator::CATCH_GET_CHILD);
				foreach($objects as $name => $object) {
					if ($name!="." && $name!="..") {
						if (file_exists($object."/setup.php")) {
							//print("<br>".$name." -->> ". $object);
							require_once($object."/setup.php");
							$app($name);
						}
					}
				}
				unset($app);
			}
		}
		
		 // add menu pages
		public function wow_manager_pages() {
			// menù principale
			add_menu_page( 'WoW-Manager', $this->menu_title, 'manage_options', $this->menu_name, array( $this, 'wow_manager_callback'), 'dashicons-businessman', 69);
			// pagina import dati DB
		}

		 // Initialise translations
		public function lang() {
			$domain = $this->textdomain;
			$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
			load_textdomain( $domain, WP_LANG_DIR . '/plugins/' . $domain . '-' . $locale . '.mo' );
			load_plugin_textdomain( $domain, false, dirname(plugin_basename($this->file)).'/languages/' );
		}

		 // Defines constants for the plugin.
		public function constants() {

			if (! defined( 'WOW_MANAGER_TEXT')){
				define( 'WOW_MANAGER_TEXT', sanitize_file_name(basename($file,'.php')));
			}
			 // Set the messages defs.
			define( 'WOW_MANAGER_MSG_STATUSERR', 'error' );
			define( 'WOW_MANAGER_MSG_STATUSINFO', 'info' );
			define( 'WOW_MANAGER_MSG_STATUSWARN', 'warning' );
			define( 'WOW_MANAGER_MSG_STATUSSUCC', 'success' );
			define( 'WOW_MANAGER_MSG_CONN_NODATA', sprintf(
			__('Connessione fallita con il DB WoW-Commerce: ritorna alla pagina di <a href="%s" >impostazioni</a> e inserisci dei dati validi per la connessione!', $this->textdomain), admin_url( 'admin.php?page=wow-options-page&tab=conn')));
			define( 'WOW_MANAGER_MSG_CONN_ERRDATA', sprintf(
			__('Connessione fallita con il DB WoW-Commerce: ritorna alla pagina di <a href="%s" >impostazioni</a> e modifica i dati di connessione!', $this->textdomain), admin_url( 'admin.php?page=wow-options-page&tab=conn')));
			define( 'WOW_MANAGER_MSG_CONN_OKDATA', __("Connessione stabilita correttamente: il DB &eacute; pronto per l'importazione!", $this->textdomain) );
			define( 'WOW_MANAGER_MSG_NO_DISMISS', false );
			define( 'WOWMGR_MSG_ERR', 'error' );
			define( 'WOWMGR_MSG_INFO', 'info' );
			define( 'WOWMGR_MSG_WARN', 'warning' );
			define( 'WOWMGR_MSG_SUCC', 'success' );
			define( 'WOWMGR_MSG_NODISMISS', false );
			define( 'WOWMGR_MSG_SIDISMISS', true );

		}

		 // Include core files for both: admin and public
		public function includes() {
			require_once( 'includes/lib/class-wow-db.php' );
			require_once( 'includes/lib/class-wp-combine-query.php' );
			require_once( 'includes/lib/class-DataSourceCSV.php' );
			require_once( 'includes/lib/class-wow-admin-api.php' );
			require_once( 'includes/lib/class-wow-custom-admintable.php' );
			// custom taxonomies
			require_once( 'includes/lib/class-wow-custom-taxonomy.php' );
			require_once( 'includes/lib/class-wow-custom-taxonomy-image.php' );
			require_once( 'includes/lib/class-wow-standard-taxonomy-image.php' );
			require_once( 'includes/lib/class-wow-cherry-taxonomy-image.php' );
			// custom attributes
			require_once( 'includes/lib/class-wow-custom-attributes.php' );
			// custom post type
			require_once( 'includes/lib/class-wow-custom-post-type.php' );
		}

		/**
		 * Load frontend normalize CSS.
		 * @access  public
		 * @since   1.0.0
		 * @return void
		 */
		public function normalize_styles () {
			wp_register_style( $this->_token . '-normalize-frontend', esc_url( $this->assets_url ) . 'css/normalize.css', array(), $this->version );
			wp_enqueue_style( $this->_token . '-normalize-frontend' );
		} // End enqueue_styles ()
	

		/**
		 * Load frontend CSS.
		 * @access  public
		 * @since   1.0.0
		 * @return void
		 */
		public function enqueue_styles () {
			wp_register_style( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'css/frontend.css', array(), $this->version );
			wp_enqueue_style( $this->_token . '-frontend' );
		} // End enqueue_styles ()
	
		/**
		 * Load frontend Javascript.
		 * @access  public
		 * @since   1.0.0
		 * @return  void
		 */
		public function enqueue_scripts () {
			wp_register_script( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'js/frontend' . $this->script_suffix . '.js', array( 'jquery' ), $this->version );
			wp_enqueue_script( $this->_token . '-frontend' );
		} // End enqueue_scripts ()
	
		/**
		 * Load admin CSS.
		 * @access  public
		 * @since   1.0.0
		 * @return  void
		 */
		public function admin_enqueue_styles ( $hook = '' ) {
		wp_register_style( $this->_token . '-farbtastic', esc_url( $this->assets_url ) . 'farbtastic/farbtastic.css', array(), $this->version );
		wp_enqueue_style( $this->_token . '-farbtastic' );
			wp_register_style( $this->_token . '-admin', esc_url( $this->assets_url ) . 'css/admin.css', array(), $this->version );
			wp_enqueue_style( $this->_token . '-admin' );
			wp_enqueue_style( $this->_token . '-woo_wow_progbar-css', esc_url( $this->assets_url ) . 'css/jquery-ui.min.css', array(), $this->version );
			wp_enqueue_style( $this->_token . '-woo_wow-css', esc_url( $this->assets_url ) . 'css/woowow.css', array(), $this->version );
		} // End admin_enqueue_styles ()

		/**
		 * Load admin Javascript.
		 * @access  public
		 * @since   1.0.0
		 * @return  void
		 */
		public function admin_enqueue_scripts ( $hook = '' ) {
		wp_register_script( $this->_token . '-farbtastic', esc_url( $this->assets_url ) . 'farbtastic/farbtastic.js', array('jquery'), $this->version );
    	wp_enqueue_script( $this->_token . '-farbtastic' );
			wp_register_script( $this->_token . '-admin', esc_url( $this->assets_url ) . 'js/admin' . $this->script_suffix . '.js', array( 'jquery' ), $this->version );
			wp_enqueue_script( $this->_token . '-admin' );
			wp_enqueue_script( $this->_token . '-woo-wow-script', esc_url( $this->assets_url ) .'js/functions.js', array( 'jquery' , 'jquery-ui-progressbar' , 'jquery-ui-datepicker'), $this->version );
		} // End admin_enqueue_scripts ()

		public function WoW_load_config() {
			// Set class property
			global $wowdb;
			$up=new WoW_Updates($this);
			
			$this->option_gpsa = WoW_fill_gpsamain_to_array($this->basevars);
			$this->options['gpsa']=$this->option_gpsa;
			$this->option_conn = WoW_fill_conndata_to_array($this->basevars);
			$this->options['conn']=$this->option_conn;
//			$this->option_cats = get_option( $this->basevars.'wow_rep' );
//			$this->option_role = get_option( $this->basevars.'wow_role' );
			$wowdb = new WoW_wpdb($this->option_conn['store_user'],$this->option_conn['store_pass'],$this->option_conn['store_dbname'],$this->option_conn['store_host']);
			$this->roles=WoW_load_rolenames();
			$this->roles=WoW_filter_role($this->roles,$this->noValidRole);
//			loggers
//			$this->loggers->add_log('import_product', __( 'Import Product Data', $this->textdomain ));

			$this->admin = new WoW_Admin_API($this);
		}
	
		public function build_sections() {
			$this->settings->add_setting_sections(get_sections_base($this->textdomain));
			$this->settings->add_setting_sections(get_sections_conn($this->textdomain));
		}
		
		public function build_fields() {
			$this->settings->add_section_fields(get_fields_base($this->textdomain));
			$this->settings->add_section_fields(get_fields_conn($this->textdomain));
		}
		
		public function build_settings() {
			$this->settings->settings_fields();
		}
	
		 // Options init callback
		public function options_init() {
			if ( is_admin() ) {
				$this->admin = new WoW_Admin_API($this);
			}
		}
		
		 // Manager page callback
		public function wow_manager_callback() {
			global $wowdb;
			// include page
			include_once( 'wow-manager-mainpage.php' );
	   }
	
		function modifica_taxonomy(){
			$tax_args = WoW_get_taxonomy_config( $this->basevars, 'category');
			if ($tax_args['use_image']=='yes'){
				$this->WoW_Standard_Taxonomy_Images = new WoW_Standard_Taxonomy_Images('category',$tax_args,$this);
			}
		}
		
		/**
		 * Main WoW_Manager Instance
		 *
		 * Ensures only one instance of WoW_Manager is loaded or can be loaded.
		 *
		 * @since 1.0.0
		 * @static
		 * @see WoW_Manager()
		 * @return Main WoW_Manager instance
		 */
		public static function instance ( $file = '', $version = '1.0.0' ) {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self( $file, $version );
			}
			return self::$_instance;
		} // End instance ()
		public static function get_instance ( ) {
			return self::$_instance;
		} // End get_instance ()
	
		/**
		 * Cloning is forbidden.
		 *
		 * @since 1.0.0
		 */
		public function __clone () {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->version );
		} // End __clone ()
	
		/**
		 * Unserializing instances of this class is forbidden.
		 *
		 * @since 1.0.0
		 */
		public function __wakeup () {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->version );
		} // End __wakeup ()
	
// end class
	}
	
}

/**
 * Returns the main instance of WoW_Manager to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object WoW_Manager
 */
function WoW_Manager () {
	$instance = WoW_Manager::instance( __FILE__, WOW_MANAGER_VERSION );
	return $instance;
}
function WoW_Manager_Class() {
	$instance = WoW_Manager::get_instance( );
	return $instance;
}
global $wowbs;
$wowbs=WoW_Manager();
