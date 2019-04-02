<?php
class PasswordCheckModel extends PHPHand_Model
{
	function check($field,$config)
	{
	    //调用共同的验证方法 后加
	    if($config['update_check_original']=='1' && $_POST[$field])
	    {
	    	//必须验证原密码
	    	if(md5($_POST['old_'.$field])!=$_SESSION[$_POST['original_key_'.$field]])
	    	{
	    		exit('原密码验证失败。');
	    	}
	    }
	    if(isset($_POST['original_key_'.$field]))
	    {
	    	//说明原来的密码有值
	    	//此时如果不输入新密码，则返回false，表示不修改密码
	    	if(!$_POST[$field] && (!isset($_POST['repeat_'.$field]) || !$_POST['repeat_'.$field]))
	    	{
	    		//返回false，表示可以不修改密码
	    		return false;
	    	}
	    }else{
	    	if(isset($config['is_must']) && $config['is_must'] && !$_POST[$field])
	    	{
	    		exit($config['showname'].'不能留空');
	    	}
	    }
		if(!empty($_POST[$field]) && isset($_POST['repeat_'.$field]) && empty($_POST['repeat_'.$field]))
		{
			PHPHand_Action::getInstance()->error($config['showname'].'再次输入不能留空',$field);
		}
	    if(!empty($_POST[$field]) && isset($_POST['repeat_'.$field]) && $_POST[$field] != $_POST['repeat_'.$field] )
		{
			PHPHand_Action::getInstance()->error($config['showname'].'两次输入不一致',$field);
		}
		return md5($_POST[$field]);
	}
}