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
	),
	'content_block' => array(//例如 edit的block使用add的bock
		
	),
	//平台信息
	'plat_info' => array(
		'plat_name' => 'taobao',
		'oauth_url' => 'https://oauth.taobao.com/authorize',
		'token_url' => 'https://oauth.taobao.com/token',
		'app_key' => '21585000',
		'app_secret' => '458f0167cf7fe485ed1d5bdb006aa8d2',
		
		'sandbox_oauth_url' => 'https://oauth.tbsandbox.com/authorize',
		'sandbox_token_url' => 'https://oauth.tbsandbox.com/token',
		'sandbox_app_key' => '1021573922',
		'sandbox_app_secret' => 'sandbox605318536af01cf6196695c9a',
		
		'is_sandbox' => false,
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