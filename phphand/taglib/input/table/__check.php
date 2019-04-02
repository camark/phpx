<?php
class TableCheckModel extends PHPHand_Model
{
	function check($field,$config)
	{
		$array = array();
		foreach($_POST as $name => $value)
		{
			if(strpos($name,$field.'_')===0 && $value!=='')
			{
				$key = preg_replace('/^'.$field.'_/i','',$name);
				list($row,$col)=explode('$',$key);
				$array[$row][$col]=$value;
			}
		}

		return serialize($array);
	}
}