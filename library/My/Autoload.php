<?php
/**
 * 自动加载类
 */

class My_Autoload {
	static protected $_instance;

    private function __construct()
    {
    }
    
    /**
     * Singleton pattern implementation
     *
     * @return Hlg_Autoload
     */
    static public function instance()
    {
    	if (!self::$_instance) {
    		self::$_instance = new My_Autoload();
    	}
    	return self::$_instance;
    }
    
    /**
     * Register SPL autoload function
     */
    static public function register()
    {
    	spl_autoload_register(array(self::instance(), 'autoload'));
    }
    
    /**
     * Load class source code
     *
     * @param string $class
     */
    public function autoload($class)
    {
    	if (substr($class,-10) == 'Controller') {
    		if (strpos($class, '_') === false) {
    			$classFile = $class;
    		} else {
    			$classArr = explode('_',$class);
    			$classFile = ucwords($classArr[0]) . '_Controller_' . ucwords(trim(str_replace($classArr[0], '', $class),'_'));
    			$classFile = str_replace(' ', DIRECTORY_SEPARATOR, ucwords(str_replace('_', ' ', $classFile)));
    		}
    	} else {
    		$classFile = str_replace(' ', DIRECTORY_SEPARATOR, ucwords(str_replace('_', ' ', $class)));
    		
    	}
    	$classFile.= '.php';
    	return include $classFile;
    }
}
