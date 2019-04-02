<?php
class Check_or_notCheckModel extends PHPHand_Model
{
	function check($field,$config)
	{
		if(isset($_POST[$field])) return $config['check_value'];
		return $config['empty_value'];
	}
}