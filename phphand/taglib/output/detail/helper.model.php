<?php
class Output_detail_helperModel extends PHPHand_Model
{
	function display($config,$view_power=1,$edit_support='',$data_id=0)
	{
		if($edit_support && !is_string($edit_support))
		{
			exit('edit_support格式不正确');
		}
		$data_id=(int)$data_id;
		if($edit_support && (!is_int($data_id) || !$data_id))
		{
			exit('如果edit_support参数设置了，那么必须设置data_id参数');
		}
		$this->view->setAbsoluteDir(dirname(__FILE__));
		$this->view->sign('config',$config);
		$this->view->sign('edit_support',$edit_support);
		$this->view->sign('view_power',$view_power);
		$this->view->sign('data_id',$data_id);

		$part_index = $this->query->get('part_index');
		$part_sub_index = $this->query->get('part_sub_index');
		if($part_index!==false)
		{
			$html = $this->view->display('display','','return');
			if($part_sub_index===false)
			{
				preg_match_all('/(<div class="detail-sub pr15 pl15" part_index="'.$part_index.'"[\s\S]+?)<\!\-\-SUB\.END\.FLAG\-\->/i',$html,$matches,PREG_SET_ORDER);
				echo $matches[sizeof($matches)-1][1];
			}else{
				preg_match('/(<div class="detail-sub pr15 pl15" part_index="'.$part_index.'" part_sub_index="'.$part_sub_index.'"[\s\S]+?)<\!\-\-SUB\.END\.FLAG\-\->/i',$html,$match);
				echo $match[1];
			}
			return;
		}
		$this->view->display('display');
	}
}