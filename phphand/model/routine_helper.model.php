<?php
/**
 * routine_helper 路由助手模型
 *
 */
class Routine_helperModel extends PHPHand_Model{
	/**
	 * get_url 方法
	 *
	 * @$entrance String 入口文件
	 * @$class String 控制器
	 * @$method String 方法名
	 * @$param Array 参数名
	 */
	function get_url($entrance,$class,$method,$param=array()){
		$url=$entrance.'?class='.$class.'&method='.$method;
		foreach($param as $name => $value){
			$url.='&'.urlencode($name).'='.urlencode($value);
		}
		return $url;
	}
}