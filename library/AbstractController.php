<?php

abstract class AbstractController {
	//module
	protected $module;
	//controller
	protected $controller;
	//action
	protected $action;

	//配置文件
	protected $config;
	//模板变量
	protected $t = array();
	//static目录url地址.
	protected $staticurl = '';
	
	public function __construct()
	{
		$this->module = Dispatcher::getInstance()->getModule();
		$this->controller = Dispatcher::getInstance()->getController();
		$this->action = Dispatcher::getInstance()->getAction();
		$this->config = Common::getConfig();
		$this->staticurl = Common::getConfigUrl('staticurl');
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
		header("location:$url");
	}

	/**
	* controller中指定跳转站内地址
	* @param string $routePath 例:[模块名]/[控制器名]/[方法名]
	* @param array $routeParams 例:array('p'=>2);
	*/
	public function redirect($path, $arguments=array()) 
	{
		$url = $this->getUrl($path,$arguments);
		header("location:$url");
	}
	
	/**
	* 模板中指定站内跳转地址. eg.: $this->getUrl("module/controller/action",array('key1'=>'value1'));
	* @param string $routePath 例:[模块名]/[控制器名]/[方法名]
	* @param array $routeParams 例:array('p'=>2);
	*/
	public function getUrl($routePath,$requestParams=array())
	{
		if(empty($routePath)){
			throw new Exception('getUrl param 1 can not empty.');
		}
        
		$routeArray = explode('/', $routePath);
		
		if(count($routeArray) != 3){
			throw new Exception('getUrl param 1 error.');
		}
		
		$module = ('*'===$routeArray[0]) ? $this->module : $routeArray[0];
		$controller = ('*'===$routeArray[1]) ? $this->controller : $routeArray[1];
		$action = ('*'===$routeArray[2]) ? $this->action : $routeArray[2];
		$requestUri = '';
		$tailUrl = '?m='.$module.'&c='.$controller.'&a='.$action;
		if (!empty($requestParams)) {
			if (is_array($requestParams)){//若是数组,拼好requesturi
				$requestUri = http_build_query($requestParams);
			}else{//若不是数组，则直接作为参数输出
				$requestUri = $requestParams;
			}
			$tailUrl .= '&'.$requestUri;
		}

		$webhostUrl = Common::getWebHostUrl();
		$url = $webhostUrl . $tailUrl;
		
		return $url;
	}
	
	/**
	* 获取配置文件中的各种url,默认取webhost
	*
	*/
//	public function getConfigUrl($urlKey='webhost'){
//		return $this->config[$urlKey];
//	}
}
