<?php
$params=array(
	'name' => array('type'=>'*'),
	
	'minlength' => array(
		'type' => '*',
		
		'showname' => '最短输入长度',
		'default' => 0,
		'input'=>'text',
	),
	'maxlength' => array(
		'type' => '*',

		'showname' => '最大输入长度',
		'input' => 'text',
	),
	'default_value' => array(
		'type' => '*',
		
		'showname' => '默认值',
		'default' => '',
		'input' => 'text',
	),
	
	'width' => array(
		'type' => '*',
		
		'showname' => '宽度',
		'default' => '600',
		'input' => 'text',
	),
);