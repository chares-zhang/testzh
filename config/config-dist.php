<?php

if (!defined('GUARD')) { exit('非法访问'); }

return array(
	// database
	'db' => array(
		1 => array('type' => 'mysql', 'host' => 'localhost', 'port' => '3306', 'username' => 'root', 'password' => '', 'dbname' => 'app', 'weight' => 100), 
	),
	// memcached
	'memcache' => array(
		1 => array('host' => '', 'port' => 11211, 'weight' => 100),
	), 
	// base 
	'timezone' => 'Asia/Shanghai', 
	'table_div' => '100', // 分表分为100张
	'table_bit' => '3',   // 表名为3位数字
	'switch' => array( 
		'SHARDING' => 1, 
		'HORIZ' => 0,
	),
	// url
	'staticurl' => 'http://app.testzh.com/static', // 静态文件存放地址
	'webhost' => 'http://app.testzh.com', 
	//platform
	'oauth_url' => 'https://oauth.taobao.com/authorize',
	'token_url' => 'https://oauth.taobao.com/token',
	'app_key' => '21573922',
	'app_secret' => '2a99b98605318536af01cf6196695c9a',

	'sandbox_oauth_url' => 'https://oauth.tbsandbox.com/authorize',
	'sandbox_token_url' => 'https://oauth.tbsandbox.com/token',
	'sandbox_app_key' => '1021573922',
	'sandbox_app_secret' => 'sandbox605318536af01cf6196695c9a',

	'is_sandbox' => false,
	// domain
	'cookie' => array('expire' => time() + 86400, 'path' => '/', 'domain' => ''),

);
?>
