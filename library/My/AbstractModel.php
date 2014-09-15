<?php

abstract class AbstractModel {
	/**
	 * 数据库连接
	 */
	protected $_db;
	
	/**
	 * 表名
	 */
	protected $_name;
	
	/**
	 * 模块名
	 */
	protected $_module;
	
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
	
	public function __construct() 
	{
	}

	/*
	 * 根据$uid找到对应的集群
	* @param $uid
	*/
	public function db($uid){
		if($this->_isCommon){//是否公共集群,true则是
			$this->_db = Common::getDb($this->_module,$uid,true);
		}else{
			$this->_db = Common::getDb($this->_module,$uid);
		}
		return $this->_db;
	}
	
	/**
	 * 获取表名
	 * 若需要分表,则在model中设置$_tableDiv分表数量
	 * 分表算法：取余并补齐4位数.
	 */
	public function getMainTable($divKey=null){
		$divKey = $this->getDivKey($divKey);
		switch($this->_tableDivType){
		    case 'UID':
		    	if(isset($this->_tableDiv) && $this->_tableDiv > 1){
		    		if($divKey !== null){
		    			return $this->_name . '_' . str_pad($divKey % $this->_tableDiv, 4, '0', STR_PAD_LEFT);
		    		}
		    	}
		    	break;
		    case 'MONTH':
		    	//按月份分表
		    	if(!$divKey){
		    		return $this->_name.'_'.date("Ym");
		    	}else{
		    		$divTime = strtotime($divKey);
		    		return $this->_name . '_' . date("Ym",$divTime);
		    	}
		    	break;
		    case 'WEEK':
		    	//按自然周分表
		    	if(!$divKey){
		    		return $this->_name . '_' . date("YW");
		    	}else{
		    		$divTime = strtotime($divKey);
		    		return $this->_name . '_' . date("YW",$divTime);
		    	}
		    	break;
		}
		return '`'.$this->_name.'`';
	}
	
	/**
	 * 获取分表依据的原数据,shopId 或者 当前日期
	 * @param unknown $divKey
	 * @return unknown|Ambigous <unknown, string>
	 */
	public function getDivKey($divKey){
		if($this->_tableDivType == "UID"){
			return ($divKey !== null) ? $divKey : My::getUid();
		}else{
			return ($divKey !== null) ? $divKey : date("Ymd");
		}
	}
	
	/**
	 * 获取多条记录
	 * @param array $params
	 * @param int $uid
	 * @return multitype:
	 */
	public function fetchItems($params,$divKey=null){
		$divKey = $this->getDivKey($divKey);
		// 		$where = isset($params['where']) ? $params['where'] : '1=1';
		$whereArr = isset($params['where']) ? $params['where'] : array();
		if(!empty($whereArr) && is_string($whereArr)) {
			$whereArr = self::createWhere($whereArr);
		}
		$where = isset($whereArr['where_str']) ? $whereArr['where_str'] : '1=1';
		$bind = isset($whereArr['bind']) ? $whereArr['bind'] : array();
		$field = isset($params['field']) ? $params['field'] : '*';
	
		$mainTable = $this->getMainTable($divKey);
		if(!empty($field)){
			if(is_array($field)){
				$fieldStr = implode(',',$field);
			}else{
				$fieldStr = $field;
			}
		}else{
			$fieldStr = '*';
		}
	
		//若没limit，限制一次取10000条！
		if (stripos($where,"limit") === false) {
			$where .= " limit 10000";
		}
	
		$sql = "select {$fieldStr} from {$mainTable} where {$where} ";
	
		try{
			$items = $this->db($divKey)->fetchAll($sql,$bind);
		}catch(Exception $e){
			$error = $this->db(0)->getError();
			if($this->checkTableNotExists($error,$divKey)){
				return array();
			}else{
				throw $e;
			}
		}
	
		if(empty($items)){
			$items = array();
		}
		return $items;
	}
	
