<?php 
require_once( 'class-wow-wooutilities.php' );
$app = function ($module) {
	$this->modules[$module]=WoW_WooUtilities::instance( $this, $module);
};

//$app('wooutils');
