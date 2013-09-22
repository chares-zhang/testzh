<?php
require_once realpath(dirname(dirname(__FILE__)) . '/library/Init.php');
error_reporting(E_ALL | E_STRICT);
$dispatcherM = Dispatcher::getInstance();
try{
	$dispatcherM->dispatch();
}catch(Exception $e){
	//@TODO:计入日志.
//	echo 11;exit;
	var_dump($e);
	exit;
}