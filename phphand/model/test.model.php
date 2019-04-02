<?php
class TestModel extends PHPHand_Model{
	function test($msg){
		echo('<h1>Test result:'.$msg.'</h1>');
	}
}