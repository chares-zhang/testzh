<?php
if (!defined('GUARD')) { exit('非法访问'); }
return array(
	//db
	'db' => array(
		0 => array('host' => '127.0.0.1', 'port'=> 3306, 'username' => 'root', 'password' => '', 'dbname' => 'test', 'weight' => 100),
	),
	//memcached
	'memcache' => array(
		1 => array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 100)
	),
	//base
	'timezone' => 'Asia/Shanghai',

	//url
	'staticurl' => 'http://testzh.testzh.com/public/static',//灵活配置,适应静态资源分离
	'webhost' => 'http://testzh.testzh.com/index.php',//必须配置
	
	//domain
	'cookie' => array('expire' => time()+3600*24, 'path' => '/', 'domain' => '.testzh.com'),//可选配置,看有没有用到cookie

	//admin
	'adminhost' => 'http://testzh.testzh.com/admin/index.php',//必须配置

	//tools
	'tools' => array(
		'admin' => 'testzh',
		'passwd' => 'testzh',
	),
);
?>
