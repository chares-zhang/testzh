<?php
/**
 * ui
 * @author chares
 *
 */
class Core_UiController extends Core_BaseController 
{

	public function wquiAction() 
	{
		$this->loadLayout('core/ui_wqui');
		$this->renderLayout();
	}

	public function qnuiAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function suiAction()
	{
		$this->loadLayout('core/ui_sui');
		$this->renderLayout();
	}

	public function myuiAction()
	{
		$this->loadLayout('core/ui_myui');
		$this->renderLayout();
	}
}
