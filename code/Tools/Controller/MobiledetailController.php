<?php
/**
 * 手机详情
 * @author chares
 *
 */
class Tools_MobiledetailController extends Core_BaseController 
{
	public function indexAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function getTbItemsAction()
	{
		$response = $this->getAsyncResponse();
		$block = Common::getBlock('tools/mobiledetail_list_item');
		
		$output = $data;
		$response->setOutput($output);
		$response->toJson();
		$response->sendResponse();
	}
}

