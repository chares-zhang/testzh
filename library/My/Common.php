<?php
/**
 * 框架相关的公用函数
 * @category   library
 * @author     chares zhang <linchare@gmail.com>
 * @version    $Id: Common.php 1 2012-09-09 16:14:39Z php $
 */

class Common{
	private static $_config = array();
	static private $_staticModel;

    static public function getConfig($filename = 'config' )
    {
        if (empty(self::$_config[$filename])) {
            $file = CONFIG_PATH . "/{$filename}.php";
            if (!file_exists($file)) {
                throw new Exception("配置文件{$file}不存在");
            }
            self::$_config[$filename] = require($file);
        }
        return self::$_config[$filename];
    }
    
    /**
     * 连接数据库
     * @param string $module
     * @param int $shopId
     * @param bool $isCommon (true,连公共集群;false根据shopId来获取集群)
     * @return Ambigous <Hlg_Db>
     */
    public static function getDb($module,$uid,$isCommon=false)
    {
    	static $db = array();
    	static $dbConf = array();
    	static $dbInfo = array();
    
    	$dbGroup = self::getDbGroup();
    	$dbInstance = self::getDbInstance($module, $uid, $isCommon);
    	$dbkey = $dbGroup . '_' . $dbInstance . '_' .$module;
    
    	//10分钟没释放的连接就释放掉
    	$expired = time() - 600;
    	if (!empty($db) && is_array($db)) {
    		foreach($db as $k=>$subDb){
    			if ($subDb['time'] < $expired) {
    				$subDb['handler'] = null;
    				unset($db[$k]);
    			}
    		}
    	}
    	if(empty($db[$dbkey])){
    		if(empty($dbConf)){
    			$dbConf = self::getConfig('db_conf');
    		}
    		if(empty($dbInfo)){
    			$dbInfo = self::getConfig('db_info');
    		}
    			
    		if (!isset($dbConf[$dbGroup]['module_dbname'][$module])) {
    			throw new Exception('module_dbname:'.$module.' not exist');
    		}
    		$dbName = $dbConf[$dbGroup]['module_dbname'][$module];//根据模块名获取库名
    			
    		$dbInfoArr = $dbInfo[$dbInstance];//获取实例信息.
    		$dbInfoArr['dbname'] = $dbName;
    		$db[$dbkey]['handler'] = new Db($dbInfoArr);
    	}
    
    	$db[$dbkey]['time'] = time();
    	return $db[$dbkey]['handler'];
    }
    
    /**
     * 获取数据库分组信息.
     */
    public static function getDbGroup()
    {
    	static $config = array();

    	if(empty($config)){
    		$config = self::getConfig('config');
    	}
    
    	$dbGroup = '';
    	if (isset($config['plat_info']['db_group'])) {
    		$dbGroup = $config['plat_info']['db_group'];
    	}
    	if (empty($dbGroup)) {
    		throw new Exception('db_group not exist');
    	}
    	return $dbGroup;
    }
    
    /**
     * @TODO 完成对应关系
     * 根据模块名$module,$shopId获取对应的实例.
     */
    public static function getDbInstance($module, $uid, $isCommon=false)
    {
    	$dbGroup = self::getDbGroup();
    	if(true === $isCommon){//如果$isCommon===true,则返回公共库的集群id
    		$instanceId = 'sharding_id_common';//暂时不启用公共集群，全部配置到旧集群.
    	}else{
    		$instanceId = self::getClustersId($uid);
    	}
    	$dbConf = self::getConfig('db_conf');
    	if (!isset($dbConf[$dbGroup])) {
    		throw new Exception('dbGroup:'.$dbGroup.' not exist');
    	}
    	if (!isset($dbConf[$dbGroup]['module_instance'][$instanceId][$module])) {
    		throw new Exception('module_instance:'.$module.' not exist');
    	}
    	return $dbConf[$dbGroup]['module_instance'][$instanceId][$module];
    }
    
    /**
     * @TODO 根据uid获取分库的shardingId.
     * shardingId对应于config.php中的model_instance的键.eg:0,1,2
     */
    public static function getClustersId($shopId)
    {
    	return 'sharding_id_0';
    }
    
