<?php
/**
 * 登陆类
 * @author chares
 *
 */
class Access_Model_Taobao_Access
{
	const PLAT_TAOBAO = 'taobao'; //淘宝平台
	
	//获取当前登陆的uid
	public function getLoginUid()
	{
		$cookieSid = Core_Model_Cookie::get('sid');
		$cookieUid = Core_Model_Cookie::get('uid');
		//存在cookie则证明已经登录了.
		if ($cookieSid && $cookieUid && $this->tokenVerify($cookieUid, $cookieSid)) {
			return $cookieUid;
		} else {
			return false;
		}
		
	}
	
	//登陆验证
	public function tokenVerify($uid,$sid)
	{
		$session = Common::getModel('core/');
// 		if (Core_Model_Session::getInstance($uid)->valid($sid)) {
// 			return true;
// 		} else {
// 			return false;
// 		}
	}
	
	//获取oauthUrl,淘宝登陆页面url
	public static function getOauthUrl()
	{
		$config = Common::getConfig();
		$isSandbox = $config['plat_info']['is_sandbox'];
		if ($isSandbox == true) {
			$oauthUrl = $config['plat_info']['sandbox_oauth_url']
			.'?client_id='.Common::getAppKey()
			.'&response_type=code&redirect_uri='
					.Common::getConfigUrl('base_url');
		} else {
			$oauthUrl = $config['plat_info']['oauth_url']
			.'?client_id='.Common::getAppKey()
			.'&response_type=code&redirect_uri='
					.Common::getConfigUrl('base_url');
		}
		return $oauthUrl;
	}
	
	//获取tokenUrl，淘宝获取用户信息的url
	public static function getMainTokenUrl()
	{
		$config = Common::getConfig();
		$isSandbox = $config['plat_info']['is_sandbox'];
		if ($isSandbox == true) {
			return $config['plat_info']['sandbox_token_url'];
		} else {
			return $config['plat_info']['token_url'];
		}
	}
	
	/**
	 * 用户未登陆时的用户登陆逻辑
	 * 若是授权出错，则提示错误。
	 * 若是code授权回调请求，则进入code授权回调逻辑去登陆系统
	 * 若是top_appkey授权回调请求，则进入top_appkey授权回调逻辑去登陆系统
	 * 若什么都不是，则跳转到淘宝登陆授权页面。
	 */
	public function access()
	{
		$oauthUrl = self::getOauthUrl();
		if (isset($_REQUEST['error']) && $_REQUEST['error'] == 'access_denied') {//授权失败
			if (isset($_REQUEST['error_description']) && strpos($_REQUEST['error_description'], 'parent account') !== false) {
				header("Content-Type:text/html;charset=utf-8");
				echo "主号未授权该子帐号使用该应用，请联系主号授权后再来使用.<a href='http://fuwu.taobao.com/ser/detail.htm?service_id=239'>返回应用页面</a>";
				die();
        	}
			$this->redirectUrl($oauthUrl);
		} elseif (isset($_REQUEST['code'])) {//code授权,成功,获取访问令牌,新增用户or登陆逻辑.
			$code = $_REQUEST['code'];
			$this->_tbAccess($code);
			//跳转到软件首页.
			$defaultRoute = Common::getDefaultRoute();
			Common::redirect($defaultRoute);
		} elseif (isset($_REQUEST['top_appkey'])) {//top_appkey授权
			//暂时没用.
// 			$topAppkey = $_REQUEST['top_appkey'];
// 			$topSession = $_REQUEST['top_session'];
// 			$topParameters = $_REQUEST['top_parameters'];
// 			$topSign = $_REQUEST['top_sign'];
// 			var_dump('topParameters',$topParameters);
// 			exit;
		} elseif (isset($_REQUEST['test'])) { //超级管理员登陆
			if (!$this->uid) {//未登录.
				$uid = 1;
				$this->_doLogin($uid);
				
				//跳转到软件首页.
				$defaultRoute = Common::getDefaultRoute();
				Common::redirect($defaultRoute);

			} else {//已登录

			}
		} else {
			//跳转到软件首页.
			Common::redirectUrl($oauthUrl);
		}
	}
	
