<?php
class Tree_selectorCheckModel extends PHPHand_Model
{
	function check($field,$config)
	{
		return $_POST[$field];
	}
	
	function search($field,$config,&$row)
	{
		$value = $this->query->get($field);
		if(!$value) return '';
		$str = $value.$this->get_all_child_values($value,$config);
		$row[$field]=$value;
		return " AND `$field` IN ($str)";
	}
	
	function get_all_child_values($value,$config)
	{
		$sql="SELECT `{$config['value_column']}` FROM {$config['from_table']} WHERE `{$config['fid_column']}`='$value'";
		$query = $this->db->query($sql);
		$str = '';
		while($rs=mysql_fetch_assoc($query))
		{
			$str.=',';
			$str.=$rs[$config['value_column']];
			$str.=$this->get_all_child_values($rs[$config['value_column']],$config);
		}
		return $str;
	}

}