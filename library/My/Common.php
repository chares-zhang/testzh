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
	 * 引入类
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
	
	public static function computeTableId($uid)
	{
		$config = self::getConfig('config');
		$table_div = $config['table_div'];
		$table_bit = $config['table_bit'];
		return '_' . str_pad($uid % $table_div, $table_bit, '0', STR_PAD_LEFT);
	}

	public static function shardId($uid)
	{
		if(!$uid){
			echo 'shardId missing param uid';
		}
		$router = self::M('Pfrouter')->getRouter($uid);
		return $router['shardid'];
	}

	/*
	* 新建适配的时候用
	* 根据config.php中配置的weight,获取shardId
	* @return int 1,2...
	*/
	public static function getShardId()
	{
		if(!defined('SHARDING') || !SHARDING){
			return 1;
		}
		$config = self::getConfig('config');
		$servers = $config['db'];
		
		$weight = array();
		$total = 0;
		$count = count($servers);
		if(count($servers)>1){//数据库主库超过1个
			foreach($servers as $params){
				if(!isset($params['weight'])){
					throw new Exception('pls set mutipl db weight', -1);
				}
				$total += $params['weight'];
				$weight[] = $total;//10,20,70=>$weight=array(10,30,100);
			}
			$random = rand(1,$total);//随机一个表
			for($i=1;$i<=$count;$i++){
				if($i == 0){
					if($random <= $weight[$i]){
						return $i;
					}
				}else{
					if($random>$weight[$i-1] && $random <= $weight[$i]){
						return $i;
					}
				}
			}
		}else{//只有一个主库,默认是database->master->params
			return 1;
		}
	}

	/**
	* 获取配置文件中的各种url,默认取webhost
	*
	*/
	public static function getConfigUrl($urlKey='base_url')
	{
		$config = self::getConfig();
		return $config['main_info'][$urlKey];
	}

	
	//获取oauthUrl
	public static function getOauthUrl()
	{
		$config = self::getConfig();
		$isSandbox = $config['is_sandbox'];
		if ($isSandbox == true) {
			$oauthUrl = $config['sandbox_oauth_url'] 
					.'?client_id='.self::getAppKey()
					.'&response_type=code&redirect_uri='
					.self::getWebhostUrl();
		} else {
			$oauthUrl = $config['oauth_url'] 
					.'?client_id='.self::getAppKey()
					.'&response_type=code&redirect_uri='
					.self::getWebhostUrl();
		}
		return $oauthUrl;
	}
	
	//获取tokenUrl
	public static function getMainTokenUrl()
	{
		$config = self::getConfig();
		$isSandbox = $config['is_sandbox'];
		if ($isSandbox == true) {
			return $config['sandbox_token_url'];
		} else {
			return $config['token_url'];
		}
	}
	
	//获取AppKey
	public static function getAppKey()
	{
		$config = self::getConfig();
		$isSandbox = $config['is_sandbox'];
		if ($isSandbox == true) {
			return $config['sandbox_app_key'];
		} else {
			return $config['app_key'];
		}
	}

	//获取AppSecret
	public static function getAppSecret()
	{
		$config = self::getConfig();
		$isSandbox = $config['is_sandbox'];
		if ($isSandbox == true) {
			return $config['sandbox_app_secret'];
		} else {
			return $config['app_secret'];
		}
	}
	
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
}

