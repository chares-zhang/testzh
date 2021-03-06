<?php
if (!defined('GUARD')) { exit('非法访问'); }
return array(
	//框架信息
	'main_info' => array(
		'timezone' => 'Asia/Shanghai',
		'domain' => 'testzh.testzh.com',
		'base_url' => 'http://testzh.testzh.com',
		'static_url' => 'http://testzh.testzh.com/static',
		'default_route' => 'core/index/index',
		//支持分布式memcached
		'memcache' => array(
			'mem_1' => array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 100),
// 			'mem_2' => array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 100),
		),
		'memcached' => array(
				'mem_1' => array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 100),
// 				'mem_2' => array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 100),
		),
		'ocs' => array(
// 				m.jst.ocs.aliyuncs.com:11211:3bcf0904054e11e3:jst_568999
			'mem_1' => array('host' => 'm.jst.ocs.aliyuncs.com', 'port' => 11211, 'weight' => 100),
		),
		'session_save' => 'memcache',
		'session_name' => 'MYSID',
		'login_type' => 'single', //single单点登陆; multi多点登陆
		'layout_template' => 'core/page',
		'layout_theme' => 'base',
	),
	'content_block' => array(//例如 edit的block使用add的bock
		
	),
	//平台信息
	'plat_info' => array(
		'plat_name' => 'taobao',
		'oauth_url' => 'https://oauth.taobao.com/authorize',
		'token_url' => 'https://oauth.taobao.com/token',
		'app_key' => '23102769',
		'app_secret' => 'fb05721f337f3ef8a01df5f65a6a95d7',
		'gateway_url' => 'http://gw.api.taobao.com/router/rest',

		'sandbox_oauth_url' => 'https://oauth.tbsandbox.com/authorize',
		'sandbox_token_url' => 'https://oauth.tbsandbox.com/token',
		'sandbox_app_key' => '1023102769',
		'sandbox_app_secret' => 'sandboxf337f3ef8a01df5f65a6a95d7',
		'sandbox_gateway_url' => 'http://gw.api.tbsandbox.com/router/rest',
		
		'is_sandbox' => true,
		'db_group' => 'plat_name_taobao',//每个平台不同数据库分组
	),
	//cookie信息
	'cookie' => array(
		'expire' => time()+3600*24,
		'path' => '/',
		'domain' => '.testzh.com',
		'httponly' => true,
		'issecure' => false,
	),
	'block' => array(
		'index/add' => 'index/index',
	),
	
);

?>