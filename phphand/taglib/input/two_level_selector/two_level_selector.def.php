<?php
$params=array(
	'name' => array(
		'type' => '*',
	),
	'default_value' => array(
		'type' => '*',
		'required' => 'false',
		'default' => '0',

		'input' => 'text',
		'showname' => '默认值',
	),

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
);