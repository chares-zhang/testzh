<?php

class Admin_TopUserApp extends AbstractModel
{
	protected $_name = 'top_user_app';
	
    /**
     * 是否开启缓存
     * @var boolean
     */
	private $_cachable = false;

	public function getTopUserAppRows(){
		$sql = "select * from {$this->_name}";
    	$rows = $this->db()->fetchArray($sql);
		return $rows;
	}
	
	public function getRowByServiceCode($serviceCode){
		$sql = "select * from {$this->_name} where service_code = '".$serviceCode."' limit 1";
    	return $this->db()->fetchRow($sql);
	}

	public function addTopUserApp($data){
//		$data['created'] = time();
//		$data['updated'] = time();
		$ret = $this->db()->insert($this->_name, $data);
		return $ret ? true : false;
	}

	public function setTopUserApp($data,$where){
//		$data['updated'] = time();
		$ret = $this->db()->update($this->_name, $data, $where);
		return $ret ? true : false;
	}

}
