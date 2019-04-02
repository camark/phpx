<?php
class Select_specialCheckModel extends PHPHand_Model
{
	function check($field,$config)
	{
		return @$_POST[$field];
	}
}