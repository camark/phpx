<?php
class Icon_selectorCheckModel extends PHPHand_Model
{
	function check($field,$config)
	{
		return @$_POST[$field];
	}
}