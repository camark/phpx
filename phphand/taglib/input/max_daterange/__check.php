<?php
class Max_daterangeCheckModel extends PHPHand_Model
{
	function check($field,$config)
	{
		$b = $_POST[$field.'_begin'];
		if(!$b) $b = 0;
		else $b = $this->date_helper->get_time_stamp($b);
		
		$e = $_POST[$field.'_end'];
		if(!$e) $e=0;
		else $e = $this->date_helper->get_time_stamp($e);
		return $b.','.$e;
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