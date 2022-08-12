<?php 
require_once( 'class-wow-debug.php' );
$app = function ($module) {
	$this->modules[$module]=WoW_Debug::instance( $this, $module);
};

//$app('wooutils');
