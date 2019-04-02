<?php
include __ROOT__.'/phphand/taglib/base/qrcode/lib/qrlib.php';

$qr_data = md5($param.url);
if(!is_dir(__ROOT__.'/phphand/taglib/base/qrcode/caches/')) mkdir(__ROOT__.'/phphand/taglib/base/qrcode/caches/');
if(!is_dir(__ROOT__.'/phphand/taglib/base/qrcode/caches/'.substr(md5($qr_data),0,2).'/')) mkdir(__ROOT__.'/phphand/taglib/base/qrcode/caches/'.substr(md5($qr_data),0,2));
if(!is_dir(__ROOT__.'/phphand/taglib/base/qrcode/caches/'.substr(md5($qr_data),0,2).'/'.substr(md5($qr_data),2,2).'/')) mkdir(__ROOT__.'/phphand/taglib/base/qrcode/caches/'.substr(md5($qr_data),0,2).'/'.substr(md5($qr_data),2,2));
$filename = __ROOT__.'/phphand/taglib/base/qrcode/caches/'.substr(md5($qr_data),0,2).'/'.substr(md5($qr_data),2,2).'/'.preg_replace('/(\/|&|\?|\.|:)/is','_',$qr_data).'.png';
if(file_exists($filename))
{
	unlink($filename);
}
$rf = 'https://'. $_SERVER['HTTP_HOST'] . '__TAG__/caches/'.substr(md5($qr_data),0,2).'/'.substr(md5($qr_data),2,2).'/'.preg_replace('/(\/|&|\?|\.|:)/is','_',$qr_data).'.png';

$errorCorrectionLevel = 'L';

$matrixPointSize = 7;


QRcode::png($param.url ,$filename,$errorCorrectionLevel,$matrixPointSize,0);
if($param.method=='default'){
?><img src="{$rf}" style="margin:0 auto;" /><?php
}else{
header('location:'. $rf);
}
?>