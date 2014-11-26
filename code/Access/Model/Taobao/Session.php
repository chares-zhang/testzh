<?php
class Access_Model_Taobao_Session
{
	static private $_instance;
	private $_sessionName;
	
	public function __construct(){
		$this->init();
	}
	
	public function init()
	{
		$mainInfo = Common::getMainInfo();
		$cookieInfo = Common::getCookieInfo();
		switch($mainInfo['session_save']) {
		    case 'memcache':
		    	@ini_set('session.save_handler', 'memcache'); //定义了来存储和获取与会话关联的数据的处理器的名字。默认为 files。
		    	session_save_path($this->getSessionSavePath()); //Get and/or set the current session save path
		    	break;
		    default:
		    	session_module_name('files');
		    	break;
		}
		$cookieParams = array(
				'lifetime' => $cookieInfo['expire'],
				'path'     => $cookieInfo['path'],
				'domain'   => $cookieInfo['domain'],
				'secure'   => $cookieInfo['issecure'],
				'httponly' => $cookieInfo['httponly'],
		);
		call_user_func_array('session_set_cookie_params', $cookieParams);
		
		$this->setSessionName($mainInfo['session_name']);//不用应用系统，用不重复的session名; 支持多应用在这边加入应用id
	}
	
	public function setSessionName($sessionName)
	{
		$this->_sessionName = $sessionName;
		session_name($sessionName); //Get and/or set the current session name
	}
	
	public function getSessionName()
	{
		return $this->_sessionName;
	}
	
	public static function getInstance()
	{
		if (!self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function start($data)
	{
		//写进session中必须有的字段为uid
		if (!isset($data['uid'])) {
			throw new Exception('session start error:index uid not found');
		}
		$uid = $data['uid'];
		$sid = md5(sprintf('%010s', $uid) . microtime(true) . rand(1000, 9999)); //支持多应用，在这边加入应用id
		session_id($sid);
		session_start();
		$this->setSessionData($data);
	}
	
	public function valid($uid)
	{
		$cookieSid = Core_Model_Cookie::getCookie($this->getSessionName());
		if(!empty($cookieSid)) {
			session_id($cookieSid);
		} else {
			return false;
		}
		session_start();
		
		if (isset($_SESSION) && isset($_SESSION['uid'])) {
			if ($uid == $_SESSION['uid']) {
				return true;
			}
		}
		return false;
	}
	
	public function getSessionId()
	{
		session_id();
	}
	
	public function getSessionSavePath()
	{
		$config = Common::getMainInfo();
		$sessionSave = $config['session_save'];
		$memInfoArr = $config[$sessionSave];
		$res = array();
		if (!empty($memInfoArr) && is_array($memInfoArr)) {
			foreach($memInfoArr as $memInfo) {
				$res[] = "tcp://{$memInfo['host']}:{$memInfo['port']}?persistent=1&weight={$memInfo['weight']}&timeout=10&retry_interval=10";
			}
		} else {
			throw new Exception("getSessionSavePath error:config.main_info.$sessionSave is not array.");
		}
		if (count($res) > 1) {
			return join(',',$res);
		} else {
			return $res[0];
		}
	}
	
	/**
	 * 设置session值
	 * @param unknown_type $data
	 */
	public function setSessionData($data){
		if(is_array($data) && !empty($data)) {
			foreach ($data as $key => $value) {
				$_SESSION[$key] = $value;
			}
		}
	}

	/**
	 * 获取session值
	 */
	public function getSessionData()
	{
		return $_SESSION;
	}
	
	/**
	 * 清理session
	 */
	public function destroy()
	{
		$_SESSION = array();
		session_destroy();
		Core_Model_Cookie::clear();
	}
	
}
