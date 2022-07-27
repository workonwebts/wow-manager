<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists( 'WoW_Updates' ) ) {
class WoW_Updates {
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
		 * The basevars prefix
		 * @var 	string
		 * @access  public
		 * @since 	1.0.0
		 */
		public $basevars;

    /**
     * Constructor.
     */
    public function __construct($ref)
    {
        // Load l10n for fixtures creating.
//        load_plugin_textdomain( 'bookly', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	
		if ( ! $ref ) return;
		$this->appRef=$ref;
		if(isset($this->appRef->textdomain)) {
			$this->textdomain=$this->appRef->textdomain;
		} else {
			$this->textdomain=sanitize_file_name(basename(__FILE__,'.php'));
		}
		$this->basevars=$this->appRef->basevars;
		
		$db_version   = get_option( $this->appRef->_token.'_version' );
		$app_version = $this->appRef->version;
	
		if ( $app_version > $db_version && $db_version !== false ) {
	
			$db_version_underscored     = 'update_' . str_replace( '.', '_', $db_version );
			$plugin_version_underscored = 'update_' . str_replace( '.', '_', $app_version );
	
			// sort the update methods ascending
			$updates = array_filter(
				get_class_methods( $this ),
				function( $method ) { return strstr( $method, 'update_' ); }
			);
			usort( $updates, 'strnatcmp' );
	
			foreach ( $updates as $method ) {
				if ( $method > $db_version_underscored && $method <= $plugin_version_underscored ) {
					call_user_func( array( $this, $method ) );
				}
			}
	
			update_option( $this->appRef->_token.'_version', $app_version );
			WoW_popAdminMessage("Plugin ".$this->appRef->menu_title." Versione ".$app_version." aggiornato!!!", $tipo=WOW_MANAGER_MSG_STATUSSUCC, $dismiss=true);
		}
	}

    function update_2_3_1()
    {
        //global $wpdb;
    }

    function drop( $ab_tables ) {
        /** @var wpdb $wpdb */
        global $wpdb;

        if ( ! is_array( $ab_tables ) ) {
            $ab_tables = array( $ab_tables );
        }
        $get_ab_foreign_keys = "SELECT table_name, constraint_name FROM information_schema.key_column_usage WHERE REFERENCED_TABLE_SCHEMA=SCHEMA() AND REFERENCED_TABLE_NAME IN (".implode(', ', array_fill(0, count($ab_tables), '%s')).")";
        $schema = $wpdb->get_results( $wpdb->prepare( $get_ab_foreign_keys, $ab_tables ) );
        foreach ( $schema as $foreign_key ) {
            $wpdb->query( "ALTER TABLE `$foreign_key->table_name` DROP FOREIGN KEY `$foreign_key->constraint_name`" );
        }
        $wpdb->query( 'DROP TABLE IF EXISTS `'.implode("`,\r\n `",$ab_tables).'` CASCADE;' );
    }

}
}
