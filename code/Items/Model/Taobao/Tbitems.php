<?php
/**
 * 淘宝商品类
 * @author chares
 *
 */
class Items_Model_Taobao_Tbitems
{
	/**
	 * 宝贝列表数据
	 *
	 * @var unknown
	 */
	protected $_tbData = array();
	
	/**
	 * 宝贝列表总数
	 *
	 * @var unknown
	*/
	protected $_totalRecords = 0;
	
// 	protected $_startPrice;
	
// 	protected $_endPrice;
	
	protected $_type;
	
	protected $_cid;
	
	protected $_order = "num_iid:desc";
	
	protected $_q;
	
	/**
	 * 过滤的num_iid
	 *
	 * @var unknown
	 */
	protected $_filterNumIids;
	
	/**
	 * 设置搜索起始价格
	 *
	 * @param unknown $startPrice
	 */
	public function setStartPrice($startPrice)
	{
		$this->_startPrice = $startPrice;
		return $this;
	}
	
	/**
	 * 设置搜索结束价格
	 *
	 * @param unknown $endPrice
	 */
	public function setEndPrice($endPrice)
	{
		$this->_endPrice = $endPrice;
		return $this;
	}
	
	/**
	 * 设置排序条件
	 *
	 * @param unknown $field
	 * @param string $order
	 */
	public function setOrderBy($field, $order = "desc")
	{
		$this->_order = $field . ":" . $order;
		return $this;
	}
	
	/**
	 * 设置商品销售类型
	 *
	 * @param unknown $type
	 */
	public function setApproveStatus($type)
	{
		$this->_type = strtolower($type);
		return $this;
	}
	
	/**
	 * 设置搜索条件
	 *
	 * @param unknown $q
	 */
	public function setQ($q)
	{
		$this->_q = $q;
		return $this;
	}
	
	/**
	 * 设置类目id
	 *
	 * @param unknown $cid
	 */
	public function setCid($cid)
	{
		$this->_cid = $cid;
		return $this;
	}
	
	public function get()
	{
		return $this->_tbData;
	}
	
	public function count()
	{
		return $this->_totalRecords;
	}
	
	/**
	 * 将用户输入的宝贝详细地址转化为宝贝id
	 *
	 * @param string $q
	 * @return string
	 */
	public function filterNumiid($q)
	{
		$q = htmlspecialchars_decode($q);
		$pattern = '/((item\.taobao.com\/item\.htm)|(detail\.tmall\.com\/item\.htm)).*[\?\&]id=(?P<id>\d+)/';
		if (preg_match($pattern, $q, $match)) {
			if (isset($match['id'])) {
				$q = $match['id'];
			}
		}
		return $q;
	}
	
	/**
	 * 获取宝贝库列表
	 *
	 * @param unknown $pageNum
	 * @param unknown $pageSize
	 * @param string $isLocal
	 */
	public function loadList($pageNum, $pageSize)
	{
		$this->loadFromTaobao($pageNum, $pageSize);
		return $this;
	}
	
	/**
	 * 从淘宝接口获取宝贝列表数据
	 *
	 * @param unknown $pageNum
	 * @param unknown $pageSize
	 */
	public function loadFromTaobao($pageNum, $pageSize)
	{
		$isStop = false;
		if ($this->_q) {
			$this->_q = $this->filterNumiid($this->_q);
			if (stripos($this->_q, 'e') === false && is_numeric($this->_q) && (strlen($this->_q) >= 7)) {
				$isStop = $this->loadItemByNumIid($this->_q);
			}
			if (! $isStop) {
				$isStop = $this->loadItemByOuterId($this->_q);
			}
		}
		if (! $isStop) {
			$this->loadBySearch($pageNum, $pageSize);
		}
	}
	
