<?php
$params=array(
	'name' => array('type' => '*'),
	'default_value' => array('type'=>'*','showname'=>'默认值','input'=>'text'),
	
	'category_table' => array(
		'type' => '*',
		
		'input' => 'text',
		'showname' => '分类源表',
	),
	'category_show_column' => array(
		'type' => '*',
		
		'input' => 'text',
		'showname' => '分类显示列',
	),
	'category_value_column' => array(
		'type' => '*',
		
		'input' => 'text',
		'showname' => '分类值列',
	),
	'category_fid_column' => array(
		'type' => '*',
		'required' => 'true',
		
		
		'input' => 'text',
		'showname' => '分类父关联列',
	),
	
	'target_table' => array(
		'type' => '*',
		
		'input' => 'text',
		'showname' => '目标源表',
	),
	'target_show_column' => array(
		'type' => '*',
		
		'input' => 'text',
		'showname' => '目标显示列',
	),
	'target_value_column' => array(
		'type' => '*',
		
		'input' => 'text',
		'showname' => '目标值列',
	),
	'target_fid_column' => array(
		'type' => '*',
		'required' => 'true',
		
		
		'input' => 'text',
		'showname' => '目标父关联列',
	),
	
);