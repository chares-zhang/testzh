<?php

class Core_Block_Index_Index extends Core_Block_Template 
{
	private $params;
	
	public function __construct()
	{
		parent::__construct();
		$this->initParams();
		$this->loadData();
		$this->setTemplete('index');
	}
	
	public function initParams()
	{
		$this->params = $_REQUEST;
	}
	
	public function loadData()
	{
		$rt = array(
				0=>array('num_iid'=>1234,'price'=>'66.66'),
				1=>array('num_iid'=>88,'price'=>'88.88'),
		);
		return $rt;
	}
	
}
