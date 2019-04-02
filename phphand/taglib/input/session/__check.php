<?php
class SessionCheckModel
{
	function check($field,$config)
	{
		return $_SESSION[$config['data_source']];
	}
}