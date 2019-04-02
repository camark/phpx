<?php
//这个文件是作为model文件的一个片段，可以调用任何可以调用的modal和view，可以用$this->action指代Action
class Chain_selectorCheckModel extends PHPHand_Model
{
	function check($field,$config){
		
		//调用共同的验证方法
		//TextCheckModel::getInstance()->checkBase($field,$config);
		
		if(!isset($_POST[$field]) || !$_POST[$field])
		{
			PHPHand_Action::getInstance()->error($config['showname'].'是必填项目');
		}
	}
}