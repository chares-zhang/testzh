<?php

class Core_BaseController extends AbstractController
{
	public function __construct()
	{
		parent::__construct();
		$platName = Common::getPlatName();
		$uid = Access_Model_Factory::factory($platName)->getLoginUid();
		$userM = Common::getPlatModel('access/user');
		$userRow = $userM->getUserRow($uid);
		if (!empty($userRow)) {
			Common::setUserInfo($userRow);
		}
	}
}
