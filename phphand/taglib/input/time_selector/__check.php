<?php
class Time_selectorCheckModel extends PHPHand_Model
{
	function check($field,$config)
	{
		$value = 3600*$_POST[$field.'_hour'] + $_POST[$field.'_minute']*60;
		return $value;
	}
}