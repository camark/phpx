<?php
class Base_csshock_helperModel extends PHPHand
{
	function parse_css($content,$url){
		preg_match_all('/@import\s+?url\((.+?)\);/is',$content,$matches,PREG_SET_ORDER);
		//$content = preg_replace('/@import\s+?url\((.+?)\);/is','',$content);
		
		foreach($matches as $import){
			$import_url = trim(trim($import[1],"'"),'"');
			$import_url = $this->url_helper->pathConvert(dirname($url),$import_url);
			$content = str_replace('@import url('.$import[1].');',$this->get_css_content($import_url),$content);
		}
		
		
		preg_match_all('/url\((.+?)\)/is',$content,$matches,PREG_SET_ORDER);
		
		foreach($matches as $item){
			$url_url = $this->url_helper->pathConvert(dirname($url),trim(trim($item[1],'"'),"'"));
			$content = str_replace('('.$item[1].')','---'.$url_url.')',$content);
		}
		
		$content = str_replace('---','(',$content);
		return $content;
	}
	
	function get_css_content($url){
		$content = file_get_contents(__ROOT__ . $url);
		$content = $this->parse_css($content,$url);
		return $content;
	}

}