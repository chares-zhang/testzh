<?php
set_time_limit(0);
ini_set('memory_limit', '1024M');
require_once realpath(dirname(dirname(__FILE__)) . '/library/Init.php');

abstract class AbstractCron
{
    protected $_config;

	private $_db;
    
    //initialize
    public function __construct(){
		$this->_config = Common::getConfig();
    }
	
	public function db($uid = 0){
		if(!$uid){
			$this->_db = $this->getDb();
		}else{
			$this->_db = Common::getDb(Common::shardId($uid));
		}
		return $this->_db;
	}

	public function getDb($shardId = 1){
		$this->_db = Common::getDb($shardId);
		return $this->_db;
	}

    public function log($file, $msg)
    {
        $logdir = LOGS_PATH . '/cron/';
        $logfile = $logdir .'/'. $file;

        $content =  "[".date('Y-m-d H:i:s', time())."] File: " . $file . "\n Excute: " . $msg . "\n";

        file_put_contents($logfile, $content, FILE_APPEND);
        echo $content;
    }
    
    /**
     * @return float
     */
    public function microtime_float()
	{
	    list($usec, $sec) = explode(" ", microtime());
	    return ((float)$usec + (float)$sec);
	}
}
