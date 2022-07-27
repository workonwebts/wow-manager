<?php 
require_once( 'class-wow-support.php' );
$app = function ($module) {
	$this->modules[$module]=WoW_Support::instance($this, $module);
};

//$app('support');
