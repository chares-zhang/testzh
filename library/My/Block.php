<?php
abstract class My_Block
{
	protected $module;
	protected $controller;
	protected $action;
	protected $template;
	protected $layout;

    /**
     * Internal constructor, that is called from real constructor
     *
     * Please override this one instead of overriding real __construct constructor
     *
     */
    protected function __construct()
    {
    	$this->module = Dispatcher::getInstance()->getModule();
		$this->controller = Dispatcher::getInstance()->getController();
		$this->action = Dispatcher::getInstance()->getAction();
    }
    
    public function getBlockHtml($name)
    {
    	if ($name == 'content') {
    		$config = Common::getConfig();
    		if (isset($config[$name]) && !empty($config[$name])) {
    			$blockName = $config[$name];
    		} else {
    			$blockName = $this->module . '/' . $this->controller . '_' . $this->action;
    		}
    		$block = Common::getBlock($blockName);
    	} else {
    		$block = Common::getBlock($name);
    	}
    	return $block -> toHtml(); 
    }
    
    public function toHtml()
    {
    	return '';
    }
    
//     public function loadLayout()
//     {
//     	$config = Common::getMainInfo();
//     	$blockName = $config['layout_template'];
//     	$block = Common::getBlock($blockName);
//     	$this->layout = $block;
//     }
    
//     public function getBlockHtml($name)
//     {
//     	if (!($layout = $this->getLayout()) && !($layout = Hlg::app()->getFrontController()->getAction()->getLayout())) {
//     		return '';
//     	}
//     	if (!($block = $layout->getBlock($name))) {
//     		return '';
//     	}
//     	return $block->toHtml();
//     }
    
// 	/**
//      * Assign variable
//      *
//      * @param   string|array $key
//      * @param   mixed $value
//      * @return  Base_Core_Block_Template
//      */
//     public function assign($key, $value=null)
//     {
//         if (is_array($key)) {
//             foreach ($key as $k=>$v) {
//                 $this->assign($k, $v);
//             }
//         }
//         else {
//             $this->_viewVars[$key] = $value;
//         }
//         return $this;
//     }

// 	/**
//      * Get base url of the application
//      *
//      * @return string
//      */
//     public function getBaseUrl()
//     {
//         if (!$this->_baseUrl) {
//             $this->_baseUrl = Hlg::getBaseUrl();
//         }
//         return $this->_baseUrl;
//     }

//     /**
//      * Get url of base javascript file
//      *
//      * To get url of skin javascript file use getSkinUrl()
//      *
//      * @param string $fileName
//      * @return string
//      */
//     public function getJsUrl($fileName='')
//     {
//         if (!$this->_jsUrl) {
//             $this->_jsUrl = Hlg::getBaseUrl('js');
//         }
//         return $this->_jsUrl.$fileName;
//     }
    
//     /**
//      * Retrieve request object
//      *
//      * @return Core_Controller_Request_Http
//      */
//     public function getRequest()
//     {
//     	$controller = Hlg::app()->getFrontController();
//         if ($controller) {
//             $this->_request = $controller->getRequest();
//         } else {
//             Hlg::throwSystemException(Hlg::helper('core')->__("Can't retrieve request object"));
//         }
//         return $this->_request;
//     }
    
// 	/**
//      * Set redirect into responce
//      *
//      * @param   string $path
//      * @param   array $arguments
//      */
//     protected function _redirect($path, $arguments=array())
//     {
//     	//exit(Hlg::getUrl($path, $arguments))
//         $this->getResponse()->setRedirect(Hlg::getUrl($path, $arguments));
//         return $this;
//     }

//     /**
//      * Set block's name in layout and unsets previous link if such exists.
//      *
//      * @param $name
//      * @return Mage_Core_Block_Abstract
//      */
//     public function setNameInLayout($name)
//     {
//         if (!empty($this->_nameInLayout) && $this->getLayout()) {
//             $this->getLayout()
//             ->unsetBlock($this->_nameInLayout)
//             ->setBlock($name, $this);
//         }
//         $this->_nameInLayout = $name;
//         return $this;
//     }

//     /**
//      * Set block attribute value
//      *
//      * Wrapper for method "setData"
//      *
//      * @param   string $name
//      * @param   mixed $value
//      * @return  Core_Block_Abstract
//      */
//     public function setAttribute($name, $value=null)
//     {
//         return $this->setData($name, $value);
//     }

//     /**
//      * Retrieve block html
//      *
//      * @param   string $name
//      * @return  string
//      */
//     public function getBlockHtml($name)
//     {
//         if (!($layout = $this->getLayout()) && !($layout = Hlg::app()->getFrontController()->getAction()->getLayout())) {
//             return '';
//         }
//         if (!($block = $layout->getBlock($name))) {
//             return '';
//         }
//         return $block->toHtml();
//     }
    
//     /**
//      * Before rendering html, but after trying to load cache
//      *
//      * @return Core_Block_Abstract
//      */
//     protected function _beforeToHtml()
//     {
//         return $this;
//     }

//     /**
//      * Produce and return block's html output
//      *
//      * It is a final method, but you can override _toHmtl() method in descendants if needed
//      *
//      * @return string
//      */
// //     final public function toHtml()
// //     {
// //         $html = $this->_loadCache();
// //         if (!$html) {
// //             $this->_beforeToHtml();
// //             $html = $this->_toHtml();
// //             $this->_saveCache($html);
// //         }
// //         $html = $this->_afterToHtml($html);

// //         return $html;
// //     }

//     /**
//      * Processing block html after rendering
//      *
//      * @param   string $html
//      * @return  string
//      */
//     protected function _afterToHtml($html)
//     {
//         return $html;
//     }

//     /**
//      * Override this method in descendants to produce html
//      *
//      * @return string
//      */
//     protected function _toHtml()
//     {
//         return '';
//     }

//     /**
//      * Enter description here...
//      *
//      * @return string
//      */
//     protected function _getUrlModelClass()
//     {
//         return 'core/url';
//     }

//     /**
//      * Enter description here...
//      *
//      * @return Core_Model_Url
//      */
//     protected function _getUrlModel()
//     {
//         return Hlg::getModel($this->_getUrlModelClass());;
//     }

//     /**
//      * Generate url by route and parameters
//      *
//      * @param   string $route
//      * @param   array $params
//      * @return  string
//      */
//     public function getUrl($route='', $params=array())
//     {
//         return $this->_getUrlModel()->getUrl($route, $params);
//     }

//     /**
//      * Generate base64-encoded url by route and parameters
//      *
//      * @param   string $route
//      * @param   array $params
//      * @return  string
//      */
//     public function getUrlBase64($route='', $params=array())
//     {
//         return Hlg::helper('core')->urlEncode($this->getUrl($route, $params));
//     }

//     /**
//      * Generate url-encoded url by route and parameters
//      *
//      * @param   string $route
//      * @param   array $params
//      * @return  string
//      */
//     public function getUrlEncoded($route = '', $params = array())
//     {
//         return Hlg::helper('core')->urlEncode($this->getUrl($route, $params));
//     }

//     /**
//      * Retrieve url of skins file
//      *
//      * @param   string $file path to file in skin
//      * @param   array $params
//      * @return  string
//      */
//     /*
//     public function getSkinUrl($file=null)
//     {
//         return Hlg::getCdnUrl().$file;
        
//     }
// 	*/

//     /**
//      * Retrieve messages block
//      *
//      * @return Core_Block_Messages
//      */
//     public function getMessagesBlock()
//     {
//         if (is_null($this->_messagesBlock)) {
//             return $this->getLayout()->getMessagesBlock();
//         }
//         return $this->_messagesBlock;
//     }

//     /**
//      * Set messages block
//      *
//      * @param   Core_Block_Messages $block
//      * @return  Core_Block_Abstract
//      */
//     public function setMessagesBlock(Core_Block_Messages $block)
//     {
//         $this->_messagesBlock = $block;
//         return $this;
//     }

//     /**
//      * Enter description here...
//      *
//      * @param string $type
//      * @return Core_Block_Abstract
//      */
//     public function getHelper($type)
//     {
//         return $this->getLayout()->getBlockSingleton($type);
//         //return $this->helper($type);
//     }

//     /**
//      * Enter description here...
//      *
//      * @param string $name
//      * @return Core_Block_Abstract
//      */
//     public function helper($name)
//     {
//         if ($this->getLayout()) {
//             return $this->getLayout()->helper($name);
//         }
//         return Hlg::helper($name);
//     }

//     /**
//      * Retrieve module name of block
//      *
//      * @return string
//      */
//     public function getModuleName()
//     {
//         $module = $this->getData('module_name');
//         if (is_null($module)) {
//             $class = get_class($this);
//             $module = substr($class, 0, strpos($class, '_Block'));
//             $this->setData('module_name', $module);
//         }
//         return $module;
//     }

//     /**
//      * Escape html entities
//      *
//      * @param   mixed $data
//      * @param   array $allowedTags
//      * @return  string
//      */
//     public function escapeHtml($data, $allowedTags = null)
//     {
//         return $this->helper('core')->escapeHtml($data, $allowedTags);
//     }

//     /**
//      * Wrapper for standart strip_tags() function with extra functionality for html entities
//      *
//      * @param string $data
//      * @param string $allowableTags
//      * @param bool $allowHtmlEntities
//      * @return string
//      */
//     public function stripTags($data, $allowableTags = null, $allowHtmlEntities = false)
//     {
//         return $this->helper('core')->stripTags($data, $allowableTags, $allowHtmlEntities);
//     }
    
//     /**
//      * Escape html entities in url
//      *
//      * @param string $data
//      * @return string
//      */
//     public function escapeUrl($data)
//     {
//         return $this->helper('core')->escapeUrl($data);
//     }

//     /**
//      * Escape quotes in java scripts
//      *
//      * @param mixed $data
//      * @param string $quote
//      * @return mixed
//      */
//     public function jsQuoteEscape($data, $quote = '\'')
//     {
//         return $this->helper('core')->jsQuoteEscape($data, $quote);
//     }

//     public function getNameInLayout()
//     {
//         return $this->_nameInLayout;
//     }

//     /**
//      * Prepare url for save to cache
//      *
//      * @return Core_Block_Abstract
//      */
//     protected function _beforeCacheUrl()
//     {
//     	/*
//         if (Hlg::app()->useCache(self::CACHE_GROUP)) {
//             Hlg::app()->setUseSessionVar(true);
//         }
// 		*/
//         return $this;
//     }

//     /**
//      * Replace URLs from cache
//      *
//      * @param string $html
//      * @return string
//      */
//     protected function _afterCacheUrl($html)
//     {
//     	/*
//         if (Hlg::app()->useCache(self::CACHE_GROUP)) {
//             Hlg::app()->setUseSessionVar(false);
//             Hlg_Profiler::start('CACHE_URL');
//             $html = Hlg::getSingleton('core/url')->sessionUrlVar($html);
//             Hlg_Profiler::stop('CACHE_URL');
//         }
// 		*/
//         return $html;
//     }
    
//     /**
//      * Get cache key informative items
//      * Provide string array key to share specific info item with FPC placeholder
//      *
//      * @return array
//      */
//     public function getCacheKeyInfo()
//     {
//         return array(
//             $this->getNameInLayout()
//         );
//     }
    
// 	/**
//      * Get Key for caching block content
//      *
//      * @return string
//      */
//     public function getCacheKey()
//     {
//         if (!$this->hasData('cache_key')) {
//             $this->setCacheKey($this->getNameInLayout());
//         }
//         return $this->getData('cache_key');
//     }

//     /**
//      * Get tags array for saving cache
//      *
//      * @return array
//      */
//     public function getCacheTags()
//     {
//         if (!$this->hasData('cache_tags')) {
//             $tags = array();
//         } else {
//             $tags = $this->getData('cache_tags');
//         }
//         $tags[] = self::CACHE_GROUP;
//         return $tags;
//     }

//     /**
//      * Get block cache life time
//      *
//      * @return int
//      */
//     public function getCacheLifetime()
//     {
//         if (!$this->hasData('cache_lifetime')) {
//             return null;
//         }
//         return $this->getData('cache_lifetime');
//     }

//     /**
//      * Enter description here...
//      *
//      * @return unknown
//      */
//     protected function _loadCache()
//     {
//         if (is_null($this->getCacheLifetime()) || !Hlg::app()->useCache(self::CACHE_GROUP)) {
//             return false;
//         }
//         return Hlg::app()->loadCache($this->getCacheKey());
//     }

//     /**
//      * Enter description here...
//      *
//      * @param unknown_type $data
//      * @return Core_Block_Abstract
//      */
//     protected function _saveCache($data)
//     {
//         if (is_null($this->getCacheLifetime()) || !Hlg::app()->useCache('block_html')) {
//             return false;
//         }
//         Hlg::app()->saveCache($data, $this->getCacheKey(), $this->getCacheTags(), $this->getCacheLifetime());
//         return $this;
//     }
}
