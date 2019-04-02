<?php
/**
 * 把一些通用的验证放在此类，其他类验证先调用此类的验证方法即可
 * @author Administrator
 *
 */
class TextCheckModel extends PHPHand_Model
{
	//控制器的句柄
   private static $_instance=null;
	
   //构造器，
	function __construct(){
		self::$_instance=$this;
	}
	
   public static function getInstance(){
		if(!self::$_instance){
			new self();
		}
		return self::$_instance;
	}
	/**
	 * 基本验证
	 * 必填项会验证是否为空
	 * 有长度限制的会验证长度是否符合要求
	 * @param $field
	 * @param $config
	 */
	function checkBase($field,$config)
	{

	   if(isset($config['is_must']) && $config['is_must'] && (!isset($_POST[$field]) || !$_POST[$field]))
		{
			PHPHand_Action::getInstance()->error($config['showname'].'不能留空',$field);
		}
		
		if(empty($config['pattern']) && isset($config['minlength']) && !empty($config['minlength']) && 
		     mb_strlen($_POST[$field],'utf-8')<$config['minlength'])
		{
			PHPHand_Action::getInstance()->error($config['showname'].'长度不能少于'.$config['minlength'].'个字符',$field);
		}
		if(empty($config['pattern']) && isset($config['maxlength']) && !empty($config['maxlength']) && 
		     mb_strlen($_POST[$field],'utf-8')>$config['maxlength'])
		{
			PHPHand_Action::getInstance()->error($config['showname'].'长度不能多于'.$config['maxlength'].'个字符',$field);
		}
	}
	/**
	 * 必填项会验证是否为空
	 * 有长度限制的会验证长度是否符合要求
	 * @param $field
	 * @param $config
	 */
	function check($field,$config)
	{
		if((!isset($config['is_must']) || !$config['is_must']) && !$_POST[$field]){
			return '';
		}
	   	if(isset($config['is_must']) && $config['is_must'] && (!isset($_POST[$field]) || !$_POST[$field]))
		{
			PHPHand_Action::getInstance()->error($config['showname'].'不能留空',$field);
		}
		
		$this->checkBase($field,$config);
		if( !empty($_POST[$field]) && isset($config['pattern']) )
		{
			switch($config['pattern'])
			{
				case 'int':
					if(!preg_match('/^[0-9]+?$/is',$_POST[$field]))
					{
						exit($config['showname'].'必须是一个整数');
					}
					if(isset($config['minlength']) && !empty($config['minlength']) && ( ((float)$_POST[$field]) < intval($config['minlength']) ) )
					{
						exit($config['showname'].'不能小于'.$config['minlength']);
					}
					if(isset($config['maxlength']) && !empty($config['maxlength']) && ( ((float)$_POST[$field]) > intval($config['maxlength']) ) )
					{
						exit($config['showname'].'不能大于'.$config['maxlength']);
					}
					break;
				case 'float':
					if(!preg_match('/^[0-9\.]+?$/is',$_POST[$field]))
					{
						exit($config['showname'].'必须是一个数字');
					}
					if(isset($config['minlength']) && !empty($config['minlength']) && ( ((float)$_POST[$field]) < intval($config['minlength']) ) )
					{
						exit($config['showname'].'不能小于'.$config['minlength']);
					}
					if(isset($config['maxlength']) && !empty($config['maxlength']) && ( ((float)$_POST[$field]) > intval($config['maxlength']) ) )
					{
						exit($config['showname'].'不能大于'.$config['maxlength']);
					}
					break;
				case 'email':
					if(!preg_match('/^[0-9a-z_\.\-]+?@[a-z0-9_\-\.]+?$/is',$_POST[$field]))
					{
						exit($config['showname'].'不是一个合法的Email');
					}
					break;
				case 'mobile':
					 if(!preg_match('/^[0-9\-]+$/', $_POST[$field]))
					 {
					 	exit($config['showname'].'不是一个合法的手机号');
					 }
					 break;
				case 'idcard':
					if(!$this->validation_filter_id_card($_POST[$field]))
					{
						exit($config['showname'].'不是一个合法的身份证号');
					}	 
					break;
				default:
					if(empty($config['pattern']) && isset($config['minlength']) && !empty($config['minlength']) && mb_strlen($_POST[$field],'utf-8')<$config['minlength'])
					{
						PHPHand_Action::getInstance()->error($config['showname'].'长度不能少于'.$config['minlength'].'个字符',$field);
					}
					if(empty($config['pattern']) && isset($config['maxlength']) && !empty($config['maxlength']) && mb_strlen($_POST[$field],'utf-8')>$config['maxlength'])
					{
						PHPHand_Action::getInstance()->error($config['showname'].'长度不能多于'.$config['maxlength'].'个字符',$field);
					}
			}
		}

		return $this->filterBadChar($_POST[$field]);
	}
	
     /**
	 *
	 * 过滤非法字符
	 */
	private function filterBadChar($content)
	{
		$arr = array("/script|select|insert|update|delete|union|into|load_file|outfile|group_contact/" => '');
		return $content = trim(preg_replace(array_keys($arr),array_values($arr),$content)); 
	}
	
	//身份证验证 begin
	function validation_filter_id_card($id_card)
	{
		if(strlen($id_card) == 18)
		{
			return $this->idcard_checksum18($id_card);
		}
		elseif((strlen($id_card) == 15))
		{
			$id_card = idcard_15to18($id_card);
			return $this->idcard_checksum18($id_card);
		}
		else
		{
			return false;
		}
	}
	// 计算身份证校验码，根据国家标准GB 11643-1999
	function idcard_verify_number($idcard_base)
	{
		if(strlen($idcard_base) != 17)
		{
			return false;
		}
		//加权因子
		$factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
		//校验码对应值
		$verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
		$checksum = 0;
		for ($i = 0; $i < strlen($idcard_base); $i++)
		{
			$checksum += substr($idcard_base, $i, 1) * $factor[$i];
		}
		$mod = $checksum % 11;
		$verify_number = $verify_number_list[$mod];
		return $verify_number;
	}
	// 将15位身份证升级到18位
	function idcard_15to18($idcard)
	{
		if (strlen($idcard) != 15){
			return false;
		}else{
			// 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
			if (array_search(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false){
				$idcard = substr($idcard, 0, 6) . '18'. substr($idcard, 6, 9);
			}else{
				$idcard = substr($idcard, 0, 6) . '19'. substr($idcard, 6, 9);
			}
		}
		$idcard = $idcard . $this->idcard_verify_number($idcard);
		return $idcard;
	}
	// 18位身份证校验码有效性检查
	function idcard_checksum18($idcard){
		if (strlen($idcard) != 18){ return false; }
			$idcard_base = substr($idcard, 0, 17);
		if ($this->idcard_verify_number($idcard_base) != strtoupper(substr($idcard, 17, 1))){
			return false;
		}else{
			return true;
		}
	} 
  //身份证验证end
  
	
}