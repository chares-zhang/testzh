<?php
/**
 * 登陆类
 * @author chares
 *
 */
class Core_Model_Access_Taobao
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
			$this->access();
		}
	}
	
	//登陆验证
	public function tokenVerify($uid,$sid)
	{
		if (Core_Model_Session::getInstance($uid)->valid($sid)) {
			return true;
		} else {
			return false;
		}
	}
	
	//获取oauthUrl
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
	
	/**
	 * 登陆
	 */
	public function access()
	{
// 		$webhostUrl = Common::getWebhostUrl();
		$oauthUrl = self::getOauthUrl();
// 		$tokenMainUrl = Common::getMainTokenUrl();
// 		$appKey = Common::getAppKey();
// 		$appSecret = Common::getAppSecret();

		if (isset($_REQUEST['error']) && $_REQUEST['error'] == 'access_denied') {//授权失败
			if (isset($_REQUEST['error_description']) && strpos($_REQUEST['error_description'], 'parent account') !== false) {
				header("Content-Type:text/html;charset=utf-8");
				echo "主号未授权该子帐号使用该应用，请联系主号授权后再来使用.<a href='http://fuwu.taobao.com/ser/detail.htm?service_id=239'>返回应用页面</a>";
				die();
        	}
			$this->redirectUrl($oauthUrl);
		} elseif (isset($_REQUEST['code'])) {//授权成功,获取访问令牌,新增用户or登陆逻辑.
			$code = $_REQUEST['code'];
			$this->_tbAccess($code);
			//跳转到软件首页.
			$this->redirect('core/index/index');
		} elseif (isset($_REQUEST['top_appkey'])) {
			//暂时没用.
			$topAppkey = $_REQUEST['top_appkey'];
			$topSession = $_REQUEST['top_session'];
			$topParameters = $_REQUEST['top_parameters'];
			$topSign = $_REQUEST['top_sign'];
			var_dump('topParameters',$topParameters);
			exit;
		} elseif (isset($_REQUEST['test'])) {
			if (!$this->uid) {//未登录.
				$uid = 1;
				$this->_doLogin($uid);
				//跳转到软件首页.
				$this->redirect('core/index/index');

				//跳转到自动橱窗首页.
				//$this->redirect('tools/showcase/index');
			} else {//已登录

			}
		} else {
			//是否登录
			if (!$this->uid) {//未登录.
				$this->redirectUrl($oauthUrl);
			} else {//已登录

			}
		}
	}
	
	//淘宝授权后的处理逻辑. 创建新用户新用户 或者 更新用户信息. 然后跳转到软件首页.
	private function _tbAccess($code)
	{
		//获取登录令牌
		$result = self::getLoginUserInfo($code);
		if (isset($result['error'])) { //获取登录令牌不成功,则跳转到登录登录页面重新授权
//			throw new Exception($result['error']);
			$oauthUrl = Common::getOauthUrl();
			$this->redirectUrl($oauthUrl);
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

		$coreUserM = Common::getModel('core/user');
		$where = "nick='{$tbUserNick}'";
		$userRow = $coreUserM->getRow($where,'*');
		if (empty($userRow)) {//新增用户
			$userRow = array(
				'nick' => $tbUserNick,
				'tb_user_id' => $tbUserId,
				'w2_expired' => $w2Expired,
				'r2_expired' => $r2Expired,
				'access_token' => $accessToken,
				'sub_access_token' => $accessToken,
				're_expired' => $reExpired,
				'refresh_token' => $refreshToken,
				'is_access_token_expired' => 0,
//				'version_no' => '1',
				'lastlogin' => time(),
			);
			$uid = $coreUserM->addUserItem($userRow);
		} else {//老用户更新.
			if (!empty($tbUserNick)) {
				$userRow['nick'] = $tbUserNick;
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
			$res = $coreUserM->updateUserItem($userRow,$where);
		}
		//完成登录.
		$this->_doLogin($uid);
	}
	
	//获取登陆信息
	public static function getLoginUserInfo($code)
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
		$res = Common::post($tokenMainUrl,$params);
		return json_decode($res[1],true);
	}
	
	private function _doLogin($uid)
	{
		Core_Model_Session::getInstance($uid)->start();
		$sid = Core_Model_Session::getInstance($uid)->session_id();
		Core_Model_Cookie::set('sid', $sid, time() + 3600, '/');
		Core_Model_Cookie::set('uid', $uid, time() + 3600, '/');
	}
	
}
