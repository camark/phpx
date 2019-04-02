<?php
class Select_rangeCheckModel extends PHPHand_Model
{
	function check($field,$config)
	{
		if($config['name1']==$config['name2'])
		{
			return $_POST[$field][0].','.$_POST[$field][1];
		}
		$a = $_POST[$field.$config['name1']];
		
		$b = $_POST[$field.$config['name2']];
		return array($field.$config['name1']=>$a,$field.$config['name2']=>$b);
	}

	function search($field,$config,&$row)
	{
		
	}
}