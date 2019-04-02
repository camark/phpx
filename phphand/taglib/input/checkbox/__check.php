<?php
class CheckboxCheckModel extends PHPHand_Model
{
	function check($field,$config)
	{
		if($config['mode']==1){
			$value = '';
			if(isset($_POST[$field]))
			{
				if(is_array($_POST[$field])){
					foreach($_POST[$field] as $val)
					{
						if($value!='') $value.=',';
						$value .= $val;
					}
				}else{
					$value .= $_POST[$field];
				}
			}
			if($value=='') ;
			return $value;
		}else{
			$data_source = $config['data_source'];
			if(strpos($data_source,':')!==false)
			{
				$data_source = $this->input->get_true_value($data_source);
			}

			$sh_config = array('data_source'=> $data_source,'id_column'=>$config['id_column'],'title_column'=>$config['title_column']);
			$sh_options=$this->hp->get_options($sh_config);
			unset($sh_options['']);

			$values = array();

			foreach($sh_options as $sh_value => $sh_title)
			{
				if(isset($_POST[$field.'_'.$sh_value]))
				{
					$values[$field.'_'.$sh_value] = $_POST[$field.'_'.$sh_value];
				}
			}

			return $values;
		}
	}
}