<?php

abstract class AbstractModel {
	protected $_config;
	protected $_db;
	protected $_name;
	protected $_isHoriz;//是否水平分表
	protected $_isSharding;//是否分库

	public function __construct() 
	{
		$this->_config = Common::getConfig('config');
	}

	public function db($uid = 0) {
		if ($this->_isSharding == true) {
			if (empty($uid)) {
				throw new Exception(__CLASS__.'::'.__METHOD__.": uid param not exist");
			}
			$this->_db = Common::getDb(Common::shardId($uid));
		} else {
			$this->_db = $this->getDb();
		}
		return $this->_db;
	}

	public function getDb($shardId = 0) {
		$this->_db = Common::getDb($shardId);
		return $this->_db;
	}

	public function getTableName($uid=0)
	{
		if ($this->_isHoriz == true) {
			if (empty($uid)) {
				throw new Exception(__CLASS__.'::'.__METHOD__.": uid param not exist");
			}
			$this->_name = $this->_name . Common::computeTableId($uid);
		}
		return $this->_name;
	}
	
	/**
	 * 简易获取单条记录
	 * @param array or string $value
	 * @param array or string $field
	 * @param int $uid 用于取到正确的分库分表
	 * @return mixed 
	 */
	public function load($value,$field=null,$uid=0){
		if (is_null($field)) {
			$field = 'uid';
		}
		
		if (!empty($value)) {
			if (!is_array($field)){
				$where = " {$field} = '{$value}' ";
			} else {
				$num = count($field) ;
				$strArr = array();
				for ($i = 0; $i < $num; $i++) {
					array_push($strArr, $field[$i].'='.$value[$i]);
				}
				$where = join(' and ', $strArr);
				unset($strArr);
			}
		}
		$tableName = $this->getTableName($uid);
		$sql = "select * from {$tableName} where ".$where;
		$item = $this->db($uid)->fetchRow($sql);
		return $item;
	}

	//取单条记录
	public function getRow($where,$field,$uid=0)
	{
		$tableName = $this->getTableName($uid);
		if (!empty($field)) {
			if(is_array($field)){
				$fieldStr = implode(',',$field);
			}else{
				$fieldStr = $field;
			}
		} else {
			throw new Exception(__CLASS__.'::'.__METHOD__.": field param not exist");
		}

		$sql = "select * from {$tableName} where ". $where ." limit 1";
		$row = $this->db($uid)->fetchRow($sql);
    	
		return $row;
	}

	//取多条记录
	public function getRows($where,$field,$uid=0)
	{
		$tableName = $this->getTableName($uid);
		if (!empty($field)) {
			if(is_array($field)){
				$fieldStr = implode($field,',');
			}else{
				$fieldStr = $field;
			}
		} else {
			throw new Exception("field param not exist");
		}

		$sql = "select * from {$tableName} where ". $where ." limit 1";
    	$rows = $this->db($uid)->fetchArray($sql);
		return $rows;
	}

	//增加一条记录
	public function addRow($data,$uid=0)
	{
		$tableName = $this->getTableName($uid);
		$data['created'] = isset($data['created']) ? $data['created']: time();
		$lastInsertId = $this->db($uid)->insert($tableName,$data);
		return $lastInsertId;
	}

	//更新记录
	public function setRow($data,$where,$uid=0)
	{
		$tableName = $this->getTableName($uid);
		$data['updated'] = isset($data['updated']) ? $data['updated']: time();
		$res = $this->db($uid)->update($tableName,$data,$where);
		return $res ? true : false;
	}
	
	//取分页记录
	public function getList($where,$field,$start,$limit,$uid=0)
	{
		$where .= " limit {$start},{$limit} ";
		$rows = $this->getRows($where,$field,$uid);
		return $rows;
	}
}
