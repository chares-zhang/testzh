<?php

class Core_IndexController extends Core_BaseController 
{
	public function indexAction() 
	{
		$this->loadLayout();
		$this->renderLayout();
		
// 		$this->getBlockInstance()->loadLayout();
// 		$this->getResponse()->sendResponse();
		
		
// 		$uid = 1;
// 		$userM = Common::getPlatModel('Access/user');
// 		$userRow = $userM->getUserItem($uid);
// 		var_dump($userRow);exit;
// 		include $this->template('index');
	}
	
	public function syncAction()
	{
		echo 'syncAction';
	}
	
	public function ansyncAction()
	{
		$response = $this->getAsyncResponse();
		$output = array(
			0=>array('num_iid'=>1234,'price'=>'66.66'),
			1=>array('num_iid'=>88,'price'=>'88.88'),
		);
		$response->setOutput($output);
		$response->sendResponse();
	}
	
}
