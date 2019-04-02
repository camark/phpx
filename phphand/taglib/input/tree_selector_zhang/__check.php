<?php
class Tree_selector_zhangCheckModel extends PHPHand_Model
{
	function check($field,$config)
	{
		//调用phphand/model/input.model.php共同的验证方法
		//$this->input->checkBase($field,$config);
		if(isset($_POST['__search__']))
		{
			if(!$_POST[$field]) return '';
			
			$result = explode(',',$_POST[$field]);
			foreach($result as $value){
				$this->get_all_sub_data($field,$config,$value,$result);
			}
			return implode(',',$result);
		}
		if(isset($config['is_must']) && $config['is_must'] && (!isset($_POST[$field]) || !$_POST[$field]))
		{
			exit('请选择'.$config['showname']);
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
		$array=explode(',',$str);
		if(sizeof($array)>30)
		{
			exit($config['showname'].'搜索子项过多，请选择具体子项进行搜索');
		}
		$row[$field]=$str;
		return " AND `$field` IN ($str)";
	}
	
	function get_all_child_values($value,$config)
	{
		$where_str = '';
		if(is_string($value))
		{
			$value = $this->util->get_sql_string($value);
			$where_str = " `".$config['fid_column']."`='$value' ";
		}
		else
		{
			$value = intval($value);
			$where_str = " `".$config['fid_column']."`={$value} ";
		}
		if(empty($where_str))
		{
			return false;
		}
		$sql="SELECT `{$config['value_column']}` FROM {$config['from_table']} WHERE {$where_str}";
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