<?php

class Core_Block_Page_Menu extends Core_Block_Template 
{
	public function __construct()
	{
		parent::__construct();
		$this->setTemplete('page/menu',true);
	}
	
}
