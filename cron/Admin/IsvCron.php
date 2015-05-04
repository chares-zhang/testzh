<?php
/**
 * 获取Isv信息
 *
 */
set_time_limit(0);
ini_set('memory_limit', '1024M');
error_reporting(E_ALL | E_STRICT);
require_once dirname(__FILE__) . '/AbstractCron.php';

class IsvCron extends AbstractCron
{
    public function run()
    {
		//基本信息抓取
//		$this->getBaseInfo();
		//获取应用的详细信息
		$this->getDetailInfo();
		//跟踪应用的信息
//		$this->setIsvTrace();
    }

	//基本信息抓取
	public function getBaseInfo()
	{
		$start = $this->microtime_float();
		//基本信息抓取
		$urls = array(
			'http://fuwu.taobao.com/ser/list.htm?key=&service_type=&free_order=&is_tpartner=&isMultiList=0&listType=1&cat_map_id=50105726&primary_sort=default_sort_tag&primary_sort_desc=true&se_wangwang_status=&location_id=&current=0&current_page=', //店铺基础服务 【店铺/装修(24)】
			'http://fuwu.taobao.com/ser/list.htm?key=&service_type=&free_order=&is_tpartner=&isMultiList=0&listType=1&cat_map_id=50047508&primary_sort=default_sort_tag&primary_sort_desc=true&se_wangwang_status=&location_id=&current=0&current_page=', //营销推广 【促销管理(217)】
			'http://fuwu.taobao.com/ser/list.htm?key=&service_type=&free_order=&is_tpartner=&isMultiList=0&listType=1&cat_map_id=50042470&primary_sort=default_sort_tag&primary_sort_desc=true&se_wangwang_status=&location_id=&current=0&current_page=', //店铺管理 【商品管理(266) 】
			'http://fuwu.taobao.com/ser/list.htm?key=&service_type=&free_order=&is_tpartner=&isMultiList=0&listType=1&cat_map_id=50105775&primary_sort=default_sort_tag&primary_sort_desc=true&se_wangwang_status=&location_id=&current=0&current_page=', //客服绩效管理 【客户服务(71)】
			'http://fuwu.taobao.com/ser/list.htm?key=&service_type=&free_order=&is_tpartner=&isMultiList=0&listType=1&cat_map_id=50042461&primary_sort=default_sort_tag&primary_sort_desc=true&se_wangwang_status=&location_id=&current=0&current_page=', //数据分析	【行业/店铺分析(35)】
			'http://fuwu.taobao.com/ser/list.htm?key=&service_type=&free_order=&is_in_activity=&is_mix_sale=&is_tpartner=&isMultiList=1&listType=0&cat_map_id=51048019&primary_sort=user_count&primary_sort_desc=true&se_wangwang_status=&location_id=&current=0&current_page=',//【流量推广(243)】
			'http://fuwu.taobao.com/ser/list.htm?key=&service_type=&free_order=&is_in_activity=&is_mix_sale=&is_tpartner=&isMultiList=1&listType=0&cat_map_id=51042015&primary_sort=user_count&primary_sort_desc=true&se_wangwang_status=&location_id=&current=0&current_page=',//【订单管理(259)】
			'http://fuwu.taobao.com/ser/list.htm?key=&service_type=&free_order=&is_in_activity=&is_mix_sale=&is_tpartner=&isMultiList=0&listType=1&cat_map_id=51032021&primary_sort=user_count&primary_sort_desc=true&se_wangwang_status=&location_id=&current=0&current_page=',//【客户关系管理(125)】
			'http://fuwu.taobao.com/ser/list.htm?key=&service_type=&free_order=&is_in_activity=&is_mix_sale=&is_tpartner=&isMultiList=0&listType=1&cat_map_id=50064621&primary_sort=user_count&primary_sort_desc=true&se_wangwang_status=&location_id=&current=0&current_page=',//【移动应用(49)】
		);

		$num = $newUserNum = $newAppNum = 0;
		$spiderM = Common::getModel('Spider');
		$isvRuleM = Common::getModel('admin/isvRule');
		$topUserM = Common::getModel('admin/topUser');
		$topUserAppM = Common::getModel('admin/topUserApp');
		
		foreach($urls as $urlPath){
			$pages = range(1,30);
			
			$n = 0;
			foreach($pages as $page){
				$url = $urlPath . $page;
				$contents = $spiderM::getHtml($url);
				$dataRows = $isvRuleM->getIsv($contents);
				foreach($dataRows as $data){
					//echo $n."::".$num++."\n";
					$num++;
					//插入 top_user 表
					$userData = $appData = array();
					$userData['nick'] = $data['nick'];
					$userData['isv_name'] = $data['isv_name'];
					$userRow = $topUserM->getUserByNick($userData['nick']);
					if(!empty($userRow)){
						$where = "nick = '".$userData['nick']."'";
						$res = $topUserM->setTopUser($userData,$where);
						$userId = $userRow['user_id'];
					}else{
						$userData['pwd'] = md5('taobaoisv');
						$userId = $topUserM->addTopUser($userData);
						$newUserNum++;
					}
					//插入 top_user_app 表
					$appData = $data;
					unset($appData['nick']);//去掉nick节点
					unset($appData['isv_name']);//去掉nick节点
					$appData['user_id'] = $userId; //加入user_id节点
					$appRow = $topUserAppM->getRowByServiceCode($appData['service_code']); //取不到时,返回false
					if(!empty($appRow)){
						$where = "service_code = '".$appData['service_code']."'";
						$res = $topUserAppM->setTopUserApp($appData,$where);
					}else{
						$res = $topUserAppM->addTopUserApp($appData);
						$newAppNum++;
					}
					echo $num.":".$appData['service_code']."\n";
				}
				$n++;
				//echo $n."\n";
			}
		}

		$timespan = $this->microtime_float() - $start;
		$log = date('Y-m-d H:i:s')." : Update Complete!; New User $newUserNum rows; New App $newAppNum rows; Total $num rows; runtime:$timespan";
        Util::log('cron_ivs',$log, 1);
	}
	
