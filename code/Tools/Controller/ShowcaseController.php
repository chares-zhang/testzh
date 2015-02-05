<?php
/**
 * 自动橱窗
 * @author chares
 *
 */
class Tools_ShowcaseController extends Core_BaseController 
{
	public function indexAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function recommendAction() 
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function index_bakAction() 
	{
		//echo "uid:".$this->uid;
		$userRow = $this->userRow;
		
//		$taobao = new TopClient();
		
		if (isset($_POST['uid']) && $_POST['uid']) {
			$this->_indexValid($_POST);
			$uid = $_POST['uid'];
			
//  `uid` int(11) NOT NULL DEFAULT '0',
//  `keywords` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
//  `type` enum('1','2','3','4') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1' COMMENT '推荐方式',
//  `seller_cids` varchar(1024) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '分类',
//  `num_min` int(11) NOT NULL DEFAULT '5' COMMENT '剩余商品数量低于多少不推荐',
//  `enable` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT '是否开启,0关闭,1开启',
//  `open_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最近一次开启服务的时间',
//  `close_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最近一次关闭服务的时间',
//  `begin_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '任务开始时间',
//  `finish_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '任务结束时间',
//  `status` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT '0不在队列中，1在队列中',
//  `showcase_num` int(10) NOT NULL DEFAULT 0 COMMENT '橱窗数量',
//  `update_num_time` datetime NOT NULL DEFAULT 0 COMMENT '同步橱窗数量的时间',
//  `showcase_error` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
//  `created` int(10) NOT NULL COMMENT '创建时间',
//  `updated` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
			

			$sellerCids = (isset($_POST['seller_cids']) && !empty($_POST['seller_cids'])) ? implode(',',$_POST['seller_cids']) : '';
			$configData = array(
				'uid' => $uid,
				'keywords' => isset($_POST['keywords']) ? $_POST['keywords'] : '',
				'type' => $_POST['type'],
				'seller_cids' => $sellerCids,
				'num_min' => isset($_POST['num_min']) ? $_POST['num_min'] : '',
				'enable' => isset($_POST['enable']) ? $_POST['enable'] : '0',
				'keywords' => isset($_POST['keywords']) ? $_POST['keywords'] : '',

			);
			$showcaseConfigM = Common::M('Tools_ShowcaseConfig');
			$showcaseConfigRow = $showcaseConfigM->load($uid);
			if (!empty($showcaseConfigRow)) {//更新
				$where = "id = '{$showcaseConfigRow['id']}'";
				$showcaseConfigM->setRow($configData,$where,$uid);
			} else {//增加
				$showcaseConfigM->addRow($configData,$uid);
			}
			
		}

		$this->assign('userRow',$userRow);
		include $this->template('index');
	}

	//index提交验证.
	private function _indexValid($params)
	{
		//uid 不能为空.
		if (!isset($params['uid'])) {
			throw new Exception("params uid needed");
		}

		//type 不能为空.
		if (!isset($params['type'])) {
			throw new Exception("params type needed");
		}

		//uid不能为空.
		if (!isset($params['uid'])) {
			throw new Exception("params uid needed");
		}

	}
	
}

