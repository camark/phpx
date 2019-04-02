<?php
class form_advanced_search_helperModel extends PHPHand_Model
{
	function get_all_input_item($template_id)
	{
		$fids = $this->data_template->get_fid_chain($template_id);
		$sql="SELECT * FROM max_data_template_item WHERE template_id IN (".implode(',',$fids).")";
		$query = $this->db->query($sql);
		$items = array();
		while($item = mysql_fetch_assoc($query))
		{
			$input_config = unserialize($item['config']);
			if($item['key'] && $item['title'] && isset($input_config['input']) && $input_config['input'])
			{
				$items[$item['key']] = $item['title'];
			}
		}
		
		return $items;
	}
}