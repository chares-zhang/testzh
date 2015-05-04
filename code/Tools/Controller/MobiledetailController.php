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
		$response = Common::getAsyncResponse();
		$params = $this->getParams();
		$pageNum = isset($params['page_num']) ? $params['page_num'] : 1;
		$pageSize = isset($params['page_size']) ? $params['page_size'] : 10;
		$approveStatus = isset($params['approve_status']) ? $params['approve_status'] : 'ONSALE';//商品上传后的状态,出售中ONSALE,仓库中INSTOCK
		$cid = isset($params['cid']) ? $params['cid'] : ''; // 店铺类目id
		$q = isset($params['q']) ? $params['q'] : '';
		$itemOrder = isset($params['item_order']) ? $params['item_order'] : 'list_time:asc';
		
		$tbitemM = Common::getPlatModel('items/tbitems');
		if (! empty($approveStatus)) {
			$tbitemM->setApproveStatus(strtolower($approveStatus));
		}
		if (! empty($cid)) {
			$tbitemM->setCid($cid);
		}
		if (! empty($q)) {
			$tbitemM->setQ($q);
		}
		if (! empty($itemOrder)) {
			list ($orderKey, $orderValue) = explode(':', $itemOrder);
			$tbitemM->setOrderBy($orderKey, $orderValue);
		}
		$itemListData = $tbitemM->LoadList($pageNum, $pageSize)->get(); // 返回数组
		$res = array();
		if (!empty($itemListData)) {
			foreach ($itemListData as $k=>$itemList) {
				$res[$k]['title'] = $itemList['title'];
				$res[$k]['num_iid'] = $itemList['num_iid'];
				$res[$k]['outer_id'] = $itemList['outer_id'];
				$res[$k]['pic_url'] = $itemList['pic_url'];
				$res[$k]['price'] = $itemList['price'];
			}
		}
		$totalRecords = $tbitemM->count();
		
// 		$block = Common::getBlock('tools/mobiledetail_list_item');
// 		$data = $block->getData();
// 		$totalRecords = $block->getTotalRecords();
		
		$result = array(
			'total_records' => (int)$totalRecords,
			'data' => $res,
			'page_num' => (int)$pageNum,
			'page_size' => (int)$pageSize
		);
		$response->setResult($result);
		$response->toJson();
		$response->sendResponse();
	}
	
	/**
	 * 批量发布手机详情
	 */
	public function batchPublishAction(){
// 		$response = Hlg::getModel('core/asyncResponse');
// 		$generateType = (int)$this->getRequest()->getParam('generate_type', self::GENERATE_TYPE_PHOTO);
// 		$deleteType = (int)$this->getRequest()->getParam("delete_type", 3);
// 		$numIids = implode(',', $this->getRequest()->getParam('num_iids'));
// 		$shopId = Hlg::getShop()->getId();
// 		if(empty($numIids)){
// 			Hlg::throwException("请选择需要生成的宝贝");
// 		}
// 		$hlgapi = Hlg::getModel("core/hlgapi");
// 		try{
// 			$hlgapi->api("mobiledesc.item.batch.generate",array("shop_id" => $shopId,"num_iids" => $numIids,"delete_type" => $deleteType,"generate_type" => $generateType,"is_publish" => 1));
// 			$response->setPayload(array("status" => 'ok'));
// 			$this->getResponse()->setBody($response->toJson());
// 		}catch(HlgApiException $e){
// 			Hlg::throwException($e);
// 		}
	}
}

