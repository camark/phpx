<?php
class Server extends PHPHand_Action
{
	function _default()
	{
		$key_source = $this->query->get('key_source');
		$key = urldecode($this->query->get('key'));

		$sql = "SELECT * FROM `$key_source` WHERE `key` LIKE '%$key%' OR '$key' LIKE CONCAT('%',`key`,'%')";

		$from_table = urldecode($this->query->get('from_table'));
		$value_column = $this->query->get('value_column');
		$state = $this->query->get('state');

		$keys = array();
		$query = $this->db->query($sql);

		while($rs = mysql_fetch_assoc($query))
		{
			if($rs['data_num']==-1 || $rs['data_num_update']<time()-24*3600)
			{
				$theKey = $rs['key'];
				if(preg_match('/^\{(.+?),(.+?)\}$/i',$from_table,$match))
				{
					$data_template_name = $match[2];
					$template = $this->data_template->get_by('name',$data_template_name);

					$data_num = $this->data_template->count($template['template_id'],array(
							$value_column => urlencode($theKey),
						));
				}else{
					$data_num = $this->{$from_table}->count("`$value_column` LIKE '%$theKey%'");
				}
				if($data_num)
				{
					$rs['data_num'] = $data_num;
					$this->{$key_source}->none_pre()->_update(array(
						'data_num'	=> $data_num,
						'data_num_update'=>time(),
					),array('key'=>$theKey));
				}
			}
			$keys[] = $rs;
		}

		/*
		

		if(preg_match('/^\{(.+?),(.+?)\}$/i',$from_table,$match))
		{
			$table = $match[1];
			$data_template_name = $match[1];
			$template = $this->data_template->get_by('name',$data_template_name);

			$datas = $this->data_template->query($table,$template['template_id'],array(
				$value_column => $key,
			));
		}else{
			$sql="SELECT `$value_column` as value_column,`$show_column` as show_column FROM `$from_table` WHERE `$value_column` LIKE '%$key%'";
			$datas = $this->db->getMany($sql);
			
			//$this->sign('sql',$sql);
			//$this->sign('key',$key);
			$this->view->setAbsoluteDir(dirname(__FILE__));
			$this->display();
		}
		*/

		$this->sign('keys',$keys);
		$this->sign('key',$key);
		$this->view->setAbsoluteDir(dirname(__FILE__));
		$this->display();
		
	}
}