<?php

class Admin_TopIsvTrace extends AbstractModel
{
	protected $_name = 'top_ivs_trace';
	
    /**
     * 是否开启缓存
     * @var boolean
     */
	private $_cachable = false;
	

	public function getTopIsvTrace($nick){
		$sql = "select * from {$this->_name} where nick = '".$nick."' limit 1";
    	return $this->db()->fetchRow($sql);
	}
	
	public function loadByWhere($where){
		$sql = "select * from {$this->_name} where {$where} limit 1";
    	return $this->db()->fetchRow($sql);
	}

	public function setTopIsvTrace($data,$where){
//		$data['updated'] =  date('Y-m-d H:i:s',time());
		$ret = $this->db()->update($this->_name, $data, $where);
		return $ret ? true : false;
	}

	public function addTopIsvTrace($data){
//		$data['created'] =  date('Y-m-d H:i:s',time());
//		$data['updated'] =  date('Y-m-d H:i:s',time());
		$ret = $this->db()->insert($this->_name, $data);
		return $ret ? true : false;
	}

}
