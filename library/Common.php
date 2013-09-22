<?php
/**
 *
 * @category   library
 * @author     chares zhang <linchare@gmail.com>
 * @version    $Id: Common.php 1 2012-09-09 16:14:39Z php $
 */

class Common{
	private static $_config = array();

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
    
	public static function getDb($shardId = 0)
	{
		static $db = array();
		static $dbConfig = array();
		if(empty($db[$shardId]))
		{
			if(empty($dbConfig)){
				$config = self::getConfig('config');
				$dbConfig = $config['db'][$shardId];
			}
			$db[$shardId] = new Db($dbConfig);
		}
		return $db[$shardId];
	}
	
	public static function M($model)
	{
		static $staticModel;
		if (empty($staticModel[$model])) {
			if(class_exists($model)){
				$staticModel[$model] = new $model();
			}
		}
		if(empty($staticModel[$model])){
			echo "$model class not found ";
			exit;
		}
		return $staticModel[$model];
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
	public static function getConfigUrl($urlKey='webhost')
	{
		$config = self::getConfig();
		return $config[$urlKey];
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
	
	//获取webhostUrl
	public static function getWebhostUrl()
	{
		$config = self::getConfig();
		return $config['webhost'];
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
	
}

