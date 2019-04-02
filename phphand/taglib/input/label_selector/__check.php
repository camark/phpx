<?php
class SelectCheckModel extends PHPHand_Model
{
	function check($field,$config)
	{
		return @$_POST[$field];
	}
}