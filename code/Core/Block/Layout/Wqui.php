<?php

class Core_Block_Layout_Wqui extends Core_Block_Template 
{
	public function __construct()
	{
		parent::__construct();
		$this->setTemplete('core/ui_wqui',true);
	}
	
	public function getStaticUrl()
	{
		$mainInfo = Common::getMainInfo();
		return $mainInfo['static_url'];
	}
	
	
}
