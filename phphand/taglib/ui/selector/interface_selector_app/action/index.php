<?php
class Index extends PHPHand_Action{
	function get_label(){
		$from_table=$this->query->get('from_table');
		$value_column=$this->query->get('value_column');
		$show_column=$this->query->get('show_column');
		$value=$this->query->get('value');
		$id=$this->query->get('id');
		
		$sql="SELECT `$show_column` FROM `$from_table` WHERE `$value_column`='$value' LIMIT 1";
		$rs=$this->db->getOne($sql);
		if($rs){
			$data=$rs[$show_column];
		}else{
			$data='';
		}
		header('content-type:text/xml;charset=utf-8');
		$xml="<root id='$id'>";
		$xml.="<![CDATA[".$data."]]>";
		$xml.="</root>";
		echo $xml;
	}
	
	function get_list(){
		$from_table=$this->query->get('from_table');
		$value_column=$this->query->get('value_column');
		$show_column=$this->query->get('show_column');
		$value=$this->query->get('value');
		$id=$this->query->get('id');
		$page=intval($this->query->get('page'));
		$state1=$this->query->get('state1');
		$state2=$this->query->get('state2');
		if(empty($page)) $page=1;

		$sql="SELECT * FROM `$from_table`";
		if($state1){
			$sql.=" WHERE ".$state1;
		}
		
		$this->sign('page',$page);
		$this->sign('show_column',$show_column);
		$this->sign('value_column',$value_column);
		$this->sign('from_table',$from_table);
		$this->sign('value',$value);
		$this->sign('sql',$sql);
		if($state2 && preg_match('/\$\.(.+?)$/',$state2,$match)){
			$column=$match[1];
			$sql2="SELECT * FROM `$from_table` WHERE $state2";
			$this->sign('column',$column);
			$this->sign('sql2',$sql2);
			$this->display('get_list2');
			exit;
		}
		$this->display();
	}
}