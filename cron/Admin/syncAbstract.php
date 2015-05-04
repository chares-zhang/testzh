<?php

define("GUARD", 1);
define("ROOT_PATH", dirname(dirname(__FILE__)));
define("CONFIG_PATH", ROOT_PATH . '/config');
define("MODEL_PATH", ROOT_PATH . '/models');
define("LIBRARY_PATH", ROOT_PATH . '/library');
define("LOG_PATH", ROOT_PATH . '/logs');

require_once CONFIG_PATH . '/config.php';
require_once LIBRARY_PATH . '/Common.php';
require_once LIBRARY_PATH . '/Db.php';
require_once MODEL_PATH . '/MC.php';
require_once MODEL_PATH . '/AbstractModel.php';
require_once MODEL_PATH . '/UserRouter.php';


$config = Common::getConfig('config');
date_default_timezone_set($config['timezone']);

set_time_limit(180);
ini_set('memory_limit', '1024M');

abstract class syncAbstract
{
  protected $_db;

  public function __construct($shareID=1)
  {
    $this->init();
  }

  public function init()
  {
  }

  public function db($uid = 0) {
    if (!$uid) {
      $this->_db = $this->getDb();
    } else {
      $this->_db = Common::getDb(Common::shardId($uid));
    }   
    return $this->_db;
  }

  public function getDb($shardId = 1) {
    $this->_db = Common::getDb($shardId);
    return $this->_db;
  }
  
  public function log($file, $msg)
  {
    $logfile = LOG_PATH . '/cron/'. $file;
    
    $content = "[" . date('Y-m-d H:i:s', time()) . "] File: " . $file . "\n Excute: " . $msg . "\n";
    
    file_put_contents($logfile, $content, FILE_APPEND);
    echo $content;
  }
  
  public function microtime_float()
  {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
  }

  abstract public function run($db, $maxshift = 10000);

}
