<?php
define('APP_PATH', realpath(dirname(dirname(__FILE__))));
define('LOGS_PATH', APP_PATH . '/logs');
define('CONFIG_PATH', APP_PATH . '/config');
define('LIBRARY_PATH', APP_PATH . '/library');
define('PUBLIC_PATH', APP_PATH . '/public');
define('TEMPLATE_PATH', APP_PATH . '/template');
define('GUARD', 1);	//门神

set_include_path(
	LIBRARY_PATH . PATH_SEPARATOR 
	. LIBRARY_PATH . '/top' . PATH_SEPARATOR
	. LIBRARY_PATH . '/top/request' . PATH_SEPARATOR
	. APP_PATH . '/models' . PATH_SEPARATOR 
	. APP_PATH . '/controllers' . PATH_SEPARATOR 
	. get_include_path()
);

require_once(LIBRARY_PATH . '/functions.php');
require_once(LIBRARY_PATH . '/Dispatcher.php');

spl_autoload_register('myAutoload');
set_error_handler('myErrorHandler');

/** 
  * 获取配置文件
  */
$config = Common::getConfig('config');

/**
  * 设置时区
  */
date_default_timezone_set($config['timezone']);

/**
  *  防止XML注入攻击
  */
doHtmlspecialchars();
doStripslashes();

?>