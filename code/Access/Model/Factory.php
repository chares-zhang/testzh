<?php
class Access_Model_Factory
{
	const PLAT_TAOBAO = 'taobao'; //淘宝平台，多点登陆
	const PLAT_QQ = 'qq'; //腾讯开放平台，单点登陆
	
	public static function factory($platName)
	{
		$platM = Common::getPlatModel('access/access');
		return $platM;
	}
	
}
