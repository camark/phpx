<?php
class Com_timthumb_helperModel extends PHPHand_Action
{
	function thumb($u,$w,$h)
	{
		if(preg_match('/\/([^\/]+?)\.([^\/\.]+?)$/i',$u,$match))
		{
			$save_name = $match[1];
			if($w) $save_name.='w'.$w;
			if($h) $save_name.='h'.$h;
			$save_name.='.'.$match[2];
		}else{
			$save_name=base64_encode($u).'w'.$w.'h'.$h;
		}

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

		$sn = dirname(__FILE__).'/temp/'.base64_encode($u.$w.$h);
		file_put_contents($sn,$res);

		return $sn;
	}
}