<?php
define('SROOT',dirname(__FILE__));
if(!isset($_GET['key'])) exit('错误的参数');
function get_app_dir(){
	if(!file_exists(SROOT.'/data/'.$_GET['key'].'.php')){
		exit('错误的参数');
	}
	include SROOT.'/data/'.$_GET['key'].'.php';
	return $path;
}
include SROOT.'/../../../phphand.php';

$app=PHPHand::getAction('interface_selector_app','index');
$app->run();