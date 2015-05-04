<?php
class Core_Model_Taobao extends TopClient 
{
	//淘宝API正式环境入口，可删除了。
	const GATEWAY_URL = "http://gw.api.taobao.com/router/rest";
	
	//淘宝API沙箱环境入口,可删除了。
	const SANDBOX_GATEWAY_URL = "http://gw.api.tbsandbox.com/router/rest";
	
	public function __construct() {
		$this->appkey = Common::getAppKey();
		$this->secretKey = Common::getAppSecret();
		$this->gatewayUrl = Common::getGatewayUrl();
	}
	
	public function execute($request, $session = null)
	{
		if($this->checkRequest) {
			try {
				$request->check();
			} catch (Exception $e) {
				$result->code = $e->getCode();
				$result->msg = $e->getMessage();
				return ;
			}
		}
		//组装系统参数
		$sysParams["app_key"] = $this->appkey;
		$sysParams["v"] = $this->apiVersion;
		$sysParams["format"] = $this->format;
		$sysParams["sign_method"] = $this->signMethod;
		$sysParams["method"] = $request->getApiMethodName();
		$sysParams["timestamp"] = date("Y-m-d H:i:s");
		$sysParams["partner_id"] = $this->sdkVersion;
		if (null != $session) {
			$sysParams["session"] = $session;
		}
		
		//获取业务参数
		$apiParams = $request->getApiParas();
		//签名
		$sysParams["sign"] = $this->generateSign(array_merge($apiParams, $sysParams));
		
		//系统参数放入GET请求串
		$requestUrl = $this->gatewayUrl . "?";
		foreach ($sysParams as $sysParamKey => $sysParamValue) {
			$requestUrl .= "$sysParamKey=" . urlencode($sysParamValue) . "&";
		}
		$requestUrl = substr($requestUrl, 0, -1);
		
		//发起HTTP请求
		try {
			$resp = $this->curl($requestUrl, $apiParams);
		} catch (Exception $e) {
// 			$this->logCommunicationError($sysParams["method"],$requestUrl,"HTTP_ERROR_" . $e->getCode(),$e->getMessage());
			$apiParamsJson = json_encode($apiParams);
			
			$te = new Top_Exception;
			$te->setErrorType('HTTP_ERROR');
			$te->setTbCode($e->getCode());
			$te->setTbMsg('HTTP_ERROR_'.$e->getMessage());
			$te->setApiMethod($sysParams['method']);
			$te->setRequestUrl($requestUrl);
			$te->setApiParams($apiParamsJson);
			throw $te;
		}
		
		//解析TOP返回结果
		$respWellFormed = false;
		if ("json" == $this->format) {
			$respObject = json_decode($resp);
			if (null !== $respObject) {
				$respWellFormed = true;
				foreach ($respObject as $propKey => $propValue) {
					$respObject = $propValue;
				}
			}
		} else if("xml" == $this->format) {
			$respObject = @simplexml_load_string($resp);
			if (false !== $respObject) {
				$respWellFormed = true;
			}
		}
		
		//返回的HTTP文本不是标准JSON或者XML，记下错误日志
		if (false === $respWellFormed) {
			$apiParamsJson = json_encode($apiParams);
			$tbResp = json_encode($respObject);
			
			$te = new Top_Exception;
			$te->setErrorType('FORMAT_ERROR');
			$te->setTbCode($e->getCode());
			$te->setTbMsg('HTTP_ERROR_'.$e->getMessage());
			$te->setApiMethod($sysParams['method']);
			$te->setRequestUrl($requestUrl);
			$te->setApiParams($apiParamsJson);
			$te->setTbResp($resp);
			
		}
		
		//如果TOP返回了错误码，记录到业务错误日志中
		if (isset($respObject->code)) {
			$apiParamsJson = json_encode($apiParams);
			$tbResp = json_encode($respObject);
			
			$te = new Top_Exception;
			$te->setErrorType('API_ERROR');
			$code = isset($respObject->code) ? (string)$respObject->code : '';
			$msg = isset($respObject->msg) ? (string)$respObject->msg : '';
			$subCode = isset($respObject->sub_code) ? (string)$respObject->sub_code : '';
			$subMsg = isset($respObject->sub_msg) ? (string)$respObject->sub_msg : '';
			$te->setTbCode($code);
			$te->setTbMsg($msg);
			$te->setSubCode($subCode);
			$te->setSubMsg($subMsg);
			$te->setApiMethod($sysParams['method']);
			$te->setRequestUrl($requestUrl);
			$te->setApiParams($apiParamsJson);
			$te->setTbResp($tbResp);
			throw $te;
		}
		
		return $respObject;
	}
	/**
	 *  重写TopClient::execute方法
	 *
	 * @param unknown_type $request
	 * @param unknown_type $session
	 * @return unknown
	 */
// 	public function execute($request, $session = null)
// 	{
// 		if($this->checkRequest) {
// 			try {
// 				//$request->check();
// 			} catch (Exception $e) {
// 				$result = new stdClass();
// 				$result->code = $e->getCode();
// 				$result->msg = $e->getMessage();
// 				return $result;
// 			}
// 		}
// 		if(strpos(__FILE__,'dev')){
// 			if(isset($_SERVER['SERVER_ADDR'])){
// 				//说明为浏览器访问
// 				$serverAddr = $_SERVER['SERVER_ADDR'];
// 				if($serverAddr == '223.4.57.27'){
// 					return $this->_proxy($request);
// 				}else{
// 					//28的线上测试机
// 					return $this->_execute($request,$session);
// 				}
// 			}
// 			//任务调用，则进行proxy转发至线上环境做调用
// 			return $this->_proxy($request);
// 		}else{
// 			return $this->_execute($request, $session);
// 		}
// 	}
	
