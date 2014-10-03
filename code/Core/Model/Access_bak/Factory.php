<?php
class Core_Model_Access_Factory
{
	const PLAT_TAOBAO = 'taobao'; //淘宝平台，多点登陆
	const PLAT_QQ = 'qq'; //腾讯开放平台，单点登陆
	
	public static function factory($platName)
	{
		$platM = Common::getModel('core/access_'.$platName);
		return $platM;
	}
	
}
