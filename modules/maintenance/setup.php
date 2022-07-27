<?php 
require_once( 'class-wow-maintenance.php' );
$app = function ($module) {
	$this->modules[$module]=WoW_Maintenance::instance( $this, $module);
};

//$app('maintenance');
