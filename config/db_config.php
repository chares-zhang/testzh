<?php
if (!defined('GUARD')) { exit('非法访问'); }
	//各个实例的数据库信息
	return array(
		/*****微博平台 start*****/
		//支持多平台
		'plat_name_weibo' => array(
			'module_dbname' => array(
				'core' => 'testzh',
			),
			'module_instance' => array(
				//水平分库sharding_id=0(分表依据uid)
				'sharding_id_0' => array(
					'core' => 'DB_1',
				),
				//水平分库sharding_id=0(分表依据uid)
				'sharding_id_1' => array(
				),
				//公共,水平分库时可以拆出来,特点:与分表依据(uid)无关的数据放在这里面.
				'sharding_id_common' => array(
					'core' => 'DB_1',
				),
				
			)
		),
		/*****微博平台 end*****/
		/*****腾讯平台 begin*****/
		'plat_name_qq' => array(
			'module_dbname' => array(
				'core' => 'testzh',
			),
			'module_instance' => array(
				//水平分库sharding_id=0(分表依据uid)
				'sharding_id_0' => array(
					'core' => 'DB_1',
				),
				//水平分库sharding_id=0(分表依据uid)
				'sharding_id_1' => array(
				),
				//公共,水平分库时可以拆出来,特点:与分表依据(uid)无关的数据放在这里面.
				'sharding_id_common' => array(
					'core' => 'DB_1',
				),
				
			)
		),
		/*****腾讯拍拍 end*****/
		
	
	);
?>
