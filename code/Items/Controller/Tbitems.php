<?php
/**
 * 淘宝商品类
 * @author chares
 *
 */
class Items_TbitemsController extends Core_BaseController 
{
	public function loadAction()
	{
		$params = $this->getParams();
		$pageSize = (int)$this->getParam('page_size', 10);
		$pageNum = (int)$this->getParam('page_num', 1);
		$approveStatus = isset($params['approve_status']) ? $params['approve_status'] : 'ONSALE';//商品上传后的状态,出售中ONSALE,仓库中INSTOCK
		$cid = isset($params['cid']) ? $params['cid'] : ''; // 店铺类目id
		$q = isset($params['q']) ? $params['q'] : '';
// 		$itemOrder = isset($params['item_order']) ? $params['item_order'] : 'list_time:asc';
		
		$tbitemsM = Common::getPlatModel('items/tbitems');
		if (! empty($approveStatus)) {
			$tbitemsM->setApproveStatus(strtolower($approveStatus));
		}
		if (! empty($cid)) {
			$tbitemsM->setCid($cid);
		}
		if (! empty($q)) {
			$tbitemsM->setQ($q);
		}
// 		if (! empty($itemOrder)) {
// 			list ($orderKey, $orderValue) = explode(':', $itemOrder);
// 			$tbitemsM->setOrderBy($orderKey, $orderValue);
// 		}
		$itemListData = $tbitemsM->LoadList($pageNum, $pageSize)->get(); // 返回数组
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
		$totalRecords = $tbitemsM->count();

		$response = Common::getAsyncResponse();
		$result = array(
			'total_records' => $totalRecords,
			'data' => $res,
			'page_num' => $pageNum,
			'page_size' => $pageSize
		);
		$response->setResult($result);
		$response->toJson();
		$response->sendResponse();
	}
	
	
}

