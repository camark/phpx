<?php
$params=array(
	'name' => array('type'=>'*'),
	'name1' => array('type'=>'string','showname'=>'名称1','input'=>'text'),
	'name2' => array('type'=>'string','showname'=>'名称2','input'=>'text'),
	'unit' => array('type'=>'string','showname'=>'单位 ','input'=>'text','default'=>'万'),
    'minlength' => array('type' => '*','showname' => '最短输入长度','input' => 'text'),
	'maxlength' => array('type' => '*','showname' => '最大输入长度','input' => 'text'),
    'pattern' => array('type' => '*','showname' => '验证规则','input' => 'text'),
    'name_position' => array('type' => '*','showname' => '名称位置','input'=>'select','data_source'=>'前置=1,后置=2','default_value'=>1),
);