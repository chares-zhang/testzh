<?php

class Core_IndexController extends Core_BaseController 
{
	public function indexAction() 
	{
		// $taobao = Common::getModel('core/taobao');
		// $tbSession = '6201f141a3c876e24c1040ZZ245aabaca59539413242df61769384252';
		
		// $numIid = '22466447540';
		// $fields = 'title,desc_modules,price,pic_url,desc,sku,is_fenxiao,approve_status,is_virtual';
		// $req = new ItemGetRequest;
		// $req->setFields($fields);
		// $req->setNumIid($numIid);
		// try{
		// 	$taobao->format = 'json';
		// 	$resp = $taobao->execute($req);
		// 	var_dump($resp);
		// 	exit;
		// }catch(Exception $e){
		//     var_dump($e);exit;
		// }
		
		$this->loadLayout();
		$this->renderLayout();
		
// 		$this->getBlockInstance()->loadLayout();
// 		$this->getResponse()->sendResponse();
		
		
// 		$uid = 1;
// 		$userM = Common::getPlatModel('Access/user');
// 		$userRow = $userM->getUserRow($uid);
// 		var_dump($userRow);exit;
// 		include $this->template('index');
	}
	
	public function logoutAction()
	{
		Access_Model_Taobao_Session::getInstance()->destroy();
		$this->redirect('core/index/index');
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
		$response->toJson();
		$response->sendResponse();
	}
	
}
