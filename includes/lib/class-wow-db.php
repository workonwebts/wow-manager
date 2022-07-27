<?php 
if ( ! defined( 'ABSPATH' ) ) exit;
if( ! class_exists( 'WoW_wpdb' ) ) {
	class WoW_wpdb extends wpdb {
		
		/*
		* The wowdb status connction.
		* stato connessione al db esterno
		* @var     integer
		* @access  public
		* @since   1.0.0
		*/
		public $status_conn = 0;
		
		public $appRef;

		/**
		* Connects to the database server and selects a database
		*
		* PHP5 style constructor for compatibility with PHP5. Does
		* the actual setting up of the class properties and connection
		* to the database.
		*
		* @link https://core.trac.wordpress.org/ticket/3354
		* @since 2.0.8
		*
		* @global string $wp_version
		*
		* @param string $dbuser     MySQL database user
		* @param string $dbpassword MySQL database password
		* @param string $dbname     MySQL database name
		* @param string $dbhost     MySQL database host
		*/
		public function __construct( $dbuser, $dbpassword, $dbname, $dbhost ) {
			register_shutdown_function( array( $this, '__destruct' ) );

			if ( WP_DEBUG && WP_DEBUG_DISPLAY ) {
					$this->show_errors();
			}

			// Use ext/mysqli if it exists unless WP_USE_EXT_MYSQL is defined as true
			if ( function_exists( 'mysqli_connect' ) ) {
					$this->use_mysqli = true;

					if ( defined( 'WP_USE_EXT_MYSQL' ) ) {
							$this->use_mysqli = ! WP_USE_EXT_MYSQL;
					}
			}


			if ( (trim($dbuser)=='' || trim($dbpassword)=='' || trim($dbname)=='' || trim($dbhost)=='') ) {
				$this->status_conn=0;
			} else { // connection.

				$this->dbuser     = $dbuser;
				$this->dbpassword = $dbpassword;
				$this->dbname     = $dbname;
				$this->dbhost     = $dbhost;
	
				// wp-config.php creation will manually connect when ready.
				if ( defined( 'WP_SETUP_CONFIG' ) ) {
						return;
				}
	
				$this->db_connect(false);
				if($this->ready){
					$this->status_conn=1;
				} else {
					$this->status_conn=-1;
				}
			}
		}
		
		public function __destruct() {
            if(method_exists('wpdb','__destruct')) {
                parent::__destruct();
            }
		}
	}
}
?>