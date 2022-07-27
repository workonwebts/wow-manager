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

if( ! class_exists( 'WoW_Logger' ) ) {
class WoW_Logger {
	
	/**
	* nome variabile memorizzazione log
	**/
	public $name;
	
	/**
	* nome variabile memorizzazione log
	**/
	public $logvar;
	
	/**
	* formato data del sito
	**/
	public $format_date;
	
	/**
	* formato time del sito
	**/
	public $format_time;
	
	/**
	* offset time del sito
	**/
	public $offset_time;

  /**
	 * The domain translation
	 * @var 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public $textdomain;

  /**
	 * The parent object if exist
	 * @var 	object
	 * @access  public
	 * @since 	1.0.0
	 */
	public $appRef;
	public $title;
	public $appdir;
	public $appurl;

	/**
	 * Constructor function
	 * @main_app -> main application prefix
	 * @$log_app -> type or procedure to log
	 */
	public function __construct ($main_app, $log_app, $log_desc, $ref) {
		$this->name = $log_app;
		$this->title = $log_desc;
		$this->logvar= $main_app."_".$log_app."_log";
		if (! get_option($this->logvar)) {
			add_option($this->logvar,0);
		}
		$this->format_date = get_option( 'date_format' );
		$this->format_time = get_option( 'time_format' );
		$this->offset_time = get_option( 'gmt_offset' ) * 3600;
		if ($ref) {
			$this->appRef=$ref;
		}
		if(isset($this->appRef->textdomain)) {
			$this->textdomain=$this->appRef->textdomain;
		} else {
			$this->textdomain=sanitize_file_name(basename(__FILE__,'.php'));
		}
		$this->appdir=trailingslashit(WOW_MANAGER_DIR.'modules/loggers');
		$this->appurl=trailingslashit(WOW_MANAGER_URI.'modules/loggers');
		$this->init_log();
			
//		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 10, 1 );
	}

	/**
	 * Init Logger
	 * @param  time   $time data
	 * @param  array $args format time to display ex: array( 'format' => 'date_and_time' or 'date' or 'time');
	 * @return string
	 */
	public function init_log() { 
		$current_user_id = $this->get_curr_userid();
		$time = $this->get_local_time();
		$log = get_option( $this->logvar );
		
		if ( ! is_array( $log ) ) {
			$log = array();
			array_push( $log, array( $time, __( 'Inizio Registrazione Eventi.', $this->textdomain), $current_user_id ) );
		}
		return update_option( $this->logvar, $log );
	}
	
	public function get_logtitle() {
		return $this->title;
	}
	
	public function get_logname() {
		return $this->name;
	}
	
	public function get_logvar() {
		return $this->logvar;
	}

	/**
	 * Get time with offset
	 * @return time
	 */
	public function get_local_time( ) {
	
		$time = time() + $this->offset_time;
		return $time;
	}

	/**
	 * Get user id
	 * @return id
	 */
	public function get_curr_userid( ) {
	
		$current_user = wp_get_current_user();
		$current_user_id = $current_user->ID;
	
		return $current_user_id;
	}

	/**
	 * Get time in nice format accordling to site settings
	 * @param  time   $time data
	 * @param  array $args format time to display ex: array( 'format' => 'date_and_time' or 'date' or 'time');
	 * @return string
	 */
	public function get_nice_time( $time, $args = false ) {
	
		$defaults = array( 'format' => 'date_and_time' );
		extract( wp_parse_args( $args, $defaults ), EXTR_SKIP );
	
		if ( ! $time)
			return false;
	
		if ( $format == 'date' )
			return date( $this->format_date, $time );
			
		if ( $format == 'time' )
			return date( $this->format_time, $time );
			
		if ( $format == 'date_and_time' ) 
			return date( $this->format_date, $time ) . " " . date( $this->format_time, $time );
	
		return false;
	}

	/**
	 * Write log
	 * @param event description to write
	 * @return true or false
	 */
	public function scrivi_log( $event ) {
		$current_user_id = $this->get_curr_userid();
		$time = $this->get_local_time();
		$log = get_option( $this->logvar );
		if ( ! is_array( $log ) ) {
			$log = array();
			$this->init_log();
			$log = get_option( $this->logvar );
			$time = $this->get_local_time();
		}
		array_push( $log, array( $time, $event, $current_user_id ) );
		return update_option( $this->logvar, $log );
	}

	/**
	 * Read log
	 * @return array of events log
	 */
	public function leggi_log() {
		$log = get_option( $this->logvar );
		// If no log created yet, create one
		if ( ! is_array( $log ) ) {
			$this->init_log();
		}
		return array_reverse( get_option( $this->logvar ) );
	}

	/**
	 * Delete log
	 * delete log and write event of deletion
	 * @return true or false
	 */
	public function delete_log() {
		$log             = array();
		$current_user_id = $this->get_curr_userid();
		$time            = $this->get_local_time();
		array_push( $log, array( $time, __( 'Elenco Eventi Cancellato.', $this->textdomain), $current_user_id ) );
		return update_option( $this->logvar, $log );
	}


	public function display_log_tab() {
		$continue = false;
		include_once( $this->appdir.'logger-view-tabs.php' );
	}
	
}
}

?>