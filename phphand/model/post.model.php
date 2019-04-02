<?php

/**
 * PostModel类
 * 该类作为一个通用的表单管理模型
 */
class PostModel extends PHPHand_Model{
	/**
	 * fetchImage
	 *
	 * 根据表单数据中的<img />标签的src属性，将图片从网络采集到本地
	 */
	function fetchImages($name){
		if(!isset($_POST[$name])) return;
		$content=$_POST[$name];
		
		preg_match_all("/<img[^>]+?src=[\"'\s]*?([^\"'\s]+?)[\"'\s>]/is",$content,$matches,PREG_PATTERN_ORDER);
		foreach($matches[1] as $url){
			$pictureUrl=strtolower($url);
			if(strpos($pictureUrl,'http://')===0 && !strpos($pictureUrl,strtolower($_SERVER['HTTP_HOST']) || true)){
				$type = preg_replace("/.+?[\.]([^\.]+?)$/is","\\1",$pictureUrl);
				$newName=time().'_'.rand(1000,9999).'.'.$type;
				$byte=file_get_contents($pictureUrl);
				if($byte){
					if(!is_dir("File")) @mkdir("File");
					if(!is_dir("File/PostFetch/")) @mkdir("File/PostFetch/");
					$picture=fopen("File/PostFetch/".$newName,"w");
					fwrite($picture,$byte);
					fclose($picture);
					$content=str_replace(trim($url),"File/PostFetch/".$newName,$content);
				}
			}
		}
		$_POST[$name]=$content;
	}
}
?>

