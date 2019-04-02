<?php
class DatepickCheckModel extends PHPHand_Model
{
	function check($field,$config)
	{
		if($config['data_type']=='2') return $_POST[$field];
		return strtotime($_POST[$field]);
	}
    function search($field,$config,&$row)
	{
		$sql='';
		$begin = $this->query->get($field.'_begin');
		$end = $this->query->get($field.'_end');
		if(!$begin && !$end) return '';
		
		if($begin)
		{
			$begin = $this->date_helper->get_time_stamp($begin);
			$sql.=" AND `$field`>'$begin'";
			$row[$field.'_begin']=$begin;
		}
		if($end)
		{
			$end = $this->date_helper->get_time_stamp($end);
			$sql.=" AND `$field`<'$end'";
			$row[$field.'_end']=$end;
		}
		return $sql;
	}
	
}