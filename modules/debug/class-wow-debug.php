<?php 
/*
Plugin Name: WoW - Debugger
Plugin URI:  
Description: Plugin per il debug di sistema
Version: 1.0
Author: Andrea Starz
Author URI: 
License: GPLv2
Text Domain: wowdebug
Domain Path: /languages
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit; 
}

//add_action('plugins_loaded', array(WowDebug::instance(), 'setup'));

if( ! class_exists( 'WoW_Debug' ) ) {
	class WoW_Debug {
	
		public $version = '1.5.0';

		/**
		 * The single instance of WoW_Class.
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
		public $config;
		public $logger;
		public $fields;
		public $fldnames;

		public $slug = 'wow-debug';
		public $aSections;
		public $aExclude;
		public $aInclude;

	 /**
		* Constructor
		*/
	
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
			$this->init_hooks();
		}
		
		public function init_hooks() {
			// Hook
			add_action( 'wow_load_settings_sections', array( $this, 'build_sections'),13);
			add_action( 'wow_load_settings_fields', array( $this, 'build_fields'),23);
			add_action( 'wow_load_settings', array( $this, 'build_settings'),33);
			add_action( 'wow_load_settings_assets', array( $this, 'admin_setting_assets'),99); // per caricamento script in pagina settings
			//  mode
			add_action( 'plugins_loaded', array( $this, 'includes' ), 71);// Include necessary files

			$is_enabled_dbg = get_option($this->basevars.'debug_enable');
			if ($is_enabled_dbg=='1'){
				$this->setup();
			}
		}


		 /**
		* Setup
		*/	
		public function setup() {
			add_action( 'admin_menu', array($this,'wow_debug_page'));
			add_action('wp_ajax_get_var_dump', array($this, 'get_var_dump'));
			add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));		
			$this->aExclude=array('_GET','_POST','_COOKIE','_FILES','_SERVER','_REQUEST','GLOBALS','_ENV','_SESSION');
			$this->aInclude=array('rewrite_rules','woocommerce_permalinks');
		}
		
		// include function for global use
		public function includes() {

		}

		public function wow_debug_page() {
			//add_submenu_page( 'index.php', 'WoW-Debug', 'WoW-Debug', 'manage_options', $this->slug, array($this,'wow_debug_callback'));
			add_submenu_page( $this->appRef->menu_name, __('Debug Vars', $this->textdomain ), '<span class="dashicons dashicons-welcome-view-site"></span>'.__('Debug Vars', $this->textdomain ), 'manage_options', $this->slug, array($this,'wow_debug_callback'),90);
		}

		public function admin_enqueue_scripts($hook) { 
			// Only load on plugin page
			if ($hook!='wow-manager_page_wow-debug') {
				return;
			}
			// Plugin stylesheet and script
			wp_enqueue_style($this->slug, plugins_url('/assets/css/style.css', __FILE__), array(), $this->version);	
			wp_enqueue_script($this->slug, plugins_url('/assets/js/script.js', __FILE__), array('jquery', 'jquery-ui-core', 'jquery-ui-tabs'), $this->version);
			add_action( 'shutdown', array($this,'wow_debug_vars'));
		}	

		private function get_config() {
			$conf=array();
			foreach($this->fldnames as $id=>$name){
				$conf[$name]=get_option($this->basevars.$name);
			}
			return $conf;
		}
		// include function for global use
		public function admin_setting_assets() {

		}
		
		public function build_sections() {
	//		$this->settings=$this->appRef->settings;
			$sec[]=array('wowdebug',
				__( 'Debug Variabili', $this->textdomain ),
				__( 'Visualizzazione variabili e classi di sistema.', $this->textdomain )
			);
			$this->settings->add_setting_sections($sec);
		}
		/*
		private function make_fields() {
		return $fld;
		}*/
		public function build_fields() {
			$this->fldnames[]='debug_enable';
			$fld[]=array('wowdebug',
					array(
						'id' 			=> 'debug_enable',
						'label'			=> __( 'Abilita Debug' , $this->textdomain ),
						'description'	=> __( 'Abilita la funzionalità di debug per la visualizzazione delle variabili e classi di sistema.', $this->textdomain ),
						'type'			=> 'radio',
						'options'		=> array( '0' => 'Disabilitato', '1' => 'Abilitato' ),
						'default'		=> '0',
						'disabled'		=> 'no',//'yes', // yes , no or function callback that returns yes or no
						'placeholder'	=> __( '', $this->textdomain ),
						'callback'		=> ''
					)
			);
			$this->fields=$fld;
			$this->settings->add_section_fields($fld);
		}
		
		public function build_settings() {
			$this->settings->settings_fields();
		}
	
		
		
		// debug functions
		public function wow_debug_vars() {
			$this->aSections=array_keys($GLOBALS);
			?>
			<script>
			jQuery("#res_link").append('<?php echo $this->get_sections_link() ?>');
			jQuery("#res").append('<?php echo $this->get_sections_div() ?>');
			jQuery("#variabili").append('<?php echo $this->get_vars_section() ?>');
	//		jQuery("#res").tabs();
			</script>
			<?php
		}

		public function get_var_dump() {
			add_action( 'shutdown', array($this,'get_var_dump_down'));
		}

		public function get_var_dump_down() {
			if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wowdebug')) {
				wp_die(__('Security check failed',$this->slug));	
			}
			// Get var
			$var = $_POST['glob_var'];
			if (in_array($var,$this->aInclude) ) {
				$val=get_option($var);
			} else {
				$val=$GLOBALS[$var];
			}
			$html_div='<p>'.$var.'</p>';
			$html_div.='<p><pre>'.print_r($val, true).'</pre></p>';//htmlspecialchars('',ENT_QUOTES|ENT_XHTML)
			exit($html_div);
		}

		function wow_debug_callback($hook) {
			include("wow-debug-page.php");
		}

		public function get_vars_section() {
			$html_var="";
			foreach($this->aSections as $k=>$var) {
				if (! in_array($var,$this->aExclude) ) {
					if (!is_array($GLOBALS[$var]) && !is_object($GLOBALS[$var])) {
						$html_var.='<div id="div_'.$var.'">';
						$html_var.='<pre>'.$var.' = '.htmlspecialchars($GLOBALS[$var],ENT_QUOTES|ENT_XHTML).'</pre>';
						$html_var.='</div>'.'\n';
					}
				}
			}
			return $html_var;
		}

		public function get_sections_link() {
			$html_li="";
			foreach($this->aInclude as $k=>$v) {
				if (! in_array($v,$this->aExclude) ) {
					$html_li .='<li><a href="#div_'.$v.'"><span>'.$v.'</span></a></li>\n';
				}
			}
			foreach($this->aSections as $k=>$v) {
				if (! in_array($v,$this->aExclude) ) {
					if (is_array($GLOBALS[$v]) || is_object($GLOBALS[$v])) {
						$html_li .='<li><a href="#div_'.$v.'"><span>'.$v.'</span></a></li>\n';
					}
				}
			}
			return $html_li;
		}

		public function get_sections_div() {
			$html_div="";
			foreach($this->aInclude as $k=>$var) {
				if (! in_array($var,$this->aExclude) ) {
					$html_div.='<div id="div_'.$var.'">';
					$html_div.='<p class="button-cont"><input type="button" class="button button-primary loadvars-buttons" id="'.$var.'" value="Load '.$var.'" /></p>';
					$html_div.='<div id="result_'.$var.'">&nbsp;</div>';
					$html_div.='</div>'.'\n';
				}
			}
			foreach($this->aSections as $k=>$var) {
				if (! in_array($var,$this->aExclude) ) {
					if (is_array($GLOBALS[$var]) || is_object($GLOBALS[$var])) {
						$html_div.='<div id="div_'.$var.'">';
						$html_div.='<p class="button-cont"><input type="button" class="button button-primary loadvars-buttons" id="'.$var.'" value="Load '.$var.'" /></p>';
						$html_div.='<div id="result_'.$var.'">&nbsp;</div>';
						$html_div.='</div>'.'\n';
					}
				}
			}
			return $html_div;
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

?>
