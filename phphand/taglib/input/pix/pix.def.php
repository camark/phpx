<?php
$params=array(
	'name' => array('type'=>'*'),
	'exts' => array('type' => '*','showname'=>'文件类型','input'=>'text'),
	'max_num' => array('type'=>'*','showname'=>'最多上传数量','input'=>'text','required'=>'false','default'=>10),
	'save_api' => array(
		'type' => '*',
		'showname' => '存储接口',
		'input' => 'text',
		'required' => 'false',
		'default' => '',
	),
);