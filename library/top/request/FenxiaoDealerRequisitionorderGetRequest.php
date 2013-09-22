<?php
/**
 * TOP API: taobao.fenxiao.dealer.requisitionorder.get request
 * 
 * @author auto create
 * @since 1.0, 2013-06-12 16:48:02
 */
class FenxiaoDealerRequisitionorderGetRequest
{
	/** 
	 * 采购申请单最迟修改时间。与start_date字段的最大时间间隔不能超过30天
	 **/
	private $endDate;
	
	/** 
	 * 多个字段用","分隔。 fields 如果为空：返回所有采购申请单对象(dealer_orders)字段。 如果不为空：返回指定采购单对象(dealer_orders)字段。 例1： dealer_order_details.product_id 表示只返回product_id 例2： dealer_order_details 表示只返回明细列表
	 **/
	private $fields;
	
	/** 
	 * 查询者自己在所要查询的采购申请单中的身份。
1：供货方。
2：采购方。
注：填写其他值当做错误处理。
	 **/
	private $identity;
	
	/** 
	 * 采购申请单状态。
0：全部状态。
1：采购方刚申请，待供货方审核。
2：供货方驳回，待采购方审核。
3：供货方修改后，待采购方审核。
4：采购方驳回修改，待供货方再审核。
5：双方审核通过，待采购方付款。
6：采购方已付款，待供货方确认。
7：付款成功，待供货方出库。
8：供货方出库，待采购方入库。
9：采购方入库，交易成功。
10：采购申请单关闭。

注：无值按默认值0计，超出状态范围返回错误信息。
	 **/
	private $orderStatus;
	
	/** 
	 * 页码（大于0的整数。无值或小于1的值按默认值1计）
	 **/
	private $pageNo;
	
	/** 
	 * 每页条数（大于0但小于等于50的整数。无值或大于50或小于1的值按默认值50计）
	 **/
	private $pageSize;
	
	/** 
	 * 采购申请单最早修改时间
	 **/
	private $startDate;
	
	private $apiParas = array();
	
	public function setEndDate($endDate)
	{
		$this->endDate = $endDate;
		$this->apiParas["end_date"] = $endDate;
	}

	public function getEndDate()
	{
		return $this->endDate;
	}

	public function setFields($fields)
	{
		$this->fields = $fields;
		$this->apiParas["fields"] = $fields;
	}

	public function getFields()
	{
		return $this->fields;
	}

	public function setIdentity($identity)
	{
		$this->identity = $identity;
		$this->apiParas["identity"] = $identity;
	}

	public function getIdentity()
	{
		return $this->identity;
	}

	public function setOrderStatus($orderStatus)
	{
		$this->orderStatus = $orderStatus;
		$this->apiParas["order_status"] = $orderStatus;
	}

	public function getOrderStatus()
	{
		return $this->orderStatus;
	}

	public function setPageNo($pageNo)
	{
		$this->pageNo = $pageNo;
		$this->apiParas["page_no"] = $pageNo;
	}

	public function getPageNo()
	{
		return $this->pageNo;
	}

	public function setPageSize($pageSize)
	{
		$this->pageSize = $pageSize;
		$this->apiParas["page_size"] = $pageSize;
	}

	public function getPageSize()
	{
		return $this->pageSize;
	}

	public function setStartDate($startDate)
	{
		$this->startDate = $startDate;
		$this->apiParas["start_date"] = $startDate;
	}

	public function getStartDate()
	{
		return $this->startDate;
	}

	public function getApiMethodName()
	{
		return "taobao.fenxiao.dealer.requisitionorder.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->endDate,"endDate");
		RequestCheckUtil::checkNotNull($this->identity,"identity");
		RequestCheckUtil::checkNotNull($this->startDate,"startDate");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
