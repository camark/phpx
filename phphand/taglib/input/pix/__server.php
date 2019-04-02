<?php
class Server extends PHPHand_Action
{
	function upload()
	{
		if(sizeof($_POST)>0 || sizeof($_FILES)>0)
		{
			$ds=$this->query->get('ds');
			$show=$this->query->get('showname');

			if($show){
			}else if(!$ds){
				$ds='jpg,gif,png,flv,mp3,wav,mp4,swf,mov,3gp,mpg,amr,awd,vox,ogg,pcm,doc,docx,rtf,pdf,txt';
				$show = 'uploading';
			}else{
				if(strpos($ds,'flv')!==false)
				{
					$show ='video';
				}elseif(strpos($ds,'mp3')!==false)
				{
					$show = 'audio';
				}elseif(strpos($ds,'jpg')!==false){
					$show='photo';
				}else{
					$show= 'resume';
				}
			}
			switch(trim($_FILES['file']['type'])){
			case 'image/gif':
			case 'image/jpeg':
			case 'image/jpg':
			case 'image/png':
				$check_flag =  $this->check('file',array(
					'maxsize' => 1024*1024*40,
					'showname' => $show,
					'path' => 'uploads',
					'ext' => $ds,
				));
				if($this->query->get('box_id')) 
				{
					echo $this->query->get('box_id').','.$check_flag;
				}
				else
				{
					echo $check_flag;
				}
				break;
			default:
				$check_flag =  $this->check('file',array(
					'maxsize' => 1024*1024*40,
					'showname' => $show,
					'path' => 'uploads',
					'ext' => $ds,
				));
				if($this->query->get('box_id')) 
				{
					echo $this->query->get('box_id').','.$check_flag;
				}
				else
				{
					echo $check_flag;
				}
			}
		}
	}


	function check($field,$config)
	{
		$config['showname'] = urldecode($config['showname']);
		$file=$_FILES[$field];
		if($file['error']){
			PHPHand_Action::getInstance()->error("请为{$config['showname']}选择文件",$field);
		}
		if($file['size']==0){
			PHPHand_Action::getInstance()->error("请为{$config['showname']}上传文件",$field);
		}
		if((int)$file['size']>$config['maxsize']){
			PHPHand_Action::getInstance()->error("{$config['showname']}文件太大了",$field);
		}
		$dotPosition=strrpos($file['name'], ".");
		if(!$dotPosition){
			PHPHand_Action::getInstance()->error("{$config['showname']}文件类型缺乏",$field);
		}
		$ext=substr($file['name'],$dotPosition+1,strlen($file['name'])-$dotPosition);
		$exts = explode(',',$config['ext']);
		if(!in_array(strtolower($ext),$exts) && !in_array('*',$exts)){
			PHPHand_Action::getInstance()->error("{$config['showname']}文件类型不正确。",$field);
		}

		if($this->config->get('save_handler'))
		{
			$filename=date('Y_m_d_H_i_s').'_'.rand(100,999).'.'.$ext;
			$save_handler=explode('.',$this->config->get('save_handler'));
			$filepath = $this->{$save_handler[0]}->{$save_handler[1]}($file['tmp_name'],$filename);
		}else{
			$filepath = $this->{'input.pix.helper'}->form_filepath($config,$ext);
			@move_uploaded_file($file['tmp_name'],__ROOT__.$filepath);
		}
		$this->{'input.pix.helper'}->check_db();
		$this->attachment->none_pre()->_insert(array(
			'path' => $filepath,
			'createdate' => time(),
			'usage' => 0,
		));

		return $filepath;
	}
}