<?php

class User extends AbstractModel 
{
  protected $_name = 'user';

  private $_cachable = false;

  public function getUser($uid) 
  {
    if ($this->_cachable) {
      $sql = "select * from {$this->_name} where uid = '" . $uid . "' limit 1";
      return $this->db()->fetchRow($sql);
    }

    $row = MC::getInstance()->get($this->_name . '::' . $uid);
    if (!$row) {
      $sql = "select * from {$this->_name} where uid = '" . $uid . "' limit 1";
      $row = $this->db()->fetchRow($sql);
      if ($row) {
        MC::getInstance()->set($this->_name . '::' . $uid, $row, 3600);
      }
    }
    return $row;
  }

  public function addUser($data) 
  {
    $data['createtime'] = time();
    $data['lastlogin'] = time();
    $data['updatetime'] = time();
    $res = $this->db()->insert($this->_name, $data);
    return $res ? true : false;
  }
}
