<?php
class Tree_selector_zhangOutputModel extends PHPHand_Model
{
	function output($data,$config,$return=false)
	{
		if(strpos($config['input'],'example.')===0)
		{
			$config = $this->input->get_example_detail($config);
		}
		$output='';
		if(!empty($data))
		{
			if(is_array($data))
			{
				foreach($data as &$v)
				{
					$v = intval($v);
				}
				$data = implode(",",$data);
			}
			$sql="SELECT * FROM {$config['from_table']} WHERE `{$config['value_column']}` IN ($data)";
			$query=$this->db->query($sql);
			$n=0;			
			if($query){
				while($rs=mysql_fetch_assoc($query))
				{
					if($n>0) $output.=',';
					$output.=$rs[$config['show_column']];
					$n++;
				}
			}
			if($n==0 && !$return) $output='&nbsp';
		}
		
		if(!$return) echo $output;
		else
			return $output;
	}
}