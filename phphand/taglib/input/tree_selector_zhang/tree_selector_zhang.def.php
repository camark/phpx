<?php
$params=array(
	'name' => array('type'=>'*'),
	'from_table' => array(
		'type' => '*',
		
		'input' => 'text',
		'showname' => '源表',
	),
	'show_column' => array(
		'type' => '*',
		
		'input' => 'text',
		'showname' => '显示列',
	),
	'value_column' => array(
		'type' => '*',
		
		'input' => 'text',
		'showname' => '值列',
	),
	'fid_column' => array(
		'type' => '*',
		'required' => 'true',
		
		
		'input' => 'text',
		'showname' => '父关联列',
	),
	'max_selection' => array(
		'type' => '*',
		'required' => 'true',
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