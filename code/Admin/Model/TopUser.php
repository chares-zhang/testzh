<?php

class Admin_Model_TopUser extends AbstractModel
{
	protected $_name = 'top_user';
	
    /**
     * 是否开启缓存
     * @var boolean
     */
	private $_cachable = false;
	

	public function getUserByNick($nick){
		$sql = "select * from {$this->_name} where nick = '".$nick."' limit 1";
    	return $this->db(0)->fetchRow($sql);
	}

	public function setTopUser($data,$where){
//		$data['updated'] = time();
		$ret = $this->db(0)->update($this->_name, $data, $where);
		return $ret ? true : false;
	}

	public function addTopUser($data){
//		$data['created'] = time();
//		$data['updated'] = time();
		$ret = $this->db(0)->insert($this->_name, $data);
		return $ret ? true : false;
	}

}
