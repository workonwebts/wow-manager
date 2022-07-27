<?php 
require_once( 'class-wow-sysinfo.php' );
$app = function ($module) {
	$this->modules[$module]=WoW_Sysinfo::instance( $this, $module);
};

//$app('sysinfo');
