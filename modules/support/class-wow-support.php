<?php

if ( ! defined( 'ABSPATH' ) ) exit;
if( ! class_exists( 'WoW_Support' ) ) {
	class WoW_Support {

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
			$this->appdir=dirname( __FILE__ );
			$this->appdir=trailingslashit(WOW_MANAGER_DIR.'modules/'.$module);
			$this->appurl=trailingslashit(WOW_MANAGER_URI.'modules/'.$module);
	
		// Load Settings
			add_action( 'wow_load_settings_sections', array( $this, 'build_sections'),11);
			add_action( 'wow_load_settings_fields', array( $this, 'build_fields'),21);
			add_action( 'wow_load_settings', array( $this, 'build_settings'),31);
			$is_enabled_page = get_option($this->basevars.'support_page_enabled');
			if ($is_enabled_page=='yes'){
				// pagina personale
				add_action( 'admin_menu', array( $this, 'personal_page'), 199);
				// class actions and filters
				// aggiunta testo al footer e personalizzazione action link 
				add_filter( 'admin_footer_text', array($this, 'admin_footer_text'), 1, 2 );
				add_filter( 'plugin_action_links_' . plugin_basename($this->appRef->file), array($this, 'plugin_action_links'));
			}			
	
		}
		
		 // Manager menu
		public function personal_page() {
			// menu item
				add_submenu_page($this->appRef->menu_name, __( 'Supporto', $this->textdomain ), '<span class="dashicons dashicons-admin-site"></span>'.__( 'Supporto', $this->textdomain ), 'read', 'wow-credits', array( $this, 'personal_callback'));
	   }

		 // Manager page callback
		public function personal_callback() {
			global $wowdb;
			// include page
			include_once( $this->appdir.'support-view-page.php' );
	   }
	   

		public function build_sections() {
	//		$this->settings=$this->appRef->settings;
			$sec[]=array('credits',
				__( 'Crediti e Supporto', $this->textdomain ),
				__( 'Imposta contatti per sviluppo e supporto.', $this->textdomain )
			);
			$this->settings->add_setting_sections($sec);
		}
		
		public function build_fields() {
			$fld[]=array('credits',
				array(
					'id' 			=> 'support_page_enabled',
					'label'			=> __( 'Abilita pagina supporto: ', $this->textdomain ),
					'description'	=> __( 'Abilitate la pagina e il footer di supporto.', $this->textdomain ),
					'type'			=> 'radio',
					'placeholder'	=> __( '', $this->textdomain ),
					'options'		=> array( 'no' => __('Disabilitata.',$this->textdomain), 'yes' => __('Abilitata',$this->textdomain) ),
					'default'		=> 'no'
				)
			);
			$fld[]=array('credits',
				array(
					'id' 			=> 'support_name',
					'label'			=> __( 'Nominativo o azienda di assistenza: ', $this->textdomain ),
					'description'	=> __( 'Nominativo o azienda che ha eseguito lo sviluppo e personalizzazione.', $this->textdomain ),
					'type'			=> 'text',
					'placeholder'	=> __( 'Inserire il nominativo', $this->textdomain ),
					'default'		=> 'Work On Web di Starz Andrea'
				)
			);
			$fld[]=array('credits',
				array(
					'id' 			=> 'support_link_enabled',
					'label'			=> __( 'Abilita link al supporto: ', $this->textdomain ),
					'description'	=> __( 'Abilitate il collegamento alla pagina di supporto.', $this->textdomain ),
					'type'			=> 'radio',
					'placeholder'	=> __( '', $this->textdomain ),
					'options'		=> array( 'no' => __('Nessuna azione.',$this->textdomain), 'yes' => __('Indirizzo Esterno',$this->textdomain) ),
					'default'		=> 'no'
				)
			);
			$fld[]=array('credits',
				array(
					'id' 			=> 'support_link_url',
					'label'			=> __( 'Indirizzo del supporto: ', $this->textdomain ),
					'description'	=> __( 'Indirizzo della pagina di supporto.', $this->textdomain ),
					'type'			=> 'text',
					'placeholder'	=> __( 'Inserire l\'indirizzo della pagina comprensivo di http o https', $this->textdomain ),
					'default'		=> 'http://www.work-on-web.it'
				)
			);
			$fld[]=array('credits',
				array(
					'id' 			=> 'support_link_mail',
					'label'			=> __( 'Indirizzo mail del supporto: ', $this->textdomain ),
					'description'	=> __( 'Indirizzo e-mail di supporto.', $this->textdomain ),
					'type'			=> 'text',
					'placeholder'	=> __( 'Inserire l\'indirizzo e-mail del supporto', $this->textdomain ),
					'default'		=> 'webmaster@'
				)
			);
			$fld[]=array('credits',
				array(
					'id' 			=> 'support_link_phone',
					'label'			=> __( 'Telefono del supporto: ', $this->textdomain ),
					'description'	=> __( 'Numero telefonico del supporto.', $this->textdomain ),
					'type'			=> 'text',
					'placeholder'	=> __( 'Inserire il numero telefonico del supporto', $this->textdomain ),
					'default'		=> '0'
				)
			);
			$this->settings->add_section_fields($fld);
		}
		
		public function build_settings() {
			$this->settings->settings_fields();
		}
		
		/**
		 * Support Text Builder
		*/
		public function buildCustomText() {
			$_text='';
			$is_enabled = get_option($this->basevars.'support_link_enabled');
			if ($is_enabled=='yes') {
				$_text=sprintf( __( 'Piattaforma WordPress personalizzata da <a href="%s" target="_blank">%s</a>', $this->textdomain ),get_option($this->basevars.'support_link_url'),get_option($this->basevars.'support_name'));
			} else {
				$_text=sprintf( __( 'Piattaforma WordPress personalizzata da %s', $this->textdomain ),get_option($this->basevars.'support_name'));
			}
			return $_text;
		}
		public function buildSupportText() {
			$_text='';
			$is_enabled = get_option($this->basevars.'support_link_enabled');
			if ($is_enabled=='yes') {
				$_text.=sprintf( __( ' - Per assistenza e sviluppo contattare il %s', $this->textdomain ),get_option($this->basevars.'support_link_phone'));
				$_text.=sprintf( __( ' o inviare una mail a questo <a href="mailto:%s">indirizzo</a>.', $this->textdomain ),get_option($this->basevars.'support_link_mail'));
			}
			return $_text;
		}
		/**
		 * footer indicator
		*/
		function admin_footer_text( $footer_text ) {
			global $current_screen;
			// list of admin pages we want this to appear on
			$pages = array(	$this->appRef->menu_name );

			$_text1=$this->buildCustomText();
			$_text2=$this->buildSupportText();
	
			if ( isset( $current_screen->id ) /*&& in_array( $current_screen->id, $pages ) */) {
				$footer_text = $_text1.$_text2;
			}
			return $footer_text;
		}

		/**
		* Create action link
		*
		* @return array	
		*/	
		public function plugin_action_links($links) { 
			$is_enabled = get_option($this->basevars.'support_link_enabled');
	
			$page_link = array('setup' =>"<a href='edit.php?page=" . WOW_MANAGER_SLUG . "'>".$this->appRef->menu_title."</a>"); 
			$links = array_merge($page_link, $links);
			if ($is_enabled=='yes') {
				$site_link = array('support' => '<a href="'.get_option($this->basevars.'support_link_url').'" target="_blank">' . __('Support', $this->textdomain) . '</a>');
				$links = array_merge($links, $site_link);
			}
			return $links; 
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
