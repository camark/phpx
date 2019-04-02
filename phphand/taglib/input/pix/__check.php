<?php
class PixCheckModel extends PHPHand_Model
{
	function check($field,$config)
	{
		if(is_array($_POST[$field])){
			foreach($_POST[$field] as $path)
			{
				$this->attachment->none_pre()->_update(array(
					'usage' => '#ADD',
				),array('path' => $path));
			}
			$this->remove_used_cache();
			return implode(',',$_POST[$field]);
		}
		if(trim($_POST[$field])){
			$exploded=explode(',',trim($_POST[$field]));
			foreach($exploded as $path)
			{
				$this->attachment->none_pre()->_update(array(
					'usage' => '#ADD',
				),array('path' => $path));
			}
		}
		$this->remove_used_cache();
		return $_POST[$field];
	}

	function remove_used_cache()
	{
		$query=$this->db->query("SELECT * FROM attachment WHERE `usage`=0 AND createdate<".(time()-3600));
		while($rs=mysql_fetch_assoc($query))
		{
			if(file_exists(__ROOT__.$rs['path']))
			{
				try{
					unlink(__ROOT__.$rs['path']);
				}catch(Exception $e){}
			}
			$this->db->query("DELETE FROM attachment WHERE attachment_id=".$rs['attachment_id']);
		}
	}

	//处理原有的图片
	function check_original($field,$config,$update_table,$update_id)
	{
		$test = $this->{$update_table}->none_pre()->get($update_id);
		if(!$test) return;
		if(isset($test['template_id']))
		{
			$data = $this->data_template->get_value($update_table,$update_id,$field);
		}else{
			$data = $test[$field];
		}
		if($data)
		{
			$data=explode(',',$data);
			foreach($data as $addr)
			{
				$attachment=$this->attachment->none_pre()->get_by('path',$addr);
				if($attachment){
					if($attachment['usage']<=1)
					{
						$this->attachment->none_pre()->_delete('attachment_id',$attachment['attachment_id']);
						@unlink(__ROOT__.$addr);
					}else{
						$this->attachment->none_pre()->_update(array(
							'usage' => '#MINUS',
						),$attachment['attachment_id']);
					}

				}
			}
		}
	}
}