	/**
	 * 获取分页多条记录
	 * @param array $params
	 * @param int $pageSize  每页几条数据
	 * @param int $pageId	页数,从第0页开始.
	 * @param int $uid
	 * @return array $result = array('total_result'=>$totalResult,'data'=>$items);
	 */
	public function fetchPageItems($params,$pageSize,$pageId,$divKey=null)
	{
		// 		$where = isset($params['where']) ? $params['where'] : '1=1';
		$whereArr = isset($params['where']) ? $params['where'] : array();
		if(!empty($whereArr) && is_string($whereArr)) {
			$whereArr = self::createWhere($whereArr);
		}
		$where = isset($whereArr['where_str']) ? $whereArr['where_str'] : '1=1';
		$bind = isset($whereArr['bind']) ? $whereArr['bind'] : array();
		$field = isset($params['field']) ? $params['field'] : '*';
	
		$start = (int)(($pageId-1) * $pageSize);
		$limit = (int)$pageSize;
		$pos = stripos($where,' limit ');
		if($pos === false){
			// 			$where = $where . " limit $start,$limit";
			$where = $where . " limit ?,?";
			array_push($bind, $start, $limit);
		}else{
			// 			$where = substr($where,0,$pos) . " limit $start,$limit";
			$where = substr($where,0,$pos) . " limit ?,?";
			array_push($bind, $start, $limit);
		}
		if(!empty($field)){
			if(is_array($field)){
				$fieldStr = "SQL_CALC_FOUND_ROWS ".implode(',',$field);
			}else{
				$fieldStr = "SQL_CALC_FOUND_ROWS ".$field;
			}
		}else{
			$fieldStr = "SQL_CALC_FOUND_ROWS *";
		}
	
		$newparams = array();
		$newparams['where'] = self::createWhere($where,$bind);
		$newparams['field'] = $fieldStr;
	
		//取记录
		$items = $this->fetchItems($newparams,$divKey);
		//取总数
		$newSqlParams = array(
				'sql' => "SELECT FOUND_ROWS()"
		);
		$totalResult = $this->fetchOne($newSqlParams);
	
		//返回
		$result = array();
		$result['total_result'] = intval($totalResult);
		$result['data'] = $items;
		return $result;
	}
	
	/**
	 * 支持join的获取分页多条记录
	 * @param $sql
	 * @param int $pageSize  每页几条数据
	 * @param int $pageId	页数,从第0页开始.
	 * @param int $uid
	 * @return array $result = array('total_result'=>$totalResult,'data'=>$items);
	 */
	public function fetchJoinPageItems($sqlParams,$pageSize,$pageId,$divKey=null){
		if (!empty($sqlParams) && is_string($sqlParams)) {
			$sqlParams = self::createSql($sqlParams);
		}
		$sql = isset($sqlParams['sql']) ? $sqlParams['sql'] : '';
		$bind = isset($sqlParams['bind']) ? $sqlParams['bind'] : array();
	
		$sql = trim($sql);
		$sqlpos = strpos($sql,'SQL_CALC_FOUND_ROWS');
		if ($sqlpos === false) {
			$sql = 'select SQL_CALC_FOUND_ROWS' . substr($sql,6);
		}
	
		$start = (int)(($pageId-1) * $pageSize);
		$limit = (int)$pageSize;
		$pos = stripos($sql,' limit ');
		if($pos === false){
			// 			$sql = $sql . " limit $start,$limit";
			$sql = $sql . " limit ?,?";
			array_push($bind, $start, $limit);
		}else{
			// 			$sql = substr($sql,0,$pos) . " limit $start,$limit";
			$sql = substr($sql,0,$pos) . " limit ?,?";
			array_push($bind, $start, $limit);
		}
		//取记录
	
		$items = $this->db($divKey)->fetchAll($sql,$bind);
		//取总数
		$newSqlParams = array(
				'sql' => "SELECT FOUND_ROWS()"
		);
		$totalResult = $this->fetchOne($newSqlParams);
			
		//返回
		$result = array();
		$result['total_result'] = intval($totalResult);
		$result['data'] = $items;
		return $result;
	}
	
