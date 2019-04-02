<?php
class Server extends PHPHand_Action
{
	function update_column()
	{
		$table = $this->query->get('table');
		$field = $this->query->get('field');
		$value = urldecode(str_replace('****','--',$this->query->get('value')));
		$data_id=$this->query->get('data_id');
		
		$id_column = preg_replace('/^.+?_([^_]+?)$/is','\\1',$table).'_id';

		
		$this->{$table}->none_pre()->_update(array(
			$field => $value,
		),$data_id);
		
	}

	function add_column()
	{
		$table = $this->query->get('table');
		$field = $this->query->get('field');
		$value = urldecode(str_replace('****','--',$this->query->get('value')));
		
		$id_column = preg_replace('/^.+?_([^_]+?)$/is','\\1',$table).'_id';

		
		$id = $this->{$table}->none_pre()->_insert(array(
			$field => $value,
		));
		echo $id;
	}
}