<?php

if ( ! defined( 'ABSPATH' ) ) exit;
if( ! class_exists( 'WoW_Maintenance' ) ) {
	class WoW_Maintenance {
		
		public $version = '1.1.0';

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
		public $maint_role;
		public $is_maintenance;
		public $options_maint;
	
		public function __construct ( $ref='' , $module='') {
	
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
			$this->maint_role=(WoW_is_woocommerce_activated()?'manage_woocommerce':'manage_options');
			$this->is_maintenance=get_option($this->basevars.'maintenance_enabled');
			$this->options_maint=array();
			require_once( $this->appdir . 'wow-maintenance-functions.php' );

			// Load Settings
			add_action( 'wow_load_settings_sections', array( $this, 'build_sections'),12);
			add_action( 'wow_load_settings_fields', array( $this, 'build_fields'),22);
			add_action( 'wow_load_settings', array( $this, 'build_settings'),32);
			// maintenance mode
			add_action( 'plugins_loaded', array( $this, 'includes' ), 71);// Include necessary files
			add_action( 'plugins_loaded', array( $this, 'maintenance_load' ), 100);// Load config data
			add_action( 'admin_menu', array( $this, 'maintenance_page'), 99);
			add_action('template_redirect', array( $this, 'maintenance' ), 1);
			add_action('wow_load_settings_assets', array( $this, 'maintenance_assets'),100);
			// maintenance indicator
			add_action('admin_bar_menu', array( $this, 'indicator' ), 100);
			if ($this->is_maintenance){
				add_action('admin_notices',array($this,'maintenance_activemsg'));
			}
		}

		// load config vars
		public function maintenance_load() {
			$this->options_maint = WoW_fill_maintenance_to_array($this->basevars);
			if ($this->appRef){
				$this->appRef->option_maint=$this->options_maint;
			}
		}
		// include function for global use
		public function includes() {

		}
		// Manager menu
		public function maintenance_page() {
			// menu item
			add_submenu_page($this->appRef->menu_name, __( 'Manutenzione e Aggiornamento', $this->textdomain ), '<span class="dashicons dashicons-hammer"></span>'.__( 'Manutenzione', $this->textdomain ), $this->maint_role, 'wow-maintenance', array( $this, 'maintenance_callback'),70);
		}
		public function maintenance_assets() {
//			wp_register_script( $this->appRef->_token . '-maint-settings-js', $this->appurl . 'settings' . $this->appRef->script_suffix . '.js', array( 'jquery' ), $this->version );
			wp_register_script( $this->appRef->_token . '-maint-settings-js', $this->appurl . 'settings' . '.js', array( 'jquery' ), $this->version );
			wp_enqueue_script( $this->appRef->_token . '-maint-settings-js' );
		}
		// Messaggio Maintenance Attivo
		public function maintenance_activemsg() {
			WoW_popAdminMessage("La modalità di manutenzione è attiva! Ricordarsi di disattivarla a fine lavori o il sito non sar&agrave; visibile.",WOW_MANAGER_MSG_STATUSWARN,WOW_MANAGER_MSG_NO_DISMISS);
		}
		// Maintenance page callback
		public function maintenance_callback() {
			//global $wowdb;
			// include page
			include_once( $this->appdir.'wow-maintenance-view-page.php' );
		}
		// Maintenance options save
		public function status_maintenance_save( $post_id = null ) {
			// Bail if we're doing an auto save
			if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
			 
			// if our nonce isn't there, or we can't verify it, bail
			if( !isset( $_POST['wow_maintenance_nonce'] ) || !wp_verify_nonce( $_POST['wow_maintenance_nonce'], 'wow_maintenance' ) ) return;
			 
			 
			// now we can actually save the data
			if( isset( $_POST['wow_mm_enabled'] ) ) {
				update_option( $this->basevars.'maintenance_enabled', $_POST['wow_mm_enabled'] );
				$this->cache_plugin();
				wp_safe_redirect( $_POST['_wp_http_referer'] );
			}
		}
		
		public function build_sections() {
	//		$this->settings=$this->appRef->settings;
			$sec[]=array('maintenance',
				__( 'Manutenzione', $this->textdomain ),
				__( 'Imposta cosa visualizzare durante la manutenzione/aggiornamento del sito.', $this->textdomain )
			);
			$this->settings->add_setting_sections($sec);
		}
		
		public function build_fields() {
			$fld[]=array('maintenance',
				array(
					'id' 			=> 'maintenance_redirect',
					'label'			=> __( 'Redireziona su: ', $this->textdomain ),
					'description'	=> __( 'Selezionare se redirezionare su una pagina o un indirizzo esterno.', $this->textdomain ),
					'type'			=> 'radio',
					'placeholder'	=> __( '', $this->textdomain ),
					'options'		=> array( 'no' => __('Nessuna azione.',$this->textdomain), 'page' => __('Pagina',$this->textdomain), 'url' => __('Indirizzo esterno',$this->textdomain) ),
					'default'		=> 'no'
				)
			);
			$fld[]=array('maintenance',
					array(
						'id' 			=> 'redirect_page',
						'label'			=> __( 'Pagina:', $this->textdomain ),
						'description'	=> __( 'Seleziona la pagina di redirezione.', $this->textdomain ),
						'type'			=> 'select',
						'options'		=> WoW_get_pages(),
						'placeholder'	=> __( '', $this->textdomain ),
						'disabled'		=> 'WoW_is_enabled_url',//'yes', // yes , no or function callback that returns yes or no or array ('func',param/array_param)
						'default'		=> ''
					)
			);
			$fld[]=array('maintenance',
					array(
						'id' 			=> 'redirect_url',
						'label'			=> __( 'URL:' , $this->textdomain ),
						'description'	=> __( 'Inserire l\'URL di redirezione (comprensivo di http:// o https://.', $this->textdomain ),
						'type'			=> 'text',
						'disabled'		=> 'WoW_is_enabled_page',//'yes', // yes , no or function callback that returns yes or no
						'default'		=> '',
						'placeholder'	=> __( 'URL redirect', $this->textdomain )
					)
			);
			$fld[]=array('maintenance',
					array(
						'id' 			=> 'maintenance_enabled',
						'label'			=> __( '' , $this->textdomain ),
						'description'	=> __( '', $this->textdomain ),
						'type'			=> 'hidden',
						'placeholder'	=> __( '', $this->textdomain ),
						'default'		=> 0
					)
			);
			$this->settings->add_section_fields($fld);
		}
		
		public function build_settings() {
			$this->settings->settings_fields();
		}
	
		/**
		 * admin bar indicator
		*/
		public function indicator($wp_admin_bar) {
	
			$enabled = apply_filters('wow_admin_bar_indicator_enabled', $enabled = true);
			if (! $this->maint_role) {
				return false;
			}
	
			if (!$enabled) {
				return false;
			}
	
			$is_enabled = $this->is_maintenance;
//			$status = _x('Manutenzione Non Attiva -> Negozio Aperto', 'Admin bar indicator', $this->textdomain);
			$status = _x('Site On-Line', 'Admin bar indicator', $this->textdomain);
	
			if ($is_enabled) {
//				$status = _x('Manutenzione Attiva -> Negozio Chiuso', 'Admin bar indicator', $this->textdomain);
				$status = _x('Site Off-Line', 'Admin bar indicator', $this->textdomain);
			}
	
			$indicatorClasses = $is_enabled ? 'wow-mm-indicator wow-mm-indicator--enabled' : 'wow-mm-indicator';
	
			$indicator = array(
				'id' => 'wow-mm-indicator',
				'title' => '<span class="ab-icon dashicon-before dashicons-hammer"></span> ' . $status,
				'parent' => false,
				'href' => get_admin_url(null, 'admin.php?page=wow-maintenance'),
				'meta' => array(
					'title' => _x('Maintenance Mode', 'Admin bar indicator', $this->textdomain),
					'class' => $indicatorClasses,
				)
			);
	
			$wp_admin_bar->add_node($indicator);
		}
	
		/**
		 * Maintenance Mode
		*/
		public function maintenance() {
			$is_enabled = $this->is_maintenance;
			$user=wp_get_current_user();
			$page_options = get_option( $this->basevars.'redirect_page' );
			$url_options = get_option( $this->basevars.'redirect_url' );
			$redir_options = get_option( $this->basevars.'maintenance_redirect' );
			if ($is_enabled && ! $user->has_cap($this->maint_role)) {
				switch ($redir_options) {
					case 'page':
						if ( ! is_page( $page_options )) { 
							$this->cache_plugin();
							wp_safe_redirect( get_permalink( $page_options) );
							exit;
						}
						break;
					case 'url':
						wp_safe_redirect( $url_options ,503);
						exit;
						break;
					default:
					wp_die('none redirect ! ');
						break;
				}
			}
		}
	
		/**
		 * detect cache plugins
		*/
		public function cache_plugin() {
	//		if ( ! current_user_can( 'activate_plugins' ) ) return;
	//		$plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
	//		check_admin_referer( "activate-plugin_{$plugin}" );
			
			// Clear Cachify Cache
			if ( has_action('cachify_flush_cache') ) {
				do_action('cachify_flush_cache');
			}
			
			// Clear Super Cache
			if ( function_exists( 'wp_cache_clear_cache' ) ) {
				ob_end_clean();
				wp_cache_clear_cache();
			}
			
			// Clear W3 Total Cache
			if ( function_exists( 'w3tc_pgcache_flush' ) ) {
				ob_end_clean();
				w3tc_pgcache_flush();
			}
			
			// Clear WP-Rocket Cache
			if ( function_exists( 'rocket_clean_domain' ) ) {
				rocket_clean_domain();
			}
		}




	/**
	 * Main WoW_Class Instance
	 *
	 * Ensures only one instance of WoW_Class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
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
