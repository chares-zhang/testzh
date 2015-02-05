<?php

class Core_Block_Page_Menu extends Core_Block_Template 
{
	public function __construct()
	{
		parent::__construct();
		$this->setTemplete('page/menu',true);
	}
	
	public function isCurrent()
	{
		$controller = $this->controller;
	}

	public function setCurrent($controller)
	{
	}

	public function inModule($moduleArr)
	{
		if (in_array($this->module,$moduleArr)) {
			return true;
		}
		return false;
	}

	/**
	 * array(
	 *		'module_name1' => array('controller_name1','controller_name2'),
	 * 		'module_name2' => array('controller_name1','controller_name2'),
	 * )
	 *
	 */
	public function inController($controllerMenu)
	{
		if (is_array($controllerMenu) && !empty($controllerMenu)) {
			foreach($controllerMenu as $module=>$controllers){
				if ( $this->module == $module && in_array($this->controller,$controllers) ) {
					return true;
				}
			}
		}
		return false;
	}
}