	/**
	 * 获取单条记录
	 * @param array $params
	 * @param int $uid
	 * @return mixed
	 */
	public function fetchItem($params,$divKey=null){
		$divKey = $this->getDivKey($divKey);
		$whereArr = isset($params['where']) ? $params['where'] : array();
		if(!empty($whereArr) && is_string($whereArr)) {
			$whereArr = self::createWhere($whereArr);
		}
		$where = isset($whereArr['where_str']) ? $whereArr['where_str'] : '1=1';
		$bind = isset($whereArr['bind']) ? $whereArr['bind'] : array();
		$field = isset($params['field']) ? $params['field'] : '*';
	
		$mainTable = $this->getMainTable($divKey);
		if(!empty($field)){
			if(is_array($field)){
				$fieldStr = implode(',',$field);
			}else{
				$fieldStr = $field;
			}
		}else{
			$fieldStr = '*';
		}
		$sql = "select {$fieldStr} from {$mainTable} where {$where}";
	
		try{
			$item = $this->db($divKey)->fetchRow($sql,$bind);
		}catch(Exception $e){
			$error = $this->db(0)->getError();
			if($this->checkTableNotExists($error,$divKey)){
				return array();
			}else{
				throw $e;
			}
		}
	
		return $item;
	}
	
	/**
	 * 简易获取单条记录
	 * @param array or string $value
	 * @param array or string $field
	 * @param int $uid 用于取到正确的分库分表
	 * @return mixed
	 */
	public function load($value,$field=null,$divKey=null)
	{
		$divKey = $this->getDivKey($divKey);
		if (is_null($field)) {
			$field = $this->_primaryKey;
		}
	
		$bind = array();
		if (!is_null($value)) {
			if (!is_array($field)){
				// 				$where = " {$field} = '{$value}' ";
				$where = " {$field} = ? ";
				$bind[] = $value;
			} else {
				$num = count($field) ;
				$strArr = array();
				for ($i = 0; $i < $num; $i++) {
					array_push($strArr, "{$field[$i]} = ?");
					$bind[] = $value[$i];
					// 					array_push($strArr, $field[$i].'='."'".$value[$i]."'");
				}
				$where = join(' and ', $strArr);
				unset($strArr);
			}
		} else {
			$where = '1';
		}
	
		$where .= " limit 1";
		$mainTable = $this->getMainTable($divKey);
		$sql = "select * from {$mainTable} where {$where}";
		var_dump($bind);
		echo $sql;exit;
		$sqlParams = self::createSql($sql,$bind);
	
		try{
			$item = $this->db($divKey)->fetchRow($sql,$bind);
		}catch(Exception $e){
			$error = $this->db(0)->getError();
			if($this->checkTableNotExists($error,$divKey)){
				return array();
			}else{
				throw $e;
			}
		}
	
		return $item;
	}
	
	/**
	 * 获取第一行的第一列数据
	 * @param string $sql
	 * @param int $uid
	 * @return unknown
	 */
	public function fetchOne($sqlParams,$divKey=null)
	{
		if (!empty($sqlParams) && is_string($sqlParams)) {
			$sqlParams = self::createSql($sqlParams);
		}
		$divKey = $this->getDivKey($divKey);
		$sql = isset($sqlParams['sql']) ? $sqlParams['sql'] : '';
		$bind = isset($sqlParams['bind']) ? $sqlParams['bind'] : array();
	
		try{
			$item = $this->db($divKey)->fetchRow($sql,$bind);
		}catch(Exception $e){
			$error = $this->db(0)->getError();
			if($this->checkTableNotExists($error,$divKey)){
				return array();
			}else{
				throw $e;
			}
		}
	
		if(empty($item)){
			return 0;
		}
		$item = array_values($item);
		return $item[0];
	}
	
	/**
	 * 获取第一列数据
	 * @param string $sql
	 * @param int $uid
	 * @return unknown
	 */
	public function fetchCol($sqlParams,$divKey=null)
	{
		if (!empty($sqlParams) && is_string($sqlParams)) {
			$sqlParams = self::createSql($sqlParams);
		}
		$divKey = $this->getDivKey($divKey);
		$sql = isset($sqlParams['sql']) ? $sqlParams['sql'] : '';
		$bind = isset($sqlParams['bind']) ? $sqlParams['bind'] : array();
	
		try{
			$items = $this->db($divKey)->fetchAll($sql,$bind);
		}catch(Exception $e){
			$error = $this->db(0)->getError();
			if($this->checkTableNotExists($error,$divKey)){
				return array();
			}else{
				throw $e;
			}
		}
	
		$result = array();
		if (!empty($items)) {
			foreach($items as $item){
				$item = array_values($item);
				$result[] = $item[0];
			}
		}
		return $result;
	}
	
