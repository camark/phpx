<?php
class Server extends PHPHand_Action
{
	function _default()
	{
		$from_table = $this->query->get('from_table');
		$value_column = $this->query->get('value_column');
		$show_column = $this->query->get('show_column');
		$state = urldecode($this->query->get('state'));
		$state = $this->{'input.tree_selector.helper'}->get_state($state);
		$state = $this->input->get_true_value($state);
		$key = urldecode($this->query->get('key'));


		$sql="SELECT `$value_column` as value_column,`$show_column` as show_column FROM `$from_table` WHERE `$show_column` LIKE '%$key%'";
		if($state)
		{
			$sql.=" AND ".$state;
		}
		$this->sign('sql',$sql);
		$this->sign('key',$key);
		$this->view->setAbsoluteDir(dirname(__FILE__));
		$this->display();
	}

	function get_default_show()
	{
		$from_table=$this->query->get('from_table');
		$value_column=$this->query->get('value_column');
		$show_column=$this->query->get('show_column');
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
		$sql="SELECT `$show_column`,`$value_column` FROM `$from_table` WHERE `$value_column` IN ($str)";
		if($state) $sql.=" AND ".urldecode($state);
		$html = '';
		$query =$this->db->query($sql);
		while($rs = mysql_fetch_assoc($query))
		{
			if($html!='') $html.=',';
			$html.='<label _value="'.$rs[$value_column].'">'.$rs[$show_column].'<span class="glyphicon glyphicon-remove"></span></label>';
		}
		echo $html;
	}
}