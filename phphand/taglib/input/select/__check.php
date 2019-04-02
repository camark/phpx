<?php
class SelectCheckModel extends PHPHand_Model
{
	function check($field,$config)
	{
		if(isset($config['is_must']) && $config['is_must'] && (!isset($_POST[$field]) || $_POST[$field]===''))
		{
			exit($this->lang->get($config['showname']).$this->lang->get('是必选项目'));
		}
		return @$_POST[$field];
	}
}