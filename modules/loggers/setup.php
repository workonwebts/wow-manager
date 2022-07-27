<?php 
require_once( 'class-wow-logger-manager.php' );
$app = function ($module) {
	$this->modules[$module]=Wow_Logger_Manager::instance($this->_token, $this, $module);
	$this->loggers=$this->modules[$module];
};

//$app('loggers');
