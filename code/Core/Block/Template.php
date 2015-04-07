<?php

class Core_Block_Template extends My_Block 
{
// 	protected $module;
// 	protected $controller;
// 	protected $action;
	protected $template;
	
	/**
	 * controller中指定显示的模板,只能指定当前module的模板.
	 * @param string $filename 文件名(不含后缀).
	 * @param bool $common 是否为公共,true则指定根目录下,false则指定template/[module]/[controller]/$filename.pthml中的模板
	 */
	public function setTemplete($filename,$isCommon=false)
	{
		$theme = Common::getTheme();
		if ($isCommon) {
			if (strpos($filename, '/')===false) {
				$this->template = TEMPLATE_PATH . DS . $theme . DS . "{$filename}.phtml";
			} else {
				$fileArr = explode('/', $filename);
				$filename = str_replace(' ', DS,(implode(' ',$fileArr)));
				$this->template = TEMPLATE_PATH . DS . $theme . DS . "{$filename}.phtml";
			}
		} else {
			$this->template = TEMPLATE_PATH . DS . $theme . DS . strtolower($this->module . DS . $this->controller . DS . "{$filename}.phtml");
		}
	}
	
	public function toHtml()
	{
		ob_start();
		
		try {
			include $this->template;
		} catch (Exception $e) {
			ob_get_clean();
			throw $e;
		}
		
		$html = ob_get_clean();
		
		return $html;
	}
	
}
