<?php
class Tree_selectorCheckModel extends PHPHand_Model
{
	function check($field,$config)
	{
		//调用共同的验证方法 
		//TextCheckModel::getInstance()->checkBase($field,$config);<br />
		if(isset($_POST['__search__']))
		{
			if(!$_POST[$field]) return '';
			
			$result = explode(',',$_POST[$field]);
			foreach($result as $value){
				$this->get_all_sub_data($field,$config,$value,$result);
			}
			return implode(',',$result);
		}
		return $_POST[$field];
	}
	
	function get_all_sub_data($field,$config,$fid,&$array)
	{
		$sql = "SELECT `".$config['value_column']."` FROM `".$config['from_table']."` WHERE `".$config['fid_column']."`='$fid'";
		$query = $this->db->query($sql);
		while($rs=mysql_fetch_assoc($query))
		{
			$array[] = $rs[$config['value_column']];
			$this->get_all_sub_data($field,$config,$rs[$config['value_column']],$array);
		}
	}
	function search($field,$config,&$row)
	{
		if(isset($_POST[$field]))
		{
			$value = $_POST[$field];
		}else $value = $this->query->get($field);

		if(!$value) return '';
		$value=str_replace('%2C',',',$value);
		$array = explode(',',str_replace('%2C',',',$value));
		foreach($array as $item){
			$str = $value.$this->get_all_child_values($item,$config);
		}
		$row[$field]=$str;
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