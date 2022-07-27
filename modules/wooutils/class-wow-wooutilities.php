<?php

if ( ! defined( 'ABSPATH' ) ) exit;
			require_once( 'wow-wooutilities-functions.php' );
if( ! class_exists( 'WoW_WooUtilities' ) ) {
	class WoW_WooUtilities {
		
		public $version = '1.1.1';

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
		public $delprod_role;
		public $delprod_menu;
		public $is_delprod;
		public $config;
		public $isWoocommerce;
		public $logger;
		public $fields;
		public $fldnames;
	
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
			$this->isWoocommerce=WoW_is_woocommerce_activated();
			$this->delprod_role=($this->isWoocommerce?'manage_woocommerce':'manage_options');
			$this->init_hooks();
		}
		
		public function init_hooks() {
			// Hook
			add_action( 'wow_load_settings_sections', array( $this, 'build_sections'),13);
			add_action( 'wow_load_settings_fields', array( $this, 'build_fields'),23);
			add_action( 'wow_load_settings', array( $this, 'build_settings'),33);
			//  mode
			add_action( 'plugins_loaded', array( $this, 'includes' ), 71);// Include necessary files
			add_action( 'admin_menu', array( $this, 'delprod_page'), 99);
			add_action( 'plugins_loaded', array( $this, 'wooutils_load' ), 100);// Load config data
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ), 99, 1 );
			add_action( 'wow_load_settings_assets', array( $this, 'admin_setting_assets'),99); // per caricamento script in pagina settings
		}

		// load config vars
		public function wooutils_load() {
			//_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->appRef->version );
			$this->fields=$this->make_fields();
			$this->config=$this->get_config();
			$this->is_delprod=$this->config['delete_product_enable'];//get_option($this->basevars.'delete_product_enable');
			$this->delprod_menu=$this->config['delete_product_menu'];//get_option($this->basevars.'delete_product_menu');
			$is_log_active=$this->config['delete_product_log_enable'];//get_option($this->basevars.'delete_product_log_enable');
			$this->appRef->options=array_merge( $this->appRef->options, array('woo_utils'=>$this->config));
			if ($is_log_active>0) {
				$this->appRef->loggers->add_log('delete_product', __( 'Bulk Delete Product', $this->textdomain ));
				$this->logger=$this->appRef->loggers->get_log('delete_product');
			}
		}
		
		// include function for global use
		public function includes() {

		}
		public function admin_setting_assets() {
//			wp_register_script( $this->appRef->_token . '-delprod-settings-js', $this->appurl . 'settings' . $this->appRef->script_suffix . '.js', array( 'jquery' ), $this->version );
			wp_register_script( $this->appRef->_token . '-wooutils-settings-js', $this->appurl . 'settings' . '.js', array( 'jquery' ), $this->version );
			wp_enqueue_script( $this->appRef->_token . '-wooutils-settings-js' );
		}
		public function admin_assets() {
			// scripts & styles
			if ($this->isWoocommerce) {
				global $woocommerce;
				wp_register_script( 'woocommerce_admin', $woocommerce->plugin_url() . '/assets/js/admin/woocommerce_admin.min.js', array ('jquery', 'jquery-ui-widget', 'jquery-ui-core' ), '1.0' );
				wp_enqueue_script( 'woocommerce_admin' );
				wp_enqueue_style( 'woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css' );
			}
		}
	
		// Manager menu
		public function delprod_page() {
			// menu item
			if ($this->isWoocommerce && $this->is_delprod) {
				add_submenu_page(($this->delprod_menu=='parent'?$this->appRef->menu_name:$this->delprod_menu), __( 'Cancella Tutti i Prodotti', $this->textdomain ), '<span class="dashicons dashicons-warning"></span>'.__( 'Cancella Prodotti', $this->textdomain ), $this->delprod_role, 'wow-delete-product', array( $this, 'remove_all_products'));
			}
		}
		 // remove product page callback
		public function remove_all_products() {
			// include page
			include_once( $this->appdir.'wow-deleteproduct-page.php' );
		}
		public function build_sections() {
	//		$this->settings=$this->appRef->settings;
			$sec[]=array('wooutils',
				__( 'WooCommerce Utility', $this->textdomain ),
				__( 'Utilità varie per interfacciarci con WooCommerce.', $this->textdomain )
			);
			$this->settings->add_setting_sections($sec);
		}
		private function get_config() {
			$conf=array();
			foreach($this->fldnames as $id=>$name){
				$conf[$name]=get_option($this->basevars.$name);
			}
			return $conf;
		}
		private function make_fields() {
		$this->fldnames[]='delete_product_enable';
			$fld[]=array('wooutils',
					array(
						'id' 			=> 'delete_product_enable',
						'label'			=> __( 'Utility Svuota Prodotti' , $this->textdomain ),
						'description'	=> __( 'Abilita la funzionalità per lo svuotamento dello shop (cancella tutti i prodotti).', $this->textdomain ),
						'type'			=> 'radio',
						'options'		=> array( '0' => 'Disabilitato', '1' => 'Abilitato' ),
						'default'		=> '0',
						'disabled'		=> (WoW_is_woocommerce_activated()?'no':'yes'),//'yes', // yes , no or function callback that returns yes or no
						'placeholder'	=> __( '', $this->textdomain ),
						'callback'		=> ''
					)
			);
		$this->fldnames[]='delete_product_menu';
			$fld[]=array('wooutils',
					array(
						'id' 			=> 'delete_product_menu',
						'label'			=> __( 'Inserisci nel Menù' , $this->textdomain ),
						'description'	=> __( 'Seleziona il menù dove inserire la voce per la cancellazione.', $this->textdomain ),
						'type'			=> 'select',
						'options'		=> array( 'parent' => 'WoW Manager (Menù Applicazione)', 'woocommerce' => 'Menù di WooCommerce' ),
						'default'		=> 'parent',
						'disabled'		=> (WoW_is_woocommerce_activated()?'no':'yes'),//'yes', // yes , no or function callback that returns yes or no
						'placeholder'	=> __( '', $this->textdomain ),
						'callback'		=> ''
					)
			);
		$this->fldnames[]='delete_product_log_enable';
			$fld[]=array('wooutils',
					array(
						'id' 			=> 'delete_product_log_enable',
						'label'			=> __( 'Abilita log azioni' , $this->textdomain ),
						'description'	=> __( 'Abilita il tracciamento delle funzioni.', $this->textdomain ),
						'type'			=> 'radio',
						'options'		=> array( '0' => 'Disabilitato', '1' => 'Abilitato' ),
						'default'		=> 'no',
						'disabled'		=> (WoW_is_woocommerce_activated()?'no':'yes'),//'yes', // yes , no or function callback that returns yes or no
						'placeholder'	=> __( '', $this->textdomain ),
						'callback'		=> ''
					)
			);
		return $fld;
		}
		public function build_fields() {
			$this->settings->add_section_fields($this->fields);
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
