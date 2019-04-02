<?php
class FileCheckModel extends PHPHand_Model
{
	function check($field,$config)
	{
		return NULL;
		$file=$_FILES[$field];
		if($file['error']){
			PHPHand_Action::getInstance()->error("请为{$config['showname']}选择文件",$field);
		}
		if($file['size']==0){
			PHPHand_Action::getInstance()->error("请为{$config['showname']}上传文件",$field);
		}
		if(!empty($config['maxsize']) && (int)$file['size']>$config['maxsize']){
			PHPHand_Action::getInstance()->error("{$config['showname']}文件太大了",$field);
		}
		
		$dotPosition=strrpos($file['name'], ".");
		if(!$dotPosition){
			PHPHand_Action::getInstance()->error("{$config['showname']}文件类型缺乏",$field);
		}
		
		$ext=substr($file['name'],$dotPosition+1,strlen($file['name'])-$dotPosition);
		$exts = explode(',',$config['ext']);
		if(!empty($config['ext']) && !in_array(strtolower($ext),$exts) && !in_array('*',$exts)){
			PHPHand_Action::getInstance()->error("{$config['showname']}文件类型不正确。",$field);
		}
		
		$fileTempName=time()."_".rand(1000,9999).".".$ext;
		$date = date('Y_m');
		
		$tempPathArray=split('/',$config['path'].'/'.$date);
		$tempPath='';
		foreach($tempPathArray as $f){
			if($tempPath=='') $tempPath=$f;
			else $tempPath.='/'.$f;
			if(!is_dir(__ROOT__.'/'.$tempPath)) @mkdir(__ROOT__.'/'.$tempPath);
		}

		
		@move_uploaded_file($file['tmp_name'],__ROOT__.'/'.$config['path'].'/'.$date.'/'.$fileTempName);
		return '/'.$config['path'].'/'.$date.'/'.$fileTempName;
	}
}