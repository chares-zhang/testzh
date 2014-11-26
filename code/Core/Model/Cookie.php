<?php

class Core_Model_Cookie 
{
	public static function isSetCookie($name) 
	{
		return isset($_COOKIE[$name]);
	}

	public static function get($name)
	{
		if (self::isSetCookie($name)) {
			$value = $_COOKIE[$name];
			$value = unserialize(base64_decode($value));
		} else {
			$value = '';
		}
		return $value;
	}

	public static function getCookie($name)
	{
		if (self::isSetCookie($name)) {
			$value = $_COOKIE[$name];
		} else {
			$value = '';
		}
		return $value;
	}
	
	public static function set($name, $value, $expire='', $path='', $domain='')
	{
		$cookieInfo = Common::getCookieInfo();
		if ($expire == '') {
			$expire = $cookieInfo['expire'];
		}
		if (empty($path)) {
			$path = $cookieInfo['path'];
		}
		if (empty($domain)) {
			$domain = $cookieInfo['domain'];
		}
		$expire = !empty($expire) ? $expire : 0;
		$value = base64_encode(serialize($value));
		setcookie($name, $value, $expire, $path, $domain);
		$_COOKIE[$name] = $value;
	}
	
	public static function delete($name) 
	{
		$cookieInfo = Common::getCookieInfo();
		if (!isset($cookieInfo['path'])) {
			throw new Exception('error:cookie path not set.');
		}
		if (!isset($cookieInfo['domain'])) {
			throw new Exception('error:cookie domain not set.');
		}
		$path = $cookieInfo['path'];
		$domain = $cookieInfo['domain'];
		
		self::set($name, '', time()-3600, $path,$domain);
		unset($_COOKIE[$name]);
	}

	public static function clear() 
	{
		if (!empty($_COOKIE)) {
			foreach ($_COOKIE as $k=>$v){
				self::delete($k);
			}
		}
	} 
}
