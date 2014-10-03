<?php
class Access_Model_Taobao_Session extends AbstractModel 
{
	static private $_instance;
	
	public function __construct($uid)
	{
		
	}

	public static function getInstance($uid)
	{
		if (!self::$_instance) {
			self::$_instance = new self($uid);
		}
		return self::$_instance;
	}
	
	public function start()
	{
		switch($config['session_save']) {
		    case 'memcache':
		    	@ini_set('session.save_handler', 'memcache');
		    	session_save_path($this->getSessionSavePath());
		    	break;
	    	default:
	    		session_module_name('files');
	    		if (is_writable(Hlg::getBaseDir('session'))) {
	    			session_save_path($this->getSessionSavePath());
	    		}
	    		break;
		}
	}
	
	
}
