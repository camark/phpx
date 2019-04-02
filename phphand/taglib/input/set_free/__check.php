<?php
class Set_freeCheckModel
{
	function check($field,$config)
	{
		if(preg_match('/^post:(.+?)$/is',$config['value'],$match))
		{
			if(!isset($_POST[$match[1]])) return '';
			if(is_array($_POST[$match[1]])) return implode(',',$_POST[$match[1]]);
			return $_POST[$match[1]];
		}
		return $config['value'];
	}
}