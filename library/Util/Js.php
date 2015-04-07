<?php
/**
 * js相关组件
 * @author mingyue
 *
 */

class Util_Js
{
	protected $_jsUrl = array();
	
    /**
     * Retrieve framed javascript
     *
     * @param   string $script
     * @return  script
     */
    public function getScript($script)
    {
        return '<script type="text/javascript">'.$script.'</script>';
    }

    /**
     * Retrieve javascript include code
     *
     * @param   string $file
     * @return  string
     */
    public function includeScript($file)
    {
        return '<script type="text/javascript" src="'.$this->getJsUrl($file).'"></script>'."\n";
    }

    /**
     * set module body js url
     *
     * @param unknown_type $file
     * @param unknown_type $module
     */
    public function setJsUrl($file,$module = null){
    	if(!$module){
    		$module = Dispatcher::getInstance()->getModule();
    	}
    	$this->_jsUrl[$module] = $file;
    }

    /**
     * Retrieve JS file url
     *
     * @param   string $file
     * @return  string
     */
    public function getJsUrl($module = null)
    {
       	if(!$module){
    		$module = Dispatcher::getInstance()->getModule();
    	}
    	return isset($this->_jsUrl[$module])?$this->_jsUrl[$module]:'';
    }
}
