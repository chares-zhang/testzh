<?php
/**
 * 返回类
 */
class Response {
	private $_instance;
	private $headers = array();
	private $output;
	private $level = 0;
	
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
	}
	
	public function toJson()
	{
		$this->output = json_encode($this->output);
		return $this;
	}
	
}