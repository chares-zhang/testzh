<?php
class Top_Exception extends Exception
{
	/**
	 * 接口返回值,json格式
	 * 
	 * @var unknown
	 */
	protected $_tbResp;
	
	/**
	 * 错误类型分三种
	 * HTTP_ERROR http请求失败
	 * FORMAT_ERROR  格式错误
	 * API_ERROR 正常返回，但是报错
	 * @var unknown
	 */
	protected $_errorType;
	
	/**
	 * 返回请求的url
	 * @var unknown
	 */
	protected $_requestUrl;
	
	/**
	 * 返回请求的api参数 ,json格式
	 * @var unknown
	 */
	protected $_apiParams;
	
	/**
	 * 返回接口名
	 * @var string
	 */
	protected $_apiMethod;
	
	/**
	 * 返回的code 
	 * @var unknown
	 */
	protected $_code;
	
	/**
	 * 返回的msg
	 * @var unknown
	 */
	protected $_msg;
	
	/**
	 * 返回的sub_code
	 * @var unknown
	 */
	protected $_subCode;
	
	/**
	 * 返回的sub_msg
	 * @var unknown
	 */
	protected $_subMsg;
	
    public function setTbResp($tbResp)
	{
		$this->_tbResp = $tbResp;
		return $this;
	}
	
	public function setErrorType($errorType)
	{
		$this->_errorType = $errorType;
		return $this;
	}
	
	public function setRequestUrl($requestUrl)
	{
		$this->_requestUrl = $requestUrl;
		return $this;	
	}
	
	public function setApiParams($apiParams)
	{
		$this->_apiParams = $apiParams;
		return $this;
	}
	
	public function setApiMethod($apiMethod)
	{
		$this->_apiMethod = $apiMethod;
		return $this;
	}
	
	public function setTbCode($code)
	{
		$this->_code = $code;
		return $this;	
	}
	
	public function setTbMsg($msg)
	{
		$this->_msg = $msg;
		return $this;	
	}
	
	public function setSubCode($subCode)
	{
		$this->_subCode = $subCode;
		return $this;
	}
	
	public function setSubMsg($subMsg)
	{
		$this->_subMsg = $subMsg;
		return $this;
	}
	
	public function getTbResp()
	{
		return $this->_tbResp;	
	}
	
	public function getErrorType()
	{
		return $this->_errorType;	
	}

	public function getRequestUrl()
	{
		return $this->_requestUrl;
	}
	
	public function getApiParams()
	{
		return $this->_apiParams;
	}
	
	public function getApiMethod()
	{
		return $this->_apiMethod;
	}
	
	public function getTbCode()
	{
		return $this->_code;
	}
	
	public function getTbMsg()
	{
		return $this->_msg;
	}
	
	public function getSubCode()
	{
		return $this->_subCode;
	}
	
	public function getSubMsg()
	{
		return $this->_subMsg;
	}
	
	public function __toString()
	{
		return parent::__toString();
	}
}
