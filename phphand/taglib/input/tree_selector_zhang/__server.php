<?php
class Server extends PHPHand_Action
{
	function _default()
	{
		$from_table=$this->query->get('from_table');
		$value_column=$this->query->get('value_column');
		$show_column=$this->query->get('show_column');
		$fid_column=$this->query->get('fid_column');
		$fid = intval($this->query->get('fid'));
		$value=explode(',',$this->query->get('value'));
		
		if(!$fid) $fid=0;
		$all_chains = array();
		if(!$fid){
			if($value)
			{
				$all_values = $value;
				foreach($all_values as $id)
				{
					//$chain = array();
					while($id){
						$rs = $this->{$from_table}->none_pre()->get($id);
						if($rs['fid']) array_unshift($all_chains,$rs['fid']);
						$id = $rs['fid'];
					}
					//$all_chains[]=$chain;
				}
			}
			$this->sign('level',0);
		}
		$this->sign('all_chains',$all_chains);
		
		$id_column = preg_replace('/^.+?_([^_]+?)$/is','\\1',$from_table).'_id';
		$sq = "SHOW columns FROM `$from_table` LIKE '$id_column'";
		$one = $this->db->getOne($sq);
		if(!$one)
		{
			$id_column = $value_column;
		}
	
		$array = array();
		foreach($value as $val)
		{
			$val=trim($val);
			if(!$val) continue;
			$array[]=$val;
		}
		$this->sign('value_array',$array);
		
		$state = urldecode($this->query->get('state'));
		$state = $this->{'input.tree_selector.helper'}->get_state($state);
		
		
		$multi_parent=intval($this->query->get('multi_parent'));
		if(!$multi_parent) $multi_parent=0;
		$this->sign('multi_parent',$multi_parent);

		$sql="SELECT `$value_column`,`$show_column`,`$id_column` AS id FROM `$from_table` WHERE";
		$sql_model = "SELECT `$value_column`,`$show_column`,`$id_column` AS id FROM `$from_table` WHERE";
		if(!$multi_parent){
			if(preg_match("/(^|\s|`)$fid_column`?\s*?=/i",$state) && $fid==0){
				$sql.=" ".$state;
				$state="";
			}else{
				if($state) $state = preg_replace("/(^|\s|`)$fid_column`?\s*?=\s*?\S+?(\s|$)/i","\\2",$state);
				$sql.=" `$fid_column`='$fid'";
			}
			$sql_model.=" `$fid_column`='[fid]'";
		}else{
			$sql.=" CONCAT(',',`$fid_column`,',') LIKE '%,$fid,%'";
			$sql_model.=" CONCAT(',',`$fid_column`,',') LIKE '%,[fid],%'";
		}
		if($state) $sql.=" AND ".$state;

		
		$this->sign('sql_model',$sql_model);

		$this->sign('id_column',$id_column);
		$this->sign('value_column',$value_column);
		$this->sign('show_column',$show_column);
		$this->sign('fid_column',$fid_column);
		$this->sign('from_table',$from_table);
		$this->sign('sql',$sql);
		$this->view->setAbsoluteDir(dirname(__FILE__));
		$this->view->display('listener');
	}
	
	function get_default_show()
	{
		$from_table=$this->query->get('from_table');
		$value_column=$this->query->get('value_column');
		$show_column=$this->query->get('show_column');
		$fid_column=$this->query->get('fid_column');
		$value = $this->query->get('value');
		$state = urldecode($this->query->get('state'));
		$state = $this->{'input.tree_selector.helper'}->get_state($state);
		
		$vals = explode(',',$value);
		$str = '';
		foreach($vals as $val)
		{
			$val=trim($val);
			if(!$val) continue;
			if($str!='') $str.=',';
			$str.="'$val'";
		}
		
		if(!$value) exit('');
		$sql="SELECT DISTINCT(`$show_column`) FROM `$from_table` WHERE `$value_column` IN ($str)";
		if($state) $state = preg_replace("/AND\s*?(^|\s|`)$fid_column`?\s*?=\s*?\S+?(\s|$)/i","\\2",$state);
		if($state) $sql.=" AND (".urldecode($state).")";

		$html = '';
		$query =$this->db->query($sql);
		while($rs = mysql_fetch_assoc($query))
		{
			if($html!='') $html.=',';
			$html.=$rs[$show_column];
		}
		echo $html;
	}
}