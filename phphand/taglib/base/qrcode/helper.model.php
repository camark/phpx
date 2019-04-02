<?php
class Base_qrcode_helperModel extends PHPHand
{
	function get_qrcode($url,$matrixPointSize=7)
	{
		include_once __ROOT__.'/phphand/taglib/base/qrcode/lib/qrlib.php';

		$qr_data = md5($url);
		if(!is_dir(__ROOT__.'/phphand/taglib/base/qrcode/caches/')) mkdir(__ROOT__.'/phphand/taglib/base/qrcode/caches/');
		if(!is_dir(__ROOT__.'/phphand/taglib/base/qrcode/caches/'.substr(md5($qr_data),0,2).'/')) mkdir(__ROOT__.'/phphand/taglib/base/qrcode/caches/'.substr(md5($qr_data),0,2));
		if(!is_dir(__ROOT__.'/phphand/taglib/base/qrcode/caches/'.substr(md5($qr_data),0,2).'/'.substr(md5($qr_data),2,2).'/')) mkdir(__ROOT__.'/phphand/taglib/base/qrcode/caches/'.substr(md5($qr_data),0,2).'/'.substr(md5($qr_data),2,2));
		$filename = __ROOT__.'/phphand/taglib/base/qrcode/caches/'.substr(md5($qr_data),0,2).'/'.substr(md5($qr_data),2,2).'/'.preg_replace('/(\/|&|\?|\.|:)/is','_',$qr_data).'.png';
		if(file_exists($filename))
		{
			unlink($filename);
		}
		$rf = 'https://'. $_SERVER['HTTP_HOST'] . $this->env->get('app_url').'/phphand/taglib/base/qrcode/caches/'.substr(md5($qr_data),0,2).'/'.substr(md5($qr_data),2,2).'/'.preg_replace('/(\/|&|\?|\.|:)/is','_',$qr_data).'.png';

		$errorCorrectionLevel = 'L';


		QRcode::png($url ,$filename,$errorCorrectionLevel,$matrixPointSize,0);

		return $rf;

	}
}