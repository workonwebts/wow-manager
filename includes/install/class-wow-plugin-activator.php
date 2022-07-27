<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    WoW_BookStore
 * @subpackage WoW_BookStore/includes
 * @author     Your Name <email@example.com>
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // disable direct access
}
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wow-plugin-activator.php
 */
function activate_WoW_Manager() {
	$act=new WoW_Activator(WOW_MANAGER_BASEVARS, WOW_MANAGER_TOKEN, WOW_MANAGER_VERSION);
	$act->activate();
}

if( ! class_exists( 'WoW_Activator' ) ) {
class WoW_Activator {
	
	public $basevars;
	public $_token;
	public $version;

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	
	public function __construct($vars,$_token,$ver) {
		$this->basevars=$vars;
		$this->_token=$_token;
		$this->version=$ver;
	}
	
	public function activate() {
		$my=new WoW_Installer($this);
		$my->install();
		if ($my->install()) {
			add_action( 'admin_notices', array ( $this, 'mess_act' ) );
		} else {
			add_action( 'admin_init', array ( $this, 'deactivate' ));
			add_action( 'admin_notices', array ( $this, 'mess_acterr' ) );
		}
		
	}
	public function mess_act() {
		WoW_popAdminMessage("Plugin ".WOW_MANAGER_TITLE." &eacute; installato!!!", $tipo=WOW_MANAGER_MSG_STATUSSUCC, $dismiss=true);
	} 
	public function mess_acterr() {
		WoW_popAdminMessage("Plugin ".WOW_MANAGER_TITLE." &eacute; non installato!!!", $tipo=WOW_MANAGER_MSG_STATUSERR, $dismiss=true);
	} 
	public function deactivate() {
		deactivate_plugins(plugin_basename(__FILE__));
	}
}
}
