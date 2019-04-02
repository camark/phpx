<?php
class Sql_debuggerModel extends PHPHand{
	function out_print($sql)
	{
		if(isset($_SESSION['debugger']) && $_SESSION['debugger']=='phphand')
		{
			echo '<div style="background:#ff0;color:red;border:1px solid #d00;">';
			echo $sql;
			echo '</div>';
		}
	}
}