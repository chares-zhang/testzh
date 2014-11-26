<?php

class Curl {
	
	public static function post($url, $params)
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
			curl_setopt($ch, CURLOPT_USERAGENT, 'Island API PHP Client 1.0 (curl) ' . phpversion());
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			$result = curl_exec($ch);
			$errno = curl_errno($ch);
			curl_close($ch);
			return array($errno, $result);
		} else {
			throw new Exception ("curl_init function not exist");
		}
	}

}
