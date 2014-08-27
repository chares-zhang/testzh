<?php

class Core_IndexController extends Core_BaseController 
{
	public function indexAction() 
	{
		echo 'index';
		$uid = 888888;
		$userM = Common::getModel('core/user');
		$userRow = $userM->getUserItem($uid);
		var_dump($userRow);exit;
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
