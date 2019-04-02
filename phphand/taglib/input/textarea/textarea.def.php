<?php
$params=array(
	'name' => array(
	),
	'default_value' => array(
		'type' => '*',

		'showname' => '默认值',
		'input' => 'text',
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
	'cols' => array(
		'type' => '*',
		'showname' => '列数',
		'input' => 'text',
		'default' => '50',
	),
	'rows' => array(
		'type' => '*',
		'showname' => '行数',
		'input' => 'text',
		'default' => '7',
	),
	'trans_lines' => array(
		'type' => '*',
		'showname' => '格式转换',
		'input' => 'select',
		'data_source' => '是=y,否=n',
		
		'required'=>'false',
		'default' => '1',
	),
);