	/**
	 * 直接执行淘宝接口
	 * @param unknown $request
	 * @param unknown $params
	 * @throws Ambigous <Exception, Top_Exception>
	 */
// 	public function _execute($request, $session = null)
// 	{
// 		if($this->checkRequest) {
// 			try {
// 				//$request->check();
// 			} catch (Exception $e) {
// 				$result = new stdClass();
// 				$result->code = $e->getCode();
// 				$result->msg = $e->getMessage();
// 				return $result;
// 			}
// 		}
	
// 		//组装系统参数
// 		$sysParams["app_key"] = $this->appkey;
// 		$sysParams["v"] = $this->apiVersion;
// 		$sysParams["format"] = $this->format;
// 		$sysParams["sign_method"] = $this->signMethod;
// 		$sysParams["method"] = $request->getApiMethodName();
// 		$sysParams["timestamp"] = date("Y-m-d H:i:s");
// 		$sysParams["partner_id"] = $this->sdkVersion;
// 		if (null != $session)
// 		{
// 			$sysParams["session"] = $session;
// 		}
	
// 		//获取业务参数
// 		$apiParams = $request->getApiParas();
	
// 		//签名
// 		$sysParams["sign"] = $this->generateSign(array_merge($apiParams, $sysParams));
	
// 		//系统参数放入GET请求串
// 		$requestUrl = $this->gatewayUrl . "?";
// 		foreach ($sysParams as $sysParamKey => $sysParamValue)
// 		{
// 			$requestUrl .= "$sysParamKey=" . urlencode($sysParamValue) . "&";
// 		}
// 		$requestUrl = substr($requestUrl, 0, -1);
// 		//重试逻辑
// 		$retryCount = $this->_retryCount;
// 		while($retryCount > 0) {
// 			//重试次数减一
// 			$retryCount--;
				
// 			//发起HTTP请求
// 			try {
// 				$resp = $respStr = $this->curl($requestUrl, $apiParams);
// 			} catch (Exception $e) {
// 				if ($retryCount == 0) {
// 					$subCode = $e->getCode();
// 					$subMsg = $e->getMessage();
// 					$e = new Top_Exception();
// 					$code = "top.http_error";
// 					$msg = "网络连接错误";
// 					$e->setRsp($requestUrl)
// 					->setTbCode($code)
// 					->setTbMsg($msg)
// 					->setSubCode($subCode)
// 					->setSubMsg($subMsg);
// 					$this->_errorLog($e, $apiParams);
// 					throw $e;
// 				}
// 				sleep(1);
// 				continue;
// 			}
			 
	
// 			//解析TOP返回结果
// 			$respWellFormed = false;
// 			if ("json" == $this->format) {
// 				$respObject = json_decode($resp);
// 				if (null !== $respObject)
// 				{
// 					$respWellFormed = true;
// 					foreach ($respObject as $propKey => $propValue)
// 					{
// 						$respObject = $propValue;
// 					}
// 				}
// 			} else if("xml" == $this->format) {
// 				$respObject = @simplexml_load_string($resp);
// 				if (false !== $respObject) {
// 					$respWellFormed = true;
// 				}
// 			}
	
// 			//返回的HTTP文本不是标准JSON或者XML，记下错误日志
// 			if (false === $respWellFormed) {
// 				// 重试次数是否用完
// 				if ($retryCount == 0) {
// 					$e = new Top_Exception();
// 					$code = "top.http_response_not_well_format";
// 					$msg = "淘宝返回数据格式不正确";
// 					$e->setRsp($respStr)->setTbCode($code)->setTbMsg($msg);
// 					$this->_errorLog($e, $apiParams);
// 					throw $e;
// 				}
// 				continue;
// 			}
	
// 			//如果TOP返回了错误码，记录到业务错误日志中
// 			if (isset($respObject->code)) {
// 				$e = new Top_Exception();
// 				$code = $respObject->code;
// 				$msg = $respObject->msg;
// 				$e->setRsp($respStr)->setTbCode($code)->setTbMsg($msg);
// 				if (isset($respObject->sub_code)) {
// 					$subCode = $respObject->sub_code;
// 					$e->setSubCode($subCode);
// 				}
// 				if (isset($respObject->sub_msg)) {
// 					$subMsg = $respObject->sub_msg;
// 					$e->setSubMsg($subMsg);
// 				}
// 				//记录所有业务错误
// 				$this->_errorLog($e, $apiParams);
	
// 				//业务逻辑出错都不重试，直接抛出让上层处理
// 				throw $e;
// 			}
				
// 			return $respObject;
// 		}
// 	}
	
	
	/**
	 * 淘宝过来的json会出现\t,\n字符不转义的，原样返回的情况，需要转义
	 * @param unknown $json
	 * @return mixed
	 */
	private function _jsonParse($json){
		$json = str_replace(array("\t","\n"),array("\\t","\\n"),$json);
		return $json;
	}
	
}
