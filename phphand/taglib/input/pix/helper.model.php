<?php
class Input_pix_helperModel extends PHPHand_Model
{
	//删除某个记录的时候
	//检查该记录中是否有对上传文件的引用
	//如果有引用，则在上传记录表中减少引用的数字统计
	//当数字统计为0的时候，则自动删除记录表中对应的文件
	//并且同时删除相关文件（文件生命周期结束）

	//首先要检查该表的config配置文件，以确认该表中有哪些字段使用<input:pix />
	//控件进行了文件上传，只有使用该控件的字段的数据是有
	function cut_pix_usage($table,$data_id,$config)
	{

	}



	function save_data_to_file($data,$config,$save_to_extension='txt')
	{
		$filepath = $this->form_filepath($config,$save_to_extension);
		$this->check_db();
		file_put_contents(__ROOT__.$filepath,$data);

		return $filepath;
	}

	function check_db()
	{
		if(!is_dir(dirname(__FILE__).'/host/')) mkdir(dirname(__FILE__).'/host/');
		if(!file_exists(dirname(__FILE__).'/host/'.$_SERVER['HTTP_HOST'].'.installed'))
		{
			$this->db->query("CREATE TABLE `attachment`(
				`attachment_id` INT(11) NOT NULL AUTO_INCREMENT,
				`path` VARCHAR(128) NOT NULL,
				`usage` SMALLINT(3) NOT NULL DEFAULT '0' COMMENT '当前应用统计',
				`createdate` INT(10) NOT NULL DEFAULT '0',
				PRIMARY KEY (`attachment_id`)
				)ENGINE=INNODB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");
			file_put_contents(dirname(__FILE__).'/host/'.$_SERVER['HTTP_HOST'].'.installed',date('Y-m-d H:i:s'));
		}
	}

	function form_filepath($config,$ext)
	{
		$fileTempName=time()."_".rand(1000,9999).".".$ext;
		$date = date('Y_m');
		
		$tempPathArray=explode('/',$config['path'].'/'.$date);
		$tempPath='';
		foreach($tempPathArray as $f){
			if($tempPath=='') $tempPath=$f;
			else $tempPath.='/'.$f;
			if(!is_dir(__ROOT__.'/'.$tempPath)) @mkdir(__ROOT__.'/'.$tempPath);
		}

		return '/'.$config['path'].'/'.$date.'/'.$fileTempName;
	}
}