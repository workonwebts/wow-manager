<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists( 'WoW_Installer' ) ) {
class WoW_Installer
{
    private $app_data;
    private $options;
	private $app_tables;
	
	  /**
		 * The parent object
		 * @var 	object
		 * @access  public
		 * @since 	1.0.0
		 */
		public $appRef;
	
	  /**
		 * The basevars prefix
		 * @var 	string
		 * @access  public
		 * @since 	1.0.0
		 */
	private $basevars;
	private $_token;
	private $version;

    /**
     * Constructor.
     */
    public function __construct($ref) {

		if ( ! $ref ) return;
		$this->appRef=$ref;
		$this->basevars=$this->appRef->basevars;
		$this->_token=$this->appRef->_token;
		$this->version=$this->appRef->version;
		// preset data
        $this->app_data = $this->set_app_data();
        $this->options = $this->set_options();
        $this->app_tables = $this->set_app_tables();
    }
	
        /**
         * App_Data.
         */
	private function set_app_data() {
		return array(
            '_data_loaded'                         => '0',
            // DB version.
            '_version'                          => '0.0',
            // Timestamp when the plugin was installed.
            '_installation_time'                   => ''
		);
	}

        /**
         * Options.
         */
	private function set_options() {
		return array(
		  'support_link_enabled' => 'no',
		  'support_link_url' => 'https://www.work-on-web.it',
		  'support_link_mail' => 'webmaster@work-on-web.it',
		  'support_link_phone' => '+39.338.6076900',
		  'support_page_enabled' => 'no',
		  'support_name' => 'Work On Web di Starz Andrea',
		  'store_url' => get_option('siteurl'),
		  'store_host' => 'localhost',
		  'store_user' => DB_USER,
		  'store_pass' => DB_PASSWORD,
		  'store_dbname' => DB_NAME,
		  'delete_product_enable' => '0',
		  'delete_product_menu' => 'parent',
		  'enable_column_image' => '0',
		  'enable_default_cat_image' => '0',
		  'default_category_image' => '',
		  'category_use_image' => 'no',
		  'category_enable_column_image' => '0',
		  'category_enable_default_image' => '0',
		  'category_default_image' => '',
		  'product_enable_default_image' => '0',
		  'product_default_image' => '',
		  'maintenance_redirect' => 'no',
		  'redirect_page' => '',
		  'redirect_url' => '',
		  'maintenance_enabled' => '0',
		);
	}
	
        /**
         * Tables.
         */
	private function set_app_tables() {
		return array(
		);
	}
	
    /**
     * Install.
     */
    public function install() {
        // Create tables and load data if it hasn't been loaded yet.
        if ( ! get_option( $this->_token.'_data_loaded' ) ) {
            $this->_create_tables();
            $this->_create_options();
			$this->_create_data();
        }
		return true;
    }

    /**
     * Create app data.
     */
    private function _create_data() {
        update_option( $this->_token.'_data_loaded', '1' );
        update_option( $this->_token.'_version', $this->version );
        update_option( $this->_token.'_installation_time', time() );
    }

	/**
     * Create empty options.
     */
    private function _create_options() {
        // Add options.
        foreach ( $this->options as $name => $value ) {
            add_option( $this->basevars.$name, $value, '', 'yes' );
        }
    }

    /**
     * Create tables in database.
     */
    private function _create_tables() {
        // Add tables.
        foreach ( $this->app_tables as $tbl_name => $tbl_struct ) {
            $this->_create_table_from_struct( $tbl_name, $tbl_struct );
        }

    }
	
	private function _create_table_from_struct($tbl,$stru) {
        /** @global wpdb $wpdb */
        global $wpdb;
		$engine="InnoDB";
		$chset=defined(DB_CHARSET)?DB_CHARSET:"utf8mb4_unicode_520_ci";
		$collate=defined(DB_COLLATE)?DB_COLLATE:"utf8_general_ci";
		$qry="";
		$qry.="CREATE TABLE IF NOT EXISTS `";
		$qry.=$tbl;
		$qry.="` (";
		$qry.=$stru;
		$qry.=") ENGINE = {$engine} DEFAULT CHARACTER SET = {$chset} COLLATE = {$collate}";
        $wpdb->query($qry);
/*
        $wpdb->query(
            'CREATE TABLE IF NOT EXISTS `' . AB_Staff::getTableName() . '` (
                `id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_user_id`         BIGINT(20) UNSIGNED,
                `avatar_url`         VARCHAR(255) DEFAULT "",
                `avatar_path`        VARCHAR(255) DEFAULT "",
                `full_name`          VARCHAR(128) DEFAULT "",
                `email`              VARCHAR(128) DEFAULT "",
                `phone`              VARCHAR(128) DEFAULT "",
                `google_data`        VARCHAR(255) DEFAULT "",
                `google_calendar_id` VARCHAR(255) DEFAULT "",
                `position`           INT NOT NULL DEFAULT 9999
            ) ENGINE = INNODB
            DEFAULT CHARACTER SET = utf8
            COLLATE = utf8_general_ci'
        );
*/
	}

    /**
     * Uninstall.
     */
    public function uninstall() {
//print_r($this);exit;
        $this->_remove_options();
        $this->_drop_tables();
		$this->_remove_data();
		return true;
   }

    /**
     * Remove data.
     */
	private function _remove_data() {
        update_option( $this->_token.'_data_loaded', '0' );
        delete_option( $this->_token.'_version' );
        delete_option( $this->_token.'_installation_time' );
		
	}
    private function _remove_options() {
        // Remove options.
        foreach ( $this->options as $name => $value ) {
            delete_option( $this->basevars.$name );
        }

    }

    private function _drop_fk( $ww_tables ) {
        /** @var wpdb $wpdb */
        global $wpdb;
        $get_ab_foreign_keys =
            'SELECT table_name, constraint_name
               FROM information_schema.key_column_usage
              WHERE REFERENCED_TABLE_SCHEMA=SCHEMA()
                AND REFERENCED_TABLE_NAME IN (' . implode( ', ', array_fill( 0, count( $ww_tables ), '%s' ) ) .
            ')';
        $schema = $wpdb->get_results( $wpdb->prepare( $get_ab_foreign_keys, $ww_tables ) );
        foreach ( $schema as $foreign_key )
        {
            $wpdb->query( "ALTER TABLE `$foreign_key->table_name` DROP FOREIGN KEY `$foreign_key->constraint_name`" );
        }
    }

    private function _drop_tables() {
        /** @var wpdb $wpdb */
        global $wpdb;
		if (count($this->app_tables) > 0) {
			$this->_drop_fk( $this->app_tables );
			$wpdb->query( 'DROP TABLE IF EXISTS `' . implode( '`, `', array_keys($this->app_tables )) . '` CASCADE;' );
		}
    }

}
}