	/**
	 * code授权回调后的处理
	 * 1.获取用户信息
	 * 2.新建、更新用户表
	 * 3.登陆处理cookie和session
	 * @param unknown_type $code
	 */
	private function _tbAccess($code)
	{
		//获取登录令牌
		$result = self::getLoginUserInfo($code);
		if (isset($result['error'])) { //获取登录令牌不成功,则跳转到登录登录页面重新授权
//			throw new Exception($result['error']);
			$oauthUrl = self::getOauthUrl();
			Common::redirectUrl($oauthUrl);
		}
		$tbUserNick = urldecode($result['taobao_user_nick']);
		$tbUserId = $result['taobao_user_id'];
		$subTbUserNick = isset($result['sub_taobao_user_nick'])?urldecode($result['sub_taobao_user_nick']):'';
		$subTbUserId = isset($result['sub_taobao_user_id'])?$result['sub_taobao_user_id']:'';
		$reExpiresIn = $result['re_expires_in'];
		$reExpired = date('Y-m-d H:i:s',strtotime("+ $reExpiresIn seconds"));
		$w2ExpiresIn = (int)$result['w2_expires_in'];
		$w2Expired = date('Y-m-d H:i:s',strtotime("+ $w2ExpiresIn seconds"));
		$r2ExpiresIn = $result['r2_expires_in'];
		$r2Expired = date('Y-m-d H:i:s',strtotime("+ $r2ExpiresIn seconds"));
		$refreshToken = $result['refresh_token'];
		$accessToken   = $result['access_token'];
		
		$accessUserM = Common::getPlatModel('access/user');
		$userRow = $accessUserM->getUserItemByName($tbUserNick);
		if (empty($userRow)) {//新增用户
			$userRow = array(
				'username' => $tbUserNick,
				'tb_user_id' => $tbUserId,
				'w2_expired' => $w2Expired,
				'r2_expired' => $r2Expired,
				'access_token' => $accessToken,
				'sub_access_token' => $accessToken,
				're_expired' => $reExpired,
				'refresh_token' => $refreshToken,
				'is_access_token_expired' => 0,
				'version_no' => '1',
				'last_login' => date('Y-m-d H:i:s'),
			);
			$uid = $accessUserM->addUserItem($userRow);
		} else {//老用户更新.
			if (!empty($tbUserNick)) {
				$userRow['username'] = $tbUserNick;
			}
			if (!empty($tbUserId)) {
				$userRow['tb_user_id'] = $tbUserId;
			}
			$userRow['w2_expired'] = $w2Expired;
			$userRow['r2_expired'] = $r2Expired;
			$userRow['access_token'] = $accessToken;
			$userRow['sub_access_token'] = $accessToken;
			$userRow['re_expired'] = $reExpired;
			$userRow['refresh_token'] = $refreshToken;
			$uid = $userRow['uid'];
			$where = "uid = '{$uid}'";
			$res = $accessUserM->updateUserItem($userRow,$where);
		}
		//完成登录.
		$this->_doLogin($uid);
	}
	
	//获取登陆信息
	public static function getLoginUserInfo($code)
	{
		$baseUrl = Common::getBaseUrl();
		$appKey = Common::getAppKey();
		$appSecret = Common::getAppSecret();
		$tokenMainUrl = self::getMainTokenUrl();
		$params = array(
			'client_id'	=> $appKey,
			'client_secret' => $appSecret,
			'grant_type' => 'authorization_code',
			'code' => $code,
			'redirect_uri' => $baseUrl
		);
		$res = Common::post($tokenMainUrl,$params);
		return json_decode($res[1],true);
	}
	
	private function _doLogin($uid)
	{
		Access_Model_Taobao_Session::getInstance($uid)->start();
		$sid = Access_Model_Taobao_Session::getInstance($uid)->session_id();
		Core_Model_Cookie::set('sid', $sid, time() + 3600, '/');
		Core_Model_Cookie::set('uid', $uid, time() + 3600, '/');
	}
	
}
