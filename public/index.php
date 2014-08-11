<?php
error_reporting(E_ALL | E_STRICT);
header("Content-type: text/html; charset=utf-8");
require_once realpath(dirname(dirname(__FILE__)) . '/library/My/Init.php');
$dispatcherM = Dispatcher::getInstance();
try{
	$dispatcherM->dispatch();
}catch(Exception $e){
	var_dump($e);
	exit;
}