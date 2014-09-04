<?php
class Core_Access
{
	const PLAT_TAOBAO = 'taobao'; //淘宝平台
	
	public static function factory($code)
	{
		$appKey = Common::getAppKey();
		$appSecret = Common::getAppSecret();
		$webhostUrl = Common::getWebhostUrl();
		$tokenMainUrl = Common::getMainTokenUrl();
		$params = array(
			'client_id'	=> $appKey,
			'client_secret' => $appSecret,
			'grant_type' => 'authorization_code',
			'code' => $code,
			'redirect_uri' => $webhostUrl
		);
		$res = Util::post($tokenMainUrl,$params);
		return json_decode($res[1],true);

	}

	
}