    /**
     * 引入类
     * @param unknown_type $modelName
     */
	public static function getModel($modelName)
	{
		$modelName = trim($modelName);
		if (strpos($modelName, '/')===false) {
			$modelName = ucfirst($modelName);
		}else{
			$modelArr = explode('/', trim($modelName));
			$modelArr[0] = $modelArr[0] . '_Model';
			$modelName = str_replace(' ', '_',ucwords((implode(' ',$modelArr))));
		}
		return self::requireModel($modelName);
	}
	
	/**
	 * 实例化类，并返回其对象
	 * @param unknown $modelName
	 */
	public static function requireModel($modelName)
	{
		if(empty(self::$_staticModel[$modelName])){
			if(class_exists($modelName)){
				self::$_staticModel[$modelName] = new $modelName();
			}
		}
		if(empty(self::$_staticModel[$modelName])){
			echo "$modelName class not found ";
			exit;
		}
		return self::$_staticModel[$modelName];
	}
	
	/**
	* 获取配置文件中的各种url,默认取webhost
	*
	*/
	public static function getConfigUrl($urlKey)
	{
		$config = self::getConfig();
		return $config['main_info'][$urlKey];
	}
	
	/**
	 * 获取配置文件中的各种url,默认取webhost
	 *
	 */
	public static function getBaseUrl($urlKey='base_url')
	{
		return self::getConfigUrl('base_url');
	}
	
	//获取AppKey
	public static function getAppKey()
	{
		$config = self::getConfig();
		$isSandbox = $config['plat_info']['is_sandbox'];
		if ($isSandbox == true) {
			return $config['plat_info']['sandbox_app_key'];
		} else {
			return $config['plat_info']['app_key'];
		}
	}

	//获取AppSecret
	public static function getAppSecret()
	{
		$config = self::getConfig();
		$isSandbox = $config['plat_info']['is_sandbox'];
		if ($isSandbox == true) {
			return $config['plat_info']['sandbox_app_secret'];
		} else {
			return $config['plat_info']['app_secret'];
		}
	}
	
	/**
	 * 模板中指定站内跳转地址. eg.: $this->getUrl("module/controller/action",array('key1'=>'value1'));
	 * @param string $routePath 例:[模块名]/[控制器名]/[方法名]
	 * @param array $routeParams 例:array('p'=>2);
	 */
	public static function getUrl($routePath,$requestParams=array())
	{
		if(empty($routePath)){
			throw new Exception('getUrl param 1 can not empty.');
		}
	
		$requestUri = '';
		$tailUrl = $routePath;
		if (!empty($requestParams)) {
			if (is_array($requestParams)){//若是数组,拼好requesturi
				$requestUri = http_build_query($requestParams);
			}else{//若不是数组，则直接作为参数输出
				$requestUri = $requestParams;
			}
			$tailUrl .= '?'.$requestUri;
		}
	
		$webhostUrl = Common::getBaseUrl();
		$url = $webhostUrl . $tailUrl;
	
		return $url;
	}
	
	/**
	 * 指定跳转到$url地址.
	 * @param string $url
	 */
	public static function redirectUrl($url)
	{
		header("location:$url");
	}
	
	/**
	 * 指定跳转站内地址
	 * @param string $routePath 例:[模块名]/[控制器名]/[方法名]
	 * @param array $routeParams 例:array('p'=>2);
	 */
	public static function redirect($path, $arguments=array())
	{
		$url = self::getUrl($path,$arguments);
		header("location:$url");
	}
	
	
	/**
	 * 获取默认路径
	 * @throws Exception
	 */
	public static function getDefaultRoute()
	{
		$config = self::getConfig();
		if (!isset($config['main_info'])) {
			throw new Exception("config error:main_info not exist.");
		}
		if (!isset($config['main_info']['default_route'])) {
			throw new Exception("config error:main_info。default_route not exist.");
		}
		return $config['main_info']['default_route'];
		
	}
	
