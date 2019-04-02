<?php
class Server extends PHPHand_Action
{
	function css()
	{
		$folder = dirname(__FILE__).'/cache';
		$cache_name=md5($this->query->get('f'));
		header('content-type:text/css');
		if(file_exists($folder.'/'.$cache_name.'.css') && $this->config->get('debug_mode')==false)
		{
			echo file_get_contents($folder.'/'.$cache_name.'.css');
			exit;
		}
		$files = explode('|',urldecode($this->query->get('f')));
		$content = '';
		foreach($files as $file)
		{
			$content.= "@import url('".str_replace('$','/',$file)."?".time()."');";
		}
		echo $content;
		file_put_contents($folder.'/'.$cache_name.'.css',$content);
	}
}