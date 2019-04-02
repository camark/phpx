<?php
$params=array(
	'title' => array(
		'type' => 'string',
		'required' => 'true',
		'default' => '列表',
	),
	'table' => array(
		'type' => 'string',
		'required' => 'true',
		'default' => '',
	),
	'show_column' => array(
		'type' => 'string',
		'required' => 'true',
		'default' => '',
	),
	'id_column' => array(
		'type' => 'string',
		'required' => 'false',
		'default' => '',
	),
	'href' => array(
		'type' => 'string',
		'required' => 'false',
		'default' => '#',
	),
);