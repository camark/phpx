<?php
class Data_tableModel extends PHPHand_Model
{
	private $groups = array();
	private $mapping_class = NULL;
	private $mapping_method = NULL;
	function append_button_group($group)
	{
		$this->{'output.table.helper'}->append_button_group($group);
	}
	
	function get_button_groups()
	{
		return $this->{'output.table.helper'}->get_button_groups();
	}
	
	function set_reflector($class,$method)
	{
		$this->mapping_class = $class;
		$this->mapping_method = $method;
	}
}