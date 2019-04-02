<?php
class Text_doubleCheckModel extends PHPHand_Model
{
	function check($field,$config)
	{
		//验证必填，2项中有1项为空，就验证不通过
	   if(isset($config['is_must']) && $config['is_must'] && 
	       ( (!isset($_POST[$field.$config['name1']]) || !$_POST[$field.$config['name1']]) ||
	        (!isset($_POST[$field.$config['name2']]) || !$_POST[$field.$config['name2']])
	       )
	    )
		{
			PHPHand_Action::getInstance()->error($config['showname'].'不能留空',$field);
		}
		
		if(empty($config['pattern']) && !empty($config['minlength']) && 
		    ( mb_strlen($_POST[$field.$config['name1']],'utf-8') < intval($config['minlength']) || 
		      mb_strlen($_POST[$field.$config['name2']],'utf-8') < intval($config['minlength'])
		    )
		)
		{
			PHPHand_Action::getInstance()->error($config['showname'].'长度不能少于'.$config['minlength'].'个字符',$field);
		}
		
		
		if(empty($config['pattern']) && !empty($config['maxlength']) && 
		    ( mb_strlen($_POST[$field.$config['name1']],'utf-8') > intval($config['maxlength']) || 
		       mb_strlen($_POST[$field.$config['name2']],'utf-8') > intval($config['maxlength']) )
		   )
	    {
			PHPHand_Action::getInstance()->error($config['showname'].'长度不能多于'.$config['maxlength'].'个字符',$field);
		}
		
	   if(isset($config['pattern']))
		{
			switch($config['pattern'])
			{
				case 'int':
					if(!preg_match('/^[0-9]+?$/is',$_POST[$field.$config['name1']]) || 
					   !preg_match('/^[0-9]+?$/is',$_POST[$field.$config['name2']]) 
					   ){
						exit($config['showname'].'必须是一个数字');
					}
					
					if(!empty($config['minlength']) && 
					   ( $_POST[$field.$config['name1']] < intval($config['minlength']) ||
					     $_POST[$field.$config['name2']] < intval($config['minlength']))
					){
						exit($config['showname'].'不能小于'.$config['minlength']);
					}
					
					if(!empty($config['maxlength']) && 
					   ( $_POST[$field.$config['name1']] > intval($config['maxlength']) ||
					     $_POST[$field.$config['name2']] > intval($config['maxlength']))
					 ){
						exit($config['showname'].'不能大于'.$config['maxlength']);
					}
					
					if($_POST[$field.$config['name1']] > $_POST[$field.$config['name2']] )
					{
						exit($config['showname'].'前者不能大于后者');
					}
					break;
			}
		}
		
		if($config['name_position']==0){
			return $_POST[$field.$config['name1']].','.$_POST[$field.$config['name2']];

		}else if($config['name_position']==2){
			return array($field . $config['name1'] => $_POST[$field.$config['name1']],$field . $config['name2'] => $_POST[$field.$config['name2']]);

		}else{
			return array($config['name1'].$field => $_POST[$field.$config['name1']],$config['name2'].$field => $_POST[$field.$config['name2']]);
		}
	}

	function search($field,$config,&$row)
	{
		$sql='';
		if(isset($_POST[$field.$config['name1']])){
			$begin = (int)$_POST[$field.$config['name1']];
		}else{
			$begin = (int)$this->query->get($field.$config['name1']);
		}
		if(isset($_POST[$field.$config['name2']])){
			$end = (int)$_POST[$field.$config['name2']];
		}else{
			$end = (int)$this->query->get($field.$config['name2']);
		}
		//$end = $this->query->get($field..$config['name2']);
		if(!$begin && !$end) return '';
		
		if($begin)
		{
			//$begin = $this->date_helper->get_time_stamp($begin);
			$sql.=" AND `$field`>'$begin'";
			$row[$field.'_begin']=$begin;
		}
		if($end)
		{
			$end = $this->date_helper->get_time_stamp($end);
			$sql.=" AND `$field`<'$end'";
			$row[$field.'_end']=$end;
		}
		return $sql;
	}
}