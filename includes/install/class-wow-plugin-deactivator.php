<?php

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
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
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wow-plugin-deactivator.php
 */
function deactivate_WoW_Manager() {
	$act=new WoW_Deactivator(WOW_MANAGER_BASEVARS, WOW_MANAGER_TOKEN, WOW_MANAGER_VERSION);
	$act->deactivate();
}

if( ! class_exists( 'WoW_Deactivator' ) ) {
class WoW_Deactivator {
	
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

	public function __construct($vars, $_token, $ver) {
		$this->basevars=$vars;
		$this->_token=$_token;
		$this->version=$ver;
	}

	public function deactivate() {
		$my=new WoW_Installer($this);
		$my->uninstall();
		if ($my->uninstall()) {
			add_action( 'admin_notices', array ( $this, 'mess_deact' ) );
		} else {
			add_action( 'admin_init', array ( $this, 'err_deactivate' ) );
			add_action( 'admin_notices', array ( $this, 'mess_deacterr' ) );
		}
		
	}
	public function mess_deact() {
		WoW_popAdminMessage("Plugin ".WOW_MANAGER_TITLE." disinstallato!!!", $tipo=WOW_MANAGER_MSG_STATUSINFO, $dismiss=true);
	} 
	public function mess_deacterr() {
		WoW_popAdminMessage("Plugin ".WOW_MANAGER_TITLE." &eacute; disattivato ma disinstallazione non completata!!!", $tipo=WOW_MANAGER_MSG_STATUSERR, $dismiss=true);
	} 
	public function err_deactivate() {
	}

}
}
