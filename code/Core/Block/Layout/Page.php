<?php

class Core_Block_LayoutPage extends Core_Block_Template 
{
	public function __construct()
	{
		parent::__construct();
		$this->setTemplete('page',true);
	}
	
	public function getStaticUrl()
	{
		$mainInfo = Common::getMainInfo();
		return $mainInfo['static_url'];
	}
	
	
}
