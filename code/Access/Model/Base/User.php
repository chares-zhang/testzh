<?php
/**
 * 用户表基本操作
 * 是否分表：N
 * 关系类型：1-1
 * @author chares
 *
 */
class Access_Model_Base_User extends AbstractModel
{
	/**
	 * 表名
	 */
	protected $_name = 'access_user';
	
	/**
	 * 模块名
	 */
	protected $_module = 'access';
	
	/**
	 * 是否为公共库,默认false.
	 */
	protected $_isCommon = false;
	
	/**
	 * 分表数量,若数量大于1,则表示需要分表,$_tableDivType='UID'时有用.
	 */
	protected $_tableDiv;
	
	/**
	 * 分表依据,只支持按 UID,MONTH,WEEK 分表,默认UID
	 */
	protected $_tableDivType = 'UID';
	
	/**
	 * 主键id
	 */
	protected $_primaryKey = 'uid';
	
	/**
	 * 是否使用缓存
	 */
	protected $_cachable = false;
	
	/**
	 * 通过主键获取单个用户
	 */
	public function getUserRow($uid)
	{
		return $this->load($uid);
	}
	
	/**
	 * 通过用户名获取单个用户
	 */
	public function getUserRowByName($username)
	{
		return $this->load($username,'username');
	}
	
	/**
	 * 新增用户
	 * 新增单条记录
	 * @param array $data
	 * @return boolean
	 */
	public function addUserRow($data)
	{
		$data['my_created'] = date('Y-m-d H:i:s');
		$data['my_updated'] = date('Y-m-d H:i:s');
		$res = $this->insertItem($data);
		return $res ? true : false;
	}

	/**
	 * 更新用户信息
	 * 更新单条记录
	 * @param unknown $data
	 * @param unknown $uid
	 */
	public function updateUserRow($data,$uid)
	{
		$data['my_updated'] = date('Y-m-d H:i:s');
		$where = "uid = '{$uid}'";
		$this->updateItem($data, $where);
		return $uid;
	}
	
	
}
