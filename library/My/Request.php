<?php
/**
 * 请求类
 */

class Response {
    private function __construct() {
    	$this->setRequestUri();
    	$this->_urlMatch();
    }

    private function _urlMatch()
    {
    	$defaultRoute = explode('/',Common::getDefaultRoute());
    	$requestUri = $this->getRequestUri();
    	$path = $requestUri?explode('/',trim($requestUri,'/')):array();
    	
    	if(isset($path[0]) && !empty($path[0])) {
    		$module = $path[0];
    	} else {
    		$module = $defaultRoute[0];
    	}
    	
    	if(isset($path[1]) && !empty($path[1])) {
    		$controller = $path[1];
    	} else {
    		$controller = $defaultRoute[1];
    	}
    	
    	if(isset($path[2]) && !empty($path[2])) {
    		$action = $path[2];
    	} else {
    		$action = $defaultRoute[2];
    	}
    	
    	$this->setModule($module);
    	$this->setController($controller);
    	$this->setAction($action);
    }
    
    public function getRequestUri()
    {
    	return self::$_requestUri;
    }
    
    public function setRequestUri(){
    	$requestUri = $_SERVER['REQUEST_URI'];
    	$pos = strpos($requestUri, '?');
    	if ($pos) {
    		self::$_requestUri = substr($requestUri, 0, $pos);
    	} else {
    		self::$_requestUri = $requestUri;
    	}
    	return self::$_requestUri;
    }
    
    public function dispatch(){
		$controllerName = ucwords(self::getModule()) . '_' . ucwords(self::getController()) . 'Controller';
		if (class_exists($controllerName)) {
			$controllerObject = new $controllerName();
		} else {
			throw new Exception('no controller');
		}
        $actionName = self::getAction().'Action';
        $controllerObject->$actionName();
    }

    public function setModule($module)
    {
    	self::$_module = $module;
    }
    
    public function setController($controller)
    {
    	self::$_controller = $controller;
    }
    
    public function setAction($action)
    {
    	self::$_action = $action;
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
