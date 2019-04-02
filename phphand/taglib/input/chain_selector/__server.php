<?php
class Server extends PHPHand_Action
{
	function _default()
	{
		$from_table=$this->query->get('category_table');
		$value_column=$this->query->get('category_value_column');
		$show_column=$this->query->get('category_show_column');
		$fid_column=$this->query->get('category_fid_column');
		$fid=$this->query->get('fid');
		if(!$fid) $fid=0;

		$sql="SELECT `$value_column`,`$show_column` FROM `$from_table` WHERE `$fid_column`='$fid'";
		$this->sign('value_column',$value_column);
		$this->sign('show_column',$show_column);
		$this->sign('fid_column',$fid_column);
		$this->sign('from_table',$from_table);
		$this->sign('sql',$sql);
		$this->view->setAbsoluteDir(dirname(__FILE__));
		$this->display('category');
	}

	function target()
	{
		$from_table=$this->query->get('target_table');
		$value_column=$this->query->get('target_value_column');
		$show_column=$this->query->get('target_show_column');
		$fid_column=$this->query->get('target_fid_column');
		$fid=$this->query->get('fid');
		
		$category_table = $this->query->get('category_table');
		$category_value_column = $this->query->get('category_value_column');
		$category_fid_column = $this->query->get('category_fid_column');
		
		
		$all_child_ids = $this->get_all_child($category_table,$category_value_column,$category_fid_column,$fid);
		if($all_child_ids=='')
		{
			$all_child_ids = $fid;
		}else{
			$all_child_ids = $fid.','.$all_child_ids;
		}
		
		$sql="SELECT `$value_column`,`$show_column` FROM `$from_table` WHERE `$fid_column` IN ($all_child_ids)";

		$this->sign('value_column',$value_column);
		$this->sign('show_column',$show_column);
		$this->sign('fid_column',$fid_column);
		$this->sign('from_table',$from_table);
		$this->sign('sql',$sql);
		$this->view->setAbsoluteDir(dirname(__FILE__));
		$this->display('target');
	}
	
	function get_all_child($table,$value_column,$fid_column,$value)
	{
		$sql="SELECT `$value_column` FROM `$table` WHERE `$fid_column`='$value'";
		$query = $this->db->query($sql);
		$ids = '';
		while($rs=mysql_fetch_assoc($query))
		{
			if($ids!='') $ids.=',';
			$ids.=$rs[$value_column];
			
			$child_ids=$this->get_all_child($table,$value_column,$fid_column,$rs[$value_column]);
			if($child_ids!='')
			{
				$ids.=','.$child_ids;
			}
		}
		return $ids;
	}
}