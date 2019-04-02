<?php
class Plugin_jobModel extends PHPHand_Model{
	function get_list($job_position,$user_id=0){
		$sql="SELECT  `file` FROM pre_plugin_job WHERE position='$job_position'";
		$query=$this->db->query($sql);
		$list=array();
		while($job=@mysql_fetch_array($query)){
			$list[]=$job['file'];
		}
		return $list;
	}
}