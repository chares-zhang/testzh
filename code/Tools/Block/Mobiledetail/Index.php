<?php

class Tools_Block_Mobiledetail_Index extends Core_Block_Template 
{
	public function __construct()
	{
		parent::__construct();
		Util::factory('js')->setJsUrl('tools/mobiledetail/index.js');
		$this->setTemplete('index');
	}
	
}
