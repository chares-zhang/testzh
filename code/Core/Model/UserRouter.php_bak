<?php

class UserRouter extends AbstractModel 
{
	protected $_name = 'user_router';

	private $_cachable = false;

	public function getRouter($uid)
	{
		if (!$this->_cachable) {
			$sql = "select * from {$this->_name} where uid = '" . $uid . "' limit 1";
			return $this->db()->fetchRow($sql);
		}

		$row = MC::getInstance()->get($this->_name . '::' . $uid);
		if (!$row) {
			$sql = "select * from {$this->_name} where uid = '" . $uid . "' limit 1";
			$row = $this->db()->fetchRow($sql);
			if ($row && $this->_cachable) {
				MC::getInstance()->set($this->_name . '::' . $uid, $row, 3600);
			}
		}

		return $row;
	}

	public function addRouter($data) 
	{
		$res = $this->db()->insert($this->_name, $data);
		if ($res) {
			$insertId = $this->db()->insertId();
			return $insertId;
		}
		return $res;
	}
}
