<?php

class Core_IndexController extends Core_BaseController 
{
	public function indexAction() 
	{
		echo 'index';
		$userM = Common::M('core/user');
		$userM->fetchItem();
		exit;
	}
	
	public function logoutAction()
	{
// 		Session::getInstance($uid)->clear($uid);
	    Cookie::delete('sid');
		Cookie::delete('uid');
		echo 'logout success';
		exit;
	}
}
