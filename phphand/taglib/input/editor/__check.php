<?php
class EditorCheckModel extends PHPHand_Model
{
	function check($field,$config)
	{
		//调用共同的验证方法
		//TextCheckModel::getInstance()->checkBase($field,$config);
		
		return $_POST[$field];
	}
}