<?php

class Core_IndexController extends BaseController 
{
	public function indexAction() 
	{
		//echo "uid:".$this->uid;
		$userRow = $this->userRow;

		$this->assign('userRow',$userRow);
		include $this->template('index');
	}
	
	public function logoutAction()
	{
		Session::getInstance($uid)->clear($uid);
	    Cookie::delete('sid');
		Cookie::delete('uid');
		echo 'logout success';
		exit;
	}
}
