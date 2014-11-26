<?php

class Core_Block_Ui_Wqui extends Core_Block_Template 
{
	private $params;
	
	public function __construct()
	{
		parent::__construct();
		$this->setTemplete('wqui');
	}
	
}
