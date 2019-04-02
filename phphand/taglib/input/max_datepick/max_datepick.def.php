<?php
$params=array(
	'name' => array(
	),
	'method' => array(
		'type' => '*',
		'required' => 'false',
		'default' => 'date',
	),
	'show_default' => array(
		'type' => '*',

		'showname' => '默认当前',
		'input' => 'select',
		'data_source' => '是=1,否=0',
	),
	'must' => array(
		'type' => '*',

		'showname' => '必填',
		'input' => 'select',
		'data_source' => '是=1,否=0',
	),
	'default_value' => array(
		'type' => '*',
		'showname' => '默认值',
		'input' => 'text',
	),
	'data_type' => array(
		'type' => '*',
		'showname' => '数据类型',
		'input' => 'select',
		'data_source' => '数字类型=1,字符类型=2',
		'default' => 1,
	),
);