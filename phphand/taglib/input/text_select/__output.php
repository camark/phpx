<?php
class Input_selectOutputModel extends PHPHand_Model{

	function output($data,$config)
	{
		if(!$data){
			echo '';
			return;
		}
		
		$array =explode(',',$data);
		$echo_started_flag = false;
		foreach($array as $item)
		{
			$sql = "SELECT `{$config['show_column']}` FROM `{$config['from_table']}` WHERE `{$config['value_column']}`='$item'";
			$rs = $this->db->getOne($sql);
			if($rs)
			{
				if($echo_started_flag) echo ',';
				echo $rs[$config['show_column']];
				$echo_started_flag = true;
			}
		}
	}
}