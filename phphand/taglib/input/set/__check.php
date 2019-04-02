<?php
class SetCheckModel
{
	function check($field,$config)
	{
		return $_POST[$field];
		if(preg_match('/^post:(.+?)$/is',$config['value'],$match))
		{
			return $_POST[$match[1]];
		}
		return $config['value'];
	}
}