	/**
	 * 增加记录
	 * @param array $data
	 * @return string 返回最后插入的主键id
	 */
	public function insertItem($data){
		//按UID分表的时候
		if($this->_tableDivType == 'UID'){
			if(isset($data['shop_id'])){
				$divKey = $data['shop_id'];
			}
		}
		if(!isset($divKey)){
			$divKey = null;
		}
		$divKey = $this->getDivKey($divKey);
		$mainTable = $this->getMainTable($divKey);
	
		try{
			$lastInsertId = $this->db($divKey)->insert($mainTable,$data);
		}catch(Exception $e){
			$error = $this->db(0)->getError();
			if($this->checkTableNotExists($error,$divKey)){
				$lastInsertId = $this->db($divKey)->insert($mainTable,$data);
			}else{
				throw $e;
			}
		}
	
		return $lastInsertId;
	}
	
	/**
	 * 删除记录
	 * @param array $data
	 * @return string
	 */
	public function deleteItem($where, $data=array()){
		//按UID分表的时候
		if($this->_tableDivType == 'UID'){
			if(isset($data['shop_id'])){
				$divKey = $data['shop_id'];
			}
		}
		if(!isset($divKey)){
			$divKey = null;
		}
		$divKey = $this->getDivKey($divKey);
	
		if(is_string($where)){
			$whereArr = self::createWhere($where);
		}else{
			$whereArr = $where;
		}
		$where = isset($whereArr['where_str']) ? $whereArr['where_str'] : '';
		if (empty($where)) {
			throw new Exception("DELETE_WHERE_IS_EMPTY");
			return;
		}
		$bind = isset($whereArr['bind']) ? $whereArr['bind'] : array();
	
		$mainTable = $this->getMainTable($divKey);
	
		try{
			$lastInsertId = $this->db($divKey)->delete($mainTable,$where,$bind);
		}catch(Exception $e){
			$error = $this->db(0)->getError();
			if($this->checkTableNotExists($error,$divKey)){
				return array();
			}else{
				throw $e;
			}
		}
	
		return $lastInsertId;
	}
	
	/**
	 * 更新记录
	 * @param array $data
	 * @param string $where
	 * @return number 返回更新影响的记录数
	 */
	public function replaceItem($data){
		//按UID分表的时候
		if($this->_tableDivType == 'UID'){
			if(isset($data['shop_id'])){
				$divKey = $data['shop_id'];
			}
		}
		if(!isset($divKey)){
			$divKey = null;
		}
		$divKey = $this->getDivKey($divKey);
	
		$mainTable = $this->getMainTable($divKey);
	
		try{
			$effectRow = $this->db($divKey)->replace($mainTable,$data);
		}catch(Exception $e){
			$error = $this->db(0)->getError();
			if($this->checkTableNotExists($error,$divKey)){
				$effectRow = $this->db($divKey)->replace($mainTable,$data);
			}else{
				throw $e;
			}
		}
	
		return $effectRow;
	}
	
	/**
	 * 更新记录
	 * @param array $data
	 * @param string $where
	 * @return number 返回更新影响的记录数
	 */
	public function updateItem($data, $where){
		//按UID分表的时候
		if($this->_tableDivType == 'UID'){
			if(isset($data['shop_id'])){
				$divKey = $data['shop_id'];
			}
		}
		if(!isset($divKey)){
			$divKey = null;
		}
		$divKey = $this->getDivKey($divKey);
	
		if(is_string($where)){
			$whereArr = self::createWhere($where);
		}else{
			$whereArr = $where;
		}
		$where = isset($whereArr['where_str']) ? $whereArr['where_str'] : '';
		if (empty($where)) {
			throw new Exception("UPDATE_WHERE_IS_EMPTY");
			return;
		}
		$bind = isset($whereArr['bind']) ? $whereArr['bind'] : array();
		$mainTable = $this->getMainTable($divKey);
	
		try{
			$effectRow = $this->db($divKey)->update($mainTable,$data,$where,$bind);
		}catch(Exception $e){
			$error = $this->db(0)->getError();
			if($this->checkTableNotExists($error,$divKey)){
				return array();
			}else{
				throw $e;
			}
		}
	
		return $effectRow;
	}
	
