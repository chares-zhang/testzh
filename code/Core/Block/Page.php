<?php

class Core_Block_Page extends Core_Block_Template 
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
	
	public function getBodyId()
	{
		$bodyId = $this->module . '_' . $this->controller . '_' . $this->action;
		return $bodyId;
	}

	public function getUserName()
	{
		$userInfo = Common::getUserInfo();
		$userName = $userInfo['username'];
		return $userName;
	}
}
