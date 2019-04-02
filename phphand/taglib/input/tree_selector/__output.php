<?php
class Tree_selectorOutputModel extends PHPHand_Model
{
	function output($data,$config)
	{
		if(strpos($config['input'],'example.')===0)
		{
			$config = $this->input->get_example_detail($config);
		}
		$sql="SELECT * FROM {$config['from_table']} WHERE `{$config['value_column']}` IN ($data)";
		$query=$this->db->query($sql);
		$n=0;
		if($query){
			while($rs=mysql_fetch_assoc($query))
			{
				if($n>0) echo ',';
				echo $rs[$config['show_column']];
				$n++;
			}
		}
		if($n==0) echo '&nbsp';
	}
}