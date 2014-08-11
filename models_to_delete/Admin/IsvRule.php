<?php

class Admin_IsvRule extends AbstractModel
{
	//获取淘宝应用数据
	public function getIsv($contents){
		preg_match_all("/<td>[\s\S]*?<div class=\"item-meta\">(.*?)<\/div>[\s\S]*?<\/td>/is",$contents,$match);
		$data = array();
		if(!empty($match[0]) && is_array($match[0])){
			foreach($match[0] as $key=>$matchContent){
				//取标题
				preg_match_all("/<h4><a target=\"_blank\"  href=\"(.*?)\">(.*?)<\/a><\/h4>/is",$matchContent,$matchtitle);
				if(!empty($matchtitle[2]) && is_array($matchtitle[2])){
					$data[$key]['app_name'] = $this->unescapeDec($matchtitle[2][0]);
				}
				
				//取service_code 和 img
				preg_match_all("/<div class=\"img ui-icon-radius\"><a target=\"_blank\" href=\"(.*?)\"><img   src=\"(.*?)\"    width=\"80\" height=\"80\" \/><\/a><\/div>/is",$matchContent,$matchResult);
				if(!empty($matchResult[1]) && is_array($matchResult[1])){
					$detailUrl = $matchResult[1][0];
					$data[$key]['detail_url'] = $detailUrl;
					preg_match_all("/service_code=(.*?)&/is",$detailUrl,$codeMatch);
					$data[$key]['service_code'] = $codeMatch[1][0];
				}
				if(!empty($matchResult[2]) && is_array($matchResult[2])){
					$data[$key]['img'] = $matchResult[2][0];
				}

				//取昵称
				preg_match_all("/<span class=\"J_WangWang\" data-nick=\"(.*?)\"/is",$matchContent,$matchNick);
				if(!empty($matchNick[1][0]) && is_array($matchNick[1])){
					$data[$key]['nick'] = $this->unescapeDec($matchNick[1][0]);
				}else{
					$data[$key]['nick'] = '官方';
				}
				
				//取公司名
				preg_match_all("/<p class=\"concat\">(.*?)<\/p>/is",$matchContent,$matchIsv);
				if(isset($matchIsv[1][0])){
					preg_match_all("/<a target=\"_blank\" href=\"(.*?)\" target=\"_blank\">(.*?)<\/a>/is",$matchIsv[1][0],$matchIsvName);
					if(isset($matchIsvName[2][0])){
						$data[$key]['isv_name'] = $this->unescapeDec(trim($matchIsvName[2][0]));
					}else{
						$data[$key]['isv_name'] = $this->unescapeDec(trim(strip_tags($matchIsv[1][0])));
					}
				}
			}
		}

		return $data;
	}
	
