<?php

abstract class AbstractController {
    protected $module;
    protected $controller;
    protected $action;
    protected $params;
    
    public $block;
    public $layout;

    public function __construct()
    {
        $this->module = Dispatcher::getInstance()->getModule();
        $this->controller = Dispatcher::getInstance()->getController();
        $this->action = Dispatcher::getInstance()->getAction();
    }
    
    public function getParams()
    {
    	$params = Dispatcher::getInstance()->getParams();
    	return $params;
    }
    
    public function getParam($name,$default)
    {
    	$param = Dispatcher::getInstance()->getParam($name,$default);
    	return $param;
    }
    
    public function loadLayout($blockName = null)
    {
    	if ($blockName === null) {
	    	$config = Common::getMainInfo();
	    	$blockName = $config['layout_template'];
    	}
    	$block = Common::getBlock($blockName);
    	$this->layout = $block;
    }
    
    public function renderLayout()
    {
    	$response = $this->getResponse();
    	$output = $this->layout->toHtml();
    	$response->setOutput($output);
    	$response->sendResponse();
    }
    
//     public function getBlockInstance($blockName = null)
//     {
//     	//默认取当前的block
//     	if ($blockName === null) {
//         	$blockName = $this->module . '/' . $this->controller . '_' . $this->action;
//     	}
//         $this->block = Common::getBlock($blockName);
        
//         return $this->block;
//     }
    
    public function getResponse()
    {
        $response = Response::getInstance();
        return $response;
    }
    
    public function getAsyncResponse()
    {
        $response = Response::getInstance();
        Response::getInstance()->addHeader('Content-Type','application/json');
        return $response;
    }
    
    /**
    * controller中指定显示的模板,只能指定当前module的模板.
    * @param string $filename 文件名(不含后缀).
    * @param bool $common 是否为公共,true则指定到module根目录下,false则指定template/[module]/[controller]/$filename.pthml中的模板
    */
    public function template($filename, $common=false) 
    {
        if ($common) {
            return TEMPLATE_PATH . "/" . ucwords($this->module) . "/{$filename}.phtml";
        } else {
            return TEMPLATE_PATH . "/" . ucwords($this->module) . "/" . ucwords($this->controller) . "/{$filename}.phtml";
        }
    }
    
    /**
    * 统一给模板中要显示的变量赋值.
    *
    */
    public function assign($key, $value)
    {
        $this->t[$key] = $value;
    }
    
    /**
    * controller中指定跳转到$url地址.
    * @param string $url
    */
    public function redirectUrl($url)
    {
        Common::redirectUrl($url);
    }

    /**
    * controller中指定跳转站内地址
    * @param string $routePath 例:[模块名]/[控制器名]/[方法名]
    * @param array $routeParams 例:array('p'=>2);
    */
    public function redirect($path, $arguments=array()) 
    {
        Common::redirect($path,$arguments);
    }
    
    /**
    * 模板中指定站内跳转地址. eg.: $this->getUrl("module/controller/action",array('key1'=>'value1'));
    * @param string $routePath 例:[模块名]/[控制器名]/[方法名]
    * @param array $routeParams 例:array('p'=>2);
    */
    public function getUrl($routePath,$requestParams=array())
    {
        return Common::getUrl($routePath,$requestParams);
    }
    
}
