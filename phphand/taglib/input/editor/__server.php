<?php
class Server extends PHPHand_Action
{
	function _default()
	{
		$file = $_FILES['upfile'];
		if($file['error']){
			$this->output("请选择文件",false);
		}
		if($file['size']==0){
			$this->output("请上传文件",false);
		}
		if((int)$file['size']>1024*1000*2){
			$this->output("文件太大了",false);
		}
		$dotPosition=strrpos($file['name'], ".");
		if(!$dotPosition){
			$this->output("文件类型缺乏",false);
		}
		$ext=substr($file['name'],$dotPosition+1,strlen($file['name'])-$dotPosition);
		$exts = explode(',','jpg,gif,png');
		if(!in_array(strtolower($ext),$exts) && !in_array('*',$exts)){
			$this->output("文件类型不正确。",false);
		}

		if($this->config->get('save_handler'))
		{
			$filename=date('Y_m_d_H_i_s').'_'.rand(100,999).'.'.$ext;
			$save_handler=explode('.',$this->config->get('save_handler'));
			$filepath = $this->{$save_handler[0]}->{$save_handler[1]}($file['tmp_name'],$filename);
		}else{
			$filepath = $this->{'input.pix.helper'}->form_filepath($config,$ext);
			$filename_arr = explode('/',$filepath);
			$filename = $filename_arr[sizeof($filename_arr)-1];
			@move_uploaded_file($file['tmp_name'],__ROOT__.$filepath);
		}

		$this->output('SUCCESS',array(
			'name' => $filename,
			'url' => $filepath,
			'type' => $ext,
		));
	}

	function output($state,$info)
	{
		if($state=='SUCCESS')
		{
			echo json_encode(array(
				'originalName' => $_FILES['upfile']['name'],
				'name' => $info['name'],
				'url' => $info['url'],
				'type' => $info['type'],
				'size' => $_FILES['upfile']['size'],
				'state' => 'SUCCESS',
			));
		}else{
			echo json_encode(array(
				'originalName' => $_FILES['upfile']['name'],
				'name' => NULL,
				'url' => NULL,
				'size' => $_FILES['upfile']['size'],
				'type' => NULL,
				'state' => $state,
			));
		}
	}
}

//上传成功
//{"originalName":"2841_558581_505746.jpg","name":"14634459355499.jpg","url":"upload\/20160517\/14634459355499.jpg","size":251774,"type":".jpg","state":"SUCCESS"}

//上传失败
//{"originalName":"4.jpg","name":null,"url":null,"size":488737,"type":".jpg","state":"\u4e0d\u5141\u8bb8\u7684\u6587\u4ef6\u7c7b\u578b"}