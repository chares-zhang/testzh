<?php

class Core_TestController extends Core_BaseController 
{

	public function indexAction() 
	{
		echo "test success!";

	}

	public function testAction() {
		$this->redirect('*/*/index');
//		include $this->template('test');
	}
}
