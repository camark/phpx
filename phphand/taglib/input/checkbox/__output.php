<?php
class CheckboxOutputModel extends PHPHand_Model{

	function output($data,$config)
	{
		$sh_options=$this->hp->get_options($config);
		unset($sh_options['']);
		$array = explode(',',$data);
		$flag = false;
		foreach($array as $val){
			if(isset($sh_options[$val])){
				if($flag) echo ',';
				$flag=true;
				echo $sh_options[$val];
			}
		}
	}
}