	/**
	 * 根据numIid获取宝贝
	 *
	 * @param unknown $numIid
	 * @return void boolean
	 */
	public function loadItemByNumIid($numIid)
	{
		$taobao = Common::getModel('core/taobao');
		$req = new ItemGetRequest();
		$req->setNumIid($numIid);
		$fields = 'num_iid,nick,title,price,pic_url,outer_id,is_virtual,list_time,delist_time,num,seller_cids,has_discount,has_showcase,sell_point,approve_status';
		$req->setFields($fields);
		try {
			$userRow = Common::getUserInfo();
			$tbSession = $userRow['access_token'];
			$collection = $taobao->execute($req, $tbSession);
			$tbData = $collection->item;
			if ($this->_type && isset($tbData->approve_status) && $tbData->approve_status != $this->_type) {
				$this->_tbData = array();
				$this->_totalRecords = 0;
				return false;
			}
			$item = array();
			$item['num_iid'] = $tbData->num_iid;
			$item['title'] = isset($tbData->title) ? $tbData->title : "";
			$item['nick'] = isset($tbData->nick) ? $tbData->nick : "";
			$item['price'] = (float) isset($tbData->price) ? (float) $tbData->price : 0;
			$item['pic_url'] = isset($tbData->pic_url) ? $tbData->pic_url : "";
			$item['outer_id'] = isset($tbData->outer_id) ? $tbData->outer_id : "";
			$item['is_virtual'] = $tbData->is_virtual;
			$item['list_time'] = isset($tbData->list_time) ? $tbData->list_time : "";
			$item['delist_time'] = isset($tbData->delist_time) ? $tbData->delist_time : "";
			$item['seller_cids'] = isset($tbData->seller_cids) ? $tbData->seller_cids : "";
			$item['has_discount'] = $tbData->has_discount;
			$item['sell_point'] = isset($tbData->sell_point) ? htmlspecialchars_decode($tbData->sell_point) : '';
			$item['num'] = $tbData->num;
			$item['approve_status'] = isset($tbData->approve_status) ? $tbData->approve_status : '';
			$this->_tbData = array(
				$item
			);
			$this->_totalRecords = 1;
			return true;
		} catch (Top_Exception $e) {
			$userRow = Common::getUserInfo();
			$uid = $userRow['uid'];
			$tbResp = $e->getTbResp();
			$apiMethod = $e->getApiMethod();
			$requestUrl = $e->getRequestUrl();
			$apiParams = $e->getApiParams();
			$params = array(
					'uid' => $uid,
					'keyword' => $apiMethod
			);
			Common::logApiError($tbResp,$apiMethod,$requestUrl,$apiParams,$params);
			
			return false;
// 			$msg = $e->getTbMsg();
// 			Common::responseError($msg);
		} catch (Exception $e){
			return false;
		}
	}
	
	/**
	 * 根据outer_id获取宝贝
	 *
	 * @param unknown $outId
	 * @return boolean
	 */
	public function loadItemByOuterId($outId)
	{
		$taobao = Common::getModel('core/taobao');
		$req = new ItemsCustomGetRequest();
		$req->setOuterId($outId);
		$req->setFields('num_iid,title,price,pic_url,outer_id,is_virtual,list_time,delist_time,num,seller_cids,has_discount,has_showcase,approve_status');
		try {
			$userRow = Common::getUserInfo();
			$tbSession = $userRow['access_token'];
			$collection = $taobao->execute($req, $tbSession);
			if (! isset($collection->items)) {
				return false;
			}
			$tbData = $collection->items->item;
			$items = array();
			foreach ($tbData as $tbItem) {
				if ($this->_type && isset($tbItem->approve_status) && $tbItem->approve_status != $this->_type) {
					continue;
				}
				$item['num_iid'] = $tbItem->num_iid;
				$item['title'] = isset($tbItem->title) ? $tbItem->title : "";
				$item['nick'] = isset($tbItem->nick) ? $tbItem->nick : "";
				$item['price'] = (float) isset($tbItem->price) ? (float) $tbItem->price : 0;
				$item['pic_url'] = isset($tbItem->pic_url) ? $tbItem->pic_url : "";
				$item['outer_id'] = isset($tbItem->outer_id) ? $tbItem->outer_id : "";
				$item['is_virtual'] = $tbItem->is_virtual;
				$item['list_time'] = isset($tbItem->list_time) ? $tbItem->list_time : "";
				$item['delist_time'] = isset($tbItem->delist_time) ? $tbItem->delist_time : "";
				$item['seller_cids'] = isset($tbItem->seller_cids) ? $tbItem->seller_cids : "";
				$item['has_discount'] = $tbItem->has_discount;
				$item['num'] = $tbItem->num;
				$items[] = $item;
			}
			$this->_tbData = $items;
			$this->_totalRecords = count($items);
			return true;
		} catch (Top_Exception $e) {
			
			$userRow = Common::getUserInfo();
			$uid = $userRow['uid'];
			$tbResp = $e->getTbResp();
			$apiMethod = $e->getApiMethod();
			$requestUrl = $e->getRequestUrl();
			$apiParams = $e->getApiParams();
			$params = array(
					'uid' => $uid,
					'keyword' => $apiMethod
			);
			Common::logApiError($tbResp,$apiMethod,$requestUrl,$apiParams,$params);
			
			return false;
// 			$msg = $e->getTbMsg();
// 			Common::responseError($msg);
		} catch (Exception $e){
			return false;
		}
	}
	
