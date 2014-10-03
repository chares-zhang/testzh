<?php
/**
 * 配置文件
 * 1.支持多平台(数据库按照平台分组)
 * 2.支持分表分库
 * 
 */
if (!defined('GUARD')) { exit('非法访问'); }
	//各个实例的数据库信息
	return array(
		/*****微博平台 start*****/
		'plat_name_weibo' => array(
			//模块 - 库名
			'module_dbname' => array(
				'core' => 'testzh',
			),
			//模块 - 实例名
			'module_instance' => array(
				//水平分库sharding_id=0(分表依据uid)
				'sharding_id_0' => array(
					'core' => 'MYDB_1',
					'access' => 'MYDB_1',
				),
				//水平分库sharding_id=0(分表依据uid)
				'sharding_id_1' => array(
					'core' => 'MYDB_1'
				),
				//公共,水平分库时可以拆出来,特点:与分表依据(uid)无关的数据放在这里面.
				'sharding_id_common' => array(
					'core' => 'MYDB_1',
				),
				
			)
		),
		/*****微博平台 end*****/
		/*****腾讯平台 begin*****/
		'plat_name_qq' => array(
			'module_dbname' => array(
				'core' => 'testzh',
				'access' => 'testzh',
			),
			'module_instance' => array(
				//水平分库sharding_id=0(分表依据uid)
				'sharding_id_0' => array(
					'core' => 'MYDB_1',
					'access' => 'MYDB_1',
				),
				//水平分库sharding_id=0(分表依据uid)
				'sharding_id_1' => array(
				),
				//公共,水平分库时可以拆出来,特点:与分表依据(uid)无关的数据放在这里面.
				'sharding_id_common' => array(
					'core' => 'MYDB_1',
				),
				
			)
		),
		/*****腾讯平台 end*****/
		/*****淘宝平台 begin*****/
		'plat_name_taobao' => array(
				'module_dbname' => array(
					'core' => 'testzh',
					'access' => 'testzh',
				),
				'module_instance' => array(
					//水平分库sharding_id=0(分表依据uid)
					'sharding_id_0' => array(
						'core' => 'MYDB_1',
						'access' => 'MYDB_1',
					),
					//水平分库sharding_id=0(分表依据uid)
					'sharding_id_1' => array(
					),
					//公共,水平分库时可以拆出来,特点:与分表依据(uid)无关的数据放在这里面.
					'sharding_id_common' => array(
						'core' => 'MYDB_1',
					),
	
				)
		),
		/*****淘宝平台 end*****/
		/*****腾讯拍拍 begin*****/
		'plat_name_paipai' => array(
				'module_dbname' => array(
					'core' => 'testzh',
					'access' => 'testzh',
				),
				'module_instance' => array(
					//水平分库sharding_id=0(分表依据uid)
					'sharding_id_0' => array(
						'core' => 'MYDB_1',
						'access' => 'MYDB_1',
					),
					//水平分库sharding_id=0(分表依据uid)
					'sharding_id_1' => array(
					),
					//公共,水平分库时可以拆出来,特点:与分表依据(uid)无关的数据放在这里面.
					'sharding_id_common' => array(
						'core' => 'MYDB_1',
					),
		
				)
		),
		/*****腾讯拍拍 end*****/
	
	);
?>
