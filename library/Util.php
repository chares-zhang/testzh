<?php
class Util
{
    static private $_datetime;
   
	static public function getTimeMs(){
		$time = explode ( " ", microtime () );  
		$time = $time[1] . ($time[0] * 1000);
		$time2 = explode ( ".", $time );  
		$time = $time2 [0]; 
		return $time;
	}

    static public function getMicroTime()
    {
        $a = explode(' ', microtime());
        return $a[0] + $a[1];
    }
    
    /**
     * @return string
     */
    static public function datetime()
    {
        if(!self::$_datetime) {
            self::$_datetime = date('Y-m-d H:i:s', time());
        }
        return self::$_datetime;
    }
    
    /**
     * @return int
     * Enter description here ...
     */
    static public function timestamp()
    {
    	return time();
    }


    /**
     * @return string
     */
    static public function ip()
    {
        if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $onlineip = getenv('HTTP_CLIENT_IP');
        } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $onlineip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $onlineip = getenv('REMOTE_ADDR');
        } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $onlineip = $_SERVER['REMOTE_ADDR'];
        }
        return $onlineip;
    }
    
    /**
     * @TODO
     * @param string $url
     * @param array  $data
     * @return mixed
     */
    static public function post($url, $params)
    {
        $str = '';
        foreach ($params as $k=>$v) {
            if (is_array($v)) {
                foreach ($v as $kv => $vv) {
                    $str .= '&' . $k . '[' . $kv  . ']=' . urlencode($vv);
                }
            } else {
                $str .= '&' . $k . '=' . urlencode($v);
            }
        }
		$str = substr($str, 1);
        if (function_exists('curl_init')) {
            // Use CURL if installed...
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $str);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'DZG API PHP Client 1.0 (curl) ' . phpversion());
            $result = curl_exec($ch);
            $errno = curl_errno($ch);
            curl_close($ch);
            return array($errno, $result);
        } else {
            // Non-CURL based version...
            $context =
            array('http' =>
                    array('method' => 'POST',
                        'header' => 'Content-type: application/x-www-form-urlencoded'."\r\n".
                                    'User-Agent: DZG API PHP Client 1.0 (non-curl) '.phpversion()."\r\n".
                                    'Content-length: ' . strlen($str),
                        'content' => $str));
            $contextid = stream_context_create($context);
            $sock = fopen($url, 'r', false, $contextid);
            if ($sock) {
                $result = '';
                while (!feof($sock)) {
                    $result .= fgets($sock, 4096);
                }
                fclose($sock);
            }
        }
        return array(0, $result);
    }
    
	static public function postStr($url, $str)
    {
        if (function_exists('curl_init')) {
            // Use CURL if installed...
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $str);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'DZG API PHP Client 1.0 (curl) ' . phpversion());
            $result = curl_exec($ch);
            $errno = curl_errno($ch);
            curl_close($ch);
            return array($errno, $result);
        } else {
            // Non-CURL based version...
            $context =
            array('http' =>
                    array('method' => 'POST',
                        'header' => 'Content-type: application/x-www-form-urlencoded'."\r\n".
                                    'User-Agent: DZG API PHP Client 1.0 (non-curl) '.phpversion()."\r\n".
                                    'Content-length: ' . strlen($str),
                        'content' => $str));
            $contextid = stream_context_create($context);
            $sock = fopen($url, 'r', false, $contextid);
            if ($sock) {
                $result = '';
                while (!feof($sock)) {
                    $result .= fgets($sock, 4096);
                }
                fclose($sock);
            }
        }
        return array(0, $result);
    }

    /**
     * 日志
     * 
     * @param $filename
     * @param $content
     */
    static public function log($filename, $content, $output = false)
    {
        if(is_object($content) || is_array($content)) {
            $content = var_export($content, 1);
        }
        $content = Util::datetime() . '::' . $content . "\n";
        if($output) echo $content;
        @file_put_contents(LOGS_PATH . '/' . $filename . '.log', $content, FILE_APPEND);
    }
    
    /**
     * 错误日志
     * 
     * @param $filename
     * @param $content
     */
    static public function err($filename, $content, $output = false)
    {
        if(is_object($content) || is_array($content)) {
            $content = var_export($content, 1);
        }
        $content = Util::datetime() . '::' . $content . "\n";
        if($output) echo $content;
        @file_put_contents(LOGS_PATH . '/' . $filename . '.err', $content, FILE_APPEND);
    }

	/**
	 * firephp 调试函数，主要用于线上调试。输出firebug 控制台。
	 * @param mixed $vars
	 */
	static public function firelog($vars){
        $writer = new Zend_Log_Writer_Firebug();
        $logger = new Zend_Log($writer);

        $request = new Zend_Controller_Request_Http();
        $response = new Zend_Controller_Response_Http();
        $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
        $channel->setRequest($request);
        $channel->setResponse($response);

        // Start output buffering
        ob_start();

        // Now you can make calls to the logger
        $logger->log($vars, Zend_Log::INFO);

        // Flush log data to browser
        $channel->flush();
        $response->sendHeaders();
	}

	/*
	 * xhprof-0.9.2性能跟踪
	 */
	static public function XHProf()
	{
		if (function_exists('xhprof_enable')) {
			//if (mt_rand(1, 10000) == 1) {
				//xhprof_enable();
				//xhprof_enable(XHPROF_FLAGS_NO_BUILTINS);
				xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);

				//register_shutdown_function('shut_down');
			//}
		}
	}

	static public function shut_down()
	{
        if (function_exists('xhprof_enable')) {
            $now = date('Y-m-d H:i:s');
		    $xhprof_data = xhprof_disable();

		    $xhprof_root = dirname(__FILE__) . '/../public/tools/xhprof/';
		    include_once $xhprof_root . 'xhprof_lib/utils/xhprof_lib.php';
		    include_once $xhprof_root . 'xhprof_lib/utils/xhprof_runs.php';
            require_once dirname(__FILE__) . '/../config.php';

		    $xhprof_runs = new XHProfRuns_Default();
		    $run_id = $xhprof_runs->save_run($xhprof_data, 'train');

            $str = MC::getInstance()->get('xhprof') . "<br><a href='/tools/xhprof/xhprof_html/index.php?run=$run_id&source=train' target='_blank'>$now</a>";
            MC::getInstance()->set('xhprof', $str);
        }
	}

	static public function typeChange(&$array,$key){
		if(strpos($array,'.')){
			$array = (float)$array;
		}elseif(is_numeric($array)){
			$array = (int)$array;
		}
	}

	static public function strToNumeric($array){
		array_walk($array,'Util::typeChange');
		return $array;
	}
}
