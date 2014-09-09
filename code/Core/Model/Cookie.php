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

	public static function set($name, $value, $expire='', $path='', $domain='')
	{
		$config = Common::getConfig('config');
		if ($expire == '') {
			$expire = $config['cookie']['expire'];
		}
		if (empty($path)) {
			$path = $config['cookie']['path'];
		}
		if (empty($domain)) {
			$domain = $config['cookie']['domain'];
		}
		$expire = !empty($expire) ? $expire : 0;
		$value = base64_encode(serialize($value));
		$a = setcookie($name, $value, $expire, $path, $domain);
		$_COOKIE[$name] = $value;
	}

	public static function delete($name) 
	{
		self::set($name, '', time()-3600);
		unset($_COOKIE[$name]);
	}

	public static function clear() 
	{
		unset($_COOKIE);
	} 
}
