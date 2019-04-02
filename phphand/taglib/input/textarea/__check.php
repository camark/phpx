<?php
class TextareaCheckModel extends PHPHand_Model
{
	function check($field,$config)
	{
		
		//调用共同的验证方法
		//TextCheckModel::getInstance()->checkBase($field,$config);
		if(isset($config['trans_lines']) && $config['trans_lines']=='n'){
			return $_POST[$field];
		}else{
			return str_replace("\n","<br/>",$_POST[$field]);
		}
	}
}