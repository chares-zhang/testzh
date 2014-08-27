<?php
/**
 * 公共类，逻辑相关的公用函数
 * @author chares
 *
 */

class My{
	static private $_uid;
	
	public static function getUid()
	{
		return self::$_uid;
	}
	
	public static function setUid($uid)
	{
		self::$_uid = $uid;
	}
	
	
}