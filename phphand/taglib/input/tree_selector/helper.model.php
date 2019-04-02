<?php
class Input_tree_selector_helperModel extends PHPHand_Model
{
	function set_state($key,$value)
	{
		$serial_name = 'unic_state_key'.uniqid().time().rand(100,999);
		$this->view->sign($key,$serial_name);
		$this->cache->write($serial_name,$value);
	}

	function get_state($key)
	{
		return $this->cache->read($key);
	}
}