<?php
/**
 * 返回类
 */
class Response {
	private static $_instance;
	private $headers = array();
	private $output;
	private $level = 0;
	
	public static function getInstance() {
		if(self::$_instance == null) {
			self::$_instance = new Response();
			return self::$_instance;
		} else {
			return self::$_instance;
		}
	}
	
	private function __construct() {
		
	}
	
	public function addHeader($key, $value) {
		$this->headers[$key] = $value;
	}
	 
	public function removeHeader($key) {
		if (isset($this->headers[$key])) {
			unset($this->headers[$key]);
		}
	}
	 
	public function redirect($url) {
		header('Location: ' . $url);
		exit;
	}
	 
	public function setOutput($output) {
		$this->output = $output;
		return $this;
	}
	 
	public function sendResponse() {
		$ouput = $this->output;
		
		if (!headers_sent()) {
			foreach ($this->headers as $key => $value) {
				header($key . ': ' . $value);
			}
		}
		echo $ouput;
		exit;
	}
	
	public function toJson()
	{
		$this->output = json_encode($this->output);
		return $this;
	}
	
	public function setError($errorMsg,$result=null)
	{
		$this->output = array(
			'success' => false,
			'error_msg' => $errorMsg,
			'result' => ($result !== null)?$result:""
		);
	}
	
	public function setResult($result)
	{
		$this->output = array(
			'success' => true,
			'error_msg' => '',
			'result' => $result
		);
	}
	
}