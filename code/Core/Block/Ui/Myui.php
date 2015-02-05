<?php

class Core_Block_Ui_Myui extends Core_Block_Template 
{
	private $params;
	
	public function __construct()
	{
		parent::__construct();
		$this->setTemplete('myui');
	}
	
	public function getStaticUrl()
	{
		$mainInfo = Common::getMainInfo();
		return $mainInfo['static_url'];
	}
	
}
