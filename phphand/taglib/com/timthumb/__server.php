<?php
class Server extends PHPHand_Action
{
	function _default()
	{
		$u = $this->query->get('url');
		$w = $this->query->get('w');
		$h = $this->query->get('h');

		if(preg_match('/\/([^\/]+?)\.([^\/\.]+?)$/i',$u,$match))
		{
			$save_name = $match[1];
			if($w) $save_name.='w'.$w;
			if($h) $save_name.='h'.$h;
			$save_name.='.'.$match[2];
		}else{
			$save_name=base64_encode($u).'w'.$w.'h'.$h;
		}

		if($this->config->get('check_handler'))
		{
			$handler=explode('.',$this->config->get('check_handler'));
			$url = $this->{$handler[0]}->{$handler[1]}($save_name);
			if(!$url)
			{
				if($this->config->get('save_handler'))
				{
					$thumb_url = 'http://'.$_SERVER['HTTP_HOST'].$this->env->get('app_url').'/phphand/taglib/com/timthumb/timthumb.php?src='.urlencode($u);
					if($w)
					{
						$thumb_url.='&w='.$w;
					}
					if($h)
					{
						$thumb_url.='&h='.$h;
					}
					$res = file_get_contents($thumb_url);
					if(!is_dir(__ROOT__.'/tmp/')) mkdir(__ROOT__.'/tmp/');
					file_put_contents(__ROOT__.'/tmp/'.$save_name,$res);
					$save_handler = explode('.',$this->config->get('save_handler'));
					$filepath = $this->{$save_handler[0]}->{$save_handler[1]}(__ROOT__.'/tmp/'.$save_name,$save_name);
					unlink(__ROOT__.'/tmp/'.$save_name);

					$url = $this->{$handler[0]}->{$handler[1]}($save_name);
					header('location:'.$url);
				}
			}else{
				header('location:'.$url);
			}
		}else{
			header('location:phphand/taglib/com/timthumb/timthumb.php?src='.$u.'&w='.$w.'&h='.$h);
		}
	}
}