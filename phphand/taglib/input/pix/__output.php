<?php
class PixOutputModel extends PHPHand_Model
{
	function output($data,$config,$return=false)
	{
		if($return) return '';
		if(!$data){
			echo '&nbsp;';
			return;
		}
		$array = array();
		if(isset($data)){
			$arr = explode(",",$data);
			foreach($arr as $item)
			{
				$item = trim($item);
				if($item) $array[]=$item;
			}
		}
		$str='';
		foreach($array as $item)
		{
			$ia=explode('.',$item);
			if(in_array($ia[sizeof($ia)-1],array('jpg','jpeg','gif','png')))
			{
				$src = strpos($item,'http')===0?$item:($this->env->get('app_url').$item);
				$str.= '<img src="'.$this->env->get('phphand_url').'/taglib/com/timthumb/timthumb.php?src='.$src.'&w=30&h=30&q=100" style="margin:0 5px 0 0;" />';
			}else{
				$str.= '已上传文件';
			}
		}
		
		echo $str;
	}
}