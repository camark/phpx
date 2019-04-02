<?php
class List_selectorCheckModel extends PHPHand_Model
{
	function check($field,$config)
	{
		$array = array();
		$flag = false;
		foreach($_POST as $name => $value)
		{
			if(strpos($name,'list_')===0 || $name=='list') $array[$name]=$value;
		}
		
		return serialize($array);
	}
}