	//获取淘宝详细页面
	public function getAppDetail($contents){
		//<p class="desc">淘宝官方工具，已为超过220万家淘宝店铺提供权威、标准、实时、易用的数据分析服务，从流量、成交、来源、装修、直通车、买家等多维度透视店铺核心数据，有效提升经营。目前标准包已免费向所有卖家开放。</p>
		//应用简介
		preg_match_all("/<p class=\"desc\">(.*?)<\/p>/is",$contents,$matchDesc);
		if(isset($matchDesc[1]) && !empty($matchDesc[1])){
			$data['desc'] = trim($matchDesc[1][0]);
		}
	
		//评星
		preg_match_all("/<span class=\"grade\">(.*?)<\/span>/is",$contents,$matchScore);
		if(isset($matchScore[1]) && !empty($matchScore[1])){
			$data['score'] = (float)trim($matchScore[1][0]);
		}
	
		//用户数,热度, 无评价特殊处理
		preg_match_all("/<span class=\"label-like\">有效评价：<\/span>[\s\S]*?<span class=\"count\">(.*?)<\/span>[\s\S]*?<\/li>/is",$contents,$matchComment);
		preg_match_all("/<li><span class=\"label-like\">付费使用：<\/span><span class=\"count\">(.*?)人<\/span><\/li>/is",$contents,$matchPay);
		preg_match_all("/<li><span class=\"label-like\">免费使用：<\/span><span class=\"count\">(.*?)人<\/span><\/li>/is",$contents,$matchFree);
		preg_match_all("/<li><span class=\"label-like\">浏览数：<\/span><span class=\"count\">(.*?)次\/30天<\/span><\/li>/is",$contents,$matchBrowse);
	
		$comment = $payNum = $freeNum = $browseNum = 0;
		if(isset($matchComment[1][0]) && !empty($matchComment[1][0])){
			$matchComment[1][0] = str_replace('次','',$matchComment[1][0]);
			$matchComment[1][0] = trim($matchComment[1][0]);
			//$matchComment[1][0] 可能的值：少于100, 暂无评论
				
			if(trim($matchComment[1][0])!= '' && $matchComment[1][0] != '少于100' && $matchComment[1][0] != '暂无评论'){
				$comment = trim($matchComment[1][0]);
			}
		}
		if(isset($matchPay[1][0]) && !empty($matchPay[1][0])){
			$matchPay[1][0] = trim($matchPay[1][0]);
			$payNum = (!empty($matchPay[1][0]) && $matchPay[1][0] != '少于100') ? trim($matchPay[1][0]) : 0;
		}
		if(isset($matchFree[1][0]) && !empty($matchFree[1][0])){
			$matchFree[1][0] = trim($matchFree[1][0]);
			$freeNum = (!empty($matchFree[1][0]) && $matchFree[1][0] != '少于100') ? trim($matchFree[1][0]) : 0;
		}
		if(isset($matchBrowse[1][0]) && !empty($matchBrowse[1][0])){
			$matchBrowse[1][0] = trim($matchBrowse[1][0]);
			$browseNum = (!empty($matchBrowse[1][0]) && $matchBrowse[1][0] != '少于100') ? trim($matchBrowse[1][0]) : 0;
		}
		$data['comment'] = self::strToNum($comment);
		$data['pay_num'] = self::strToNum($payNum);
		$data['free_num'] = self::strToNum($freeNum);
		$data['total_num'] = $data['pay_num'] + $data['free_num'];
		$data['browse_num'] = self::strToNum($browseNum);
	
		//服务评价分比, 有的匹配不到
		preg_match_all("/<span class=\"label-like\">易用性：<\/span>[\s\S]*?<span  class=\"[a-z]{3,4} per\"><s>(.*?)<\/s>(.*?)%<\/span>/is",$contents,$matchYiyong);
		preg_match_all("/<span class=\"label-like\">服务态度：<\/span>[\s\S]*?<span  class=\"[a-z]{3,4} per\"><s>(.*?)<\/s>(.*?)%<\/span>/is",$contents,$matchTaidu);
		preg_match_all("/<span class=\"label-like\">有效评价：<\/span>[\s\S]*?<span  class=\"[a-z]{3,4} per\"><s>(.*?)<\/s>(.*?)%<\/span>/is",$contents,$matchWending);
	
		$yiyong = $taidu = $wending = 0;
		if(isset($matchYiyong[1][0]) && !empty($matchYiyong[1][0]) && isset($matchYiyong[2][0]) && !empty($matchYiyong[2][0])){
			$yiyong = $matchYiyong[1][0] == '高于' ? (float)$matchYiyong[2][0] : -$matchYiyong[2][0];
		}
		if(isset($matchTaidu[1][0]) && !empty($matchTaidu[1][0]) && isset($matchTaidu[2][0]) && !empty($matchTaidu[2][0])){
			$taidu = $matchTaidu[1][0] == '高于' ? (float)$matchTaidu[2][0] : -$matchTaidu[2][0];
		}
		if(isset($matchWending[1][0]) && !empty($matchWending[1][0]) && isset($matchWending[2][0]) && !empty($matchWending[2][0])){
			$wending = $matchWending[1][0] == '高于' ? (float)$matchWending[2][0] : -$matchWending[2][0];
		}
	
		$data['yiyong'] = self::strToNum($yiyong);
		$data['taidu'] = self::strToNum($taidu);
		$data['wending'] = self::strToNum($wending);
		
		//获取分类信息
		preg_match_all("/<div class=\"crumb\">(.*?)<\/div>/is",$contents,$matchCatDiv);//取块.
		if(isset($matchCatDiv[1][0])){
			preg_match_all("/<a href=\"(.*?)\">(.*?)<\/a>/is",$matchCatDiv[1][0],$matchCat);//匹配所有a
			if(isset($matchCat[1])){
				$catUrl = end($matchCat[1]);//最后一个a
				preg_match_all("/cat_map_id=([0-9]+)/is",$catUrl,$catMapId);//获取分类id
				if(isset($catMapId[1][0])){
					$data['cat_map_id'] = $catMapId[1][0];
				}
			}
		}
		
		//获取电话
		preg_match_all("/<li><span class=\"label-like\">客服电话：<\/span>(.*?)<\/li>/is",$contents,$matchTel);//取块.
		if(isset($matchTel[1][0])){
			$data['tel'] = $matchTel[1][0];
		}
		
		return $data;
	}
	
	//将有万字的数字字符串转换成数字
	static public function strToNum($numStr){
		$numStr = str_replace(",","",$numStr);
		if(preg_match ("/万/i",$numStr)>0){
			$numStr = str_replace('万','',$numStr);
			$num = $numStr * 10000;
			return $num;
		}
		return $numStr;
	}
	
	//将大于1万的数字转换成带有万字的保留一位小数的数字字符串
	static public function numToStr($num){
		if($num>10000){
			$str = sprintf('%0.1f',($num/10000));
			return $str.'万';
		}
		return $num;
	}
	
	//模仿JAVASCRIPT的UNESCAPE函数的功能
    public function unescapeDec($str){
        $text = preg_replace_callback("/&#[0-9]{2,5}[;]/", array(&$this,'decToUtf8'), $str);
        return $text;
    }
    
	public function decToUtf8($ar){
		foreach ($ar as $dec){
			$dec = str_replace('&#','',$dec);
			$c = '';
			if ($dec < 128){
				$c .= chr($dec);
			}else if ($dec < 2048){
				$c .= chr(192 + (($dec - ($dec % 64)) / 64));
				$c .= chr(128 + ($dec % 64));
			}else{
				$c .= chr(224 + (($dec - ($dec % 4096)) / 4096));
				$c .= chr(128 + ((($dec % 4096) - ($dec % 64)) / 64));
				$c .= chr(128 + ($dec % 64));
			}
		}
        return $c;
    }
}
