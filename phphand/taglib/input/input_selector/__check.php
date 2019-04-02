<?php
class Input_selectorCheckModel extends PHPHand_Model
{
	function check($field,$config)
	{
		$array = array();
		$flag = false;
		foreach($_POST as $name => $value)
		{
			if($name=='input') $flag=true;
			if($flag) $array[$name]=$value;
		}
		
		return serialize($array);
	}
}