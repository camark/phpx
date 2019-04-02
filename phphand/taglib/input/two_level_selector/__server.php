<?php
class Server extends PHPHand_Action
{
	function _default()
	{
		$from_table=$this->query->get('from_table');
		$value_column=$this->query->get('value_column');
		$show_column=$this->query->get('show_column');
		$fid_column=$this->query->get('fid_column');
		$fid=$this->query->get('fid');
		if(!$fid) $fid=0;

		$sql="SELECT `$value_column`,`$show_column` FROM `$from_table` WHERE `$fid_column`='$fid'";
		$this->sign('value_column',$value_column);
		$this->sign('show_column',$show_column);
		$this->sign('fid_column',$fid_column);
		$this->sign('from_table',$from_table);
		$this->sign('sql',$sql);
		$this->view->setAbsoluteDir(dirname(__FILE__));
		$this->display('listener');
	}
}