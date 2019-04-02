<?php
class DatasourceModel extends PHPHand_Model{
	function delete($table,$column,$value){
		$this->db->query("DELETE FROM `$table` WHERE `$column`='$value'");
	}
}