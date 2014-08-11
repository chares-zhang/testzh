<?php
if (!defined('GUARD')) { exit('非法访问'); }
return array(
	//框架信息
	'main_info' => array(
		'timezone' => 'Asia/Shanghai',
		'domain' => 'testzh.testzh.com',
		'base_url' => 'http://testzh.testzh.com',
		'static_url' => 'http://testzh.testzh.com/public/static',
		'default_route' => 'core/index/index',
		//支持分布式memcached
		'memcache'=>array(
			'mem_1' => array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 100),
// 			'mem_2' => array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 100),
		),
	),
	//平台信息
	'plat_info' => array(
		'oauth_url' => 'https://oauth.taobao.com/authorize',
		'token_url' => 'https://oauth.taobao.com/token',
		'app_key' => '21573922',
		'app_secret' => '2a99b98605318536af01cf6196695c9a',
		
		'sandbox_oauth_url' => 'https://oauth.tbsandbox.com/authorize',
		'sandbox_token_url' => 'https://oauth.tbsandbox.com/token',
		'sandbox_app_key' => '1021573922',
		'sandbox_app_secret' => 'sandbox605318536af01cf6196695c9a',
		
		'is_sandbox' => false,
	),
	
);
?>
