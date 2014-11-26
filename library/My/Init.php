<?php
define('GUARD', 1);	//门神
define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);
define('APP_PATH', realpath(dirname(dirname(dirname(__FILE__)))));
define('CODE_PATH', APP_PATH . DS . 'code');
define('LOGS_PATH', APP_PATH . DS .'logs');
define('CONFIG_PATH', APP_PATH . DS . 'config');
define('LIBRARY_PATH', APP_PATH . DS . 'library');
define('PUBLIC_PATH', APP_PATH . DS . 'public');
define('TEMPLATE_PATH', APP_PATH . DS . 'template');

$paths[] = LIBRARY_PATH;
$paths[] = LIBRARY_PATH . DS . 'My';
$paths[] = LIBRARY_PATH . DS . 'top';
$paths[] = LIBRARY_PATH . DS . 'top' . DS . 'request';
$paths[] = CODE_PATH;

$appPath = implode(PS, $paths);
set_include_path($appPath . PS . get_include_path());

require_once('functions.php');
require_once('Dispatcher.php');
require_once('Autoload.php');

// spl_autoload_register('myAutoload');
My_Autoload::register();
set_error_handler('myErrorHandler');

/** 
  * 获取配置文件
  */
$config = Common::getConfig('config');

/**
  * 设置时区
  */
date_default_timezone_set($config['main_info']['timezone']);
/**
  *  防止ssx注入攻击
  */
doHtmlspecialchars();
doStripslashes();

?>