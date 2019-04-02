<?php
class SearchModel extends PHPHand_Model
{
	/**
	 * 通用检索的辅助
	 */
	function init_common($config_file,$table,$type='sql')
	{
		//$reqs = $this->query->get();
		$objects = array();
		$sql="";
		$config = $this->table_config->read($config_file);

		$row = array();
		foreach($config as $field => $field_config)
		{
			if(!isset($field_config['input'])) continue;
			$field_config = $this->input->get_example_detail($field_config);
			$class = $field_config['input'].'CheckModel';
			if(!isset($objects[$class]))
			{
				$path = PHPHAND_DIR.'/taglib/input/'.$field_config['input'].'/__check.php';
				if(!file_exists($path))
				{
					if(isset($_POST[$field]))
					{
						$value = $_POST[$field];
					}else $value = $this->query->get($field);
					if(is_array($value))
					{
						$value = implode(',',$value);
					}
					if($value!==false && $value!==''){
						$sql.=" AND `$field`='$value'";
						$row[$field]=$value;
					}
					continue;
				}
				include_once $path;
				if(!class_exists($class))
				{
					if(isset($_POST[$field]))
					{
						$value = $_POST[$field];
					}else $value = $this->query->get($field);
					if(is_array($value))
					{
						$value = implode(',',$value);
					}
					if($value!==false && $value!==''){
						 $sql.=" AND `$field`='$value'";
						$row[$field]=$value;
					}
					continue;
				}
				$obj = new $class();
				if(!method_exists($obj,'search'))
				{
					if(isset($_POST[$field]))
					{
						$value = $_POST[$field];
					}else $value = $this->query->get($field);
					
					if(is_array($value))
					{
						$value = implode(',',$value);
					}
					
					
					if($value!==false && $value!=='')
					{
						$sql.=" AND `$field`='$value'";
						$row[$field]=$value;
					}
					continue;
				}
				
				$objects[$class]=$obj;
			}else{
				$obj = $objects[$class];
			}
			
			
			$sql.=$obj->search($field,$field_config,$row);
			
		}
		$this->view->sign('row',$row);
		if($type=='sql')
			return $sql;
		else
			return $row;
	}
}