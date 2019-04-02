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
	'multi_parent' => array(
		'type' => '*',
		'required' => 'false',
		'default' => '0',
		
		'input' => 'select',
		'showname' => '多对多',
		'data_source' => '否=0,是=1',
	),
	'final_only' => array(
		'type' => '*',
		'required' => 'false',
		'default' => 'false',
		
		'input' => 'select',
		'showname' => '父类不可选',
		'data_source' => '否=false,是=true',
	),
);