	/**
	 * @TODO 未仔细斟酌
	 * @param string $url
	 * @param array  $data
	 * @return mixed
	 */
	static public function post($url, $params)
	{
		$str = '';
		foreach ($params as $k=>$v) {
			if (is_array($v)) {
				foreach ($v as $kv => $vv) {
					$str .= '&' . $k . '[' . $kv  . ']=' . urlencode($vv);
				}
			} else {
				$str .= '&' . $k . '=' . urlencode($v);
			}
		}
		$str = substr($str, 1);
		if (function_exists('curl_init')) {
			// Use CURL if installed...
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $str);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Island API PHP Client 1.0 (curl) ' . phpversion());
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			$result = curl_exec($ch);
			$errno = curl_errno($ch);
			curl_close($ch);
			return array($errno, $result);
		} else {
			// Non-CURL based version...
			$context =
			array('http' => array(
				'method' => 'POST',
				'header' => 'Content-type: application/x-www-form-urlencoded'."\r\n".
							'User-Agent: Island API PHP Client 1.0 (non-curl) '.phpversion()."\r\n".
							'Content-length: ' . strlen($str),
							'content' => $str
				)
			);
			$contextid = stream_context_create($context);
			$sock = fopen($url, 'r', false, $contextid);
			if ($sock) {
				$result = '';
				while (!feof($sock)) {
					$result .= fgets($sock, 4096);
				}
				fclose($sock);
			}
		}
		
		return array(0, $result);

// 		$ch = curl_init();
// 		curl_setopt($ch, CURLOPT_URL, $url);
// 		curl_setopt($ch, CURLOPT_FAILONERROR, false);
// 		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// 		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// 		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
// 		if (is_array($params) && 0 < count($params))
// 		{
// 			$postBodyString = "";
// 			$postMultipart = false;
// 			foreach ($params as $k => $v)
// 			{
// 				if ("@" != substr($v, 0, 1)) {//判断是不是文件上传 
// 					$postBodyString .= "$k=" . urlencode($v) . "&";
// 				} else {//文件上传用multipart/form-data，否则用www-form-urlencoded
// 					$postMultipart = true;
// 				}
// 			}
// 			unset($k, $v);
// 			curl_setopt($ch, CURLOPT_POST, true);
// 			if ($postMultipart) {
// 				curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
// 			} else {
// 				curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString,0,-1));
// 			}
// 		}
// 		$reponse = curl_exec($ch);
// 		if (curl_errno($ch)) {
// 			throw new Exception(curl_error($ch),0);
// 		} else{
// 			$httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
// 			if (200 !== $httpStatusCode)
// 			{
// 				throw new Exception($reponse,$httpStatusCode);
// 			}
// 		}
// 		curl_close($ch);
		
// 		return $reponse;
	}
	
	

	// 	public static function computeTableId($uid)
	// 	{
	// 		$config = self::getConfig('config');
	// 		$table_div = $config['table_div'];
	// 		$table_bit = $config['table_bit'];
	// 		return '_' . str_pad($uid % $table_div, $table_bit, '0', STR_PAD_LEFT);
	// 	}
	
	// 	public static function shardId($uid)
	// 	{
	// 		if(!$uid){
	// 			echo 'shardId missing param uid';
	// 		}
	// 		$router = self::M('Pfrouter')->getRouter($uid);
	// 		return $router['shardid'];
	// 	}
	
	// 	/*
	// 	* 新建适配的时候用
	// 	* 根据config.php中配置的weight,获取shardId
	// 	* @return int 1,2...
	// 	*/
	// 	public static function getShardId()
	// 	{
	// 		if(!defined('SHARDING') || !SHARDING){
	// 			return 1;
	// 		}
	// 		$config = self::getConfig('config');
	// 		$servers = $config['db'];
	
	// 		$weight = array();
	// 		$total = 0;
	// 		$count = count($servers);
	// 		if(count($servers)>1){//数据库主库超过1个
	// 			foreach($servers as $params){
	// 				if(!isset($params['weight'])){
	// 					throw new Exception('pls set mutipl db weight', -1);
	// 				}
	// 				$total += $params['weight'];
	// 				$weight[] = $total;//10,20,70=>$weight=array(10,30,100);
	// 			}
	// 			$random = rand(1,$total);//随机一个表
	// 			for($i=1;$i<=$count;$i++){
	// 				if($i == 0){
	// 					if($random <= $weight[$i]){
	// 						return $i;
	// 					}
	// 				}else{
	// 					if($random>$weight[$i-1] && $random <= $weight[$i]){
	// 						return $i;
	// 					}
	// 				}
	// 			}
	// 		}else{//只有一个主库,默认是database->master->params
	// 			return 1;
	// 		}
	// 	}
	
}

