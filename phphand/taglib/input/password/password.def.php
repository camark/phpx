<?php
$params=array(
	'name' => array(
	),
	'minlength' => array(
		'type' => '*',

		'showname' => '最短输入长度',
		'input' => 'text',
	),
	'maxlength' => array(
		'type' => '*',

		'showname' => '最大输入长度',
		'input' => 'text',
	),
	'show_repeat' => array(
		'type' => '*',
		'showname' => '显示重复密码',
		'input' => 'select',
		'data_source'=> '是=1,否=0',
		'default_value'=>'1',
	),
);