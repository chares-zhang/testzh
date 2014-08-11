<?php
class Session extends AbstractModel 
{
	protected $_name = 'core_sessions';

	static private $_instance;

	private $uid;

	private $data;

	private $_cachable = false;

	public function __construct($uid)
	{
		parent::__construct();
		$this->uid = $uid;
		$cookieSid = Cookie::get('sid');
		if (!empty($cookieSid)) {
			if ($this->_cachable) {
				$this->data = MC::getInstance()->get($this->_name . '::' . $cookieSid);
				if (empty($this->data)) {
					$this->data = array();
				}
			} else {
				$this->data = array();
				$sql = "select * from {$this->_name} where session_id = '".$cookieSid."' limit 1";
				$row = $this->db()->fetchRow($sql);
				if($row){
					$this->data = $row;
				}else{
					$this->data = array();
				}
			}
		} else {
			$this->data = array();
		}
	}

	public static function getInstance($uid)
	{
		if (!self::$_instance) {
			self::$_instance = new self($uid);
		}
		return self::$_instance;
	}
	
	//每次登陆执行这里.登陆则新增一条session.
	public function start() 
	{
		$sid = md5(sprintf('%010s', $this->uid) . microtime(true) . rand(1000, 9999));
		$data = array(
			'session_id' => $sid, 
			'uid' => $this->uid, 
			'ip' => Util::ip(), 
			'created' => time(), 
			'updated' => time()
		);
		$res = $this->db()->insert($this->_name, $data);
		if ($this->_cachable) {
			MC::getInstance()->set($this->_name . '::' . $sid, $data, 86400);
		}
		$this->data = $data;
	}

	public function session_id()
	{
		return $this->data['session_id'];
	}

	public function valid($sid)
	{
		if (empty($this->data)) {
			return false;
		}
		$this->data['created'] = time();
		if ($this->data['session_id'] == $sid && $this->uid == $this->data['uid']) {
			if($this->_cachable) {
				MC::getInstance()->set($this->_name.'::'.$sid, $this->data, 15*60);
			} else {
				$where = sprintf("session_id='%d'", $sid);
				$this->db()->update($this->_name, $this->data, $where);
			}
			return true;
		}
		return false;
	}

	public function clear($uid) 
	{
		MC::getInstance()->delete($this->_name . '::' . $uid);
		$sql = "delete from {$this->_name} where uid = '" . $uid . "' limit 1";
		$row = $this->db()->query($sql);
	}
}

//class Session extends AbstractModel 
//{
//  protected $_name = 'user_sessions';
//
//  static private $_instance;
//
//  private $uid;
//
//  private $data;
//
//  private $_cachable = false;
//
//  public function __construct($uid)
//  {
//    parent::__construct();
//    $this->uid = $uid;
//    if ($this->_cachable) {
//      $this->data = MC::getInstance()->get($this->_name . '::' . $uid);
//      if (empty($this->data)) {
//        $this->data = array();
//      }
//    } else {
//      $this->data = array();
//    }
//  }
//
//  public static function getInstance($uid)
//  {
//    if (!self::$_instance) {
//      self::$_instance = new self($uid);
//    }
//    return self::$_instance;
//  }
//
//  public function start() 
//  {
//    $sid = md5(sprintf('%010s', $this->uid) . microtime(true) . rand(1000, 9999));
//    $sql = "select * from {$this->_name} where uid = '" . $this->uid . "' limit 1";
//    $row = $this->db()->fetchRow($sql);
//    $data = array(
//      'session_id' => $sid, 
//      'uid' => $this->uid, 
//      'ip' => '0.0.0.0', 
//      'createtime' => time(), 
//      'updatetime' => time()
//    );
//
//    if ($row) { 
//      $where = sprintf("uid='%d'", $this->uid);
//      $this->db()->update($this->_name, $data, $where);
//    } else {
//      $res = $this->db()->insert($this->_name, $data);
//    }
//    if ($this->_cachable) {
//      MC::getInstance()->set($this->_name . '::' . $this->uid, $data, 86400);
//    }
//    $this->data = $data;
//  }
//
//  public function session_id()
//  {
//    return $this->data['session_id'];
//  }
//
//  public function valid($sid)
//  {
//    if (empty($this->data)) {
//      return false;
//    }
//    return ($this->data['session_id'] == $sid);
//  }
//
//  public function clear($uid) 
//  {
//    MC::getInstance()->delete($this->_name . '::' . $uid);
//    $sql = "delete from {$this->_name} where uid = '" . $uid . "' limit 1";
//    $row = $this->db()->query($sql);
//  }
//}
