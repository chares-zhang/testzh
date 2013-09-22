<?php
class Spider{
	public static function getHtml($url){
		$ch = curl_init();
		$timeout = 5;
		curl_setopt ($ch, CURLOPT_URL, "$url");
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)");
		curl_setopt ($ch, CURLOPT_ENCODING, "gzip");
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$contents = curl_exec($ch);
		curl_close($ch);
		if(empty($contents)){
			exit('<font color=red>对不起，不能正常访问该网站，请更换域名.</font>');
		}
		//$encode = mb_detect_encoding($contents,array("ASCII","UTF-8","GB2312","GBK","BIG5"));
		//$contents = iconv($encode,"UTF-8",$contents);
		return $contents;
	}
}
?>