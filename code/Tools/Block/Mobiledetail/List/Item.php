<?php

class Tools_Block_Mobiledetail_List_Item extends Core_Block_Template 
{
	private $_pageNum;
	private $_pageSize;
	
	public function __construct()
	{
		$this->_initParams();
		$data = $this->loadData();
	}
	
	private function _initParams()
	{
		$this->_pageNum = isset($this->_pageNum) ? $this->_pageNum : 1;
		$this->_pageSize = isset($this->_pageSize) ? $this->_pageSize : 10;
	}
	
	public function loadData()
	{
		$taobao = Common::getModel('core/taobao');
		$type = '1';
		
// 		$q = $params['q'];
// 		$type = $params['type'];
// 		$cid = $params['cid'];
// 		$orderBy = $params['order_by'];
// 		$hasDiscount = $params['has_discount'];
// 		$hasShowcase = $params['has_showcase'];
		
		
		switch ($type) {
			case '1'://在售
				$req = new ItemsOnsaleGetRequest;
				break;
			case '2'://库存
				$req = new ItemsInventoryGetRequest;
				break;
			default://所有
// 				$req = new ItemsSearchRequest;
// 				$shop = Hlg::getShop();
// 				$shopNick = $shop['shop_nick'];
// 				$startPrice = $params['start_price'];
// 				$endPrice = $params['end_price'];
// 				if($startPrice) {
// 					$req->setStartPrice($startPrice);
// 				}
// 				if($endPrice) {
// 					$req->setEndPrice($endPrice);
// 				}
// 				$req->setNicks($shopNick);
				break;
		}
		$pageSize = $this->_pageSize;
		$pageNum = $this->_pageNum;
		$req->setPageSize($pageSize);
		$req->setPageNo($pageNum);
		$req->setFields('num_iid,title,price,pic_url,outer_id,is_virtual,list_time,delist_time,111num,seller_cids,has_discount');
		try {
			$userRow = Common::getUserInfo();
			$tbSession = $userRow['access_token'];
			$taobao->format = 'json';
			$resp = $taobao->execute($req);
// 			$resp = $taobao->execute($req, $tbSession);
			var_dump($resp);exit;
			$this->_totalRecords = $resp->total_results;
			if (isset($resp->item_search)) {
				$tbData = $resp->item_search->items->item;
			} else if(isset($resp->items)){
				$tbData = $resp->items->item;
			} else {
				return false;
			}
// 			foreach($tbData as $tbItem){
// 				$item = array();
// 				$item['num_iid'] = $tbItem->num_iid;
// 				$item['outer_id'] = $tbItem->outer_id;
// 				$item['pic_url'] = $tbItem->pic_url;
// 				$item['title'] = $tbItem->title;
// 				$item['price'] = $tbItem->price;
// 				$items[] = $item;
// 				$this->_tbData = $items;
// 			}
		} catch (Top_Exception $e) {//exit(var_dump($e));
			var_dump(11,$e);exit;
			Hlg::throwException($e);
		} catch (Exception $e){
			Hlg::throwException($e);
		}
		
		
		
		
		
		
		
// 		$taobao = Hlg::getModel('core/taobao');
// 		$q = $params['q'];
// 		$type = $params['type'];
// 		$cid = $params['cid'];
// 		$orderBy = $params['order_by'];
// 		$hasDiscount = $params['has_discount'];
// 		$hasShowcase = $params['has_showcase'];
		
// 		if ($cid) {
// 			if (!in_array($type, array('1','2'))) {
// 				$type = '1';
// 			}
// 		}
		
// 		switch ($type) {
// 			case '1'://在售
// 				$req = new ItemsOnsaleGetRequest;
// 				break;
// 			case '2'://库存
// 				$req = new ItemsInventoryGetRequest;
// 				break;
// 			default://所有
// 				$req = new ItemsSearchRequest;
// 				$shop = Hlg::getShop();
// 				$shopNick = $shop['shop_nick'];
// 				$startPrice = $params['start_price'];
// 				$endPrice = $params['end_price'];
// 				if($startPrice) {
// 					$req->setStartPrice($startPrice);
// 				}
// 				if($endPrice) {
// 					$req->setEndPrice($endPrice);
// 				}
// 				$req->setNicks($shopNick);
// 				break;
// 		}
// 		$pageSize = $params['page_size'];
// 		$pageNo = $params['page_no'];
// 		$pageSize = $pageSize ? $pageSize :10;
// 		if(!$pageNo) $pageNo = 1;
// 		if($q) {
// 			$req->setQ($q);
// 		}
// 		if($hasDiscount){
// 			if($hasDiscount == 1){
// 				$req->setHasDiscount("true");
// 			}else{
// 				$req->setHasDiscount("false");
// 			}
// 		}
// 		if($hasShowcase){
// 			if($hasShowcase == 1){
// 				$req->setHasShowcase("true");
// 			}
// 		}
// 		if ($cid) {
// 			$cids = explode(',',$cid);
// 			if(count($cids)<32){
// 				if (in_array($type, array('1','2'))) {
// 					$req->setSellerCids($cid);
// 				}
// 			} else{
// 				$cid = '';
// 				for($i=0;$i<32;$i++){
// 					$cid .= $cids[$i].',';
// 				}
// 				$cid = substr($cid,0,-1);
// 				if (in_array($type, array('1','2'))) {
// 					$req->setSellerCids($cid);
// 				}
// 			}
// 		}
// 		if(!$orderBy) {
// 			$req->setOrderBy('list_time:asc');
// 		}
// 		if($orderBy == 'num'){
// 			$req->setOrderBy('num:desc');
// 		}
// 		$req->setPageSize($pageSize);
// 		$req->setPageNo($pageNo);
// 		$req->setFields('num_iid,title,price,pic_url,outer_id,is_virtual,list_time,delist_time,num,seller_cids,has_discount');
// 		try {
// 			$tbSession = Hlg::getShopModel()->getTbSession();
// 			$collection = $taobao->execute($req, $tbSession);
// 			$this->_totalRecords = $collection->total_results;
// 			if (isset($collection->item_search)) {
// 				$tbData = $collection->item_search->items->item;
// 			} else if(isset($collection->items)){
// 				$tbData = $collection->items->item;
// 			} else {
// 				return false;
// 			}
// 			foreach($tbData as $tbItem){
// 				$item = array();
// 				$item['num_iid'] = $tbItem->num_iid;
// 				$item['outer_id'] = $tbItem->outer_id;
// 				$item['pic_url'] = $tbItem->pic_url;
// 				$item['title'] = $tbItem->title;
// 				$item['price'] = $tbItem->price;
// 				$items[] = $item;
// 				$this->_tbData = $items;
// 			}
// 		} catch (Top_Exception $e) {//exit(var_dump($e));
// 			Hlg::throwException($e);
// 		} catch (Exception $e){
// 			Hlg::throwException($e);
// 		}
		
	}
	
}
