<?php
//header('location:'.$_SERVER['QUERY_STRING']);
header('Access-Control-Allow-Origin: *');
echo file_get_contents(dirname(__FILE__).'/'.$_SERVER['QUERY_STRING']);