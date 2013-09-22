<?php
/**
 * 路由分发器
 */

class Dispatcher {

    protected static $_instance;
    
	/**
	 * 请求模块
	 */
	private static $_module = '';
    /**
     * 请求控制器
     */
    private static $_controller = '';

    /**
     * 请求控制器的方法
     */
    private static $_action = '';

    public static function getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new Dispatcher();
            return self::$_instance;
        } else {
            return self::$_instance;
        }
    }

    private function __construct() {
		$_REQUEST['m'] = empty($_REQUEST['m']) ? 'tools' : $_REQUEST['m'];
		$_REQUEST['c'] = empty($_REQUEST['c']) ? 'showcase' : $_REQUEST['c'];
		$_REQUEST['a'] = empty($_REQUEST['a']) ? 'index' : $_REQUEST['a'];
		self::$_module = $_REQUEST['m'];
		self::$_controller = $_REQUEST['c'];
		self::$_action = $_REQUEST['a'];
    }

    public function dispatch(){
		$controllerName = ucwords(self::$_module).'_'.ucwords(self::$_controller).'Controller';
		$controllerObject = new $controllerName();
        $actionName = self::$_action.'Action';
        $controllerObject->$actionName();
    }

	public function getModule(){
		return self::$_module;
	}
	
	public function getController(){
		return self::$_controller;
	}

	public function getAction(){
		return self::$_action;
	}
}
