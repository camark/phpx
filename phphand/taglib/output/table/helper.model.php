<?php
class Output_table_helperModel extends PHPHand_Model
{
	function read($table,$fid_column,$fid)
	{
		/*if(!file_exists(__ROOT__.'/data/sdc.flag'))
		{
			$this->db->query("CREATE TABLE IF NOT EXISTS `plugin_sub_data_cache`(
				 `cache_id` int(12) NOT NULL AUTO_INCREMENT,
				  `table` VARCHAR(55),
				  `fid` int(10) NOT NULL DEFAULT '0',
				  `sub_count` INT(10) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`candidate_information_id`)
			)ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
			file_put_contents(__ROOT__.'/data/sdc.flag',date('Y/m/d H:i:s'));
		}
		
		$rs = $this->plugin_sub_data_cache->none_pre()->get_by(array(
			'table' => $table,
			'fid' => $fid,
		));
		
		if(!$rs)
		{
			
		}*/
		return $this->{$table}->none_pre()->count("`".$fid_column."`=$fid");
	}


	private $groups = array();
	private $mapping_class = NULL;
	private $mapping_method = NULL;
	function append_button_group($group,$sub=NULL)
	{
		if(is_string($group))
		{
			$find = false;
			foreach($this->groups as $this_group)
			{
				if(isset($this_group[$group]) && is_array($this_group[$group]))
				{
					$this_group[$group][] = $sub;
					$find = true;
					break;
				}
			}
			if(!$find)
			{
				$this->append_button_group(array(
					$group => $sub,
				));
			}
		}
		$this->groups[] = $group;
	}
	
	function get_button_groups()
	{
		return $this->groups;
	}
	
	function set_reflector($class,$method)
	{
		$this->mapping_class = $class;
		$this->mapping_method = $method;
	}
}