<?php
/**
 * api错误日志表基本操作
 * 是否分表：N
 * @TODO 要改成按周分表
 * 关系类型：1-N
 * @author chares
 *
 */
class Core_Model_Base_Error_Apilog extends AbstractModel
{
	/**
	 * 表名
	 */
	protected $_name = 'core_error_apilog';
	
	/**
	 * 模块名
	 */
	protected $_module = 'core';
	
	/**
	 * 是否为公共库,默认false.
	 */
	protected $_isCommon = false;
	
	/**
	 * 分表数量,若数量大于1,则表示需要分表,$_tableDivType='UID'时有用.
	 */
	protected $_tableDiv;
	
	/**
	 * 分表依据,只支持按 UID,MONTH,WEEK 分表,默认UID
	 */
	protected $_tableDivType = 'UID';
	
	/**
	 * 主键id
	 */
	protected $_primaryKey = 'log_id';
	
	/**
	 * 是否使用缓存
	 */
	protected $_cachable = false;
	
	/**
	 * 通过主键获取单条记录
	 */
	public function getErrorApiLogRow($logId)
	{
		return $this->load($logId);
	}
	
	/**
	 * 通过where条件,获取多条记录
	 */
	public function getErrorApiLogRows($where,$field=null)
	{
		$params = array();
		if (!empty($field)) {
			$params['field'] = $field;
		}
		$params['where'] = $where;
		$rows = $this->fetchItems($params);
		return $rows;
	}
	
	/**
	 * 新增单条
	 * @param array $data
	 * @return boolean
	 */
	public function addErrorApiLogRow($data)
	{
		$data['my_created'] = date('Y-m-d H:i:s');
		$data['my_updated'] = date('Y-m-d H:i:s');
		$res = $this->insertItem($data);
		return $res ? true : false;
	}
	
}
