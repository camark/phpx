<?php
$params=array(
	'name' => array(
	),
	'key_source' => array(
		'type' => '*',
		'input' => 'text',
		'showname' => '关键词数据源',
	),
	'from_table' => array(
		'type' => '*',
		
		'input' => 'text',
		'showname' => '源表',
	),

	'value_column' => array(
		'type' => '*',
		
		'input' => 'text',
		'showname' => '值列',
	),
	'max_selection' => array(
		'type' => '*',
		'required' => 'false',
		'default' => '1',
		
		'input' => 'text',
		'showname' => '最多选择',
	),
	'state' => array(
		'type' => '*',
		'required' => 'false',
		'default' => '',
		
		'input' => 'text',
		'showname' => '筛选条件',
	),
);