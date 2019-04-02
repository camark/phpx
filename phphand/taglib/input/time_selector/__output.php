<?php
class Time_selectorOutputModel
{
	function output($data,$config,$rst=null)
	{
		echo date('H:i',$data-8*3600);
	}
}