	/**
	 * 直接运行sql
	 * @param string $sql
	 * @param int $uid
	 */
	public function query($sqlParams,$divKey=null){
		$divKey = $this->getDivKey($divKey);
		 
		if (!empty($sqlParams) && is_string($sqlParams)) {
			$sqlParams = self::createSql($sqlParams);
		}
		$sql = isset($sqlParams['sql']) ? $sqlParams['sql'] : '';
		$bind = isset($sqlParams['bind']) ? $sqlParams['bind'] : array();
		 
		try{
			$stmt = $this->db($divKey)->query($sql,$bind);
		}catch(Exception $e){
			$error = $this->db(0)->getError();
			if($this->checkTableNotExists($error,$divKey)){
				return array();
			}else{
				throw $e;
			}
		}
		 
		$result = $stmt->rowCount();
		return $result;
	}
	
	/**
	 * 获取多条数据
	 */
	public function fetchAll($sqlParams,$divKey=null)
	{
		$divKey = $this->getDivKey($divKey);
		if (!empty($sqlParams) && is_string($sqlParams)) {
			$sqlParams = self::createSql($sqlParams);
		}
		$sql = isset($sqlParams['sql']) ? $sqlParams['sql'] : '';
		$bind = isset($sqlParams['bind']) ? $sqlParams['bind'] : array();
		 
		try{
			$res = $this->db($divKey)->fetchAll($sql,$bind);
		}catch(Exception $e){
			$error = $this->db(0)->getError();
			if($this->checkTableNotExists($error,$divKey)){
				return array();
			}else{
				throw $e;
			}
		}
		 
		return $res;
	}
	
	/**
	 * 获取一条数据
	 */
	public function fetchRow($sqlParams,$divKey=null)
	{
		$divKey = $this->getDivKey($divKey);
		if (!empty($sqlParams) && is_string($sqlParams)) {
			$sqlParams = self::createSql($sqlParams);
		}
		$sql = isset($sqlParams['sql']) ? $sqlParams['sql'] : '';
		$bind = isset($sqlParams['bind']) ? $sqlParams['bind'] : array();
		 
		try{
			$res = $this->db($divKey)->fetchRow($sql,$bind);
		}catch(Exception $e){
			$error = $this->db(0)->getError();
			if($this->checkTableNotExists($error,$divKey)){
				return array();
			}else{
				throw $e;
			}
		}
		 
		return $res;
	}
	
	/**
	 * 检查是否是因为表不存在而失败，是即创建新表
	 * @param array $error
	 * @param string $divKey
	 * @return PDOStatement|boolean
	 */
	public function checkTableNotExists($error,$divKey = null)
	{
		if($error[0] == "42S02" && $this->_tableDivType != 'UID'){
			if(!is_numeric($this->_tableDiv)){
				return $this->db($divKey)->query("CREATE TABLE IF NOT EXISTS ".$this->getMainTable($divKey)." LIKE ".$this->_name);
			}
		}
		return false;
	}
	
	/**
	 * 构造where数组，返回的键where_str为where模板，bind为where绑定参数
	 *
	 * @param unknown $whereStr
	 * @param unknown $bind
	 * @return multitype:unknown
	 */
	public static function createWhere($whereStr, $bind=array())
	{
		return array(
			'where_str' => $whereStr,
			'bind' => $bind
		);
	}
	
	/**
	 * 构造sql数组，返回的键sql为where模板，bind为where绑定参数
	 * 
	 */
	public static function createSql($sql, $bind=array())
	{
		return array(
			'sql_str' => $sql,
			'bind' => $bind
		);
	}
	
	
}
