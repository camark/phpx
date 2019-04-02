<?php
$path=get_app_dir().'/config.php';
include $path;
if(isset($externDir)){
	$externDir=dirname($path).'/../'.$externDir;
}