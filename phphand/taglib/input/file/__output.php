<?php
class FileOutputModel extends PHPHand_Model{
	function output($data,$config)
	{
		if(!$data)
		{
			echo '&nbsp;';
			return;
		}
		$sh_options=$this->hp->get_options($config);
		$ext = substr($data,strpos($data,'.')+1);
		
		switch($ext){
			case 'gif':
			case 'jpg':
			case 'png':
			case 'jpeg':
				echo '<img src="phphand/taglib/com/timthumb/timthumb.php?src='.$data.'&w=110&h=110&q=100" />';
				break;
			default:
				echo '<span class="glyphicon glyphicon-paperclip"></span> Attachment';
		}
	}
}