	/**
	 * 通过onsale/Inventory接口获取宝贝列表
	 *
	 * @param unknown $pageNum
	 * @param unknown $pageSize
	 * @return boolean
	 */
	public function loadBySearch($pageNum, $pageSize)
	{
		$userRow = Common::getUserInfo();
		if (empty($userRow)) {
			Common::responseError('用户信息不存在，请重新登录');
		}
		switch ($this->_type) {
			case "onsale":
				$req = new ItemsOnsaleGetRequest();
				break;
			case "instock":
				$req = new ItemsInventoryGetRequest();
				break;
			default:
				$req = new ItemsSearchRequest();
				if ($this->_startPrice) {
					$req->setStartPrice($this->_startPrice);
				}
				if ($this->_endPrice) {
					$req->setEndPrice($this->_endPrice);
				}
				$username = $userRow['username'];
				$req->setNicks($username);
		}
		if ($this->_q) {
			$req->setQ($this->_q);
		}
		if ($this->_cid) {
			$cids = explode(',', $this->_cid);
			if (is_array($cids)) {
				$cidsArr = array_chunk($cids, 32); // 取前32个元素,@TODO拆分获取
				$cid = join(',', $cidsArr[0]);
			}
			$req->setSellerCids($cid);
		}
		if ($this->_order) {
			$req->setOrderBy($this->_order);
		}
		$req->setPageSize($pageSize);
		$req->setPageNo($pageNum);
		$req->setFields('num_iid,title,price,pic_url,outer_id,is_virtual,list_time,delist_time,num,seller_cids,has_discount');
		try {
			$taobao = Common::getModel('core/taobao');
			$tbSession = $userRow['access_token'];
			$collection = $taobao->execute($req, $tbSession);
			$this->_totalRecords = $collection->total_results;
			if (isset($collection->item_search)) {
				$tbData = $collection->item_search->items->item;
			} else {
				if (isset($collection->items)) {
					$tbData = $collection->items->item;
				} else {
					return false;
				}
			}
			$items = array();
			foreach ($tbData as $tbItem) {
				$item = array();
				$item['title'] = isset($tbItem->title) ? $tbItem->title : '';
				$item['num_iid'] = isset($tbItem->num_iid) ? $tbItem->num_iid : "";
				$item['outer_id'] = isset($tbItem->outer_id) ? $tbItem->outer_id : '';
				$item['pic_url'] = isset($tbItem->pic_url) ? $tbItem->pic_url : '';
				$item['price'] = (float) isset($tbItem->price) ? (float) $tbItem->price : 0;
				$item['num'] = isset($tbItem->num) ? intval($tbItem->num) : 0;
				$item['seller_cids'] = isset($tbItem->seller_cids) ? $tbItem->seller_cids : '';
				$item['delist_time'] = isset($tbItem->delist_time) ? $tbItem->delist_time : '';
				$item['list_time'] = isset($tbItem->list_time) ? $tbItem->list_time : '';
				$item['has_discount'] = $tbItem->has_discount;
				$items[] = $item;
			}
			$this->_tbData = $items;
		} catch (Top_Exception $e) {
// 			var_dump($e);exit;
			$userRow = Common::getUserInfo();
			$uid = $userRow['uid'];
			$tbResp = $e->getTbResp();
			$apiMethod = $e->getApiMethod();
			$requestUrl = $e->getRequestUrl();
			$apiParams = $e->getApiParams();
			$params = array(
					'uid' => $uid,
					'keyword' => $apiMethod
			);
			Common::logApiError($tbResp,$apiMethod,$requestUrl,$apiParams,$params);
			
			$msg = $e->getTbMsg();
			Common::responseError($msg);
		} catch (Exception $e){
			return false;
		}
	}
}