	//获取应用的详细信息
	public function getDetailInfo()
	{
		$start = $this->microtime_float();
		
		$spiderM = Common::getModel('Spider');
		$isvRuleM = Common::getModel('admin/isvRule');
		$topUserM = Common::getModel('admin/topUser');
		$topUserAppM = Common::getModel('admin/topUserApp');
		
		//获取应用的详细信息
		unset($appRow);
		unset($appRows);
		$appRows = $topUserAppM->getTopUserAppRows();
		foreach($appRows as $key => $appRow){
			$url = $appRow['detail_url'];
			$contents = $spiderM::getHtml($url);
			$appDetail = array();
			$appDetail = $isvRuleM->getAppDetail($contents);
			//更新user
			$userData = array();
			if(isset($appDetail['tel'])){
				$userData['tel'] = $appDetail['tel'];
				$where = "user_id = ".$appRow['user_id'];
				$retUser = $topUserM->setTopUser($userData,$where);
			}
			//更新app
			$appData = $appDetail;
			unset($appData['tel']);
			$where = "service_code = '".$appRow['service_code']."'";
			$res = $topUserAppM->setTopUserApp($appData,$where);
	 		echo $appRow['service_code']."\n";

		}
		$timespan = $this->microtime_float() - $start;
		$log = date('Y-m-d H:i:s')." : Update Detail Complete! runtime:$timespan;\n";
		Util::log('cron_ivs',$log, 1);
	}

	//跟踪应用的参数
	public function setIsvTrace()
	{
		$start = $this->microtime_float();
		
		$date = date('Y-m-d');
		$yestoday = date('Y-m-d',strtotime($date)-86400);

		$topUserM = Common::getModel('admin/topUser');
		$topUserAppM = Common::getModel('admin/topUserApp');
		$topIsvTraceM = Common::M('admin/topIsvTrace');

		//跟踪应用的参数
		unset($appRow);
		unset($appRows);
		$userAppRows = $topUserAppM->getTopUserAppRows();
		$num = 0;
		foreach($userAppRows as $userAppRow){
			$num++;
			$ivsTraceData = array(
				'time' => $date,
				'app_name' => $userAppRow['app_name'],
				'service_code' => $userAppRow['service_code'],
				'cat_map_id' => $userAppRow['cat_map_id'],
				'score' => $userAppRow['score'],
				'comment' => $userAppRow['comment'],
				'free_num' => $userAppRow['free_num'],
				'pay_num' => $userAppRow['pay_num'],
				'total_num' => $userAppRow['total_num'],
				'browse_num' => $userAppRow['browse_num'],
				'yiyong' => $userAppRow['yiyong'],
				'taidu' => $userAppRow['taidu'],
				'wending' => $userAppRow['wending'],
				'source_modified' => $userAppRow['modified'],
			);
			
			//计算新增
			$where = "time = '{$yestoday}' and service_code = '{$ivsTraceData['service_code']}'";
			$ivsTraceYestodayArray = $topIsvTraceM->loadByWhere($where);
			if(!empty($ivsTraceYestodayArray)){
				$ivsTraceData['comment_new'] = $userAppRow['comment'] - $ivsTraceYestodayArray['comment'];
				$ivsTraceData['free_num_new'] = $userAppRow['free_num'] - $ivsTraceYestodayArray['free_num'];
				$ivsTraceData['pay_num_new'] = $userAppRow['pay_num'] - $ivsTraceYestodayArray['pay_num'];
				$ivsTraceData['browse_num_new'] = $userAppRow['browse_num'] - $ivsTraceYestodayArray['browse_num'];
			}else{
				$ivsTraceData['comment_new'] = $userAppRow['comment'];
				$ivsTraceData['free_num_new'] = $userAppRow['free_num'];
				$ivsTraceData['pay_num_new'] = $userAppRow['pay_num'];
				$ivsTraceData['browse_num_new'] = $userAppRow['browse_num'];
			}
			
			//保存
			$where = "time = '{$date}' and service_code = '{$ivsTraceData['service_code']}'";
			$ivsTraceArray = $topIsvTraceM->loadByWhere($where);
			
			if(empty($ivsTraceArray)){//当天没数据的，则新增
				$res = $topIsvTraceM->addTopIsvTrace($ivsTraceData);
			}else{//当天已经有数据了，则更新.
				$where = "id = '{$ivsTraceArray['id']}'";
				$topIsvTraceM->setTopIsvTrace($ivsTraceData,$where);
			}

	 		echo $userAppRow['service_code']."\n";
		}

		$timespan = $this->microtime_float() - $start;
		$log = date('Y-m-d H:i:s')." : Update Trace Complete! runtime:$timespan;\n";
		Util::log('cron_ivs',$log, 1);
	}
}

// 设置数据在数据库中的位置
//$db = $argc > 1 ? $argv[1] : 0;

$obj = new IsvCron();
$obj->run();
